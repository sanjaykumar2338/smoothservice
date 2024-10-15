<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TicketStatus;
use Illuminate\Validation\Rule;

class TicketStatusController extends Controller
{
    // List all statuses
    public function index(Request $request)
    {
        $teamMemberId = getUserID(); // Assuming getUserID() retrieves the logged-in user's ID

        $search = $request->input('search'); // Get the search input

        // Filter the TicketStatuses based on the search input and the 'added_by' field
        $ticketStatuses = TicketStatus::when($search, function ($query, $search) {
            return $query->where('name', 'LIKE', "%{$search}%");
        })->where('added_by', $teamMemberId)->paginate(8); // Apply search and paginate results based on 'added_by'

        return view('client.pages.settings.ticketstatuses.index', compact('ticketStatuses', 'search'));
    }


    // Show the form to create a new status
    public function create()
    {
        return view('client.pages.settings.ticketstatuses.add');
    }

    // Store a new status in the database
    public function store(Request $request)
    {
        $teamMemberId = getUserID(); // Assuming getUserID() retrieves the logged-in user's ID

        $request->validate([
            'name' => [
                'required',
                'max:255',
                // Add uniqueness check with 'added_by' field
                Rule::unique('ticket_statuses')->where(function ($query) use ($teamMemberId) {
                    return $query->where('added_by', $teamMemberId);
                }),
            ],
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lock_completed_orders' => 'nullable|boolean',
            'change_status_on_revision' => 'nullable|boolean',
            'enable_ratings' => 'nullable|boolean',
        ]);

        // Create new TicketStatus
        TicketStatus::create([
            'name' => $request->name,
            'color' => $request->color,
            'description' => $request->description,
            'lock_completed_orders' => $request->has('lock_completed') ? 1 : 0,  // Handle checkbox
            'change_status_on_revision' => $request->has('change_status_on_message') ? 1 : 0, // Handle checkbox
            'enable_ratings' => $request->has('enable_ratings') ? 1 : 0, // Handle checkbox
            'added_by' => $teamMemberId,
        ]);

        return redirect()->route('setting.ticketstatuses.list')->with('success', 'Ticket Status created successfully');
    }

    // Show the form to edit an existing status
    public function edit($id)
    {
        $status = TicketStatus::findOrFail($id);
        return view('client.pages.settings.ticketstatuses.edit', compact('status'));
    }

    // Update an existing status in the database
    public function update(Request $request, $id)
    {
        $teamMemberId = getUserID(); // Assuming getUserID() retrieves the logged-in user's ID

        $request->validate([
            'name' => 'required|unique:ticket_statuses,name,' . $id . ',id,added_by,' . $teamMemberId . '|max:255',
            'color' => 'required|string|max:255',
            'description' => 'nullable|string',
            'lock_completed_orders' => 'nullable|boolean',
            'change_status_on_revision' => 'nullable|boolean',
            'enable_ratings' => 'nullable|boolean',
        ]);
        
        // Ensure the ticket status exists and was created by the authenticated user
        $status = TicketStatus::where('id', $id)->where('added_by', $teamMemberId)->firstOrFail();

        // Update the ticket status
        $status->update([
            'name' => $request->name,
            'color' => $request->color,
            'description' => $request->description,
            'lock_completed_orders' => $request->has('lock_completed') ? 1 : 0, // Handle checkbox
            'change_status_on_revision' => $request->has('change_status_on_message') ? 1 : 0, // Handle checkbox
            'enable_ratings' => $request->has('enable_ratings') ? 1 : 0, // Handle checkbox
        ]);

        return redirect()->route('setting.ticketstatuses.list')->with('success', 'Ticket Status updated successfully');
    }

    // Delete a status from the database
    public function destroy($id)
    {
        $status = TicketStatus::findOrFail($id);
        $status->delete();

        return redirect()->route('setting.ticketstatuses.list')->with('success', 'Ticket Status deleted successfully');
    }
}
