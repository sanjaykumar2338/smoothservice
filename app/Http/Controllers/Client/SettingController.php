<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderStatus;

class SettingController extends Controller
{
    // List all statuses
    public function index(Request $request)
    {
        $search = $request->input('search'); // Get the search input

        $orderStatuses = OrderStatus::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })->where('added_by', auth()->id())->paginate(8); // Apply search and paginate results

        return view('client.pages.settings.orderstatuses.index', compact('orderStatuses', 'search'));
    }


    // Show the form to create a new status
    public function create()
    {
        return view('client.pages.settings.orderstatuses.add');
    }

    // Store a new status in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:order_statuses,name|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lock_completed_orders' => 'nullable|boolean',
            'change_status_on_revision' => 'nullable|boolean',
            'enable_ratings' => 'nullable|boolean',
        ]);
        
        //echo "<pre>"; print_r($request->all()); die;
        // Create new OrderStatus
        OrderStatus::create([
            'name' => $request->name,
            'color' => $request->color,
            'description' => $request->description,
            'lock_completed_orders' => $request->has('lock_completed') ? 1 : 0,  // Handle checkbox
            'change_status_on_revision' => $request->has('change_status_on_message') ? 1 : 0, // Handle checkbox
            'enable_ratings' => $request->has('enable_ratings') ? 1 : 0, // Handle checkbox
            'added_by' => auth()->id()
        ]);
    
        return redirect()->route('setting.orderstatuses.list')->with('success', 'Order Status created successfully');
    }


    // Show the form to edit an existing status
    public function edit($id)
    {
        $status = OrderStatus::findOrFail($id);
        return view('client.pages.settings.orderstatuses.edit', compact('status'));
    }

    // Update an existing status in the database
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|unique:order_statuses,name,' . $id . '|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lock_completed_orders' => 'nullable|boolean',
            'change_status_on_revision' => 'nullable|boolean',
            'enable_ratings' => 'nullable|boolean',
        ]);

        //echo "<pre>"; print_r($request->all()); die;
        $status = OrderStatus::findOrFail($id);
        $status->update([
            'name' => $request->name,
            'color' => $request->color,
            'description' => $request->description,
            'lock_completed_orders' => $request->has('lock_completed') ? 1 : 0, // Handle checkbox
            'change_status_on_revision' => $request->has('change_status_on_message') ? 1 : 0, // Handle checkbox
            'enable_ratings' => $request->has('enable_ratings') ? 1 : 0, // Handle checkbox
        ]);

        return redirect()->route('setting.orderstatuses.list')->with('success', 'Order Status updated successfully');
    }

    // Delete a status from the database
    public function destroy($id)
    {
        $status = OrderStatus::findOrFail($id);
        $status->delete();

        return redirect()->route('setting.orderstatuses.list')->with('success', 'Order Status deleted successfully');
    }
}