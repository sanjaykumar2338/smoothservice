@extends('client.client_template')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="billing-container">
        <div class="billing-header">
            <h4>Complete Your Subscription</h4>
        </div>

        <div class="billing-card">
            <p><strong>Plan:</strong> {{ $plan->name }}</p>
            <p><strong>Description:</strong> {{ $plan->description }}</p>
            <p><strong>Price:</strong> ${{ number_format($plan->price, 2) }} / {{ $plan->billing_interval }}</p>
        </div>

        <!-- Stripe Payment Form -->
        <form id="payment-form" action="{{ route('billing.process') }}" method="POST">
            @csrf
            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
            <div class="form-group">
                <label for="card-element" class="form-label">Enter your card details</label>
                <div id="card-element" class="form-control">
                    <!-- Stripe Elements will create this -->
                </div>
                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
            </div>
            <button class="btn btn-primary mt-4 w-100" id="submit-button" type="submit">
                <i class="fas fa-lock"></i> Subscribe
            </button>
        </form>
    </div>
</div>

<!-- Include Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Set your Stripe publishable key
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements();

        // Create an instance of the card Element
        const card = elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                }
            }
        });

        // Add the card Element to the `card-element` div
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element
        card.on('change', function (event) {
            const displayError = document.getElementById('card-errors');
            displayError.textContent = event.error ? event.error.message : '';
        });

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            // Disable the submit button to prevent multiple submissions
            const submitButton = document.getElementById('submit-button');
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Confirm the card payment
            const { setupIntent, error } = await stripe.confirmCardSetup(
                "{{ $clientSecret }}", // Replace with SetupIntent client secret
                {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: "{{ auth()->user()->name }}" // Add user's name dynamically
                        }
                    }
                }
            );

            if (error) {
                // Show error to your customer
                document.getElementById('card-errors').textContent = error.message;
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="fas fa-lock"></i> Subscribe';
            } else {
                // Add the payment method ID to the form
                const hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'payment_method');
                hiddenInput.setAttribute('value', setupIntent.payment_method);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        });
    });
</script>

<!-- FontAwesome for Icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

<style>
    .billing-container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .billing-header {
        margin-bottom: 20px;
        text-align: center;
    }
    .billing-card {
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
