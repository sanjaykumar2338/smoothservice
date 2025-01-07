<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\InvoiceSubscription;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Session;

class LoginController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showWorkspaceForm()
    {
        return view('auth.workspace');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // Handle the login request
    public function loginold(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Get the credentials
        $credentials = $request->only('email', 'password');
        
        // Check if 'remember' checkbox is checked
        $remember = $request->filled('remember');

        // Attempt login for web users with remember functionality
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            return redirect()->intended(route('dashboard'));
        }

        // Attempt login for team members with remember functionality
        if (Auth::guard('team')->attempt($credentials, $remember)) {
            return redirect()->intended(route('order.list')); // Team member dashboard
        }

        // Attempt login for clients with remember functionality
        if (Auth::guard('client')->attempt($credentials, $remember)) {
            return redirect()->intended(route('portal.dashboard'));
        }

        // If credentials don't match, redirect back with an error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Get the credentials
        $credentials = $request->only('email', 'password');
        
        // Check if 'remember' checkbox is checked
        $remember = $request->filled('remember');

        // Get the session domain from .env
        $sessionDomain = env('SESSION_DOMAIN', '.smoothservice.net');

        // Attempt login for web users
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $workspace = Auth::guard('web')->user()->workspace;

            // Redirect to their subdomain or fallback to default route
            if ($workspace) {
                return redirect()->intended("https://{$workspace}{$sessionDomain}/dashboard");
            }
            return redirect()->intended(route('dashboard')); // Fallback to main site
        }

        // Attempt login for team members
        if (Auth::guard('team')->attempt($credentials, $remember)) {
            $addedBy = Auth::guard('team')->user()->added_by;
            $workspace = \App\Models\User::where('id', $addedBy)->value('workspace');

            // Redirect to their subdomain or fallback to default route
            if ($workspace) {
                return redirect()->intended("https://{$workspace}{$sessionDomain}/order-list");
            }
            return redirect()->intended(route('order.list')); // Fallback to main site
        }

        // Attempt login for clients
        if (Auth::guard('client')->attempt($credentials, $remember)) {
            $addedBy = Auth::guard('client')->user()->added_by;
            $workspace = \App\Models\User::where('id', $addedBy)->value('workspace');

            // Redirect to their subdomain or fallback to default route
            if ($workspace) {
                return redirect()->intended("https://{$workspace}{$sessionDomain}/portal-dashboard");
            }
            return redirect()->intended(route('portal.dashboard')); // Fallback to main site
        }

        // If credentials don't match, redirect back with an error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function validateWorkspace(Request $request)
    {
        // Validate the workspace input
        $request->validate([
            'workspace' => 'required|string',
        ]);

        // Check if the workspace exists in the User table
        $workspace = $request->input('workspace');
        $user = \App\Models\User::where('workspace', $workspace)->first();

        if ($user) {
            // Redirect to the subdomain if the workspace exists
            $sessionDomain = env('SESSION_DOMAIN', '.smoothservice.net');
            return redirect()->intended("https://{$workspace}{$sessionDomain}");
        }

        // If the workspace doesn't exist, return with an error
        return back()->withErrors([
            'workspace' => 'Workspace does not exist.',
        ])->withInput($request->only('workspace'));
    }

    public function register(){
        return view('auth.register');
    }

    public function forget(){
        return view('auth.forget');
    }

    public function create_account(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'workspace' => [
                'required',
                'string',
                'max:255',
                'alpha_dash', // Allows letters, numbers, dashes, and underscores
                'unique:users,workspace', // Ensure it's unique across users
            ],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'workspace' => $data['workspace'], // Save workspace
        ]);

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }

    public function switchBackToAdmin()
    {
        // Check if the admin ID is stored in the session
        if (Session::has('admin_id')) {
            $adminId = Session::get('admin_id');
            
            // Log out the current client
            $user = User::find($adminId);
            
            // Log back in as the user
            Auth::guard('web')->login($user);

            // Forget the admin_id session
            \Session::forget('admin_id');

            return redirect()->route('dashboard')->with('success', 'You are now back as admin.');
        }

        return redirect()->route('login')->with('error', 'No admin session found.');
    }

    public function handleWebhook(Request $request)
    {
        $eventType = $request->input('event_type');
        if (in_array($eventType, ['BILLING.SUBSCRIPTION.CANCELLED', 'BILLING.SUBSCRIPTION.SUSPENDED', 'BILLING.SUBSCRIPTION.PAYMENT.FAILED'])) {
            $subscriptionId = $request->input('resource.id');

            // Locate the subscription in your database
            $invoiceSub = InvoiceSubscription::where('subscription_id', $subscriptionId)->first();

            if (!$invoiceSub || $invoiceSub->cancelled_at) {
                Log::info("Webhook received for subscription {$subscriptionId}, already processed or not found.");
                return response()->json(['message' => 'Subscription already processed or not found.'], 200);
            }

            // Update the subscription in the database
            $invoiceSub->update([
                'cancelled_at' => now(),
            ]);

            Log::info("Subscription {$subscriptionId} canceled due to event {$eventType}.");
            return response()->json(['message' => 'Subscription cancellation processed successfully.'], 200);
        }

        return response()->json(['message' => 'Event type not handled.'], 200);
    }
}