<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'order_no', 'service_id', 'client_id', 'note', 'status', 'user_id', 'tags', 'notification', 'date_added', 'date_due', 'date_started', 'date_completed', 'amount'];

    protected $casts = [
        'date_added' => 'datetime',
        'date_started' => 'datetime',
        'date_due' => 'datetime',
        'date_completed' => 'datetime',
    ];

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

    /**
     * Delete an order and optionally related data (e.g., tasks, team members, etc.)
     */
    public function deleteOrder()
    {
        DB::transaction(function () {
            // Optionally delete related data (e.g., tasks, team members, history)
            $this->tasks()->delete(); // Delete related tasks
            $this->teamMembers()->detach(); // Detach team members
            $this->history()->delete(); // Delete related history
            $this->tags()->detach(); // Detach tags

            // Delete the order itself
            $this->delete();
        });
    }

    /**
     * Duplicate the current order along with its related data (optional)
     *
     * @return Order The duplicated order instance
     */
    public function duplicateOrder()
    {
        // Clone the current order without duplicating the primary key or timestamps
        $newOrder = $this->replicate(['created_at', 'updated_at']);
        $newOrder->title = $newOrder->title.' Copy';
        $newOrder->order_no = $this->generateNewOrderNumber(); // Generate a new order number or ID
        $newOrder->save();

        // Ensure the relationships are loaded
        $this->loadMissing('teamMembers', 'tags', 'tasks');

        // Optionally duplicate related data like tasks, team members, or tags
        foreach ($this->tasks as $task) {
            $newOrder->tasks()->create($task->toArray()); // Duplicate tasks
        }

        // Duplicate team members if any
        if ($this->teamMembers) {
            $newOrder->teamMembers()->attach($this->teamMembers->pluck('id')->toArray()); // Duplicate team members
        }

        // Duplicate tags if any
        if ($this->tags) {
            $newOrder->tags()->attach($this->tags->pluck('id')->toArray()); // Duplicate tags
        }

        return $newOrder; // Return the duplicated order
    }

    /**
     * Generate a new order number for the duplicated order (adjust as needed)
     */
    protected function generateNewOrderNumber()
    {
        // Example: Generate a new order number (this can be customized as needed)
        $order_no = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        return $order_no;
    }
}
