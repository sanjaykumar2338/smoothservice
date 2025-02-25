<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFund extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_payment_intent',
        'amount',
        'currency',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}