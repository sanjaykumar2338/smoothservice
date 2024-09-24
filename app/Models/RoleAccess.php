<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleAccess extends Model
{
    protected $fillable = [
        'role_id', 'access_name', 'can_view', 'can_add', 'can_edit', 'can_delete'
    ];

    // Define the relationship back to the Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
