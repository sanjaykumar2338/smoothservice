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

class BillingController extends Controller
{
    public function index()
    {
        // Determine the mode (sandbox or live)
        $stripeMode = env('STRIPE_MODE', 'sandbox');

        // Define the field based on the mode
        $stripePlanField = $stripeMode === 'sandbox' ? 'stripe_plan_id_test' : 'stripe_plan_id_live';

        // Fetch plans where the relevant plan ID is not null or empty
        $plans = StripePlan::select(
            'id',
            'name',
            'description',
            'price',
            'currency',
            'billing_interval',
            'team_members',
            'additional_cost_per_member',
            "$stripePlanField as stripe_plan_id"
        )
        ->whereNotNull($stripePlanField)
        ->where($stripePlanField, '!=', '')
        ->get();

        // Prepare the plan data for the front-end
        $planData = $plans->map(function ($plan) {
            return [
                'name' => $plan->name,
                'monthlyPrice' => number_format($plan->price / 12, 2),
                'yearlyPrice' => number_format($plan->price, 2),
                'description' => $plan->description,
            ];
        });

        //echo "<pre>"; 
        //print_r($plans->toArray());
        //die;

        $user = auth()->user();
        $activeSubscription = $user->subscriptions()
            ->where('stripe_status', 'active')
            ->first();

        return view('client.pages.billing.plan', [
            'plans' => $plans,
            'planData' => $planData,
            'activeSubscription' => $activeSubscription
        ]);
    }

    public function payment(Request $request)
    {
        // Fetch the plan based on the plan ID from the request
        $plan = StripePlan::findOrFail($request->query('plan_id'));
        $user = auth()->user();

        $activeSubscription = $user->subscriptions()
            ->where('stripe_status', 'active')
            ->first();

        if ($activeSubscription) {
            return redirect()->back()->with('error', 'You already have an active subscription.');
        }

        // Initialize Stripe with your secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create a SetupIntent for the current user
        $setupIntent = SetupIntent::create([
            'customer' => auth()->user()->stripe_id, // Use the user's Stripe customer ID
        ]);

        //echo $setupIntent->client_secret; die;
        // Pass the client secret to the frontend
        return view('client.pages.billing.payment', [
            'plan' => $plan,
            'clientSecret' => $setupIntent->client_secret, // Pass the client_secret here
        ]);
    }

    public function process(Request $request)
    {
        // Validate the request
        $request->validate([
            'plan_id' => 'required|exists:stripe_plans,id',
            'payment_method' => 'required',
        ]);

        try {
            $user = auth()->user();

            // Fetch the Stripe plan
            $plan = StripePlan::findOrFail($request->input('plan_id'));

            $stripeMode = env('STRIPE_MODE', 'sandbox');
            $stripePlanId = $stripeMode === 'sandbox' ? $plan->stripe_plan_id_test : $plan->stripe_plan_id_live;

            if (empty($stripePlanId)) {
                return redirect()->back()->with('error', 'The selected plan is not configured for the current environment.');
            }

            $activeSubscription = $user->subscriptions()
                ->where('stripe_plan_id', $stripePlanId)
                ->where('stripe_status', 'active')
                ->first();

            if ($activeSubscription) {
                return redirect()->back()->with('error', 'You already have an active subscription for this plan.');
            }

            // Stripe Client
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            // Create customer if not already existing
            if (!$user->stripe_id) {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->name,
                ]);
                $user->stripe_id = $customer->id;
                $user->save();
            }

            // Attach the payment method
            $paymentMethod = $request->input('payment_method');
            $stripe->paymentMethods->attach($paymentMethod, [
                'customer' => $user->stripe_id,
            ]);

            // Set as default payment method
            $stripe->customers->update($user->stripe_id, [
                'invoice_settings' => ['default_payment_method' => $paymentMethod],
            ]);

            // Retrieve the payment method details
            $paymentDetails = $stripe->paymentMethods->retrieve($paymentMethod);

            // Save payment method details in the database
            $user->update([
                'card_brand' => $paymentDetails->card->brand,
                'card_last_four' => $paymentDetails->card->last4,
                'card_exp_month' => $paymentDetails->card->exp_month,
                'card_exp_year' => $paymentDetails->card->exp_year,
            ]);

            // Create the Stripe subscription
            $subscription = $stripe->subscriptions->create([
                'customer' => $user->stripe_id,
                'items' => [
                    ['price' => $stripePlanId],
                ],
                'expand' => ['latest_invoice.payment_intent'],
            ]);

            // Save subscription details
            StripeSubscription::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'user_id' => $user->id,
                'name' => $plan->name,
                'stripe_id' => $subscription->id,
                'stripe_status' => $subscription->status,
                'stripe_price' => $plan->price,
                'quantity' => 1,
                'plan_id' => $plan->id,
                'stripe_plan_id' => $stripePlanId,
                'trial_ends_at' => $subscription->trial_end ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end) : null,
                'ends_at' => $subscription->current_period_end ? \Carbon\Carbon::createFromTimestamp($subscription->current_period_end) : null,
                'start_at' => $subscription->current_period_start ? \Carbon\Carbon::createFromTimestamp($subscription->current_period_start) : null,
                'subscription_data' => json_encode($subscription),
                'duration' => $plan->billing_interval
            ]);

            return redirect()->route('billing')->with('success', 'Subscription successful!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to process subscription: ' . $e->getMessage());
        }
    }

    public function cancelSubscription($id)
    {
        try {
            $user = auth()->user();
            $subscription = $user->subscriptions()->where('id', $id)->first();

            if (!$subscription) {
                return redirect()->back()->with('error', 'Subscription not found.');
            }

            // Cancel the subscription on Stripe
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $stripe->subscriptions->cancel($subscription->stripe_id);

            // Update the subscription status in the database
            $subscription->update([
                'stripe_status' => 'canceled',
                'ends_at' => now(),
            ]);

            return redirect()->route('billing')->with('success', 'Subscription canceled successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to cancel subscription: ' . $e->getMessage());
        }
    }
}
