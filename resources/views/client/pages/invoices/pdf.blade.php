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

    <table class="table table-borderless">
                <thead>
                    <tr>
                        <th class="text-start">Item</th>
                        <th class="text-start">Price</th>
                        <th class="text-start">Quantity</th>
                        <th class="text-end">Item Total ({{ $invoice->currency }})</th>
                        <th class="text-end">Item Total (CAD)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <!-- Item Name -->
                        <td class="text-start">
                            {{ $item->service->service_name ?? $item->item_name }}<br>
                            @if(!empty($item->service->trial_for))
                                <span class="form-label">
                                    @php $service = $item->service @endphp
                                    ${{$service->trial_price - $item->discount}} for {{$service->trial_for}} {{ $service->trial_for > 1 ? $service->trial_period . 's' : $service->trial_period }}, then
                                    ${{ $item->service->recurring_service_currency_value - $item->discountsnextpayment}}/{{ $service->recurring_service_currency_value_two }} 
                                    {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}
                                </span>
                            @endif
                        </td>

                        <!-- Price -->
                        <td class="text-start">{{ $invoice->currency }} {{ number_format($item->price, 2) }}</td>

                        <!-- Quantity -->
                        <td class="text-start">× {{ $item->quantity }}</td>

                        <!-- Item Total -->
                        <td class="text-end">
                            @if($item->service->service_type!="onetime")
                                {{ $invoice->currency }} {{ number_format($item->price * $item->quantity - $item->discount, 2) }}
                            @else
                                {{ $invoice->currency }} {{ number_format($item->price * $item->quantity - $item->discount, 2) }}
                            @endif
                        </td>

                        <!-- Item Total in CAD -->
                        <td class="text-end">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </td>
                    </tr>
                    @endforeach

                    <!-- Upfront Payment Row -->
                    @if($invoice->upfront_payment_amount > 0)
                    <tr>
                        <td class="text-start"><strong>Upfront Payment</strong></td>
                        <td class="text-start">
                            -{{ $invoice->currency }} {{ number_format($invoice->upfront_payment_amount, 2) }}
                        </td>
                        <td class="text-start">×1</td>
                        <td class="text-end"> -{{ $invoice->currency }} {{ number_format($invoice->upfront_payment_amount, 2) }}</td>
                        <td class="text-end text-danger">
                            -${{ number_format($invoice->upfront_payment_amount, 2) }}
                        </td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <!-- Subtotal -->
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-end"><strong>Subtotal</strong></td>
                        <td class="text-end">
                            {{ $invoice->currency }} 
                            {{ number_format($invoice->total - $invoice->upfront_payment_amount, 2) }}
                        </td>
                        <td class="text-end">
                            ${{ number_format($invoice->total - $invoice->upfront_payment_amount, 2) }}
                        </td>
                    </tr>

                    <!-- Payment Due -->
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-end"><strong>Payment Due</strong></td>
                        <td class="text-end">
                            <strong>{{ $invoice->currency }} {{ number_format($invoice->total - $invoice->upfront_payment_amount, 2) }}</strong>
                        </td>
                        <td class="text-end">
                            <strong>CAD ${{ number_format($invoice->total - $invoice->upfront_payment_amount, 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
</body>
</html>
