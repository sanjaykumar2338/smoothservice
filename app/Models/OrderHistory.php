<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderHistory extends Model
{
    use HasFactory;
    protected $table = 'order_history'; // Explicitly define the correct table name

    protected $fillable = ['order_id', 'action'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
