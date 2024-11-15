<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TeamMember extends Authenticatable // Extending Authenticatable for Laravel Authentication
{
    use HasFactory, Notifiable;

    // Define the table associated with the model (optional)
    protected $table = 'team_members';

    // The attributes that are mass assignable
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'role_id', // Assuming role_id links to the roles table
        'password',
        'added_by',
        'phone_number', // Add this
        'remember_token',
    ];

    // Exclude remember_token and email_verified_at
    protected $hidden = [
        'password', // Keep the password hidden for security
        'remember_token',
    ];

    // Remove casts for email_verified_at if not needed
    protected $casts = [];

    /**
     * Define relationship with the Role model
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_members');
    }

    public function hasPermission($permission)
    {
        $roleAccess = $this->role->roleAccesses; // assuming the relationship is defined
        return $roleAccess->contains('access_name', $permission);
    }
}
