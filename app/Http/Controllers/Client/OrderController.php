<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\Client;
use App\Models\TeamMember;
use App\Models\Task;
use App\Models\History;
use App\Models\ClientStatus;
use App\Models\OrderStatus;
use App\Models\OrderTeam;
use App\Models\Tag;
use App\Models\ClientReply;
use App\Models\OrderProjectData;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderController extends Controller
{
    public function index()
    {
        $teamMemberId = getUserID();
        
        if (getUserType() == 'web') {
            // For web users (clients), fetch orders based on the logged-in user ID
            $orders = Order::with('client', 'service')->where('user_id',getUserID())->paginate(10);
        } elseif (getUserType() == 'team') {
            $teamMember = TeamMember::find($teamMemberId);
            $addedByUserId = $teamMember->added_by;

            if (checkPermission('open_orders') || checkPermission('all_orders')) {
                // Fetch all orders where user_id is the added_by user (team member creator)
                $orders = Order::with('client', 'service')
                    ->where('user_id', $addedByUserId)
                    ->orWhereHas('teamMembers', function ($query) use ($teamMemberId) {
                        // Fetch orders where the team member is directly assigned
                        $query->where('team_member_id', $teamMemberId);
                    })->with('client', 'service')
                    ->paginate(10);
            } elseif (checkPermission('assigned_orders')) {
                // Fetch only orders assigned to the team member
                $orders = Order::whereHas('teamMembers', function ($query) use ($teamMemberId) {
                    $query->where('team_member_id', $teamMemberId);
                })->with('client', 'service')->paginate(10);
            } else {
                // If the team member doesn't have any permission, return an empty order collection
                $orders = new LengthAwarePaginator([], 0, 10);
            }
        }

        //echo "<pre>"; print_r($order->teamMembers); die;
        // Fetch all clients and services (common for both types)
        $clients = Client::where('added_by',$teamMemberId)->get();
        $services = Service::where('user_id',$teamMemberId)->get();

        return view('client.pages.orders.index', compact('orders', 'clients', 'services'));
    }    

    // Show order details
    public function show($id)
    {
        $order = Order::with(['client', 'teamMembers', 'service', 'tasks' => function($query) {
            $query->where('status', 0);
        }])->where('order_no', $id)->firstOrFail();

        $team_members = TeamMember::where('added_by', $order->user_id)->get();

        $orderStatus = OrderStatus::find($order->status_id);
        $project_data = OrderProjectData::where('order_id', $order->id)->get();
        $client_replies = ClientReply::where('order_id', $order->id)->get();
        $orderstatus = OrderStatus::where('added_by', $order->user_id)->get();
        $tags = Tag::where('added_by', $order->user_id)->get();
        $existingTags = \DB::table('tags')
            ->join('order_tag', 'tags.id', '=', 'order_tag.tag_id')
            ->select('tags.id', 'tags.name') // Specify the table name for the id
            ->where('order_tag.order_id', $order->id) // Replace $orderId with your variable
            ->get();
        
        $existingTagsName = \DB::table('tags')
            ->join('order_tag', 'tags.id', '=', 'order_tag.tag_id')
            ->select('tags.name') // Only select the name
            ->where('order_tag.order_id', $order->id)
            ->pluck('name') // Get the names as a collection
            ->implode(','); // Convert the collection to a comma-separated string
        

        $orderHistory = History::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d'); // Group by date only
            });

        $teamMembers = TeamMember::with('role')->where('added_by', $order->user_id)->get();
        //echo "<pre>"; print_r($order->added_by); die;
        
        return view('client.pages.orders.show', compact('order','team_members','project_data','client_replies','orderHistory','orderstatus','orderStatus','tags','existingTags','existingTagsName','teamMembers'));
    }

    public function saveTeamMembers(Request $request)
    {
        $orderId = $request->input('order_id');
        $teamMemberIds = $request->input('team_member_ids');

        if(!checkPermission('assign_to_others')){
            return response()->json(['error' => 'no permission'], 400);
        }

        if(!checkPermission('assign_to_self')){
            return response()->json(['error' => 'no permission'], 400);
        }

        // Validate that order ID and team member IDs are provided
        if (!$orderId || !$teamMemberIds) {
            return response()->json(['error' => 'Invalid data provided'], 400);
        }

        // First, remove any existing team members for this order
        OrderTeam::where('order_id', $orderId)->delete();

        // Now, insert the new selected team members
        foreach ($teamMemberIds as $teamMemberId) {
            OrderTeam::create([
                'order_id' => $orderId,
                'team_member_id' => $teamMemberId,
            ]);
        }

        return response()->json(['success' => 'Team members saved successfully!']);
    }

    public function updateTags(Request $request, $id)
    {
        $request->validate([
            'tags' => 'required|string', // Assuming you are sending comma-separated IDs
        ]);

        $order = Order::findOrFail($id);
        $tagIds = explode(',', $request->input('tags')); // Split the string into an array of IDs

        // Assuming you have a relationship set up for tags in the Order model
        $order->tags()->sync($tagIds); // Sync tags with the order

        return response()->json(['success' => true, 'message' => 'Tags updated successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        // Validate and update the status
        $validated = $request->validate([
            'status_id' => 'required|exists:order_statuses,id',
        ]);

        //echo "<pre>"; print_r($validated); die;
        $order = Order::find($id); // Fetch the order based on your logic
        $order->status_id = $validated['status_id'];
        $order->save();

        return response()->json(['success' => true]);
    }

    // Show form to create a new order
    public function create()
    {
      
        $teamMemberId = getUserID();
        $clients = Client::where('added_by',$teamMemberId)->get();
        $services = Service::where('user_id',$teamMemberId)->get();

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
        $service = Service::find($validatedData['service_id']);

        // Create the new order
        $order = Order::create([
            'title' => $service->service_name,
            'client_id' => $validatedData['client_id'],
            'user_id' => auth()->id(),
            'service_id' => $validatedData['service_id'],
            'order_date' => $validatedData['order_date'] ?? now(), // Default to current date if not provided
            'note' => $validatedData['note'] ?? null,
            'order_no' => $order_no,  // Store the generated 8-character alphanumeric order number
        ]);

         // Store order update in history
         History::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'action_type' => 'order_created',
            'action_details' => 'Order created with the following data: ' . json_encode($request->all()),
        ]);

        // Redirect to the order detail page with success message
        return redirect()->route('order.show', ['id' => $order->order_no])->with('success', 'Order created successfully.');
    }

    public function project_data($id)
    {
        $team_members = TeamMember::where('added_by', auth()->id())->get();
        $order = Order::with(['client', 'service', 'tasks' => function($query) {
            $query->where('status', 0);
        }])->findOrFail($id);

        // Fetch the saved project data
        $project_data = OrderProjectData::where('order_id', $id)->get();

        return view('client.pages.orders.project', compact('order', 'team_members', 'project_data'));
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
        // Fetch the order or return 404 if not found
        $order = Order::findOrFail($id);

        // Validate the request
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'date_added' => 'nullable|date',
            'date_due' => 'nullable|date',
            'date_started' => 'nullable|date',
            'date_completed' => 'nullable|date',
            'amount' => 'numeric|min:0',
        ]);

        // Update the order with validated data
        $order->update($validatedData);

        // Store order update in history
        History::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'action_type' => 'order_updated',
            'action_details' => 'Order updated with the following data: ' . json_encode($validatedData),
        ]);

        // Redirect back to the order details with success message
        return redirect()->route('order.show', $order->order_no)->with('success', 'Order updated successfully.');
    }

    // Delete order
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();

        return redirect()->route('order.list')->with('success', 'Order deleted successfully.');
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

         // Store order update in history
         History::create([
            'order_id' => $order->id,
            'user_id' => auth()->id(),
            'action_type' => 'order_note',
            'action_details' => 'Order note saved with the following data: ' . json_encode($request->all()),
        ]);

        return response()->json(['success' => true, 'message' => 'Note saved successfully.']);
    }

    public function saveTask(Request $request) {
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|array' // Make this optional
        ]);
    
        // Create the task
        $task = new Task();
        $task->order_id = $request->order_id;
        $task->name = $validated['name'];
        $task->description = $validated['description'];
        $task->due_date = $validated['due_date'] ?? null;
        $task->due_type = $request->due_type ?? null;
        $task->due_period_value = $request->due_period_value ?? null;
        $task->due_period_type = $request->due_period_type ?? null;
        $task->save();
    
        // Sync members only if assigned_members is provided
        if (!empty($validated['assigned_to'])) {
            $task->members()->sync($validated['assigned_to']);
        }

         // Store order update in history
         History::create([
            'order_id' => $request->order_id,
            'user_id' => auth()->id(),
            'action_type' => 'order_updated',
            'action_details' => 'Order task created with the following data: ' . json_encode($request->all()),
        ]);
    
        return response()->json(['task' => $task]);
    }

    public function getTasks(Request $request)
    {
        // If no status is passed, show both completed and incomplete tasks
        $tasks = Task::where(function($query) use ($request) {
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
        })->get();

        return response()->json(['tasks' => $tasks]);
    }

    public function getTask($id) {
        $task = Task::with('members')->findOrFail($id);  // Load task with members
        return response()->json(['task' => $task]);
    }

    public function getTasksByStatus(Request $request, $orderId)
    {
        $status = $request->get('status', 0); // Default to incomplete tasks (status = 0)
        
        // Fetch tasks based on order ID and status
        $tasks = Task::where('order_id', $orderId)
                    ->where('status', $status)
                    ->get();

        return response()->json(['tasks' => $tasks]);
    }

    public function deleteTask($id) {
        $task = Task::findOrFail($id);
        $task->members()->detach();  // Remove associated members
        $task->delete();

        // Store order update in history
        History::create([
            'order_id' => $task->order_id,
            'user_id' => auth()->id(),
            'action_type' => 'order_updated',
            'action_details' => 'Order task deleted'
        ]);
    
        return response()->json(['success' => true]);
    }

    public function updateTask(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->name = $request->name;
        $task->description = $request->description;
        $task->due_date = $request->due_date ?? null;
        $task->due_type = $request->due_type ?? null;
        $task->due_period_value = $request->due_period_value ?? null;
        $task->due_period_type = $request->due_period_type ?? null;
        $task->save();

        // Sync the members if provided
        if (!empty($request->assigned_to)) {
            $task->members()->sync($request->assigned_to);
        }

        // Store order update in history
        History::create([
            'order_id' => $task->order_id,
            'user_id' => auth()->id(),
            'action_type' => 'order_updated',
            'action_details' => 'Order task updated'
        ]);

        return response()->json(['task' => $task]);
    }

    public function updateTaskStatus(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);
        $task->status = $request->status;
        $task->save();

        return response()->json(['success' => true]);
    }

    public function saveProjectData(Request $request)
    {
        $projectData = new OrderProjectData();
        $projectData->order_id = $request->order_id;
        $projectData->field_name = $request->field_name;
        $projectData->field_type = $request->field_type;
        $projectData->save();

        return response()->json(['success' => true]);
    }

    public function save_project_data(Request $request, $orderId)
    {
        foreach ($request->except('_token') as $key => $value) {
            // Extract field id from the input name (assuming 'field_{id}')
            $field_id = str_replace('field_', '', $key);

            // Find the specific project data field
            $projectData = OrderProjectData::find($field_id);

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

        return redirect()->route('order.project_data', $orderId)->with('status', 'Data has been saved successfully!');
    }

    public function removeProjectField($fieldId)
    {
        // Find the project data field and delete it
        $projectData = OrderProjectData::findOrFail($fieldId);
        $projectData->delete();

        return response()->json(['success' => true]);
    }

    public function exportData($id) {
        // Fetch the project data for the order
        $order = Order::findOrFail($id);
        $projectData = OrderProjectData::where('order_id', $id)->get();
    
        // Pass data to a view for rendering the PDF
        $pdf = Pdf::loadView('client.pages.orders.export_pdf', [
            'project_data' => $projectData,
            'orderId' => $order->order_no
        ]);
    
        // Return the generated PDF
        return $pdf->download('order_' . $order->order_no . '_data.pdf');
    }
    
    public function downloadFiles($id) {
        // Fetch the project data for the order with file uploads
        $order = Order::findOrFail($id);
        $projectData = OrderProjectData::where('order_id', $id)
            ->where('field_type', 'file_upload')
            ->get();
    
        // Create a temporary file for the ZIP
        $zipFile = storage_path('app/public/uploads/order_' . $order->order_no . '.zip');
        
        $zip = new \ZipArchive;
        if ($zip->open($zipFile, \ZipArchive::CREATE) === TRUE) {
            foreach ($projectData as $data) {
                if ($data->field_value && \Storage::exists('public/' . $data->field_value)) {
                    $zip->addFile(storage_path('app/public/' . $data->field_value), basename($data->field_value));
                }
            }
            $zip->close();
        }
    
        // Return the ZIP file as a download
        return response()->download($zipFile)->deleteFileAfterSend(true);
    }
    
    public function deleteData($id) {
        // Logic to delete project data related to this order
        OrderProjectData::where('order_id', $id)->delete();
        return redirect()->back()->with('success', 'Project data deleted successfully.');
    }

    public function saveReply(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'client_id' => 'required|exists:clients,id',
            'message' => 'required|string',
            'schedule_at' => 'nullable|date',
            'cancel_if_replied' => 'boolean',
            'message_type' => 'required|string',
        ]);

        $user = auth()->user();
        $senderType = $user instanceof \App\Models\Admin ? 'App\Models\Admin' : 'App\Models\Client';

        // Create a new reply
        $reply = new ClientReply();
        $reply->order_id = $request->order_id;
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
                'profile_image' => $reply->sender->profile_image ?? null,
                'sender_name' => $reply->sender->name ?? 'Unknown Sender', // Handle null sender
                'created_at' => $reply->created_at->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    public function getOrderHistory(Request $request, $orderId)
    {
        // Fetch paginated history for the order
        $orderHistory = History::where('order_id', $orderId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Paginate with 5 messages per page

        return response()->json($orderHistory);
    }
    
    public function saveNotification(Request $request)
    {
        $orderId = $request->input('order_id');
        $notificationStatus = $request->input('notification');

        // Find the order by ID and update the notification status
        $order = Order::find($orderId);
        if ($order) {
            $order->notification = $notificationStatus;
            $order->save();

            return response()->json(['success' => 'Notification status updated successfully.']);
        } else {
            return response()->json(['error' => 'Order not found.'], 404);
        }
    }

    public function deleteOrder(Order $order)
    {
        $order->deleteOrder();
        return response()->json(['success' => 'Order deleted successfully!']);
    }

    /**
     * Duplicate an order.
     */
    public function duplicateOrder(Order $order)
    {
        $newOrder = $order->duplicateOrder();
        return response()->json(['success' => 'Order duplicated successfully!', 'new_order_id' => $newOrder->id]);
    }
}
