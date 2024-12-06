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

    public function paypal(){
        return view('client.pages.integrations.paypal');
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

    public function disconnect(Request $request)
    {
        $user = Auth::user();

        // Ensure the user is connected to Stripe
        if (!$user->stripe_connect_account_id) {
            return redirect()->back()->with('error', 'No Stripe account connected.');
        }

        try {
            // Use the Stripe API to deauthorize the account
            $stripeClientId = env('STRIPE_CLIENT'); // Your Stripe Connect Client ID
            $stripeSecretKey = env('STRIPE_SECRET'); // Your Stripe Secret Key

            \Stripe\Stripe::setApiKey($stripeSecretKey);

            $response = \Stripe\OAuth::deauthorize([
                'client_id' => $stripeClientId,
                'stripe_user_id' => $user->stripe_connect_account_id,
            ]);

            // Log the successful response
            \Log::info('Stripe disconnect response: ', $response->toArray());

            // Remove the connected Stripe account ID
            $user->stripe_connect_account_id = null;
            $user->save();

            return redirect()->back()->with('success', 'Your Stripe account has been disconnected successfully.');
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Specific error for invalid account
            \Log::error('Stripe disconnect error: ' . $e->getMessage());

            if (str_contains($e->getMessage(), 'does not exist')) {
                // Update database to remove invalid Stripe account ID
                $user->stripe_connect_account_id = null;
                $user->save();

                return redirect()->back()->with('success', 'The Stripe account does not exist, but it has been removed from your account.');
            }

            return redirect()->back()->with('error', 'An error occurred while disconnecting from Stripe: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Stripe disconnect error: ' . $e->getMessage());

            // Check if the error is related to a missing account
            if (str_contains($e->getMessage(), 'does not exist')) {
                // Update database to remove invalid Stripe account ID
                $user->stripe_connect_account_id = null;
                $user->save();

                return redirect()->back()->with('success', 'The Stripe account does not exist, but it has been removed from your account.');
            }

            return redirect()->back()->with('error', 'An unexpected error occurred while disconnecting from Stripe.');
        }
    }

    public function connect()
    {
        $clientId = env('PAYPAL_CLIENT_ID');
        $redirectUrl = urlencode(env('PAYPAL_REDIRECT_URL'));
        $scope = urlencode('email');
        $url = "https://www.sandbox.paypal.com/signin/authorize?client_id={$clientId}&response_type=code&redirect_uri={$redirectUrl}";
        return redirect($url);
    }

    public function callback(Request $request)
    {
        $code = $request->query('code');

        if (!$code) {
            return redirect()->route('integrations')->with('error', 'Authorization failed or cancelled by user.');
        }

        try {
            $clientId = env('PAYPAL_CLIENT_ID');
            $clientSecret = env('PAYPAL_CLIENT_SECRET');
            $redirectUrl = env('PAYPAL_REDIRECT_URL');

            $client = new \GuzzleHttp\Client();

            $response = $client->post('https://api.sandbox.paypal.com/v1/oauth2/token', [
                'auth' => [$clientId, $clientSecret],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $redirectUrl,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $accessToken = $data['access_token'];
            $paypalUserId = $data['payer_id'];

            // Save the PayPal account ID in the user table
            $user = auth()->user();
            $user->paypal_connect_account_id = $paypalUserId;
            $user->save();

            return redirect()->route('integrations')->with('success', 'PayPal account connected successfully.');
        } catch (\Exception $e) {
            \Log::error('PayPal connect error: ' . $e->getMessage());
            return redirect()->route('integrations')->with('error', 'An error occurred while connecting to PayPal.');
        }
    }

    public function disconnectstripe()
    {
        $user = auth()->user();

        if (!$user->paypal_connect_account_id) {
            return redirect()->back()->with('error', 'No PayPal account connected.');
        }

        try {
            // Remove the PayPal account ID from the database
            $user->paypal_connect_account_id = null;
            $user->save();

            return redirect()->back()->with('success', 'Your PayPal account has been disconnected successfully.');
        } catch (\Exception $e) {
            \Log::error('PayPal disconnect error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while disconnecting from PayPal.');
        }
    }
}

