<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoicePaidConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $client;

    public function __construct($invoice, $client)
    {
        $this->invoice = $invoice;
        $this->client = $client;
    }

    public function build()
    {
        return $this->subject('Invoice Payment Confirmation')
            ->view('emails.invoice_paid_confirmation')
            ->with([
                'invoice' => $this->invoice,
                'client' => $this->client,
            ]);
    }
}