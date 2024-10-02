<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service_id',
        'note',  // Store client note
        'send_email',  // Send email notification
        'partial_payment',  // Store partial payment (upfront payment amount)
        'billing_date',  // Store custom billing date
        'currency',  // Store custom currency
        'total',  // Total invoice amount
        'due_date',  // Invoice due date
        'added_by',  // ID of the user who added the invoice
        'upfront_payment_amount'
    ];

    // Relationship with Client model
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relationship with Service model
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Relationship with InvoiceItem model (one invoice has many items)
    public function items()
    {
        return $this->hasMany(SubscriptionItem::class, 'subscription_id');
    }
}
