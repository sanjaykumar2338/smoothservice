@extends('c_main.c_dashboard')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="invoice-container">
        <div class="invoice-header">
            <h4>Pay Invoice: {{ $invoice->invoice_no }}</h4>
        </div>

        <div class="invoice-card">
            <p><strong>Client:</strong> {{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</p>
            <p><strong>Total Amount:</strong> ${{ number_format($invoice->total, 2) }}</p>
            <p><strong>Note:</strong> {{ $invoice->note }}</p>
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
                });

                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                    submitButton.disabled = false;
                    submitButton.innerHTML = 'Pay ${{ number_format($invoice->total, 2) }}';
                } else {
                    // Submit the payment details to the backend
                    const response = await fetch('{{ route("portal.invoice.payment.process", $invoice->id) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            payment_intent_id: paymentIntent.id,
                            payment_method: 'stripe',
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
