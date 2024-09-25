<?php
namespace App\Http\Controllers\TeamMember;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientWelcome;

class TeamMemberController extends Controller
{
    // List all clients
    public function dashboard(Request $request)
    {
        $query = Client::query();

        if ($request->has('search')) {
            $query->where('first_name', 'like', '%' . $request->search . '%')
                ->orWhere('last_name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        //echo auth()->user()->hasPermission('assigned_orders'); die;
        $clients = $query->orderBy('id', 'desc')->paginate(10);
        return view('team_member.team_member_pages.dashboard_page', compact('clients'));
    }
}