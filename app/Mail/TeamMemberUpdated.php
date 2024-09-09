<?php

namespace App\Mail;

use App\Models\TeamMember;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamMemberUpdated extends Mailable
{
    use Queueable, SerializesModels;
    public $teamMember;

    /**
     * Create a new message instance.
     */
    public function __construct(TeamMember $teamMember)
    {
        $this->teamMember = $teamMember;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.team_member_updated')
                    ->with(['teamMember' => $this->teamMember])
                    ->subject('Team Member Updated');
    }
}
