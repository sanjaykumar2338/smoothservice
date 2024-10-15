<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OrderStatus;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    // List all statuses
    public function index(Request $request)
    {
        // Get the team member ID (or user ID) from the custom method getUserID()
        $teamMemberId = getUserID();

        $search = $request->input('search'); // Get the search input

        // Query order statuses filtered by the team member ID
        $orderStatuses = OrderStatus::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })->where('added_by', $teamMemberId)->paginate(8); // Use $teamMemberId here for filtering

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
        // Get the team member ID (or user ID) from the custom method getUserID()
        $teamMemberId = getUserID();

        // Validate the form input
        $request->validate([
            'name' => [
                'required',
                Rule::unique('order_statuses')->where(function ($query) use ($teamMemberId) {
                    return $query->where('added_by', $teamMemberId); // Use the team member ID here
                }),
                'max:255'
            ],
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lock_completed_orders' => 'nullable|boolean',
            'change_status_on_revision' => 'nullable|boolean',
            'enable_ratings' => 'nullable|boolean',
        ]);

        // Create new OrderStatus
        OrderStatus::create([
            'name' => $request->name,
            'color' => $request->color,
            'description' => $request->description,
            'lock_completed_orders' => $request->has('lock_completed') ? 1 : 0,  // Handle checkbox
            'change_status_on_revision' => $request->has('change_status_on_message') ? 1 : 0, // Handle checkbox
            'enable_ratings' => $request->has('enable_ratings') ? 1 : 0, // Handle checkbox
            'added_by' => $teamMemberId, // Assign the team member ID here
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
        // Get the team member ID (or user ID) from the custom method getUserID()
        $teamMemberId = getUserID();

        // Validate the request
        $request->validate([
            'name' => ['required', Rule::unique('order_statuses')->ignore($id)->where(function ($query) use ($teamMemberId) {
                return $query->where('added_by', $teamMemberId);
            }), 'max:255'],
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lock_completed_orders' => 'nullable|boolean',
            'change_status_on_revision' => 'nullable|boolean',
            'enable_ratings' => 'nullable|boolean',
        ]);

        // Find the order status or fail if not found
        $status = OrderStatus::where('id', $id)->where('added_by', $teamMemberId)->firstOrFail();

        // Update the order status with the provided values
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