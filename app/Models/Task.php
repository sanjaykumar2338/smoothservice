<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'name', 'description', 'assigned_to', 'due_date', 'due_from_previous', 'due_period_value', 'due_period_type'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(TeamMember::class, 'assigned_to');
    }
}
