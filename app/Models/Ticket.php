<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_no',
        'client_id',
        'subject',
        'order_id',
        'message',
        'user_id',
        'status_id',
        'note',
        'created_by',
        'updated_by',
    ];

    // Relationship with client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tags()
    {
        return $this->belongsToMany(TicketTag::class, 'ticket_tag', 'ticket_id', 'tag_id'); // Adjust pivot table and foreign keys accordingly
    }

    public function teamMembers()
    {
        return $this->belongsToMany(TeamMember::class, 'ticket_team_member');
    }

    // Relationship with related order
    public function Order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Many-to-many relationship for CC (carbon copy) users
    public function ccUsers()
    {
        return $this->belongsToMany(TeamMember::class, 'ticket_collaborators', 'ticket_id', 'user_id');
    }

    public function status_order()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function metadata()
    {
        return $this->hasMany(TicketMetadata::class, 'ticket_id');
    }
}
