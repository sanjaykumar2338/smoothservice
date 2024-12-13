<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
USE App\Models\Order;
use App\Models\Service;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientWelcome;
use App\Models\ClientStatus;
use Illuminate\Support\Str;
use App\Mail\ClientPasswordChanged;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\ClientReply;
use DB;

class ClientController extends Controller
{
    // List all clients
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->has('search')) {
            $query->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $clients = $query->orderBy('id', 'desc')->where('added_by',getUserID())->paginate(10);
        return view('client.pages.clients.index', compact('clients'));
    }

    public function dashboard(){
        $clients = Client::where('added_by', getUserID())->count();
        $services = Service::where('user_id', getUserID())->count();
        $orders = Order::where('user_id',getUserID())->count();

        return view('client.dashboard_page', compact('clients','services','orders'));
    }

    // Show form to create a new client
    public function create()
    {
        $countries = Country::all();  // Fetch list of all countries
        return view('client.pages.clients.add', compact('countries'));
    }

    // Store new client
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name'=>'',
            'last_name'=>'',
            'email' => 'required|email|unique:clients',
            'billing_address' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'tax_id' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        // If password not provided, generate one
        $password = $validatedData['password'] ?? \Str::random(8);

        // Hash the password
        $hashedPassword = Hash::make($password);

        // Create the new client
        $client = Client::create([
            'first_name' => $validatedData['first_name'] ?? null,
            'last_name' => $validatedData['last_name'] ?? null,
            'email' => $validatedData['email'],
            'billing_address' => $validatedData['billing_address'],
            'country' => $validatedData['country'],
            'state' => $validatedData['state'],
            'postal_code' => $validatedData['postal_code'],
            'company' => $validatedData['company'],
            'tax_id' => $validatedData['tax_id'],
            'phone' => $validatedData['phone'],
            'password' => $hashedPassword,
            'added_by' => getUserID(),
        ]);

        // Send the welcome email if checked
        if ($request->has('send_welcome_email')) {
            Mail::to($client->email)->send(new ClientWelcome($client, $password));
        }

        return redirect()->route('client.list')->with('success', 'Client added successfully.');
    }

    // Show form to edit client
    public function edit($id)
    {
        $client_statues = ClientStatus::where('added_by',getUserID())->get();
        $client = Client::find($id);
        $countries = Country::all();  // Fetch list of all countries
        return view('client.pages.clients.edit', compact('client', 'countries', 'client_statues'));
    }

    // Update client
    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return redirect()->back()->with('error', 'Client not found.');
        }

        $validatedData = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'billing_address' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'tax_id' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'single_line_of_text' => 'nullable|string|max:255',
            'stripe_customer_id' => 'nullable|string|max:255',
            'account_balance' => 'nullable|numeric',
            'status' => 'nullable|integer|exists:client_statuses,id',
            'new_password' => 'nullable|string|min:6',
        ]);

        // Update all fields
        $client->first_name = $validatedData['first_name'] ?? $client->first_name;
        $client->last_name = $validatedData['last_name'] ?? $client->last_name;
        $client->email = $validatedData['email'] ?? $client->email;
        $client->billing_address = $validatedData['billing_address'] ?? $client->billing_address;
        $client->country = $validatedData['country'] ?? $client->country;
        $client->state = $validatedData['state'] ?? $client->state;
        $client->postal_code = $validatedData['postal_code'] ?? $client->postal_code;
        $client->company = $validatedData['company'] ?? $client->company;
        $client->tax_id = $validatedData['tax_id'] ?? $client->tax_id;
        $client->phone = $validatedData['phone'] ?? $client->phone;
        $client->single_line_of_text = $validatedData['single_line_of_text'] ?? $client->single_line_of_text;
        $client->stripe_customer_id = $validatedData['stripe_customer_id'] ?? $client->stripe_customer_id;
        $client->account_balance = $validatedData['account_balance'] ?? $client->account_balance;
        $client->status = $validatedData['status'] ?? $client->status;

        // Update password if a new password is provided
        if (!empty($validatedData['new_password'])) {
            $client->password = Hash::make($validatedData['new_password']);
        }

        // Save the updated client data
        $client->save();

        // Send password reset email if needed
        $sent = 'no';
        if ($request->has('send_email') && $request->send_email && $request->new_password) {
            $sent = 'yes';
            Mail::to($client->email)->send(new ClientPasswordChanged($client, $request->new_password));
        }

        // Send welcome email if required
        if ($request->has('reset_password_welcome_email') && $request->reset_password_welcome_email && $sent=='no') {
            $randomPassword = $request->new_password ?? Str::random(12);

            // Save the hashed password
            $client->password = Hash::make($randomPassword);
            $client->save();

            // Send the email with the new password
            Mail::to($client->email)->send(new ClientWelcome($client, $randomPassword));
        }

        return redirect()->route('client.list')->with('success', 'Client updated successfully.');
    }

    // Delete client
    public function destroy($id)
    {
        $client = Client::find($id);
        
        if (!$client) {
            return redirect()->route('client.list')->with('error', 'Client not found.');
        }
        
        $client->delete();
        return redirect()->route('client.list')->with('success', 'Client deleted successfully.');
    }

    public function profile(){
        return view('client.profile');
    }

    public function updateprofile(Request $request)
    {
        // Determine the user type and get the authenticated user
        $user = null;
        $tableName = ''; // Define the table name based on the user type

        if (getUserType() == 'web') {
            $user = auth()->guard('web')->user();
            $tableName = 'users'; // For web users
        } else if(getUserType() == 'team'){
            $user = auth()->guard('team')->user();
            $tableName = 'team_members'; // For team users
        } else{
            $user = auth()->guard('client')->user();
            $tableName = 'clients'; // For team users
        }

        // Validate request data
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'nullable|string|max:255',
            'email' => 'required|email|unique:' . $tableName . ',email,' . $user->id, // Unique email in specific table
            'password' => 'nullable|string|min:8',
            'phoneNumber' => 'nullable|string|max:15',
            'profile_image' => 'nullable|image|max:800', // Validate image size (max 800KB)
        ]);

        // Update the user's basic information
        if (getUserType() == 'web') {
            $user->name = $request->input('firstName');
        } else {
            // Make sure both first and last names are updated for team users
            $user->first_name = $request->input('firstName');
            $user->last_name = $request->input('lastName');
        }

        $user->email = $request->input('email');
        if (getUserType() == 'client') {
            $user->phone = $request->input('phoneNumber');
        }else{
            $user->phone_number = $request->input('phoneNumber');
        }

        $user->timezone = $request->input('timezone');
        $user->push_notification = $request->input('push_notification') ? 1 : 0;

        // Handle password update, only if provided
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $fileName = time() . '.' . $request->file('profile_image')->extension();
            $request->file('profile_image')->move(public_path('images/profile'), $fileName);
            $user->profile_image = '/images/profile/' . $fileName;
        }

        // Save user details
        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updateImage(Request $request)
    {
        // Get the authenticated user from the 'team' guard
        $user = getAuthenticatedUser();

        // Validate the image file
        $request->validate([
            'profile_image' => 'required|image|mimes:jpg,jpeg,png,gif|max:800', // 800KB max size
        ]);

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }

            // Save the new image
            $fileName = time() . '.' . $request->file('profile_image')->extension();
            $filePath = $request->file('profile_image')->storeAs('images/profile', $fileName, 'public');
            $user->profile_image = '/storage/' . $filePath;  // Store path in database
            $user->save();

            // Return the new image URL as a JSON response
            return response()->json([
                'success' => true,
                'image_url' => asset($user->profile_image)
            ]);
        }

        return response()->json(['success' => false], 400);
    }

    public function deleteImage(Request $request)
    {
        // Get the authenticated user from the 'team' guard
        $user = auth()->user();

        // Check if the user has a profile image
        if ($user->profile_image && file_exists(public_path($user->profile_image))) {
            // Delete the image file from the server
            unlink(public_path($user->profile_image));
            
            // Remove the image path from the database
            $user->profile_image = null;
            $user->save();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 400);
    }

    public function signInAsClient($clientId)
    {
        // Find the client by ID
        $client = Client::find($clientId);

        if (!$client) {
            return redirect()->route('client.list')->with('error', 'Client not found.');
        }

        // Log out the current admin (optional)
        Auth::guard('web')->logout();

        // Log in as the client
        Auth::guard('client')->login($client);

        // Redirect to the client's dashboard or any other route
        return redirect()->route('portal.dashboard')->with('success', "Signed in as {$client->first_name}.");
    }

    public function mergeClients(Request $request)
    {
        $validatedData = $request->validate([
            'source_client_id' => 'required|exists:clients,id',
            'target_client_id' => 'required|exists:clients,id|different:source_client_id',
        ]);

        $sourceClientId = $validatedData['source_client_id'];
        $targetClientId = $validatedData['target_client_id'];

        DB::beginTransaction();

        try {
            // Update orders
            Order::where('client_id', $sourceClientId)
                ->update(['client_id' => $targetClientId]);

            // Update tickets
            Ticket::where('client_id', $sourceClientId)
                ->update(['client_id' => $targetClientId]);

            // Update ticket replies
            TicketReply::where('client_id', $sourceClientId)
                ->update(['client_id' => $targetClientId]);

            // Update client replies
            ClientReply::where('client_id', $sourceClientId)
                ->update(['client_id' => $targetClientId]);

            DB::commit();

            return redirect()->route('client.list')->with('success', 'Clients merged successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while merging clients: ' . $e->getMessage());
        }
    }
}