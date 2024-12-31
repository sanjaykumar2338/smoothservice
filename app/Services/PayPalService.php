<?php

namespace App\Services;
use GuzzleHttp\Client;

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
    public function createSubscriptionPlan($productId, $planName, $description, $recurring_payment, $paypal_connect_account_id, $client, $invoice_id)
    {
        $accessToken = $this->getAccessToken();

        $planData = [
            'product_id' => $productId,
            'name' => $planName,
            'description' => $description,
            'status' => 'ACTIVE',
            'billing_cycles' => [
                [
                    'frequency' => [
                        'interval_unit' => 'MONTH',
                        'interval_count' => 1,
                    ],
                    'tenure_type' => 'REGULAR',
                    'sequence' => 1,
                    'total_cycles' => 12,
                    'pricing_scheme' => [
                        'fixed_price' => [
                            'value' => $recurring_payment,
                            'currency_code' => 'USD',
                        ],
                    ],
                ],
            ],
            'payment_preferences' => [
                'auto_bill_outstanding' => true,
                'setup_fee' => [
                    'value' => '0',
                    'currency_code' => 'USD',
                ],
                'setup_fee_failure_action' => 'CONTINUE',
                'payment_failure_threshold' => 3,
            ],
            'taxes' => [
                'percentage' => '0',
                'inclusive' => false,
            ],
            'payment_preferences' => [
                'payee_preference' => [
                    'payee' => [
                        'merchant_id' => $paypal_connect_account_id, // Route payments to the specific merchant
                    ],
                ],
            ],
        ];

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

        // Create Subscription
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
                    'given_name' => $client->first_name, // First name
                    'surname' => $client->last_name, // Last name
                ],
                'email_address' => $client->email, // Email
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
}