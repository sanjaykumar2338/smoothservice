<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Payment Confirmation</title>
</head>
<body>
    <h1>Invoice Payment Confirmation</h1>
    <p>Dear {{ $client->first_name }},</p>
    <p>Your payment for Invoice #{{ $invoice->invoice_no }} has been successfully processed.</p>
    <p>Amount: ${{ number_format($invoice->total, 2) }}</p>
    <p>Thank you for your payment!</p>
</body>
</html>
