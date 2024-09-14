<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\Client;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // List all orders
    public function index()
    {
        $orders = Order::with('client', 'service')->where('user_id', auth()->id())->paginate(10); 
        //echo "<pre>"; print_r($orders); die;
        $clients = Client::all();  // Fetch list of all clients
        $services = Service::all();  // Fetch list of all services
        return view('client.pages.orders.index', compact('orders', 'clients', 'services'));
    }

    // Show form to create a new order
    public function create()
    {
        $clients = Client::all();  // Fetch list of all clients
        $services = Service::all();  // Fetch list of all services
        return view('client.pages.orders.add', compact('clients', 'services'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'order_date' => 'nullable|date',  // Optional order date
            'note' => 'nullable|string|max:255', // Optional team note
        ]);

        // Generate an 8-character alphanumeric order number
        $order_no = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)); // Generates exactly 8 characters

        // Create the new order
        $order = Order::create([
            'client_id' => $validatedData['client_id'],
            'user_id' => auth()->id(),
            'service_id' => $validatedData['service_id'],
            'order_date' => $validatedData['order_date'] ?? now(), // Default to current date if not provided
            'note' => $validatedData['note'] ?? null,
            'order_no' => $order_no,  // Store the generated 8-character alphanumeric order number
        ]);

        // Redirect to the order detail page with success message
        return redirect()->route('client.order.show', ['id' => $order->id])->with('success', 'Order created successfully.');
    }
    
    // Show order details
    public function show($id)
    {
        $order = Order::with('client', 'service')->findOrFail($id);  // Fetch order with related client and service
        return view('client.pages.orders.show', compact('order'));
    }

    // Show form to edit order
    public function edit($id)
    {
        $order = Order::findOrFail($id);
        $clients = Client::all();  // Fetch list of all clients
        $services = Service::all();  // Fetch list of all services
        return view('client.pages.orders.edit', compact('order', 'clients', 'services'));
    }

    // Update order
    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'order_date' => 'required|date',
            'order_details' => 'nullable|string|max:255',
        ]);

        // Update order details
        $order->update($validatedData);

        return redirect()->route('client.order.show', $order->id)->with('success', 'Order updated successfully.');
    }

    // Delete order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('client.order.list')->with('success', 'Order deleted successfully.');
    }

    public function saveNote(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'note' => 'nullable|string|max:255',
        ]);

        // Find the order and update the note
        $order = Order::findOrFail($id);
        $order->note = $request->note;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Note saved successfully.']);
    }
}
