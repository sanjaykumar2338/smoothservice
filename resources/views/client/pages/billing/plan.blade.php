@extends('client.client_template')

@section('content')

<style>
    .billing-header {
        margin-bottom: 30px;
    }

    .billing-header h4 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: bold;
    }

    .billing-card {
        padding: 20px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 30px;
    }

    .plan-header {
        display: flex;
        justify-content: right;
        align-items: center;
        margin-bottom: 20px;
    }

    .plan-header button {
        width: 120px;
        padding: 10px;
        font-size: 14px;
        font-weight: bold;
        margin: 0 5px;
        border-radius: 5px;
        border: 1px solid #007bff;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }

    .plan-header button.active {
        background: #007bff;
        color: #fff;
    }

    .plan-header button.inactive {
        background: #fff;
        color: #007bff;
    }

    .plan-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .plan-item:last-child {
        border-bottom: 0;
    }

    .plan-item input[type="radio"] {
        margin-right: 10px;
    }

    .plan-features {
        font-size: 0.85rem;
        color: #6c757d;
        margin-top: 10px;
    }

    .add-team-members {
        margin-top: 20px;
        font-size: 0.9rem;
        color: #6c757d;
    }

    .btn-primary {
        width: 100%;
        padding: 15px;
        font-size: 16px;
        font-weight: bold;
        border-radius: 5px;
        background-color: #007bff;
        border: none;
        transition: all 0.3s ease-in-out;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="billing-container">
        <!-- Billing Header -->
        <div class="billing-header">
            <h4>Manage Billing</h4>
        </div>

        <!-- Billing Details -->
        <div class="billing-card">
            <div>
                <!-- Display Payment Method -->
                <p><strong>Payment Method:</strong> 
                    <span class="badge bg-primary">
                        {{ auth()->user()->card_brand ?? 'N/A' }}
                    </span>
                    •••• {{ auth()->user()->card_last_four ?? 'XXXX' }}
                </p>

                <!-- Active Subscription Details -->
                @if($activeSubscription)
                    <p><strong>{{ $activeSubscription->name }}</strong>
                        <span class="text-danger" style="background-color: antiquewhite; margin-left: 7px;">
                            Expires on {{ $activeSubscription->ends_at ? $activeSubscription->ends_at->format('M d, Y') : 'N/A' }}
                        </span>
                    </p>
                    <p>${{ number_format($activeSubscription->stripe_price, 2) }}/{{ $activeSubscription->duration }}</p>

                    <!-- Cancel Subscription Button -->
                    <form action="{{ route('subscription.cancel', $activeSubscription->id) }}" method="POST" style="margin-top: 15px;">
                        @csrf
                        @method('POST') <!-- Use POST to safely handle the cancellation -->
                        <button type="submit" class="btn btn-danger">Cancel Subscription</button>
                    </form>
                @else
                    <p>No active subscription found.</p>
                @endif
            </div>
        </div>

        <!-- Plan Selection -->
        <div class="plan-select-container">
            <div class="plan-header">
                <button id="monthly-button" class="active">Monthly</button>
                <button id="yearly-button" class="inactive">Yearly</button>
            </div>

            <!-- Plans Container -->
            <div id="plans-container" class="plans-container">
                @foreach($plans->where('billing_interval', 'yearly') as $plan)
                    <div class="plan-card">
                        <input type="radio" name="plan" class="plan-radio" id="plan-{{ $plan['id'] }}" value="{{ $plan['id'] }}" {{ $loop->first ? 'checked' : '' }}>
                        <label for="plan-{{ $plan['id'] }}" class="plan-label">
                            <div class="plan-header">
                                <h5>{{ $plan['name'] }}</h5>
                                <p class="plan-price">${{ number_format($plan['price'], 2) }}/{{ $plan['billing_interval'] }}</p>
                            </div>
                            <div class="plan-description">
                                {{ $plan['description'] }}
                            </div>
                        </label>
                    </div>
                @endforeach
            </div>

        </div>

        <!-- Add Team Members -->
        <div class="add-team-members" style="display:none;">
            <p>Your selected plan includes 5 seats. You can add more seats for $200/user/year.</p>
            <div class="input-group">
                <input type="number" min="0" max="100" class="form-control" placeholder="0" style="height: 38px;">
                <span class="input-group-text">team members</span>
            </div>
        </div>

        <!-- Continue Button -->
        <button id="continue-button" class="btn btn-primary mt-4">Continue to Payment</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const monthlyButton = document.getElementById('monthly-button');
        const yearlyButton = document.getElementById('yearly-button');
        const plansContainer = document.getElementById('plans-container');

        const plansData = @json($plans->groupBy('billing_interval'));

        function renderPlans(interval) {
            const selectedPlans = plansData[interval] || [];
            plansContainer.innerHTML = '';

            selectedPlans.forEach(plan => {
                const planHtml = `
                <div class="plan-item">
                    <div>
                        <input type="radio" name="plan" id="${plan.id}">
                        <label for="plan-${plan.id}">${plan.name}</label>
                        <div class="plan-features">${plan.description}</div>
                    </div>
                    <div>
                        <p>$${parseFloat(plan.price).toFixed(2)}/${interval}</p>
                        <small class="text-muted">Billed ${interval}</small>
                    </div>
                </div>`;
                plansContainer.insertAdjacentHTML('beforeend', planHtml);
            });
        }

        monthlyButton.addEventListener('click', function () {
            this.classList.add('active');
            this.classList.remove('inactive');
            yearlyButton.classList.remove('active');
            yearlyButton.classList.add('inactive');
            renderPlans('monthly');
        });

        yearlyButton.addEventListener('click', function () {
            this.classList.add('active');
            this.classList.remove('inactive');
            monthlyButton.classList.remove('active');
            monthlyButton.classList.add('inactive');
            renderPlans('yearly');
        });

        // Initial Render for 'yearly'
        renderPlans('monthly');
    });

    document.addEventListener('DOMContentLoaded', function () {
        const continueButton = document.getElementById('continue-button');

        continueButton.addEventListener('click', function () {
            const selectedPlan = document.querySelector('input[name="plan"]:checked');
            if (!selectedPlan) {
                alert('Please select a plan before continuing.');
                return;
            }

            const planId = selectedPlan.id;
            const paymentUrl = "{{ route('billing.subscription.payment') }}" + "?plan_id=" + encodeURIComponent(planId);
            window.location.href = paymentUrl;
        });
    });
</script>

@endsection
