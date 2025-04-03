<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Client;
use App\Models\Service;
use App\Models\Order;
use App\Models\ClientStatus;
use App\Models\OrderStatus;
use App\Models\OrderTeam;
use App\Models\Tag;
use App\Models\History;
use App\Models\User;
use App\Models\TeamMember;
use App\Models\ClientReply;
use App\Models\OrderProjectData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function dashboard(Request $request){

        $clients = Client::count();
        $services = Service::count();
        $orders = Order::count();
        return view('admin.dashboard.index')->with('clients', $clients)->with('services', $services)->with('orders', $orders);
    }

    public function orders(Request $request){
        $orders = Order::with('client', 'service')->paginate(10);
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
        $users = User::paginate(10);
        return view('admin.pages.users.index', compact('users'));
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