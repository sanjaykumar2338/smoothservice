<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Client;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;

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
            })
            ->paginate(10);

        return view('client.pages.invoices.index', compact('invoices', 'search'));
    }

    // Show the form to create a new invoice
    public function create()
    {
        $teamMemberId = getUserID();
        $clients = Client::where('added_by',$teamMemberId)->get();
        $services = Service::where('user_id',$teamMemberId)->get();

        return view('client.pages.invoices.add', compact('clients', 'services'));
    }

    // Store a new invoice in the database
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'item_names' => 'required|array',
            'item_names.*' => 'required|max:255',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'discounts' => 'nullable|array',
            'discounts.*' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
            'upfront_payment_amount' => 'nullable|numeric|min:0', // Validation for upfront payment amount if present
        ]);

        // Convert checkbox values
        $sendEmail = $request->has('send_email') ? 1 : 0; // Set to 1 if the checkbox is checked, else 0
        $partialPayment = $request->has('partial_payment') ? 1 : 0; // Set to 1 if checked
        $upfrontPaymentAmount = $partialPayment ? $request->input('upfront_payment_amount') : null; // Save upfront payment amount only if partial payment is checked
        $billingDate = $request->has('custom_billing_date') ? $request->input('billing_date') : null; // Set billing date if provided
        $currency = $request->has('custom_currency') ? $request->input('currency') : 'USD'; // Set currency, fallback to USD

        // Create the invoice record
        $invoice = Invoice::create([
            'client_id' => $request->client_id,
            'service_id' => $request->service_id,
            'due_date' => $request->due_date,
            'note' => $request->note,
            'send_email' => $sendEmail,
            'partial_payment' => $partialPayment, // This is just a flag if partial payment is selected
            'upfront_payment_amount' => $upfrontPaymentAmount, // Save the upfront payment amount
            'billing_date' => $billingDate, // Handle custom billing date
            'currency' => $currency, // Handle custom currency
            'total' => 0,  // Total to be calculated later
            'due_date' => $request->due_date,
            'added_by' => auth()->id(),
        ]);

        $totalInvoiceAmount = 0;

        // Save each item in the invoice
        foreach ($request->item_names as $index => $itemName) {
            $price = $request->prices[$index];
            $quantity = $request->quantities[$index];
            $discount = $request->discounts[$index] ?? 0;
            $itemTotal = ($price * $quantity) - $discount;
            $totalInvoiceAmount += $itemTotal;

            // Save each invoice item
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_id' => $request->service_id, // Assuming the service is tied to the invoice, not each item
                'item_name' => $itemName,
                'description' => $request->descriptions[$index] ?? null,
                'price' => $price,
                'quantity' => $quantity,
                'discount' => $discount
            ]);
        }

        // Update the total for the invoice
        $invoice->update(['total' => $totalInvoiceAmount]);

        return redirect()->route('invoices.list')->with('success', 'Invoice created successfully');
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
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'item_names' => 'required|array',
            'item_names.*' => 'required|max:255',
            'prices' => 'required|array',
            'prices.*' => 'required|numeric',
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
            'discounts' => 'nullable|array',
            'discounts.*' => 'nullable|numeric|min:0',
            'due_date' => 'nullable|date',
        ]);

        $invoice = Invoice::findOrFail($id);

        // Update the main invoice details
        $invoice->update([
            'client_id' => $request->client_id,
            'note' => $request->note,
            'send_email' => $request->send_email ? 1 : 0,
            'partial_payment' => $request->partial_payment,
            'billing_date' => $request->billing_date,
            'currency' => $request->currency,
            'due_date' => $request->due_date,
        ]);

        // Delete existing invoice items
        InvoiceItem::where('invoice_id', $id)->delete();

        $totalInvoiceAmount = 0;

        // Loop through each item and store them in the invoice_items table
        foreach ($request->item_names as $index => $itemName) {
            $price = $request->prices[$index];
            $quantity = $request->quantities[$index];
            $discount = $request->discounts[$index] ?? 0;
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'service_id' => $request->services[$index] ?? null,
                'item_name' => $itemName,
                'description' => $request->descriptions[$index] ?? null,
                'price' => $price,
                'quantity' => $quantity,
                'discount' => $discount
            ]);

            $totalInvoiceAmount += $itemTotal;
        }

        // Update the total in the invoice after recalculating from all items
        $invoice->update(['total' => $totalInvoiceAmount]);

        return redirect()->route('invoices.list')->with('success', 'Invoice updated successfully');
    }

    // Delete an invoice
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('invoices.list')->with('success', 'Invoice deleted successfully');
    }

    public function show($id)
    {
        // Retrieve the invoice by its ID along with the associated client and items
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);

        // Retrieve the services in case you want to display service information in the invoice
        $services = Service::where('user_id', auth()->id())->get();

        // Pass the invoice data to the view
        return view('client.pages.invoices.show', compact('invoice', 'services'));
    }
}
