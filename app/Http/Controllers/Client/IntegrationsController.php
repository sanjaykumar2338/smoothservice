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
use App\Models\StripePlan;
use App\Models\StripeSubscription;
use Stripe\Stripe;
use Stripe\SetupIntent;
use Stripe\StripeClient;
use Illuminate\Support\Facades\Auth;

class IntegrationsController extends Controller
{
    public function index()
    {
        return view('client.pages.integrations.index');
    }

    public function stripe(){
        return view('client.pages.integrations.stripe');
    }

    // Redirect user to Stripe for account connection
    public function redirectToStripe()
    {
        $url = "https://connect.stripe.com/oauth/v2/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id' => env('STRIPE_CLIENT'),
            'scope' => 'read_write',
            'redirect_uri' => route('stripe.callback'),
        ]);

        return redirect($url);
    }

    // Handle callback from Stripe
    public function handleCallback(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('integrations')->with('error', 'Stripe connection failed.');
        }

        $code = $request->get('code');

        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            // Exchange the authorization code for an access token
            $response = $stripe->oauth->token([
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);

            // Save the user's Stripe account ID
            $user = Auth::user();
            $user->stripe_connect_account_id = $response['stripe_user_id'];
            $user->save();

            return redirect()->route('integrations')->with('success', 'Stripe account connected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('integrations')->with('error', 'Failed to connect Stripe: ' . $e->getMessage());
        }
    }
}