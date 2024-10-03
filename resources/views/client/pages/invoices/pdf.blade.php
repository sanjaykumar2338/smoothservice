<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
        }
        .invoice-header, .invoice-info {
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .text-end {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h4>Invoice #{{ $invoice->id }}</h4>
        <p><strong>Status:</strong> {{ $invoice->status }}</p>
    </div>

    <div class="invoice-info">
        <div>
            <h6>{{ env('APP_NAME') }}</h6>
            <p>4014 Kennedy Close SW<br>Edmonton, AB T6W 3B1<br>Canada</p>
        </div>
        <div>
            <h6>Invoiced To:</h6>
            <p>{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</p>
        </div>
        <p><strong>Issued:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
        <p><strong>Paid:</strong> {{ $invoice->paid_at ? $invoice->paid_at->format('M d, Y') : 'Unpaid' }}</p>
        <p><strong>Payment Method:</strong> {{ $invoice->payment_method ?? 'N/A' }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->item_name }}</td>
                <td>${{ number_format($item->price, 2) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                <td>${{ number_format($invoice->total, 2) }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                <td><strong>${{ number_format($invoice->total, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
