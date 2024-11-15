<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientPasswordChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $password;

    public function __construct($client, $password)
    {
        $this->client = $client;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject('Your Password Has Been Updated')
                    ->view('emails.client_password_changed')
                    ->with([
                        'client' => $this->client,
                        'password' => $this->password,
                    ]);
    }
}
