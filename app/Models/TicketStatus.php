<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketStatus extends Model
{
    protected $fillable = ['name', 'color', 'description', 'lock_completed_orders', 'change_status_on_revision', 'enable_ratings', 'added_by','status_id','is_default'];

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'status_id');
    }
}
