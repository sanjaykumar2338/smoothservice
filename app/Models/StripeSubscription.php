<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeSubscription extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stripe_subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',           // Foreign key to the users table
        'stripe_id',         // Stripe subscription ID
        'stripe_status',     // Subscription status (e.g., active, canceled)
        'stripe_plan',       // Stripe plan ID
        'quantity',          // Quantity of the subscription
        'trial_ends_at',     // Trial end date
        'ends_at',           // Subscription end date
        'start_at',
        'plan_id',
        'stripe_plan_id',
        'name',
        'stripe_price',
        'subscription_data',
        'duration'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    /**
     * Get the user that owns the subscription.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if the subscription is active.
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->stripe_status === 'active';
    }

    /**
     * Check if the subscription is on trial.
     *
     * @return bool
     */
    public function isOnTrial()
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }

    /**
     * Check if the subscription is canceled.
     *
     * @return bool
     */
    public function isCanceled()
    {
        return $this->ends_at && now()->gte($this->ends_at);
    }
}
