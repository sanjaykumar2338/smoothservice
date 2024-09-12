<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Client; // Correct the namespace for the Client model

class ClientWelcome extends Mailable
{
    use Queueable, SerializesModels;

    public $client;
    public $password;

    public function __construct(Client $client, $password)
    {
        $this->client = $client;
        $this->password = $password;
    }

    public function build()
    {
        return $this->view('emails.client_welcome')
                    ->subject('Welcome to Our Platform')
                    ->with([
                        'client' => $this->client,
                        'password' => $this->password,
                    ]);
    }
}
