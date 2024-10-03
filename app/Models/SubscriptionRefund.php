<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionRefund extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'refund_reason',
        'refund_amount',
        'refunded_at',
    ];

    // Define relationship with Subscription
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }
}
