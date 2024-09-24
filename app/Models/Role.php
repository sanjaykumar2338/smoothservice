<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relationship with TeamMember (a role can have many team members)
    public function teamMembers()
    {
        return $this->hasMany(TeamMember::class);
    }

    // Relationship with RoleAccess (a role can have many access permissions)
    public function access()
    {
        return $this->hasMany(RoleAccess::class);
    }

    // Many-to-Many relationship with Permission
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permission', 'role_id', 'permission_id');
    }
}

