<?php

namespace App\Services;
use GuzzleHttp\Client;
use App\Models\Invoice;

class PayPalService
{
    protected $client;
    protected $baseUrl;
    protected $clientId;
    protected $secret;

    public function __construct()
    {
        $this->client = new Client();
        $this->clientId = env('PAYPAL_CLIENT_ID');
        $this->secret = env('PAYPAL_SECRET');
        $this->baseUrl = env('PAYPAL_MODE') === 'sandbox'
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    /**
     * Get Access Token
     */
    public function getAccessToken()
    {
        $response = $this->client->post("{$this->baseUrl}/v1/oauth2/token", [
            'auth' => [$this->clientId, $this->secret],
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
            'verify' => false,
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    /**
     * Create Subscription Plan
     */
    public function createSubscriptionPlan($productId, $planName, $description, $recurring_payment, $paypal_connect_account_id, $client, $invoice_id, $invoice)
    {
        $accessToken = $this->getAccessToken();

        // Extract recurring values from the invoice summary
        $summary = invoiceSummary($invoice);

        $setupFee = $summary['total']; // Use total amount as setup fee
        $recurringAmount = $summary['next_payment_recurring'];
        $interval = $summary['interval'];
        $intervalType = strtolower($summary['interval_type']); // Normalize case

        // Normalize intervalType to PayPal-compatible format
        $paypalIntervalMap = [
            'day' => 'DAY',
            'week' => 'WEEK',
            'month' => 'MONTH',
            'year' => 'YEAR',
        ];

        $paypalIntervalType = $paypalIntervalMap[$intervalType] ?? 'MONTH'; // Default to MONTH if undefined
        $billingCycles = [];

        // Add regular recurring billing cycle
        if ($recurringAmount > 0) {
            $billingCycles[] = [
                'frequency' => [
                    'interval_unit' => strtoupper($paypalIntervalType), // e.g., MONTH, WEEK, YEAR, DAY
                    'interval_count' => $interval,                     // e.g., every 3 months
                ],
                'tenure_type' => 'REGULAR', // Mark this as a regular cycle
                'sequence' => 1,            // First in sequence
                'total_cycles' => 12,       // Number of recurring cycles (e.g., 12 for a year)
                'pricing_scheme' => [
                    'fixed_price' => [
                        'value' => number_format($recurringAmount, 2, '.', ''), // Use recurring amount
                        'currency_code' => 'USD',                              // Currency for recurring payments
                    ],
                ],
            ];
        }

        // Prepare the plan data with setup fee
        $planData = [
            'product_id' => $productId,
            'name' => $planName,
            'description' => $description,
            'status' => 'ACTIVE',
            'billing_cycles' => $billingCycles,
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => number_format($setupFee, 2, '.', ''), // Use summary total as setup fee
                    'currency_code' => 'USD',
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3,
            ],
            'taxes' => [
                'percentage' => '0',
                'inclusive' => false,
            ],
        ];

        // Make API request to create the subscription plan
        $response = $this->client->post("{$this->baseUrl}/v1/billing/plans", [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'verify' => false,
            'json' => $planData,
        ]);

        $data = json_decode($response->getBody(), true);
        $planId = $data['id'];

        // Create the subscription
        $subscriptionData = [
            'plan_id' => $planId,
            'application_context' => [
                'brand_name' => env('APP_NAME'),
                'locale' => 'en-US',
                'return_url' => route('portal.recurring.paypal.payment.success', ['id' => $invoice_id]),
                'cancel_url' => route('portal.recurring.paypal.payment.cancel', ['id' => $invoice_id]),
            ],
            'subscriber' => [
                'name' => [
                    'given_name' => $client->first_name,
                    'surname' => $client->last_name,
                ],
                'email_address' => $client->email,
            ],
        ];

        $response = $this->client->post("$this->baseUrl/v1/billing/subscriptions", [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => $subscriptionData,
        ]);

        $result = json_decode($response->getBody(), true);
        return $result['links'][0]['href'];
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
                $interval_total[] = $service->trial_for;
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
}