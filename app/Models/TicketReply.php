<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use App\Models\Ticket;

class TicketReply extends Model
{
    use HasFactory;

    protected $table = 'ticket_replies';

    // Mass assignable attributes
    protected $fillable = [
        'ticket_id',
        'client_id',
        'message',
        'scheduled_at',       
        'cancel_on_reply',    
        'sender_id',          
        'sender_type',        
        'message_type',
    ];

    // Relationship with Ticket
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    // Relationship with Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Polymorphic relationship for Sender
    public function sender()
    {
        return $this->morphTo();
    }
}
