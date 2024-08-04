<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceParentService extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'parent_service_id',
    ];

    public $timestamps = false; // Disable timestamps if not needed
}
