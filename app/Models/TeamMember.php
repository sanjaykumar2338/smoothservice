<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;

    // Define the table associated with the model (optional, if the table name follows Laravel conventions)
    protected $table = 'team_members';

    // The attributes that are mass assignable
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'role_id', // Assuming role_id links to the roles table
        'password',
        'added_by'
    ];

    /**
     * Define relationship with the Role model
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
