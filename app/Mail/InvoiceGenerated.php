<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceGenerated extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $client;

    /**
     * Create a new message instance.
     */
    public function __construct($invoice, $client)
    {
        $this->invoice = $invoice;
        $this->client = $client;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Invoice Generated: ' . $this->invoice->invoice_no)
                    ->view('emails.invoice_generated');
    }
}