<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #dddddd;
            padding: 20px;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #f8f9fa;
        }
        .header img {
            width: 120px;
        }
        .email-content {
            padding: 20px;
        }
        .email-content h1 {
            font-size: 20px;
            color: #333;
        }
        .email-content p {
            font-size: 16px;
            color: #555;
        }
        .receipt {
            width: 100%;
            margin: 20px 0;
        }
        .receipt th, .receipt td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #dddddd;
        }
        .receipt th {
            background-color: #f8f9fa;
        }
        .total {
            font-weight: bold;
        }
        .footer {
            text-align: center;
            padding: 20px 0;
        }
        .footer a {
            background-color: #d9534f;
            color: #ffffff;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
        }
        .footer a:hover {
            background-color: #c9302c;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <img src="{{ asset('assets/img/your-logo.png') }}" alt="{{ env('APP_NAME') }}">
        </div>

        <!-- Email Content -->
        <div class="email-content">
            <h1>Order confirmation (#{{ $invoice->id }})</h1>
            <p>Hey {{ $invoice->client->first_name }},</p>
            <p>Thank you for your business. Below are the details of your order.</p>

            <!-- Receipt Table -->
            <table class="receipt" width="100%">
                <tr>
                    <th>Item</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                </tr>
                @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="3" class="total">Total</td>
                    <td class="total">${{ number_format($invoice->total, 2) }}</td>
                </tr>
            </table>

            <!-- Billing Details -->
            <h2>Billing Details</h2>
            <p>{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</p>
            <p>{{ $invoice->billing_address }}</p>
            <p>{{ $invoice->billing_city }}, {{ $invoice->billing_state }} {{ $invoice->billing_postal_code }}</p>
            <p>{{ $invoice->billing_country }}</p>
        </div>

        <!-- Footer with Button -->
        <div class="footer">
            <a href="{{ route('invoices.show', $invoice->id) }}" target="_blank">View in your account</a>
        </div>
    </div>
</body>
</html>
