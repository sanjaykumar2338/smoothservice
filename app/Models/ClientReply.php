<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Client; // Import the Client model
use App\Models\Order;

class ClientReply extends Model
{
    use HasFactory;

    protected $table = 'client_replies';

    // Mass assignable attributes
    protected $fillable = [
        'order_id',
        'client_id',
        'message',
        'scheduled_at',       // Corrected field name
        'cancel_on_reply',    // Corrected field name
        'sender_id',          // Add sender_id for polymorphic relationship
        'sender_type',        // Add sender_type for polymorphic relationship
        'message_type',
    ];

    // Define the relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function sender()
    {
        return $this->morphTo();
    }
}
