<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $fillable = ['name', 'color', 'description', 'lock_completed_orders', 'change_status_on_revision', 'enable_ratings'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'status_id');
    }
}
