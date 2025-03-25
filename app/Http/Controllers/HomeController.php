<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LandingPage;
use App\Models\Client;
use App\Models\Invoice;
uSE App\Models\Intakeform;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\ClientStatus;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientWelcome;
use App\Models\FeedbackEntry;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function order(Request $request, $landing_no){
        $landingPage = LandingPage::where('landing_no', $landing_no)->first();
        if(!$landingPage){
            abort(404);
        }

        //echo "<pre>"; print_r($landingPage); die;
        return view('client.grapejs_frontend')->with('landingPage', $landingPage)->with('slug', $landingPage->slug);
    }

    public function landingpageinfo(Request $request, $slug){
        $page = LandingPage::where('slug', $slug)->first();

        if (!$page) {
            return response()->json(['status' => 'error', 'message' => 'Page not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'html' => $page->html,
            'css' => $page->css
        ]);
    }

    public function landingpagestore(Request $request)
    {
        $slug = $request->landing_page;
        $landingPage = LandingPage::where('slug', $slug)->first();

        if (!$landingPage) {
            return response()->json(['message' => 'Landing page not found'], 404);
        }

        $user_id = $landingPage->user_id;
        $email = $request->email;

        // Check if client exists
        $client = Client::where('email', $email)->first();
        if (!$client) {
            // Generate a random password
            $randomPassword = str()->random(10);
            $client_status_id = ClientStatus::where('added_by', $user_id)
                ->where('label', 'Lead')
                ->value('id');

            // If "Lead" status doesn't exist, take any available status
            if (!$client_status_id) {
                $client_status_id = ClientStatus::where('added_by', $user_id)->value('id');
            }

            // If no statuses exist at all, set to null or a default value
            if (!$client_status_id) {
                $client_status_id = null; // Or provide a default ID if needed
            }

            // Create new client
            $client = Client::create([
                'first_name' => $request->first_name ?? $request->name ?? 'Guest',
                'last_name' => $request->last_name ?? $request->last ?? '',
                'email' => $email,
                'password' => Hash::make($randomPassword),
                'added_by' => $user_id,
                'status' => $client_status_id
            ]);

            // Send email to new client
            Mail::to($client->email)->send(new ClientWelcome($client, $randomPassword));
        }

        $invoice_no = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $invoice = Invoice::create([
            'client_id' => $client->id,
            'currency' => '',
            'total' => 0,
            'added_by' => $user_id,
            'public_key' => \Str::random(32),
            'invoice_no' => $invoice_no,
            'landing_page' => $landingPage->id,
        ]);

        $totalInvoiceAmount = 0;

        // Save each item in the invoice_items table
        foreach ($request->selectedServices as $index => $serviceId) {
            $service = $serviceId ? Service::find($serviceId) : null;
            //echo "<pre>"; print_r($service->recurring_service_currency_value); die;

            // Default values for price, discount, and trial price
                $price = 0;
            $quantity = 1;
            $discount = 0;
            $discountsnextpayment = 0;
            $trialPrice = 0;
        
            // Check for recurring service with trial price
            if ($service && $service->service_type === 'recurring' && $service->trial_for) {
                //$trialPrice = $service->trial_price ?? 0;
                $price = $service->recurring_service_currency_value;
                $trialPrice =  $service->trial_price ?? $price;
            } else if($service && $service->service_type === 'recurring' ){
                $price = $service->recurring_service_currency_value;
            } else {
                // Use regular price if not a recurring service with trial
                $price = $service->one_time_service_currency_value;
            }
            
            // Calculate total item price
            $itemTotal = ($trialPrice ? $trialPrice : ($price * $quantity)) - $discount;
            $totalInvoiceAmount += $itemTotal;
            
            //echo $price; die;
            // Save each invoice item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_id' => $service->id, // Save service if selected
                'item_name' => $service->service_name,
                'description' => $service->description,
                'price' => $trialPrice ?: $price, // Save trial price if applicable
                'quantity' => $quantity,
                'discount' => $discount,
                'discountsnextpayment' => $discountsnextpayment,
            ]);
        }        

        $title = $invoice->invoice_no.' invoice product id';
        $paypal_product_id = $this->createProduct($title);

        // Update the total for the invoice
        $invoice->update(['total' => $totalInvoiceAmount]);
        $invoice->update(['paypal_product_id' => $paypal_product_id]);
        
        if (1) {
            $companySetting = \App\Models\CompanySetting::where('user_id', $user_id)->first();
            $companyName = $companySetting->company_name ?? env('APP_NAME');
            Mail::to($client->email)->send(new \App\Mail\InvoiceGenerated($invoice, $client, $companyName));
        }

        if(!getUserID() || 1){
            \Auth::guard('web')->logout();
            \Auth::guard('client')->login($client);
        }

        // Client exists, return client ID
        return response()->json([
            'message' => 'invoice created successfully!',
            'invoice_id' => $invoice->id
        ], 200);
    }

    public function createProduct($title)
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
            'name' => $title,
            'description' => $title.' subscription.',
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
            return $product['id'];
        } catch (\Exception $e) {
            return '';
        }
    }

    public function landingpagepayment($id)
    {
        // Retrieve the invoice by its ID along with the associated client and items
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);

        if(!getUserID()){
            $client = Client::find($invoice->client_id);
            Auth::guard('web')->logout();
            Auth::guard('client')->login($client);
            //return redirect()->route('landingpagepayment', $invoice->invoice_id);
        }

        if ($invoice->paid_at) {
            return redirect()->route('portal.invoices.show',$invoice->id)->with('error', 'Invoice already paid.');
        }

        // Retrieve the services in case you want to display service information in the invoice
        $user_id = getUserID();
        $services = Service::where('user_id',$user_id)->get();

        // Fetch all users and team members added by the current logged-in user
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', $user_id)->get();
        $addedByUser = User::findOrFail($invoice->added_by);

        // Initialize variables
        $nextPaymentRecurring = 0;
        $totalDiscount = 0;
        $intervalTotal = [];
        $trialServices = [];
        $nonTrialServices = [];
        $upfrontPayment = $invoice->upfront_payment_amount > 0 ? $invoice->upfront_payment_amount : 0;
        $currency = $invoice->currency;
        $firstServiceType = null;

        foreach ($invoice->items as $item) {
            $service = $item->service;

            // Determine the first service type (month, day, year, or week)
            if (!$firstServiceType && $service->service_type == 'recurring') {
                $firstServiceType = $service->recurring_service_currency_value_two_type ?? '';
            }

            if (!empty($service->trial_for)) {
                // Trial-based services
                $trialServices[] = [
                    'name' => $service->service_name ?? $item->item_name,
                    'trialPrice' => ($service->trial_price - $item->discount),
                    'trialDuration' => $service->trial_for . ' ' . ($service->trial_for > 1 ? $service->trial_period . 's' : $service->trial_period),
                    'nextPrice' => ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment,
                    'interval' => $service->recurring_service_currency_value_two . ' ' . ($service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type)
                ];

                $nextPaymentRecurring += ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                $intervalTotal[] = $service->trial_for;
            } else {
                // Non-trial services
                $price = ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                $nonTrialServices[] = [
                    'name' => $service->service_name ?? $item->item_name,
                    'price' => $service->recurring_service_currency_value,
                    'quantity' => $item->quantity,
                    'interval' => $service->recurring_service_currency_value_two . ' ' . ($service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type)
                ];

                $nextPaymentRecurring += $price;
                $totalDiscount += $item->discount;
                $intervalTotal[] = $service->recurring_service_currency_value_two;
            }
        }

        // Calculate totals
        $nextTotalWithoutTrial = array_sum(array_column($nonTrialServices, 'price')) * $item->quantity;

        // Summary data
        $main_data = [
            'currency' => $currency,
            'trialServices' => $trialServices,
            'nonTrialServices' => $nonTrialServices,
            'nextPaymentRecurring' => $nextPaymentRecurring,
            'totalDiscount' => $totalDiscount,
            'upfrontPayment' => $upfrontPayment,
            'firstServiceType' => $firstServiceType
        ];

        $summary = [
            'total' => 0,
            'trial_amount' => 0,
            'next_payment_recurring' => 0,
            'total_discount' => 0,
            'payment_type' => 'fixed', // Default to 'fixed'
            'interval' => 1, // Default interval is 1
            'interval_type' => 'month', // Default interval type
        ];

        foreach ($invoice->items as $key => $item) {
            $service = $item->service;

            // Calculate totals and discounts
            $itemTotal = ($item->price * $item->quantity) - ($item->discount * $item->quantity);
            $summary['total'] += $itemTotal;
            $summary['total_discount'] += $item->discount * $item->quantity;

            // Trial-based services
            if (!empty($service->trial_for)) {
                $trialPrice = $service->trial_price - $item->discount;
                $summary['trial_amount'] += $trialPrice;
                $summary['next_payment_recurring'] += ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment * $item->quantity;
                $summary['payment_type'] = 'recurring';
                $summary['interval'] = $service->recurring_service_currency_value_two ?? 1;
                $summary['interval_type'] = strtolower($service->recurring_service_currency_value_two_type ?? 'month');
            } elseif ($service->service_type == 'recurring') {
                // Recurring services without trial
                $summary['next_payment_recurring'] += ($service->recurring_service_currency_value * $item->quantity) - $item->discount * $item->quantity;
                $summary['payment_type'] = 'recurring';
                $summary['interval'] = $service->recurring_service_currency_value_two ?? 1;
                $summary['interval_type'] = strtolower($service->recurring_service_currency_value_two_type ?? 'month');
            }
        }

        // Adjust totals based on upfront payments
        if ($invoice->upfront_payment_amount > 0) {
            $summary['total'] -= $invoice->upfront_payment_amount;
        }

        //echo "<pre>"; print_r($main_data); die;
        // Pass the invoice data to the view
        return view('c_main.c_pages.c_invoice.c_outside', compact('invoice', 'services', 'users', 'teamMembers', 'addedByUser', 'main_data'));
    }

    public function intakeform(Request $request, $id, $inv){
        $landing_page = LandingPage::where('id',$id)->first();
        $invoice = Invoice::where('invoice_no', $inv)->first();

        if($landing_page->intake_form && $invoice){
            $intake_form = Intakeform::where('id', $landing_page->intake_form)
                ->where('form_fields', '<>', '')
                ->first();
            
            if($intake_form){
                return view('client.intakeform_template')->with('intake_form', $intake_form)->with('invoice_no',$invoice->id)->with('landing_page', $id);
            }
        }

        return abort(404);
    }

    public function storeFeedback(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'landing_page' => 'required|integer',
                'invoice_id'   => 'nullable|integer',
                'form_data'    => 'required|array',
            ]);

            $landingPage = $validatedData['landing_page'];
            $invoiceId   = $validatedData['invoice_id'] ?? null;
            $formData    = $validatedData['form_data'];

            $cleanedData = [];

            foreach ($formData as $field) {
                $entry = [
                    'name'  => $field['name'] ?? null,
                    'type'  => $field['type'] ?? 'text',
                    'value' => $field['value'] ?? null,
                ];

                // If file field with base64 string, save file
                if (
                    $entry['type'] === 'file' &&
                    !empty($entry['value']) &&
                    preg_match('/^data:image\/(\w+);base64,/', $entry['value'])
                ) {
                    $entry['value'] = $this->saveBase64Image($entry['value']);
                }

                $cleanedData[] = $entry;
            }

            // Save to database (without extra escaping)
            $feedback = FeedbackEntry::create([
                'landing_page' => $landingPage,
                'invoice_id'   => $invoiceId,
                'user_id'      => getUserID(),
                'form_data'    => $cleanedData, // Eloquent will auto-convert to JSON if casted
            ]);

            return response()->json([
                'message'  => 'Feedback submitted successfully',
                'feedback' => $feedback
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while saving feedback',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Save Base64 Image and return the file path
     */
    private function saveBase64Image($base64String)
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64String, $matches)) {
                $imageType = $matches[1]; // Get image format (png, jpg, etc.)
                $base64String = substr($base64String, strpos($base64String, ',') + 1);
                $decodedImage = base64_decode($base64String);

                if ($decodedImage === false) {
                    return null; // Invalid base64 data
                }

                // Generate unique file name
                $fileName = 'uploads/feedback/' . uniqid() . '.' . $imageType;

                // Store image in Laravel storage (public disk)
                Storage::disk('public')->put($fileName, $decodedImage);

                // Return full URL of the stored image
                return asset('storage/' . $fileName);
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }

}
