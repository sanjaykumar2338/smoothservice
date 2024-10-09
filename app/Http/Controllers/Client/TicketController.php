<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatus;
use App\Models\TicketStatus;
use App\Models\TeamMember;
use App\Models\History;
use Illuminate\Http\Request;
use App\Models\Tag;

class TicketController extends Controller
{
    public function index()
    {
        $userId = getUserID();
        
        if (getUserType() == 'web') {
            // For web users (clients), fetch tickets based on the logged-in user ID
            $tickets = Ticket::with('client', 'order')->where('user_id', getUserID())->paginate(10);
        } elseif (getUserType() == 'team') {
            $teamMember = TeamMember::find($userId);
            $addedByUserId = $teamMember->added_by;

            if (checkPermission('open_tickets') || checkPermission('all_tickets')) {
                // Fetch all tickets where user_id is the added_by user (team member creator)
                $tickets = Ticket::with('client', 'order')
                    ->where('user_id', $addedByUserId)
                    ->orWhereHas('teamMembers', function ($query) use ($userId) {
                        // Fetch tickets where the team member is directly assigned
                        $query->where('team_member_id', $userId);
                    })->with('client', 'order')
                    ->paginate(10);
            } elseif (checkPermission('assigned_tickets')) {
                // Fetch only tickets assigned to the team member
                $tickets = Ticket::whereHas('teamMembers', function ($query) use ($userId) {
                    $query->where('team_member_id', $userId);
                })->with('client', 'order')->paginate(10);
            } else {
                // If the team member doesn't have any permission, return an empty ticket collection
                $tickets = new LengthAwarePaginator([], 0, 10);
            }
        }

        // Fetch all clients and orders (common for both types)
        $clients = Client::where('added_by', $userId)->get();
        $orders = Order::where('user_id', $userId)->get();
        $users = TeamMember::where('added_by', $userId)->get();

        return view('client.pages.tickets.index', compact('tickets', 'clients', 'orders','users'));
    }

    // Show ticket details
    public function show($id)
    {
        $userId = getUserID();
        $ticket = Ticket::with(['client', 'ccUsers', 'order'])->where('ticket_no', $id)->firstOrFail();
        $team_members = TeamMember::where('added_by', $ticket->user_id)->get();
        $ticketstatus = OrderStatus::where('added_by', $ticket->user_id)->get();
        $tags = Tag::where('added_by', $ticket->user_id)->get();
        $existingTagsName = '';
        $existingTags = [];

        // Fetch ticket history
        $ticketHistory = History::where('ticket_id', $ticket->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d'); // Group by date only
            });

        return view('client.pages.tickets.show', compact('ticket', 'team_members', 'ticketHistory', 'ticketstatus','tags','existingTagsName','existingTags'));
    }

    // Show form to create a new ticket
    public function create()
    {
        $userId = getUserID();
        $clients = Client::where('added_by', $userId)->get();
        $orders = Order::where('user_id', $userId)->get();

        return view('client.pages.tickets.create', compact('clients', 'orders'));
    }

    // Store a new ticket
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'order_id' => 'nullable|exists:orders,id',
            'message' => 'required|string',
        ]);

        // Create the new ticket
        $userId = getUserID();
        $ticket = Ticket::create([
            'ticket_no' => $this->generateTicketNumber(),
            'client_id' => $validatedData['client_id'],
            'user_id' => auth()->id(),
            'subject' => $validatedData['subject'],
            'order_id' => $validatedData['order_id'] ?? null,
            'message' => $validatedData['message'],
            'user_id' => $userId
        ]);

        // Store ticket creation history
        History::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'action_type' => 'ticket_created',
            'action_details' => 'Ticket created with the following data: ' . json_encode($request->all()),
        ]);

        // Redirect to the ticket detail page with success message
        return redirect()->route('ticket.show', ['id' => $ticket->id])->with('success', 'Ticket created successfully.');
    }

    public function generateTicketNumber($length = 6) {
        // Characters allowed: numbers and uppercase letters
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $ticketNumber = '';
    
        // Generate a random string of the given length
        for ($i = 0; $i < $length; $i++) {
            $ticketNumber .= $characters[rand(0, strlen($characters) - 1)];
        }
    
        return $ticketNumber;
    }
    

    // Show form to edit ticket
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $clients = Client::all();
        $orders = Order::all();
        return view('client.pages.tickets.edit', compact('ticket', 'clients', 'orders'));
    }

    // Update ticket
    public function update(Request $request, $id)
    {
        // Fetch the ticket or return 404 if not found
        $ticket = Ticket::findOrFail($id);

        // Validate the request
        $validatedData = $request->validate([
            'subject' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'order_id' => 'nullable|exists:orders,id',
            'message' => 'required|string',
        ]);

        // Update the ticket with validated data
        $ticket->update($validatedData);

        // Store ticket update in history
        History::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'action_type' => 'ticket_updated',
            'action_details' => 'Ticket updated with the following data: ' . json_encode($validatedData),
        ]);

        // Redirect back to the ticket details with success message
        return redirect()->route('ticket.show', $ticket->id)->with('success', 'Ticket updated successfully.');
    }

    // Delete ticket
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();

        return redirect()->route('ticket.index')->with('success', 'Ticket deleted successfully.');
    }

    // Save ticket history for updates or actions
    public function saveHistory(Request $request, $id)
    {
        $request->validate([
            'action_type' => 'required|string',
            'message' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($id);

        // Create a new history record
        History::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'action_type' => $request->action_type,
            'action_details' => $request->message,
        ]);

        return response()->json(['success' => true, 'message' => 'History updated successfully.']);
    }

    // Save assigned team members to the ticket
    public function saveTeamMembers(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $teamMemberIds = $request->input('team_member_ids');

        if(!checkPermission('assign_to_others')){
            return response()->json(['error' => 'no permission'], 400);
        }

        if(!checkPermission('assign_to_self')){
            return response()->json(['error' => 'no permission'], 400);
        }

        // Validate that ticket ID and team member IDs are provided
        if (!$ticketId || !$teamMemberIds) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        // First, remove any existing team members for this ticket
        TicketTeam::where('ticket_id', $ticketId)->delete();

        // Now, insert the new selected team members
        foreach ($teamMemberIds as $teamMemberId) {
            TicketTeam::create([
                'ticket_id' => $ticketId,
                'team_member_id' => $teamMemberId,
            ]);
        }

        return response()->json(['success' => 'Team members saved successfully!']);
    }
}
