<?php
namespace App\Http\Controllers\MainClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
USE App\Models\Order;
use App\Models\Service;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientWelcome;
use App\Models\ClientStatus;
use Illuminate\Support\Str;
use App\Mail\ClientPasswordChanged;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\ClientReply;
use App\Models\TeamMember;
use App\Models\OrderStatus;
use App\Models\OrderProjectData;
use App\Models\Tag;
use App\Models\History;
use App\Models\TicketStatus;
use App\Models\TicketTeam;
use App\Models\OrderTeam;
use App\Models\Invoice;
use App\Models\User;
use DB;

class MainClientController extends Controller
{
    public $client_id;
    public function __construct() {
        $this->client_id = getUserID();
    }

    public function dashboard()
    {
        $services = Service::where('user_id', $this->client_id)->count();
        $orders = Order::select('orders.status_id', 'order_statuses.name', DB::raw('COUNT(orders.id) as total_orders'))
            ->leftjoin('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
            ->where('orders.client_id', $this->client_id)
            ->groupBy('orders.status_id', 'order_statuses.name')
            ->get();

        return view('c_main.c_pages.c_dashboard_page', compact('services', 'orders'));
    }

    public function orders(Request $request)
    {
        $search = $request->input('search'); // Get the search query from the request
        
        // Query with optional search on 'title' and 'note'
        $orders = Order::where('client_id', $this->client_id)
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'LIKE', '%' . $search . '%')
                            ->orWhere('note', 'LIKE', '%' . $search . '%');
                });
            })
            ->paginate(5);
        
        return view('c_main.c_pages.c_order.c_index', compact('orders', 'search'));
    }
   
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
       
        return view('c_main.c_pages.c_order.c_detail', compact('order','team_members','project_data','client_replies','orderHistory','orderstatus','orderStatus','tags','existingTags','existingTagsName','teamMembers'));
    }

    public function tickets(Request $request)
    {
        $search = $request->input('search'); // Get the search query from the request

        // Query tickets with optional search on 'subject', 'message', and 'note'
        $tickets = Ticket::with('ticket_status')
            ->where('client_id', $this->client_id)
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('subject', 'LIKE', '%' . $search . '%')
                            ->orWhere('message', 'LIKE', '%' . $search . '%')
                            ->orWhere('note', 'LIKE', '%' . $search . '%');
                });
            })
            ->paginate(5);

        return view('c_main.c_pages.c_ticket.c_index', compact('tickets', 'search'));
    }

    public function ticket_add(Request $request)
    {
        $orders = Order::where('client_id', $this->client_id)->get();
        return view('c_main.c_pages.c_ticket.c_add', compact('orders'));
    }

    public function ticket_show($id)
    {
        $ticket = Ticket::with('ticket_status')->where('ticket_no', $id)->first();
        $team_members = TeamMember::where('added_by', $ticket->user_id)
            ->whereIn('role_id', [1, 2])
            ->get();

        return view('c_main.c_pages.c_ticket.c_show', compact('ticket','team_members'));
    }

    public function ticket_store(Request $request){
        
        try{
            $validatedData = $request->validate([
                'subject' => 'required',
                'order' => 'nullable',
                'editor_content' => 'required'
            ], [
                'editor_content.required' => 'The message field is required.',
            ]);            

            $statues = TicketStatus::where('is_default', 1)->first();
            $status_id = $statues ? $statues->id : null;
            $client = Client::where('id', $this->client_id)->first();

            $ticket = new Ticket;
            $ticket->subject = $request->subject;
            $ticket->order_id = $request->order;
            $ticket->client_id = $this->client_id;
            $ticket->user_id = $client->added_by;
            $ticket->message = $request->editor_content;
            $ticket->status_id = $status_id;
            $ticket->ticket_no = $this->generateTicketNumber();
            $ticket->save();

            return redirect()->route('portal.tickets')->with('success', 'Ticket created successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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

    // Save assigned team members to the ticket
    public function saveTeamMembers(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $teamMemberIds = $request->input('team_member_ids');

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

    public function saveTeamMembersOrder(Request $request)
    {
        // First, remove any existing team members for this order
        $orderId = $request->input('order_id');
        $teamMemberIds = $request->input('team_member_ids');

        OrderTeam::where('order_id', $orderId)->delete();

        // Now, insert the new selected team members
        if(!is_null($teamMemberIds)){
            foreach ($teamMemberIds as $teamMemberId) {
                OrderTeam::create([
                    'order_id' => $orderId,
                    'team_member_id' => $teamMemberId,
                ]);
            }
        }

        return response()->json(['success' => 'Team members saved successfully!']);
    }

    public function invoices(Request $request)
    {
        $search = $request->input('search'); // Get the search query from the request
        
        // Query with optional search on 'title' and 'note'
        $invoices = Invoice::with(['client', 'service', 'items'])->where('client_id', $this->client_id)
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('billing_first_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('billing_last_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('note', 'LIKE', '%' . $search . '%');
                });
            })
            ->paginate(5);
        
        
        //echo "<pre>"; print_r($invoices); die;
        return view('c_main.c_pages.c_invoice.c_index', compact('invoices', 'search'));
    }

    public function invoice_show($id)
    {
        // Retrieve the invoice by its ID along with the associated client and items
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);

        // Retrieve the services in case you want to display service information in the invoice
        $services = Service::where('user_id', auth()->id())->get();

        // Fetch all users and team members added by the current logged-in user
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', auth()->id())->get();

        // Pass the invoice data to the view
        return view('c_main.c_pages.c_invoice.c_show', compact('invoice', 'services', 'users', 'teamMembers'));
    }
}