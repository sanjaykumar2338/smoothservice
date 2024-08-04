<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTeamMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'team_member_id',
    ];

    public $timestamps = false; // Disable timestamps if not needed
}
