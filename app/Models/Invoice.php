<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
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
        'upfront_payment_amount',
        'status',
        'public_key',
        'billing_first_name',  // New billing first name field
        'billing_last_name',   // New billing last name field
        'billing_address',     // New billing address field
        'billing_city',        // New billing city field
        'billing_country',     // New billing country field
        'billing_state',       // New billing state field
        'billing_postal_code', // New billing postal/zip code field
        'billing_company',     // New billing company field
        'billing_tax_id',       // New billing tax ID field
        'paid_at',
        'payment_method',
        'paypal_product_id',
        'order_id',
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
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function refunds()
    {
        return $this->hasMany(InvoiceRefund::class, 'invoice_id');
    }

    protected $casts = [
        'paid_at' => 'datetime',
    ];
}
