<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    // Specify the fillable fields
    protected $fillable = ['name'];

    // You can also define relationships or other model-specific logic here if necessary.
}
