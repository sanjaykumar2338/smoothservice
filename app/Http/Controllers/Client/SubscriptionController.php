<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\Service;
use App\Models\Client;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    // List all subscriptions
    public function index(Request $request)
    {
        $search = $request->input('search');

        $subscriptions = Subscription::with('client', 'service')
            ->when($search, function ($query, $search) {
                return $query->whereHas('client', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->paginate(10);

        return view('client.pages.subscriptions.index', compact('subscriptions', 'search'));
    }

    // Show the form to create a new subscription
    public function create()
    {
        $teamMemberId = getUserID();
        $clients = Client::where('added_by', $teamMemberId)->get();
        $services = Service::where('user_id', $teamMemberId)->get();

        return view('client.pages.subscriptions.add', compact('clients', 'services'));
    }

    // Store a new subscription in the database
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
            'upfront_payment_amount' => 'nullable|numeric|min:0',
        ]);

        // Convert checkbox values
        $sendEmail = $request->has('send_email') ? 1 : 0;
        $partialPayment = $request->has('partial_payment') ? 1 : 0;
        $upfrontPaymentAmount = $partialPayment ? $request->input('upfront_payment_amount') : null;
        $billingDate = $request->has('custom_billing_date') ? $request->input('billing_date') : null;
        $currency = $request->has('custom_currency') ? $request->input('currency') : 'USD';

        // Create the subscription record
        $subscription = Subscription::create([
            'client_id' => $request->client_id,
            'service_id' => $request->service_id,
            'due_date' => $request->due_date,
            'note' => $request->note,
            'send_email' => $sendEmail,
            'partial_payment' => $partialPayment,
            'upfront_payment_amount' => $upfrontPaymentAmount,
            'billing_date' => $billingDate,
            'currency' => $currency,
            'total' => 0,
            'due_date' => $request->due_date,
            'added_by' => auth()->id(),
        ]);

        $totalSubscriptionAmount = 0;

        // Save each item in the subscription
        foreach ($request->item_names as $index => $itemName) {
            $price = $request->prices[$index];
            $quantity = $request->quantities[$index];
            $discount = $request->discounts[$index] ?? 0;
            $itemTotal = ($price * $quantity) - $discount;
            $totalSubscriptionAmount += $itemTotal;

            // Save each subscription item
            SubscriptionItem::create([
                'subscription_id' => $subscription->id,
                'service_id' => $request->service_id,
                'item_name' => $itemName,
                'description' => $request->descriptions[$index] ?? null,
                'price' => $price,
                'quantity' => $quantity,
                'discount' => $discount,
            ]);
        }

        // Update the total for the subscription
        $subscription->update(['total' => $totalSubscriptionAmount]);

        return redirect()->route('subscriptions.index')->with('success', 'Subscription created successfully');
    }

    // Show the form to edit an existing subscription
    public function edit($id)
    {
        $subscription = Subscription::with('items')->findOrFail($id);
        $teamMemberId = getUserID();
        $clients = Client::where('added_by', $teamMemberId)->get();
        $services = Service::where('user_id', $teamMemberId)->get();

        return view('client.pages.subscriptions.edit', compact('subscription', 'clients', 'services'));
    }

    // Update an existing subscription
    public function update(Request $request, $id)
    {
        // Validate the request
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
            'upfront_payment_amount' => 'nullable|numeric|min:0', // Add validation for upfront payment
        ]);

        // Handle checkbox values
        $sendEmail = $request->has('send_email') ? 1 : 0;
        $partialPayment = $request->has('partial_payment') ? 1 : 0;

        // Fetch the subscription
        $subscription = Subscription::findOrFail($id);

        // Update the subscription details
        $subscription->update([
            'client_id' => $request->client_id,
            'note' => $request->note,
            'send_email' => $sendEmail,
            'partial_payment' => $partialPayment, // Save partial_payment as a flag (1 or 0)
            'upfront_payment_amount' => $partialPayment ? $request->input('upfront_payment_amount') : null, // Save the upfront amount only if partial_payment is checked
            'billing_date' => $request->billing_date,
            'currency' => $request->currency,
            'due_date' => $request->due_date,
        ]);

        // Delete existing subscription items
        SubscriptionItem::where('subscription_id', $id)->delete();

        $totalSubscriptionAmount = 0;

        // Process and save subscription items
        foreach ($request->item_names as $index => $itemName) {
            $price = $request->prices[$index];
            $quantity = $request->quantities[$index];
            $discount = $request->discounts[$index] ?? 0;
            $itemTotal = ($price * $quantity) - $discount;

            // Create new subscription items
            SubscriptionItem::create([
                'subscription_id' => $subscription->id,
                'service_id' => $request->services[$index] ?? null,
                'item_name' => $itemName,
                'description' => $request->descriptions[$index] ?? null,
                'price' => $price,
                'quantity' => $quantity,
                'discount' => $discount
            ]);

            $totalSubscriptionAmount += $itemTotal;
        }

        // Update the total amount for the subscription
        $subscription->update(['total' => $totalSubscriptionAmount]);

        return redirect()->route('subscriptions.list')->with('success', 'Subscription updated successfully');
    }


    // Delete a subscription
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        $subscription->delete();

        return redirect()->route('subscriptions.index')->with('success', 'Subscription deleted successfully');
    }
}
