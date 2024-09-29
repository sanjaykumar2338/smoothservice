<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
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
    ];

    protected $hidden = [
        'password',
    ];
}
