<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMetadata extends Model
{
    protected $table = 'ticket_metadata';  // Define the table if not using Laravel's default pluralization

    // Define the inverse relationship with Ticket model
    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }
}
