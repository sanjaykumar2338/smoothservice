<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripePaymentMethod extends Model
{
    protected $table = 'stripe_payment_methods';
}
