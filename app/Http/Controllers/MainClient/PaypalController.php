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
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Subscription;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Product;
use Stripe\PaymentMethod;
use App\Models\InvoiceSubscription;
use Carbon\Carbon;
use DB;

class PaypalController extends Controller
{

}