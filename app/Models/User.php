<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use Billable; // For Stripe and Laravel Cashier integration

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number', // Additional column (if needed)
        'stripe_id', // Stripe customer ID
        'pm_type', // Payment method type (e.g., card)
        'pm_last_four', // Last 4 digits of the payment method
        'trial_ends_at', // Trial period end date
        'card_brand',
        'card_last_four',
        'card_exp_month',
        'card_exp_year',
        'stripe_connect_account_id',
        'paypal_connect_account_id',
        'workspace',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Retrieve the current subscription for the user.
     *
     * @return \Laravel\Cashier\Subscription|null
     */
    public function currentSubscription()
    {
        return $this->subscription('default'); // Assumes 'default' subscription
    }

    /**
     * Determine if the user is on a specific plan.
     *
     * @param string $planId
     * @return bool
     */
    public function isOnPlan($planId)
    {
        $subscription = $this->currentSubscription();
        return $subscription && $subscription->stripe_price === $planId;
    }

    public function subscriptions()
    {
        return $this->hasMany(StripeSubscription::class);
    }
}
