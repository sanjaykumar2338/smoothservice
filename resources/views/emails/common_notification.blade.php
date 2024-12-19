<!DOCTYPE html>
<html>
<head>
    <title>{{ $details['subject'] }}</title>
</head>
<body>
    <h1>{{ $details['subject'] }}</h1>
    <p>{{ $details['message'] }}</p>

    <h3>Subscription Details:</h3>
    <ul>
        <li>Invoice No: {{ $details['invoice']->invoice_no }}</li>
        <li>Client: {{ $details['client']->first_name }} {{ $details['client']->last_name }}</li>
        <li>Amount: ${{ number_format($details['subscription']->amount, 2) }}</li>
        <li>Interval: {{ ucfirst($details['subscription']->intervel) }}</li>
        <li>Start Date: {{ \Carbon\Carbon::parse($details['subscription']->starts_at)->format('M d, Y') }}</li>
        <li>End Date: {{ $details['subscription']->ends_at ? \Carbon\Carbon::parse($details['subscription']->ends_at)->format('M d, Y') : 'Ongoing' }}</li>
    </ul>
</body>
</html>
