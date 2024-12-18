<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoicePaid extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $client;
    public $addedByUser;

    public function __construct($invoice, $client, $addedByUser)
    {
        $this->invoice = $invoice;
        $this->client = $client;
        $this->addedByUser = $addedByUser;
    }

    public function build()
    {
        return $this->subject('Invoice Paid Notification')
            ->view('emails.invoice_paid')
            ->with([
                'invoice' => $this->invoice,
                'client' => $this->client,
                'addedByUser' => $this->addedByUser,
            ]);
    }
}