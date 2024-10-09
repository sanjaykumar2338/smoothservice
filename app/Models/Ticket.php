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
        'created_by',
        'updated_by',
    ];

    // Relationship with client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relationship with related order
    public function Order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Many-to-many relationship for CC (carbon copy) users
    public function ccUsers()
    {
        return $this->belongsToMany(User::class, 'ticket_user');
    }

    public function status_order()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }
}
