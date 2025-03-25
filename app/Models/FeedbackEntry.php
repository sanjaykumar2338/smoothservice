<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'landing_page',
        'invoice_id',
        'form_data', // Store all fields in JSON format
        'user_id',
    ];

    protected $casts = [
        'form_data' => 'array', // Ensure Laravel handles it as an array
    ];
}
