<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invoice;  // Use the correct namespace for the Invoice model

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        return $this->view('emails.invoice')
                    ->subject('Your Invoice from ' . env('APP_NAME'))
                    ->with(['invoice' => $this->invoice]);
    }
}

