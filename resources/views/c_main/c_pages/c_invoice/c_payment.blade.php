@extends('c_main.c_dashboard')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="invoice-container">
        <div class="invoice-header">
            <h4>Pay Invoice: {{ $invoice->invoice_no }}</h4>
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
                    
                    @php 
                        $next_payment_recurring = 0; 
                        $total_discount = 0;
                        $interval_total = [];
                    @endphp

                    @foreach($invoice->items as $item)
                    
                    <tr>
                        <!-- Item Name -->
                        <td class="text-start">
                            {{ $item->service->service_name ?? $item->item_name }}<br>
                            @php $service = $item->service @endphp
                            @if(!empty($item->service->trial_for))
                                <span class="form-label">
                                    ${{$service->trial_price - $item->discount}} for {{$service->trial_for}} {{ $service->trial_for > 1 ? $service->trial_period . 's' : $service->trial_period }}, then
                                    ${{ $item->service->recurring_service_currency_value}}/{{ $service->recurring_service_currency_value_two }} 
                                    {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}
                                </span>

                                @php $next_payment_recurring += ($item->service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment; @endphp

                            @else
                                @if($item->service->service_type=='recurring')
                                    ${{ $item->service->recurring_service_currency_value}}/{{ $item->service->recurring_service_currency_value_two }} 
                                    {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}

                                    @php 
                                        $next_payment_recurring += ($item->service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment; 
                                        $total_discount += $item->discount;
                                        $interval_total[] = $item->service->recurring_service_currency_value_two;
                                    @endphp
                                @endif
                                
                            @endif
                        </td>

                        <!-- Price -->
                        <td class="text-start">{{ $invoice->currency }} {{ number_format($item->price, 2) }}</td>

                        <!-- Quantity -->
                        <td class="text-start">× {{ $item->quantity }}</td>

                        <!-- Item Total -->
                        <td class="text-end">
                            @if($item->service->service_type!="onetime")
                                {{ $invoice->currency }} {{ number_format($item->price, 2) }}
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
                            {{ number_format($invoice->total - $invoice->upfront_payment_amount + $total_discount, 2) }}
                        </td>
                        <td class="text-end">
                            ${{ number_format($invoice->total - $invoice->upfront_payment_amount + $total_discount, 2) }}
                        </td>
                    </tr>

                    <!-- Payment Due -->
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-end"><strong>Payment Due</strong></td>
                        <td class="text-end">
                            <strong>{{ $invoice->currency }} {{ number_format($invoice->total - $invoice->upfront_payment_amount + $total_discount, 2) }}</strong>
                        </td>
                        <td class="text-end">
                            <strong>CAD ${{ number_format($invoice->total - $invoice->upfront_payment_amount + $total_discount, 2) }}</strong>
                        </td>
                    </tr>
                </tfoot>
        </table>

        <div class="invoice-card">
            <p><strong>Client:</strong> {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</p>
            <p><strong>Total Amount:</strong> ${{ number_format($invoice->total, 2) }}</p>
            <p><strong>Note:</strong> {{ $invoice->note }}</p>
            @if($total_discount)
                <p><strong>Discount:</strong> ${{ number_format($total_discount,2) }}</p>
            @endif
        </div>

        <!-- Stripe Payment Form -->
        <form id="payment-form" action="{{ route('portal.invoice.payment.process', $invoice->id) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="card-element" class="form-label">Enter your card details</label>
                <div id="card-element" class="form-control">
                    <!-- Stripe Elements will create this -->
                </div>
                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
            </div>
            <button class="btn btn-primary mt-4 w-100" id="submit-button" type="submit">
                <i class="fas fa-lock"></i> Pay ${{ number_format($invoice->total, 2) }}
            </button>

            @if($next_payment_recurring)
                @php
                    $interval = ceil(array_sum($interval_total) / count($interval_total));
                    $interval_text = $interval == 1 ? 'month' : $interval . ' months';
                @endphp
                <div class="d-flex justify-content-center align-items-center text-center mt-4">
                    <span class="text-muted fw-bold" style="font-size: 16px;">
                        ${{ number_format($invoice->total, 2) }} now, then ${{ number_format($next_payment_recurring - $total_discount, 2) }}/{{$interval_text}}
                    </span>
                </div>
            @endif


        </form>
    </div>
</div>

<!-- Include Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();

        // Create a card element
        const card = elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': { color: '#aab7c4' },
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a',
                },
            },
        });

        // Mount the card element
        card.mount('#card-element');

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const submitButton = document.getElementById('submit-button');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            try {
                // Fetch the client secret from the backend
                const clientSecretResponse = await fetch('{{ route("portal.invoice.payment.intent", $invoice->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });

                const clientSecretData = await clientSecretResponse.json();

                if (!clientSecretResponse.ok || !clientSecretData.clientSecret) {
                    throw new Error(clientSecretData.message || 'Failed to fetch client secret.');
                }

                const clientSecret = clientSecretData.clientSecret;

                // Confirm the payment
                const { paymentIntent, error } = await stripe.confirmCardPayment(clientSecret, {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: "{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}",
                        },
                    },
                    setup_future_usage: 'off_session' // Enable reusability for subscriptions
                });

                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Pay ${{ number_format($invoice->total, 2) }}';
                } else {

                    const interval = {{ $interval ?? 1 }}; // Default to 1 if interval is not provided
                    const response = await fetch('{{ route("portal.invoice.payment.process", $invoice->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            payment_intent_id: paymentIntent.id,
                            payment_method: paymentIntent.payment_method, // Retrieve the actual PaymentMethod ID
                            recurring_payment: "{{ number_format($next_payment_recurring - $total_discount, 2) }}",
                            interval: interval,
                        }),
                    });

                    const result = await response.json();

                    if (result.success) {
                        window.location.href = '{{ route("portal.invoices.show", $invoice->id) }}';
                    } else {
                        document.getElementById('card-errors').textContent = result.message || 'Payment failed.';
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Pay ${{ number_format($invoice->total, 2) }}';
                    }
                }
            } catch (error) {
                document.getElementById('card-errors').textContent = error.message;
                submitButton.disabled = false;
                submitButton.innerHTML = 'Pay ${{ number_format($invoice->total, 2) }}';
            }
        });
    });
</script>

<!-- FontAwesome for Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
    .invoice-container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .invoice-header {
        margin-bottom: 20px;
        text-align: center;
    }
    .invoice-card {
        margin-bottom: 30px;
    }
    #card-element {
        border: 1px solid #e0e0e0;
        padding: 10px;
        border-radius: 4px;
        background-color: #f8f9fa;
    }
    #submit-button {
        font-size: 16px;
        font-weight: bold;
    }
</style>

@endsection
