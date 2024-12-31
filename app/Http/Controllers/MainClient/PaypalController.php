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
use App\Services\PayPalService;
use GuzzleHttp\Client as GClient;
use DB;

class PaypalController extends Controller
{
    public $client_id;
    protected $payPalService;

    public function __construct(PayPalService $payPalService) {
        $this->client_id = getUserID();
        $this->payPalService = $payPalService;
    }

    public function getPaymentType($id){
        // Calculate totals and discounts
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);

        $total_discount = 0;
        $next_payment_recurring = 0;
        $interval_total = [];
        $interval = '';
        $interval_text = '';

        // Display items
        foreach ($invoice->items as $item) {
            $service = $item->service ?? null;

            if (!empty($service->trial_for)) {
                $next_payment_recurring += ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
            } else {
                if ($service && $service->service_type == 'recurring') {
                    $next_payment_recurring += ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                    $total_discount += $item->discount;
                    $interval_total[] = $service->recurring_service_currency_value_two;
                }
            }
        }

        // Calculate interval
        if ($next_payment_recurring) {
            $interval = ceil(array_sum($interval_total) / count($interval_total));
            $interval_text = $interval == 1 ? 'month' : ' month';
        }

        // Payment due calculation
        $payment_due = $invoice->total - $invoice->upfront_payment_amount + $total_discount;

        // Output client and discount details
        $client_name = $invoice->client->first_name . ' ' . $invoice->client->last_name;
        $total_amount_text = $next_payment_recurring
            ? "$" . number_format($invoice->total, 2) . " now, then $" . number_format($next_payment_recurring - $total_discount, 2) . "/{$interval_text}"
            : "$" . number_format($invoice->total, 2);
        
