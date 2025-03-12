@extends('c_main.c_dashboard')

@section('content')

<style>
    .funds-container {
        background: #fff;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    }
    .funds-container h4 {
        font-weight: bold;
    }
    .btn-primary {
        width: 100%;
    }
    .input-group-text {
        font-weight: bold;
    }
    #card-element {
        border: 1px solid #e0e0e0;
        padding: 10px;
        border-radius: 4px;
        background-color: #f8f9fa;
    }
</style>

<div class="container">
    <div class="funds-container">
        <h4 class="mb-4">Add Funds</h4>

        <!-- Current Balance -->
        <div class="mb-3">
            <label class="form-label">Current balance:</label>
            <span class="fw-bold text-dark">${{ number_format(getAuthenticatedUser()->account_balance, 2) }}</span>
        </div>

        <!-- Add Funds Form -->
        <form id="funds-form">
            @csrf

            <div class="mb-3">
                <label class="form-label" for="amount">Amount to add:</label>
                <div class="input-group">
                    <span class="input-group-text" style="padding: 4px;">$</span>
                    <input type="number" step="0.01" class="form-control" id="amount" name="amount" placeholder="0.00" required>
                </div>
            </div>

            <!-- Stripe Card Element -->
            <div class="form-group">
                <label for="card-element" class="form-label">Enter your card details</label>
                <div id="card-element" class="form-control"></div>
                <div id="card-errors" role="alert" class="text-danger mt-2"></div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mt-4">
                <i class="fas fa-lock"></i> Add Funds
            </button>
        </form>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stripe = Stripe('{{ env("STRIPE_KEY") }}', {
            stripeAccount: '{{ $accountId }}'
        });

        console.log("Stripe initialized with Account ID:", '{{ $accountId }}');

        const elements = stripe.elements();
        const card = elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    color: '#32325d',
                    fontFamily: 'Helvetica, Arial, sans-serif',
                    fontSize: '16px',
                    '::placeholder': { color: '#aab7c4' },
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a',
                },
            },
        });

        card.mount('#card-element');

        const form = document.getElementById('funds-form');

        form.addEventListener('submit', async function (event) {
            event.preventDefault();

            const button = form.querySelector('button');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            const amount = document.getElementById('amount').value;
            if (!amount || parseFloat(amount) <= 0) {
                document.getElementById('card-errors').textContent = "Please enter a valid amount.";
                button.disabled = false;
                button.innerHTML = 'Add Funds';
                return;
            }

            try {
                // 1️⃣ **Create Payment Method**
                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: card,
                    billing_details: {
                        name: "{{ getAuthenticatedUser()->first_name }} {{ getAuthenticatedUser()->last_name }}",
                        email: "{{ getAuthenticatedUser()->email }}",
                    },
                });

                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                    button.disabled = false;
                    button.innerHTML = 'Add Funds';
                    return;
                }

                // 2️⃣ **Send Payment Method to Backend**
                const response = await fetch('{{ route("portal.fund.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        payment_method: paymentMethod.id,
                        amount: amount,
                        accountId: '{{ $accountId }}'
                    }),
                });

                const result = await response.json();

                // 3️⃣ **Handle Payment Response**
                if (result.requires_action) {
                    // ✅ **Handle 3D Secure**
                    const { error: confirmError } = await stripe.confirmCardPayment(result.client_secret);

                    if (confirmError) {
                        document.getElementById('card-errors').textContent = confirmError.message;
                        button.disabled = false;
                        button.innerHTML = 'Add Funds';
                        return;
                    }

                    // 4️⃣ **Verify Payment After Confirmation**
                    const verifyResponse = await fetch('{{ route("portal.fund.verify") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            payment_intent_id: result.payment_intent_id,
                            accountId: '{{ $accountId }}' // ✅ Pass Stripe Connect ID
                        }),
                    });

                    const verifyResult = await verifyResponse.json();

                    if (verifyResult.success) {
                        window.location.href = '{{ route("portal.dashboard") }}' + '?success=' + encodeURIComponent('Funds added successfully!');
                    } else {
                        document.getElementById('card-errors').textContent = "Payment verification failed.";
                        button.disabled = false;
                        button.innerHTML = 'Add Funds';
                    }
                } else if (result.success) {
                    // ✅ **Balance Updated Case**
                    window.location.href = '{{ route("portal.dashboard") }}' + '?success=' + encodeURIComponent('Funds added successfully!');
                } else {
                    document.getElementById('card-errors').textContent = result.message || 'Payment failed.';
                    button.disabled = false;
                    button.innerHTML = 'Add Funds';
                }
            } catch (error) {
                document.getElementById('card-errors').textContent = error.message;
                button.disabled = false;
                button.innerHTML = 'Add Funds';
            }
        });
    });
</script>

@endsection
