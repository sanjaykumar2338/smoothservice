<?php
namespace App\Mail;

use App\Models\TeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamMemberWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $teamMember;
    public $roleName;
    public $password;

    public function __construct(TeamMember $teamMember, $roleName, $password)
    {
        $this->teamMember = $teamMember;
        $this->roleName = $roleName;
        $this->password = $password; // Temporary or generated password
    }

    public function build()
    {
        return $this->subject('Welcome to the Team')
                    ->view('emails.team_member_welcome');
    }
}
