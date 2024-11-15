<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    // Show the login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // Handle the login request
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

        // Attempt login for web users with remember functionality
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            return redirect()->intended(route('dashboard'));
        }

        // Attempt login for team members with remember functionality
        if (Auth::guard('team')->attempt($credentials, $remember)) {
            return redirect()->intended(route('order.list')); // Team member dashboard
        }

        // Attempt login for clients with remember functionality
        if (Auth::guard('clients')->attempt($credentials, $remember)) {
            //return redirect()->intended(route('order.list')); // Client dashboard
            echo 'Working...'; die;
        }

        // If credentials don't match, redirect back with an error
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
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
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }
}