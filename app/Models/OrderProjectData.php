<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProjectData extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'field_name', 'field_type', 'field_value'];
}
