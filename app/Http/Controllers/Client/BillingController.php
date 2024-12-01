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

class BillingController extends Controller
{
    public function index()
    {   
        return view('client.pages.billing.plan');
    }
}