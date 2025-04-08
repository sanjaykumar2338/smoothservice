<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Service;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\ClientStatus;
use App\Models\OrderStatus;
use App\Models\OrderTeam;
use App\Models\Tag;
use App\Models\History;
use App\Models\User;
use App\Models\Ticket;
use App\Models\TicketStatus;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\TicketTag;
use App\Models\TicketProjectData;
use App\Models\TicketReply;
use App\Models\TeamMember;
use App\Models\ClientReply;
use App\Models\OrderProjectData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminDashboardController extends Controller
{
    public function dashboard(Request $request){

        $clients = Client::count();
        $services = Service::count();
        $orders = Order::count();
        return view('admin.dashboard.index')->with('clients', $clients)->with('services', $services)->with('orders', $orders);
    }

    public function orders(Request $request){
        $search = $request->input('search');
        
        // Query with optional search on 'title' and 'note'
        $orders = Order::when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'LIKE', '%' . $search . '%')
                            ->orWhere('note', 'LIKE', '%' . $search . '%');
                });
            })->paginate(10);

        return view('admin.pages.orders.index')->with('orders', $orders);
    }

    public function ordersshow($id)
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
        
        return view('admin.pages.orders.show', compact('order','team_members','project_data','client_replies','orderHistory','orderstatus','orderStatus','tags','existingTags','existingTagsName','teamMembers'));
    }

    public function usersall(Request $request){
        $search = $request->input('search');
        
        // Query with optional search on 'title' and 'note'
        $users = User::when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'LIKE', '%' . $search . '%')
                            ->orWhere('email', 'LIKE', '%' . $search . '%');
                });
            })->paginate(10);

        return view('admin.pages.users.index', compact('users'));
    }

    public function ticketsall(Request $request){
        $search = $request->input('search');
        
        // Query with optional search on 'subject' and 'ticket_no'
        $tickets = Ticket::with('ticket_status')->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('subject', 'LIKE', '%' . $search . '%')
                            ->orWhere('ticket_no', 'LIKE', '%' . $search . '%');
                });
            })->paginate(10);

        return view('admin.pages.tickets.index', compact('tickets'));
    }

    public function invoices(Request $request){
        $search = $request->input('search');
        $invoices = Invoice::with('client', 'service')
            ->when($search, function ($query, $search) {
                return $query->whereHas('client', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orwhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('service_name', 'like', "%{$search}%");
                });
            })->orderBy('created_at','desc')->paginate(10);

        return view('admin.pages.invoices.index', compact('invoices', 'search'));
    }

    public function invoiceshow($id)
    {
        // Retrieve the invoice by its ID along with the associated client and items
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);
        //echo "<pre>"; print_r($invoice); die;

        // Retrieve the services in case you want to display service information in the invoice
        $services = Service::where('user_id', $invoice->added_by)->get();

        // Fetch all users and team members added by the current logged-in user
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', $invoice->added_by)->get();

        // Pass the invoice data to the view
        return view('admin.pages.invoices.show', compact('invoice', 'services', 'users', 'teamMembers'));
    }

    public function downloadInvoice($id)
    {
        // Retrieve the invoice and related data
        $invoice = Invoice::with('client', 'items')->findOrFail($id);

        // Generate the PDF using the view
        $pdf = PDF::loadView('client.pages.invoices.pdf', compact('invoice'));

        // Download the PDF
        return $pdf->download('invoice_' . $invoice->id . '.pdf');
    }

    public function subscriptions(Request $request){
        $search = $request->input('search');
        $subscriptions = Subscription::with('client', 'service')
            ->when($search, function ($query, $search) {
                return $query->whereHas('client', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orwhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('service_name', 'like', "%{$search}%");
                });
            })->paginate(10);

        return view('admin.pages.subscriptions.index', compact('subscriptions', 'search'));
    }

    public function downloadSubscription($id)
    {
        // Retrieve the subscription and related data
        $subscription = Subscription::with('client', 'items')->findOrFail($id);

        // Generate the PDF using the view
        $pdf = PDF::loadView('client.pages.subscriptions.pdf', compact('subscription'));

        // Download the PDF
        return $pdf->download('subscription_' . $subscription->id . '.pdf');
    }

    public function subscriptionshow($id)
    {
        // Fetch all users and team members added by the current logged-in user
        $subscription = Subscription::find($id);
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', $subscription->added_by)->get();

        $subscription = Subscription::with('client', 'service', 'items')->findOrFail($id);
        return view('admin.pages.subscriptions.show', compact('subscription','users', 'teamMembers'));
    }

    public function ticketshow($id)
    {
        $ticket = Ticket::with(['client', 'ccUsers', 'order', 'metadata'])->where('id', $id)->firstOrFail();
        $team_members = TeamMember::where('added_by', $ticket->user_id)
            ->whereIn('role_id', [1, 2]) // 1 for Admin, 2 for Manager
            ->get();

        $userId = $ticket->user_id;

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

        return view('admin.pages.tickets.show', compact('ticket', 'team_members', 'ticketHistory', 'ticketstatus','tags','existingTagsName','existingTags','project_data','ticketStatus','teamMembers','client_replies', 'tickets_all'));
    }

    public function logout(){
        Auth::guard('admin')->logout();
        return redirect('admin');
    }

    public function sign_in_as_user($clientId)
    {
        // Find the client by ID
        $user = User::find($clientId);

        if (!$user) {
            return redirect()->route('admin.users')->with('error', 'User not found.');
        }

        \Session::put('admin_main_id', getUserID());

        // Log out the current admin (optional)
        Auth::guard('admin')->logout();

        // Log in as the client
        Auth::guard('web')->login($user);

        // Redirect to the client's dashboard or any other route
        return redirect()->route('dashboard')->with('success', "Signed in as {$user->name}.");
    }
}