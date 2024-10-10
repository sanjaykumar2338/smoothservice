<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTeam extends Model
{
    use HasFactory;

    protected $table = 'ticket_team_member';

    // Mass assignable attributes
    protected $fillable = [
        'ticket_id',
        'team_member_id'
    ];
}