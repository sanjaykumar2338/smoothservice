<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'billing_address',
        'country',
        'state',
        'postal_code',
        'company',
        'tax_id',
        'phone',
        'password',
        'added_by',
        'stripe_customer_id',
        'account_balance',
        'status',
        'single_line_of_text',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function client_status()
    {
        return $this->belongsTo(ClientStatus::class, 'status');
    }
}
