<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceRefund extends Model
{
    protected $fillable = ['invoice_id', 'refund_reason', 'refund_amount'];

    /**
     * Define the inverse relationship to Invoice.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
