<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceSubscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',       // Reference to the invoice
        'subscription_id',  // Stripe subscription ID
        'amount',           // Subscription amount
        'currency',         // Currency of the subscription
        'intervel',         // Subscription interval (e.g., month)
        'starts_at',        // Start date of the subscription
        'ends_at',          // End date of the subscription or trial
        'cancelled_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Define the relationship to the Invoice model
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
