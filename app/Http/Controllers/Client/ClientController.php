<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientWelcome;

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

        $clients = $query->orderBy('id', 'desc')->paginate(10);
        return view('client.pages.clients.index', compact('clients'));
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
        $client = Client::find($id);
        $countries = Country::all();  // Fetch list of all countries
        return view('client.pages.clients.edit', compact('client', 'countries'));
    }

    // Update client
    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return redirect()->back()->with('error', 'Client not found.');
        }

        $validatedData = $request->validate([
            'first_name' => '',
            'last_name' => '',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'billing_address' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'tax_id' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
        ]);

        // Update client details
        $client->update($validatedData);

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
}