<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_name',
        'description',
        'addon',
        'group_multiple',
        'assign_team_member',
        'set_deadline_check',
        'set_a_deadline',
        'set_a_deadline_duration',
        'user_id',
    ];

    public function parentServices()
    {
        return $this->belongsToMany(Service::class, 'service_parent_services', 'service_id', 'parent_service_id');
    }

    public function teamMembers()
    {
        return $this->belongsToMany(User::class, 'service_team_members', 'service_id', 'team_member_id');
    }
}
