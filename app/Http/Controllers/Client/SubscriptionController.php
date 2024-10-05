<?php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\Service;
use App\Models\User;
use App\Models\TeamMember;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubscriptionMail;

class SubscriptionController extends Controller
{
    // List all subscriptions
    public function index(Request $request)
    {
        $search = $request->input('search');

        $subscriptions = Subscription::with('client', 'service')
            ->when($search, function ($query, $search) {
                return $query->whereHas('client', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orwhere('last_name', 'like', "%{$search}%");
                })->orWhereHas('service', function ($q) use ($search) {
                    $q->where('service_name', 'like', "%{$search}%");
                });
            })
            ->where('added_by', getUserID())->paginate(10);

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
        $validatedData = $request->validate([
            'coupon_code' => 'required|string|max:50|unique:coupons',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:Fixed,Percentage',
            'discount_duration' => 'required|in:Forever,First Payment',
            'applies_to' => 'required|array',
            'discount' => 'required|array',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'min_cart_amount_value' => 'nullable|numeric|min:0',
        ]);

        // Handle checkbox values
        $limitToOne = $request->has('limit_to_one') ? 1 : 0;
        $limitToNewCustomers = $request->has('limit_to_new_customers') ? 1 : 0;
        $setExpiry = $request->has('set_expiry') ? 1 : 0;
        $minCartAmount = $request->has('min_cart_amount') ? 1 : 0;

        // Create the coupon
        $coupon = Coupon::create([
            'coupon_code' => $validatedData['coupon_code'],
            'description' => $validatedData['description'],
            'discount_type' => $validatedData['discount_type'],
            'discount_duration' => $validatedData['discount_duration'],
            'limit_to_one' => $limitToOne,
            'limit_to_new_customers' => $limitToNewCustomers,
            'set_expiry' => $setExpiry,
            'expiry_date' => $validatedData['expiry_date'] ?? null,
            'min_cart_amount' => $minCartAmount ? $validatedData['min_cart_amount_value'] : null,
            'added_by' => auth()->id(),
        ]);

        // Save coupon services
        foreach ($validatedData['applies_to'] as $index => $serviceId) {
            CouponService::create([
                'coupon_id' => $coupon->id,
                'service_id' => $serviceId,
                'discount' => $validatedData['discount'][$index] ?? 0,
            ]);
        }

        return redirect()->route('coupon.list')->with('success', 'Coupon added successfully.');
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
        //echo "<pre>"; print_r($request->all()); die;
        // Custom validation logic to ensure either 'service_id' or 'item_name' is provided for each item
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'item_names' => 'required|array',
            'item_names.*' => [
                function ($attribute, $value, $fail) use ($request) {
                    $index = explode('.', $attribute)[1]; // Get the index (e.g., item_names.0 -> 0)
                    if (empty($value) && empty($request->services[$index])) {
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

        // Handle checkbox values
        $sendEmail = $request->has('send_email') ? 1 : 0;
        $partialPayment = $request->has('partial_payment') ? 1 : 0;
        $upfrontPaymentAmount = $partialPayment ? $request->input('upfront_payment_amount') : null;

        // Fetch the subscription
        $subscription = Subscription::findOrFail($id);

        // Update the subscription details
        $subscription->update([
            'client_id' => $request->client_id,
            'note' => $request->note,
            'send_email' => $sendEmail,
            'partial_payment' => $partialPayment, // Save as flag (1 or 0)
            'upfront_payment_amount' => $upfrontPaymentAmount,
            'billing_date' => $request->billing_date,
            'currency' => $request->currency ?? 'USD', // Default to USD
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
            $totalSubscriptionAmount += $itemTotal;

            // Create new subscription items
            SubscriptionItem::create([
                'subscription_id' => $subscription->id,
                'service_id' => $request->services[$index] ?? null,  // Use the correct service array
                'item_name' => $itemName ?: null,  // Use the item name if provided
                'description' => $request->descriptions[$index] ?? null,
                'price' => $price,
                'quantity' => $quantity,
                'discount' => $discount
            ]);
        }


        // Update the total amount for the subscription
        $subscription->update(['total' => $totalSubscriptionAmount]);

        return redirect()->route('subscriptions.list')->with('success', 'Subscription updated successfully');
    }


    // Delete a subscription
    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);
        //echo "<pre>"; print_r($subscription); die;
        $subscription->delete();

        return redirect()->route('subscriptions.list')->with('success', 'Subscription deleted successfully');
    }

    public function show($id)
    {
        // Fetch all users and team members added by the current logged-in user
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', auth()->id())->get();

        $subscription = Subscription::with('client', 'service', 'items')->findOrFail($id);
        return view('client.pages.subscriptions.show', compact('subscription','users', 'teamMembers'));
    }

    public function downloadSubscription($id)
    {
        // Retrieve the subscription and related data
        $subscription = Subscription::with('client', 'items')->findOrFail($id);

        // Generate the PDF using the view
        $pdf = PDF::loadView('client.pages.subscriptions.pdf', compact('subscription'));

        // Download the PDF
        return $pdf->download('subscription_' . $subscription->id . '.pdf');
    }

    public function duplicate($id)
    {
        // Fetch the original subscription with related items
        $subscription = Subscription::with('items')->findOrFail($id);

        // Create a new subscription by duplicating the original subscription's data
        $newSubscription = $subscription->replicate(); // Duplicate the main subscription fields
        $newSubscription->status = 'Draft'; // Optionally set a default status
        $newSubscription->public_key = \Str::random(32);
        $newSubscription->created_at = now(); // Set the creation date to now
        $newSubscription->updated_at = now();
        $newSubscription->save(); // Save the new subscription

        // Duplicate each item in the subscription
        foreach ($subscription->items as $item) {
            $newItem = $item->replicate(); // Duplicate the item
            $newItem->subscription_id = $newSubscription->id; // Link the new item to the new subscription
            $newItem->save(); // Save the new item
        }

        return redirect()->route('subscriptions.edit', $newSubscription->id)->with('success', 'Subscription duplicated successfully');
    }

    public function publicShow($id, Request $request)
    {
        $subscription = Subscription::findOrFail($id);

        // Validate the key
        if ($request->input('key') !== $subscription->public_key) {
            abort(403, 'Unauthorized access.');
        }

        return view('client.pages.subscriptions.show', compact('subscription'));
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

        $subscription = Subscription::findOrFail($id);
        $subscription->update($request->only([
            'billing_first_name', 'billing_last_name', 'billing_address',
            'billing_city', 'billing_country', 'billing_state',
            'billing_postal_code', 'billing_company', 'billing_tax_id'
        ]));

        return redirect()->back()->with('success', 'Billing details updated successfully.');
    }

    public function sendEmail(Request $request)
    {
        $emails = $request->input('emails');  // Array of emails
        $subscription = Subscription::findOrFail($request->input('subscriptionId'));

        foreach ($emails as $email) {
            Mail::to($email)->send(new SubscriptionMail($subscription));
        }

        return response()->json(['success' => true]);
    }

    public function refund(Request $request, Subscription $subscription)
    {
        $request->validate([
            'refund_reason' => 'required|string|max:255',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        // Store refund details in the 'subscription_refunds' table
        $subscription->refunds()->create([
            'refund_reason' => $request->refund_reason,
            'refund_amount' => $request->refund_amount,
        ]);

        // Update the subscription status if needed (e.g., partial refund, full refund)
        // $subscription->status = 'refunded'; // Update status logic if needed
        // $subscription->save();

        return redirect()->back()->with('success', 'Refund has been added successfully.');
    }

}
