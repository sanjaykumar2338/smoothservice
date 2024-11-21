<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Client;
use App\Models\Order;
use App\Models\User;
use App\Models\TicketStatus;
use App\Models\TeamMember;
use App\Models\TicketTeam;
use App\Models\TicketCollaborator;
use App\Models\TicketProjectData;
use App\Models\History;
use Illuminate\Http\Request;
use App\Models\TicketTag;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\TicketReply;

class TicketController extends Controller
{
    public function index($client_id=null)
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

        return view('client.pages.tickets.index', compact('tickets', 'clients', 'orders','users','client_id'));
    }

    // Show ticket details
    public function show($id)
    {
        $userId = getUserID();
        $ticket = Ticket::with(['client', 'ccUsers', 'order', 'metadata'])->where('ticket_no', $id)->firstOrFail();
        $team_members = TeamMember::where('added_by', $ticket->user_id)
            ->whereIn('role_id', [1, 2]) // 1 for Admin, 2 for Manager
            ->get();

        //echo "<pre>"; print_r($ticket->metadata); die;

        $ticketstatus = TicketStatus::where('added_by', $ticket->user_id)->get();
        $ticketStatus = TicketStatus::find($ticket->status_id);
        $project_data = TicketProjectData::where('ticket_id', $ticket->id)->get();
        $tags = TicketTag::where('added_by', $ticket->user_id)->get();
        
        $existingTagsName = \DB::table('ticket_tags')
            ->join('ticket_tag', 'ticket_tags.id', '=', 'ticket_tag.tag_id')
            ->select('ticket_tags.name') // Only select the name
            ->where('ticket_tag.ticket_id', $ticket->id)
            ->pluck('name') // Get the names as a collection
            ->implode(','); // Convert the collection to a comma-separated string

        //echo $existingTagsName; die;

        $existingTags = \DB::table('ticket_tags')
            ->join('ticket_tag', 'ticket_tags.id', '=', 'ticket_tag.tag_id')
            ->select('ticket_tags.id', 'ticket_tags.name') // Specify the table name for the id
            ->where('ticket_tag.ticket_id', $ticket->id) // Replace $orderId with your variable
            ->get();

        //echo "<pre>"; print_r($existingTags); die;

        // Fetch ticket history
        $ticketHistory = History::where('ticket_id', $ticket->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d'); // Group by date only
            });
        
        $teamMembers = TeamMember::with('role')->where('added_by', $ticket->user_id)->get();
        $client_replies = TicketReply::where('ticket_id', $ticket->id)->get();
        $tickets_all = Ticket::where('user_id', $userId)->where('id','!=',$ticket->id)->get();

        return view('client.pages.tickets.show', compact('ticket', 'team_members', 'ticketHistory', 'ticketstatus','tags','existingTagsName','existingTags','project_data','ticketStatus','teamMembers','client_replies', 'tickets_all'));
    }

    public function saveNotification(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $notificationStatus = $request->input('notification');

        // Find the order by ID and update the notification status
        $ticket = Ticket::find($ticketId);
        if ($ticket) {
            $ticket->notification = $notificationStatus;
            $ticket->save();

            return response()->json(['success' => 'Notification status updated successfully.']);
        } else {
            return response()->json(['error' => 'Ticket not found.'], 404);
        }
    }

    public function updateTags(Request $request, $id)
    {
        $request->validate([
            'tags' => 'required|string', // Assuming you are sending comma-separated IDs
        ]);

        $ticket = Ticket::findOrFail($id);
        $tagIds = explode(',', $request->input('tags')); // Split the string into an array of IDs

        // Assuming you have a relationship set up for tags in the Order model
        $ticket->tags()->sync($tagIds); // Sync tags with the order

        return response()->json(['success' => true, 'message' => 'Tags updated successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate and update the status
        $validated = $request->validate([
            'status_id' => 'required|exists:ticket_statuses,id',
        ]);

        //echo "<pre>"; print_r($validated); die;
        $ticket = Ticket::find($id); // Fetch the order based on your logic
        $ticket->status_id = $validated['status_id'];
        $ticket->save();

        return response()->json(['success' => true]);
    }

    public function saveProjectData(Request $request)
    {
        $projectData = new TicketProjectData();
        $projectData->ticket_id = $request->ticket_id;
        $projectData->field_name = $request->field_name;
        $projectData->field_type = $request->field_type;
        $projectData->save();

        return response()->json(['success' => true]);
    }

    public function project_data($id)
    {
        $team_members = TeamMember::where('added_by', auth()->id())->get();
        $ticket = Ticket::with(['client'])->findOrFail($id);

        // Fetch the saved project data
        $project_data = TicketProjectData::where('ticket_id', $id)->get();

        return view('client.pages.tickets.project', compact('ticket', 'team_members', 'project_data'));
    }

    public function save_project_data(Request $request, $orderId)
    {
        foreach ($request->except('_token') as $key => $value) {
            // Extract field id from the input name (assuming 'field_{id}')
            $field_id = str_replace('field_', '', $key);

            // Find the specific project data field
            $projectData = TicketProjectData::find($field_id);

            // Handle file upload
            if ($projectData->field_type == 'file_upload' && $request->hasFile($key)) {
                $filePath = $request->file($key)->store('uploads');
                $projectData->field_value = $filePath;
            } else {
                // Save other types of data
                $projectData->field_value = is_array($value) ? json_encode($value) : $value;
            }

            $projectData->save();
        }

        return redirect()->route('ticket.project_data', $orderId)->with('status', 'Data has been saved successfully!');
    }

    public function removeProjectField($fieldId)
    {
        // Find the project data field and delete it
        $projectData = TicketProjectData::findOrFail($fieldId);
        $projectData->delete();

        return response()->json(['success' => true]);
    }

    public function downloadFiles($id) {
        // Fetch the project data for the ticket with file uploads
        $ticket = Ticket::findOrFail($id);
        $projectData = TicketProjectData::where('ticket_id', $id)
            ->where('field_type', 'file_upload')
            ->get();
    
        // Create a temporary file for the ZIP
        $zipFile = storage_path('app/public/uploads/ticket_' . $ticket->ticket_no . '.zip');
    
        // Ensure the directory exists
        if (!is_dir(storage_path('app/public/uploads'))) {
            mkdir(storage_path('app/public/uploads'), 0755, true);
        }
    
        // Initialize ZIP
        $zip = new \ZipArchive;
        if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
            foreach ($projectData as $data) {
                if ($data->field_value && \Storage::exists('public/' . $data->field_value)) {
                    $zip->addFile(storage_path('app/public/' . $data->field_value), basename($data->field_value));
                }
            }
            $zip->close();
        } else {
            return response()->json(['error' => 'Failed to create ZIP file'], 500);
        }
    
        // Check if the ZIP file exists
        if (!file_exists($zipFile)) {
            return response()->json(['error' => 'ZIP file not found'], 404);
        }
    
        // Return the ZIP file as a download
        return response()->download($zipFile)->deleteFileAfterSend(true);
    }    
    
    public function deleteData($id) {
        // Logic to delete project data related to this order
        TicketProjectData::where('ticket_id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Ticket data deleted  successfully.']);
    }

    public function exportData($id) {
        // Fetch the project data for the order
        $ticket = Ticket::findOrFail($id);
        $projectData = TicketProjectData::where('ticket_id', $id)->get();
    
        // Pass data to a view for rendering the PDF
        $pdf = Pdf::loadView('client.pages.tickets.export_pdf', [
            'project_data' => $projectData,
            'ticketId' => $ticket->ticket_no
        ]);
    
        // Return the generated PDF
        return $pdf->download('ticket_' . $ticket->ticket_no . '_data.pdf');
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
        //echo "<pre>"; print_r($request->all()); die;
        // Validate the incoming request
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'subject' => 'required|string|max:255',
            'order_id' => 'nullable|exists:orders,id',
            'message' => 'required|string',
            'cc' => 'nullable|array'
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

        // Save collaborators (CC) using the TicketCollaborator model
        if (!empty($validatedData['cc'])) {
            foreach ($validatedData['cc'] as $ccUser) {
                // Extract the user ID from the "team_user_id" format
                $userId = str_replace('team_', '', $ccUser);

                // Create new TicketCollaborator record
                TicketCollaborator::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $userId,
                ]);
            }
        }

        // Redirect to the ticket detail page with success message
        return redirect()->route('ticket.show', ['id' => $ticket->ticket_no])->with('success', 'Ticket created successfully.');
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

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);

        // Delete related collaborators
        $ticket->ccUsers()->detach();  // Detach all ccUsers relationships

        // Delete related tags
        $ticket->tags()->detach();  // Detach all tag relationships

        // Delete related team members
        $ticket->teamMembers()->detach();  // Detach all team member relationships

        // Delete related metadata
        $ticket->metadata()->delete();  // Delete all related metadata records

        // Delete the ticket itself
        $ticket->delete();

        return redirect()->route('ticket.list')->with('success', 'Ticket and all related data deleted successfully.');
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

    public function saveNote(Request $request, $id)
    {
        // Validate the incoming request
        $request->validate([
            'note' => 'nullable|string|max:255',
        ]);

        // Find the order and update the note
        $order = Ticket::findOrFail($id);
        $order->note = $request->note;
        $order->save();

         // Store order update in history
         History::create([
            'ticket_id' => $order->id,
            'user_id' => auth()->id(),
            'action_type' => 'ticket_note',
            'action_details' => 'ticket note saved with the following data: ' . json_encode($request->all()),
        ]);

        return response()->json(['success' => true, 'message' => 'Note saved successfully.']);
    }

    public function saveReply(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'client_id' => 'required|exists:clients,id',
            'message' => 'required|string',
            'schedule_at' => 'nullable|date',
            'cancel_if_replied' => 'boolean',
            'message_type' => 'required|string',
        ]);

        $user = auth()->user();
        $senderType = getUserType()=='web' ? 'App\Models\User' : 'App\Models\TeamMember';

        // Create a new reply
        $reply = new TicketReply();
        $reply->ticket_id = $request->ticket_id;
        $reply->client_id = $request->client_id;
        $reply->message = $request->message;
        $reply->scheduled_at = $request->schedule_at;
        $reply->cancel_on_reply = $request->cancel_if_replied ?? false;
        $reply->sender_id = $user->id;
        $reply->sender_type = $senderType;
        $reply->message_type = $request->message_type;
        $reply->save();

        // Load sender relation
        $reply->load('sender');

        History::create([
            'order_id' => $request->order_id,
            'user_id' => auth()->id(),
            'action_type' => 'order_message',
            'action_details' => 'Order message '. json_encode($request->all()),
        ]);

        // Return response with null checks
        return response()->json([
            'success' => true,
            'reply' => [
                'message' => $reply->message,
                'id' => $reply->id,
                'profile_image' => $reply->sender->profile_image ?? null,
                'sender_name' => $reply->sender->name ?? 'Unknown Sender', // Handle null sender
                'created_at' => $reply->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function replies_edit(Request $request, $id)
    {
        $reply = TicketReply::findOrFail($id);
        $reply->message = $request->message;
        $reply->save();

        return response()->json(['success' => true, 'message' => 'Reply updated successfully']);
    }

    public function replies_destroy($id)
    {
        $reply = TicketReply::findOrFail($id);
        $reply->delete();

        return response()->json(['success' => true, 'message' => 'Reply deleted successfully']);
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
        if (!$ticketId) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        // First, remove any existing team members for this ticket
        TicketTeam::where('ticket_id', $ticketId)->delete();

        // Now, insert the new selected team members
        if(!is_null($teamMemberIds)){
            foreach ($teamMemberIds as $teamMemberId) {
                TicketTeam::create([
                    'ticket_id' => $ticketId,
                    'team_member_id' => $teamMemberId,
                ]);
            }
        }

        return response()->json(['success' => 'Team members saved successfully!']);
    }

    public function edit_info($id)
    {
        $ticket = Ticket::with('metadata', 'ccUsers')->findOrFail($id);
        $clients = Client::where('added_by', $ticket->user_id)->get();
        $orders = Order::where('user_id', $ticket->user_id)->get();
        $users = TeamMember::where('added_by', $ticket->user_id)->get();

        return view('client.pages.tickets.update', compact('ticket', 'clients', 'orders', 'users'));
    }

    public function update_info(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'related_order_id' => 'nullable|exists:orders,id',
            // Add more validation rules as needed
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->subject = $request->subject;
        $ticket->client_id = $request->client_id;
        $ticket->order_id = $request->related_order_id;
        $ticket->date_added = $request->date_added;
        $ticket->date_closed = $request->date_closed;
        $ticket->save();

        // Update metadata
        $ticket->metadata()->delete(); // Clear previous metadata
        foreach ($request->meta_key as $index => $key) {
            $ticket->metadata()->create([
                'meta_key' => $key,
                'meta_value' => $request->meta_value[$index],
            ]);
        }

        // Update collaborators
        $ticket->ccUsers()->sync($request->collaborators);

        return redirect()->route('ticket.show', $ticket->ticket_no)->with('success', 'Ticket updated successfully.');
    }

    public function mergeTickets(Request $request)
    {
        // Validate the input to ensure both source and target tickets are provided
        $request->validate([
            'source_ticket_id' => 'required|exists:tickets,id',
            'target_ticket_id' => 'required|exists:tickets,id|different:source_ticket_id',
        ]);

        // Fetch the source and target tickets
        $sourceTicket = Ticket::with(['replies', 'metadata', 'ccUsers'])->findOrFail($request->source_ticket_id);
        $targetTicket = Ticket::with(['replies', 'metadata', 'ccUsers'])->findOrFail($request->target_ticket_id);

        \DB::beginTransaction(); // Begin database transaction

        try {
            // Move all replies from source ticket to target ticket
            foreach ($sourceTicket->replies as $reply) {
                $reply->ticket_id = $targetTicket->id;
                $reply->save();
            }

            // Move all metadata from source ticket to target ticket
            foreach ($sourceTicket->metadata as $meta) {
                $meta->ticket_id = $targetTicket->id;
                $meta->save();
            }

            // Merge collaborators (CC users) without duplicating
            $existingCollaborators = $targetTicket->ccUsers->pluck('id')->toArray();
            foreach ($sourceTicket->ccUsers as $collaborator) {
                if (!in_array($collaborator->id, $existingCollaborators)) {
                    $targetTicket->ccUsers()->attach($collaborator->id);
                }
            }

            // Optionally: Delete the source ticket after merging
            $sourceTicket->delete();

            \DB::commit(); // Commit the transaction
            return redirect()->route('ticket.list')->with('success', 'Tickets merged successfully!');
        } catch (\Exception $e) {
            \DB::rollBack(); // Rollback the transaction if something goes wrong
            return redirect()->back()->withErrors(['error' => 'Failed to merge tickets: ' . $e->getMessage()]);
        }
    }

}
