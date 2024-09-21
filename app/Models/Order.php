<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_no', 'service_id', 'client_id', 'note', 'status', 'user_id', 'tags', 'notification'];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function teamMembers()
    {
        return $this->belongsToMany(TeamMember::class, 'order_team_member');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function history()
    {
        return $this->hasMany(OrderHistory::class);
    }

    public function status_order()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'order_tag', 'order_id', 'tag_id'); // Adjust pivot table and foreign keys accordingly
    }
}
