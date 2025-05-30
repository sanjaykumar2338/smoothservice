<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Client;
use App\Models\User;
use App\Models\Order;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;

class InvoiceController extends Controller
{
    // List all invoices
    public function index(Request $request)
    {
        $search = $request->input('search');
        $invoices = Invoice::with('client', 'service')
            ->when($search, function ($query, $search) {
                return $query->whereHas('client', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orwhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('service_name', 'like', "%{$search}%");
                });
            })->orderBy('created_at','desc')
            ->where('added_by', getUserID())->paginate(10);

        return view('client.pages.invoices.index', compact('invoices', 'search'));
    }

    // Show the form to create a new invoice
    public function create(Request $request, $client_id = null)
    {
        $teamMemberId = getUserID();
        $clients = Client::where('added_by',$teamMemberId)->orderBy('created_at','desc')->get();
        $services = Service::where('user_id',$teamMemberId)->orderBy('created_at','desc')->get();
        $orderId = $request->get('order_id');
        $order = '';

        if($orderId){
            $order = Order::where('order_no', $orderId)->first();
            if(!$order){
                return redirect()->back()->with('error', 'Order not found!');
            }else{
                $client_id = $order->client->id;
            }
        }

        //echo "<pre>"; print_r($services_all); die;
        return view('client.pages.invoices.add', compact('clients', 'services', 'client_id', 'order'));
    }

    // Store a new invoice in the database
    public function store(Request $request)
    {
        //echo "<pre>"; print_r($request->all()); die;
        // Validate the request
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|array', // Expecting an array of services
            'service_id.*' => 'nullable|exists:services,id', // Validate each service
            'item_names' => 'required|array',
            'item_names.*' => 'required_if:service_id,|max:255', // Required if service is not selected
            'prices' => 'required|array',
            'prices.*' => 'required|numeric',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'discounts' => 'nullable|array',
            'discounts.*' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'upfront_payment_amount' => 'nullable|numeric|min:0',
            'order_id' => 'nullable',
        ]);

        // Convert checkbox values
        $sendEmail = $request->has('send_email') ? 1 : 0;
        $partialPayment = $request->has('partial_payment') ? 1 : 0;
        $upfrontPaymentAmount = $partialPayment ? $request->input('upfront_payment_amount') : null;
        $billingDate = $request->has('custom_billing_date') ? $request->input('billing_date') : null;

        // Determine currency
        $currency = $request->has('custom_currency') ? $request->input('custom_currency') : null;
        if (!$currency) {
            // Check services for `one_time_service_currency`
            foreach ($request->service_id as $serviceId) {
                if ($serviceId) {
                    $service = Service::find($serviceId); // Fetch the service by ID
                    if ($service && $service->one_time_service_currency) {
                        $currency = $service->one_time_service_currency;
                        break; // Use the first available service's currency
                    }
                }
            }
            // Fallback to CAD if no service has a currency
            $currency = $currency ?: 'CAD';
        }

        $invoice_no = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));

        // Create the invoice record
        $user_id = getUserID();
        $invoice = Invoice::create([
            'client_id' => $request->client_id,
            'order_id' => $request->order_id,
            'due_date' => $request->due_date,
            'note' => $request->note,
            'send_email' => $sendEmail,
            'partial_payment' => $partialPayment,
            'upfront_payment_amount' => $upfrontPaymentAmount,
            'billing_date' => $billingDate,
            'currency' => $currency,
            'total' => 0,
            'added_by' => $user_id,
            'public_key' => \Str::random(32),
            'invoice_no' => $invoice_no,
        ]);

        $totalInvoiceAmount = 0;

        // Save each item in the invoice_items table
        foreach ($request->item_names as $index => $itemName) {
            $serviceId = $request->service_id[$index] ?? null;
            $service = $serviceId ? Service::find($serviceId) : null;
        
            // Default values for price, discount, and trial price
            $price = 0;
            $quantity = $request->quantities[$index];
            $discount = $request->discounts[$index] ?? 0;
            $discountsnextpayment = $request->discountsnextpayment[$index] ?? 0;
            $trialPrice = 0;
        
            // Check for recurring service with trial price
            if ($service && $service->service_type === 'recurring' && $service->trial_for) {
                //$trialPrice = $service->trial_price ?? 0;
                $price = $request->prices[$index];
            } else {
                // Use regular price if not a recurring service with trial
                $price = $request->prices[$index];
            }
        
            // Calculate total item price
            $itemTotal = ($trialPrice ? $trialPrice : ($price * $quantity)) - $discount;
            $totalInvoiceAmount += $itemTotal;
        
            // Save each invoice item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_id' => $serviceId, // Save service if selected
                'item_name' => $itemName,
                'description' => $request->descriptions[$index] ?? null,
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

        if ($sendEmail) {
            $client = Client::findOrFail($request->client_id);
            $companySetting = \App\Models\CompanySetting::where('user_id', getUserID())->first();
            $companyName = $companySetting->company_name ?? env('APP_NAME');
            Mail::to($client->email)->send(new \App\Mail\InvoiceGenerated($invoice, $client, $companyName));
        }        

        return redirect()->route('invoices.list')->with('success', 'Invoice created successfully');
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

    // Show the form to edit an existing invoice
    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $teamMemberId = getUserID();
        $clients = Client::where('added_by', $teamMemberId)->get();
        $services = Service::where('user_id', $teamMemberId)->get();

        return view('client.pages.invoices.edit', compact('invoice', 'clients', 'services'));
    }

    // Update an existing invoice
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'item_names' => 'required|array',
            'item_names.*' => [
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; // Get the index (e.g., item_names.0 -> 0)
                    if (empty($value) && empty($request->service_id[$index])) {
                        $fail('Either the item name or service must be selected for item #' . ($index + 1) . '.');
                    }
                },
                'max:255'
            ],
            'prices' => 'required|array',
            'prices.*' => 'required|numeric',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'discounts' => 'nullable|array',
            'discounts.*' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        // Find the invoice
        $invoice = Invoice::findOrFail($id);

        // Convert checkbox values
        $partialPayment = $request->has('partial_payment') ? 1 : 0;
        $sendEmail = $request->has('send_email') ? 1 : 0;

        // Determine the currency
        $currency = $request->currency;
        if (!$currency) {
            foreach ($request->service_id as $serviceId) {
                if ($serviceId) {
                    $service = Service::find($serviceId); // Fetch the service by ID
                    if ($service && $service->one_time_service_currency) {
                        $currency = $service->one_time_service_currency;
                        break; // Use the first available service's currency
                    }
                }
            }
            $currency = $currency ?: 'CAD'; // Fallback to CAD if no service has a currency
        }

        // Update the invoice details
        $invoice->update([
            'client_id' => $request->client_id,
            'note' => $request->note,
            'send_email' => $sendEmail,
            'partial_payment' => $partialPayment,
            'upfront_payment_amount' => $request->upfront_payment_amount ?? null,
            'billing_date' => $request->billing_date ?? null,
            'currency' => $currency,
            'due_date' => $request->due_date,
        ]);

        // Delete existing invoice items
        InvoiceItem::where('invoice_id', $id)->delete();

        $totalInvoiceAmount = 0;

        // Loop through each item and store them in the invoice_items table
        foreach ($request->item_names as $index => $itemName) {
            $serviceId = $request->service_id[$index] ?? null;
            $service = $serviceId ? Service::find($serviceId) : null;
        
            // Default values for price, discount, and trial price
            $price = 0;
            $quantity = $request->quantities[$index];
            $discount = $request->discounts[$index] ?? 0;
            $discountsnextpayment = $request->discountsnextpayment[$index] ?? 0;
            $trialPrice = 0;
        
            // Check for recurring service with trial price
            if ($service && $service->service_type === 'recurring' && $service->trial_for) {
                //$trialPrice = $service->trial_price ?? 0;
                $price = $request->prices[$index];
            } else {
                // Use regular price if not a recurring service with trial
                $price = $request->prices[$index];
            }
        
            // Calculate total item price
            $itemTotal = ($trialPrice ? $trialPrice : ($price * $quantity)) - $discount;
            $totalInvoiceAmount += $itemTotal;
        
            // Save each invoice item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_id' => $serviceId, // Save service if selected
                'item_name' => $itemName,
                'description' => $request->descriptions[$index] ?? null,
                'price' => $trialPrice ?: $price, // Save trial price if applicable
                'quantity' => $quantity,
                'discount' => $discount,
                'discountsnextpayment' => $discountsnextpayment,
            ]);
        }        

        // Update the total for the invoice
        $invoice->update(['total' => $totalInvoiceAmount]);

        // Redirect back with success message
        return redirect()->route('invoices.list')->with('success', 'Invoice updated successfully');
    }

    // Delete an invoice
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        if($invoice){
            InvoiceItem::where('invoice_id', $id)->delete();
        }

        $invoice->delete();
        return redirect()->route('invoices.list')->with('success', 'Invoice deleted successfully');
    }

    public function show($id)
    {
        // Retrieve the invoice by its ID along with the associated client and items
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);
        //echo "<pre>"; print_r($invoice); die;

        // Retrieve the services in case you want to display service information in the invoice
        $services = Service::where('user_id', auth()->id())->get();

        // Fetch all users and team members added by the current logged-in user
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', auth()->id())->get();

        // Pass the invoice data to the view
        return view('client.pages.invoices.show', compact('invoice', 'services', 'users', 'teamMembers'));
    }

    public function downloadInvoice($id)
    {
        // Retrieve the invoice and related data
        $invoice = Invoice::with('client', 'items')->findOrFail($id);

        // Generate the PDF using the view
        $pdf = PDF::loadView('client.pages.invoices.pdf', compact('invoice'));

        // Download the PDF
        return $pdf->download('invoice_' . $invoice->id . '.pdf');
    }

    public function duplicate($id)
    {
        // Fetch the original invoice with related items
        $invoice = Invoice::with('items')->findOrFail($id);

        // Create a new invoice by duplicating the original invoice's data
        $newInvoice = $invoice->replicate(); // Duplicate the main invoice fields
        $newInvoice->status = 'Draft'; // Optionally set a default status
        $newInvoice->public_key = \Str::random(32);
        $newInvoice->created_at = now(); // Set the creation date to now
        $newInvoice->updated_at = now();
        $newInvoice->save(); // Save the new invoice

        // Duplicate each item in the invoice
        foreach ($invoice->items as $item) {
            $newItem = $item->replicate(); // Duplicate the item
            $newItem->invoice_id = $newInvoice->id; // Link the new item to the new invoice
            $newItem->save(); // Save the new item
        }

        return redirect()->route('invoices.edit', $newInvoice->id)->with('success', 'Invoice duplicated successfully');
    }

    public function publicShow($id, Request $request)
    {
        $invoice = Invoice::findOrFail($id);

        // Validate the key
        if ($request->input('key') !== $invoice->public_key) {
            abort(403, 'Unauthorized access.');
        }

        return view('client.pages.invoices.show', compact('invoice'));
    }

    public function updateAddress(Request $request, $id)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_country' => 'required|string|max:255',
            'billing_state' => 'required|string|max:255',
            'billing_postal_code' => 'required|string|max:50',
            'billing_company' => 'nullable|string|max:255',
            'billing_tax_id' => 'nullable|string|max:50',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update($request->only([
            'billing_first_name', 'billing_last_name', 'billing_address',
            'billing_city', 'billing_country', 'billing_state',
            'billing_postal_code', 'billing_company', 'billing_tax_id'
        ]));

        return redirect()->back()->with('success', 'Billing details updated successfully.');
    }

    public function sendEmail(Request $request)
    {
        $emails = $request->input('emails');  // Array of emails
        $invoice = Invoice::findOrFail($request->input('invoiceId'));

        foreach ($emails as $email) {
            Mail::to($email)->send(new InvoiceMail($invoice));
        }

        return response()->json(['success' => true]);
    }

    public function refund(Request $request, Invoice $invoice)
    {
        $request->validate([
            'refund_reason' => 'required|string|max:255',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        // Store refund details in the 'invoice_refunds' table
        $invoice->refunds()->create([
            'refund_reason' => $request->refund_reason,
            'refund_amount' => $request->refund_amount,
        ]);

        // Update the invoice status if needed (e.g., partial refund, full refund)
        // $invoice->status = 'refunded'; // Update status logic if needed
        // $invoice->save();

        return redirect()->back()->with('success', 'Refund has been added successfully.');
    }
}
