<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Paid Notification</title>
</head>
<body>
    <h1>Invoice #{{ $invoice->id }} Paid</h1>
    <p>Dear {{ $addedByUser->name }},</p>
    <p>The invoice created for {{ $client->first_name }} {{ $client->last_name }} has been paid.</p>
    <p>Amount: ${{ number_format($invoice->total, 2) }}</p>
    <p>Thank you for your business!</p>
</body>
</html>
