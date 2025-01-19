<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Payment Confirmation</title>
</head>
<body>
    <h1>Invoice Generated</h1>
    <p>Dear {{ $client->first_name }},</p>
    <p>Your invoice #{{ $invoice->invoice_no }} has been successfully created.</p>
    <p>Total Amount: ${{ number_format($invoice->total, 2) }}</p>
    <p>Please log in to your account on 
        <strong>{{ $companyName }}</strong> 
        to review the invoice and proceed with payment.
    </p>
    <p>Thank you for your business!</p>
</body>
</html>