        if($next_payment_recurring){
            return [
                'total_amount' => $invoice->total,
                'recurring_payment' => number_format($next_payment_recurring - $total_discount, 2),
                'interval_text' => $interval_text,
                'interval' => $interval,
                'payment_type' => 'recurring',
            ];
        }else{
            return [
                'total_amount' => $invoice->total,
                'recurring_payment' => '',
                'interval_text' => '',
                'interval' => '',
                'payment_type' => 'onetime',
            ];
        }
    }

    public function createOneTimePaymentPaypal(Request $request, $id)
    {
        $invoice_info = $this->getPaymentType($id);
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);
        $client = Client::findOrFail($invoice->client_id);
        $addedByUser = User::findOrFail($invoice->added_by);

        if($addedByUser->paypal_connect_account_id==""){
            return redirect()->route('portal.invoices.show', $invoice->id)
                ->with('error', 'Seller has not connected their paypal merchant account');
        }

        $client = new GClient();
        $clientId = env('PAYPAL_CLIENT_ID');
        $secret = env('PAYPAL_SECRET');
        $baseUrl = env('PAYPAL_MODE') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        // Get Access Token
        $response = $client->post("$baseUrl/v1/oauth2/token", [
            'auth' => [$clientId, $secret],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
            'verify' => false,
        ]);

        $accessToken = json_decode($response->getBody(), true)['access_token'];

        // Create Payment Order
        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => $invoice_info['total_amount']
                    ],
                    'payee' => [
                        'merchant_id' => $addedByUser->paypal_connect_account_id,
                    ],
                ],
            ],
            'payment_source' => [
                'paypal' => [
                    'experience_context' => [
                        "payment_method_preference" => "IMMEDIATE_PAYMENT_REQUIRED",
                        "landing_page" => "LOGIN",
                        "user_action" => "PAY_NOW",
                        'return_url' => route('portal.paypal.payment.success', ['id' => $id]),
                        'cancel_url' => route('portal.paypal.payment.cancel', ['id' => $id]),
                    ],
                ],
            ],
        ];

        try {
            $response = $client->post("$baseUrl/v2/checkout/orders", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'Content-Type' => 'application/json',
                    'PayPal-Partner-Attribution-Id' => 'SMOOTHSERVICE_SP_PPCP',
                ],
                'verify' => false,
                'json' => $orderData,
            ]);

            $result = json_decode($response->getBody(), true);
            return redirect($result['links'][1]['href']);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return response()->json([
                'error' => 'Unable to create PayPal order. Please try again later.',
            ], 400);
        }
    }

    public function paypalOneTimePaymentSuccess(Request $request)
    {
        $invoiceId = $request->query('id'); // Retrieve the invoice ID from the query string
        $invoice = Invoice::findOrFail($invoiceId);

        $invoice->update([
            'paid_at' => now(),
            'payment_method' => 'paypal',
        ]);

        return redirect()->route('portal.invoices.show', $invoiceId)
            ->with('success', 'Payment successful! Your invoice has been marked as paid.');
    }

    public function paypalOneTimePaymentCancel(Request $request)
    {
        $invoiceId = $request->query('id'); // Retrieve the invoice ID from the query string
        return redirect()->route('portal.invoices.show', $invoiceId)
            ->with('error', 'Payment was canceled. Please try again.');
    }

    public function createProduct()
    {
        $client = new \GuzzleHttp\Client();
        $clientId = env('PAYPAL_CLIENT_ID');
        $secret = env('PAYPAL_SECRET');
        $baseUrl = env('PAYPAL_MODE') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        // Get Access Token
        $response = $client->post("$baseUrl/v1/oauth2/token", [
            'auth' => [$clientId, $secret],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
            'verify' => false,
        ]);

        $accessToken = json_decode($response->getBody(), true)['access_token'];

        // Create the product
        $productData = [
            'name' => 'My Subscription Product',
            'description' => 'This is a product for monthly subscription.',
            'type' => 'SERVICE', // SERVICE or PHYSICAL
            'category' => 'SOFTWARE', // Choose an appropriate category
        ];

        try {
            $response = $client->post("$baseUrl/v1/catalogs/products", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'Content-Type' => 'application/json',
                    'PayPal-Partner-Attribution-Id' => 'SMOOTHSERVICE_SP_PPCP',
                ],
                'verify' => false,
                'json' => $productData,
            ]);

            $product = json_decode($response->getBody(), true);
            return response()->json(['success' => true, 'product' => $product]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function createSubscriptionPlan(Request $request, $id)
    {
        $invoice_info = $this->getPaymentType($id);
        //echo "<pre>"; print_r($invoice_info); die;

        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);
        $client = Client::findOrFail($invoice->client_id);
        $addedByUser = User::findOrFail($invoice->added_by);

        $productId = $invoice->paypal_product_id; // Replace with your PayPal product ID
        $planName = 'Subscription Plan';
        $description = 'This is a monthly subscription plan.';

        try {
            $plan = $this->payPalService->createSubscriptionPlan($productId, $planName, $description, $invoice_info['recurring_payment'], $addedByUser->paypal_connect_account_id, $client, $invoice->id);
            return redirect($plan);
        } catch (\Exception $e) {
            return redirect()->route('portal.invoices.show', $id)
            ->with('error', $e->getMessage());
        }
    }

    public function paypalRecurringPaymentSuccess(Request $request)
    {
        $client = new \GuzzleHttp\Client();
        $clientId = env('PAYPAL_CLIENT_ID');
        $secret = env('PAYPAL_SECRET');
        $baseUrl = env('PAYPAL_MODE') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        // Get Access Token
        $response = $client->post("$baseUrl/v1/oauth2/token", [
            'auth' => [$clientId, $secret],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
        ]);

        $accessToken = json_decode($response->getBody(), true)['access_token'];

        // Extract subscription ID from the query string
        $subscriptionId = $request->query('subscription_id');

        // Get Subscription Details
        $response = $client->get("$baseUrl/v1/billing/subscriptions/$subscriptionId", [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
        ]);

        $subscription = json_decode($response->getBody(), true);

        // Save subscription details to the database
        $invoice_id = $request->query('id');
        $invoice = Invoice::with(['client', 'items'])->findOrFail($invoice_id);
        $client = Client::findOrFail($invoice->client_id);
        $addedByUser = User::findOrFail($invoice->added_by);

        // Check Subscription Status
        if ($subscription['status'] === 'ACTIVE') {
            // Mark the invoice as paid
            $invoice->update([
                'paid_at' => now(),
                'payment_method' => 'paypal',
            ]);

            // Determine the start date for the subscription
            $billingDate = $invoice->billing_date 
                ? Carbon::parse($invoice->billing_date)->startOfDay() 
                : now()->addDay(); // Default to tomorrow if no billing_date is set

            if ($billingDate->isPast()) {
                $billingDate = now()->addDay();
            }

            // Extract next billing time or calculate end date
            $nextBillingTime = isset($subscription['billing_info']['next_billing_time']) 
                ? Carbon::parse($subscription['billing_info']['next_billing_time']) 
                : $billingDate->copy()->addMonth(); // Default to 1 month

            // Save subscription details
            InvoiceSubscription::create([
                'invoice_id' => $invoice->id,
                'subscription_id' => $subscriptionId,
                'amount' => $subscription['billing_info']['outstanding_balance']['value'] ?? $invoice->total,
                'currency' => $subscription['billing_info']['outstanding_balance']['currency_code'] ?? 'cad',
                'intervel' => 'month',
                'payment_by' => 'paypal',
                'completed' => 1,
                'starts_at' => $billingDate,
                'ends_at' => $nextBillingTime,
            ]);

            // Send email notifications
            Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
            Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));

            return redirect()->route('portal.invoices.show', $invoice->id)
                ->with('success', 'Subscription activated successfully!');
        }

        return redirect()->route('portal.invoices.show', $invoice->id)
            ->with('error', 'Subscription not activated. Please try again.');
    }

    public function paypalRecurringPaymentCancel(Request $request){
        $invoiceId = $request->query('id');
        $invoice = Invoice::findOrFail($invoiceId);

        return redirect()->route('portal.invoices.show', $invoiceId)
            ->with('error','Payment Cancelled!');
    }

    public function cancelPaypalSubscription(Request $request, $id)
    {
        $invoice_sub = InvoiceSubscription::findOrFail($id);

        // Check if the subscription is already canceled
        if ($invoice_sub->cancelled_at) {
            return redirect()->back()->with('error', 'Subscription is already cancelled.');
        }

        if (!$invoice_sub || empty($invoice_sub->subscription_id)) {
            return redirect()->back()
                ->with('error', 'Subscription not found!');
        }

        $client = new \GuzzleHttp\Client();
        $clientId = env('PAYPAL_CLIENT_ID');
        $secret = env('PAYPAL_SECRET');
        $baseUrl = env('PAYPAL_MODE') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';

        // Get Access Token
        try {
            $response = $client->post("$baseUrl/v1/oauth2/token", [
                'auth' => [$clientId, $secret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);

            $accessToken = json_decode($response->getBody(), true)['access_token'];
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to generate access token. Please try again.');
        }

        // Cancel Subscription
        try {
            $response = $client->post("$baseUrl/v1/billing/subscriptions/$invoice_sub->subscription_id/cancel", [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'reason' => 'User requested cancellation.',
                ],
            ]);

            $invoice_sub->update([
                'cancelled_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Subscription cancelled successfully.');
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $errorResponse = json_decode($e->getResponse()->getBody()->getContents(), true);
            $errorMessage = $errorResponse['details'][0]['issue'] ?? 'Unable to cancel subscription. Please try again.';
            return redirect()->back()->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An unexpected error occurred. Please try again later.');
        }
    }

    public function handleWebhook(Request $request)
    {
        $eventType = $request->input('event_type');

        if (in_array($eventType, ['BILLING.SUBSCRIPTION.CANCELLED', 'BILLING.SUBSCRIPTION.SUSPENDED', 'BILLING.SUBSCRIPTION.PAYMENT.FAILED'])) {
            $subscriptionId = $request->input('resource.id');

            // Locate the subscription in your database
            $invoiceSub = InvoiceSubscription::where('subscription_id', $subscriptionId)->first();

            if (!$invoiceSub || $invoiceSub->cancelled_at) {
                Log::info("Webhook received for subscription {$subscriptionId}, already processed or not found.");
                return response()->json(['message' => 'Subscription already processed or not found.'], 200);
            }

            // Update the subscription in the database
            $invoiceSub->update([
                'cancelled_at' => now(),
            ]);

            Log::info("Subscription {$subscriptionId} canceled due to event {$eventType}.");
            return response()->json(['message' => 'Subscription cancellation processed successfully.'], 200);
        }

        return response()->json(['message' => 'Event type not handled.'], 200);
    }
}