<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    use HasFactory;

    protected $table = 'landing_pages';

    protected $fillable = [
        'title',
        'description',
        'slug',
        'is_visible',
        'status',
        'show_in_sidebar',
        'show_coupon_field',
        'fields',
        'image',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'html',
        'css',
        'json_data',
        'is_published',
        'landing_no'
    ];

    protected $casts = [
        'is_visible' => 'boolean',
        'status' => 'boolean',
        'show_in_sidebar' => 'boolean',
        'show_coupon_field' => 'boolean',
    ];
}
