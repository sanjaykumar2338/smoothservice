<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCollaborator extends Model
{
    protected $table = 'ticket_collaborators';

    protected $fillable = [
        'ticket_id',
        'user_id',
    ];

    // Relationship with Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relationship with User
    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class, 'user_id');
    }
}
