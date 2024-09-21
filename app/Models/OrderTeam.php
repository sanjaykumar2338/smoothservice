<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTeam extends Model
{
    use HasFactory;

    protected $table = 'order_team_member';

    // Mass assignable attributes
    protected $fillable = [
        'order_id',
        'team_member_id'
    ];
}