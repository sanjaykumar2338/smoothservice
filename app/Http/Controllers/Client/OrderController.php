<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Models\Client;
use App\Models\TeamMember;
use App\Models\Task;
use App\Models\OrderProjectData;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $team_members = TeamMember::where('added_by', auth()->id())->get();
        $order = Order::with(['client', 'service', 'tasks' => function($query) {
            $query->where('status', 0);
        }])->findOrFail($id);

        $project_data = OrderProjectData::where('order_id', $id)->get();
        return view('client.pages.orders.show', compact('order','team_members','project_data'));
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

        return redirect()->route('client.order.project_data', $orderId)->with('status', 'Data has been saved successfully!');
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
            'orderId' => $id
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
}
