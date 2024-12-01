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
                <p><strong>Payment Method:</strong> VISA •••• •••• •••• 0332</p>
                <p><strong>Billing Address:</strong></p>
                <p>
                    17626 10th ave SW<br>
                    Edmonton, Alberta<br>
                    T6W 1Z9, Canada
                </p>
            </div>
            <div>
                <p><strong>Basic</strong> <span class="text-danger">Expires on Jun 14, 2025</span></p>
                <p>$1188/year</p>
            </div>
        </div>

        <!-- Plan Selection -->
        <div class="plan-select-container">
            <div class="plan-header">
                <button id="monthly-button" class="inactive">Monthly</button>
                <button id="yearly-button" class="active">Yearly</button>
            </div>

            <div class="plan-item">
                <div>
                    <input type="radio" name="plan" id="basic-plan" checked>
                    <label for="basic-plan">Basic</label>
                </div>
                <div>
                    <p>$99/mo</p>
                    <small class="text-muted">Billed yearly</small>
                </div>
            </div>

            <div class="plan-item">
                <div>
                    <input type="radio" name="plan" id="pro-plan">
                    <label for="pro-plan">Pro</label>
                </div>
                <div>
                    <p>$249/mo</p>
                    <small class="text-muted">Billed yearly</small>
                </div>
            </div>

            <div class="plan-item">
                <div>
                    <input type="radio" name="plan" id="plus-plan">
                    <label for="plus-plan">Plus</label>
                </div>
                <div>
                    <p>Contact us</p>
                </div>
            </div>
        </div>

        <!-- Add Team Members -->
        <div class="add-team-members">
            <p>Your selected plan includes 5 seats. You can add more seats for $200/user/year.</p>
            <div class="input-group">
                <input type="number" min="0" max="100" class="form-control" placeholder="0">
                <span class="input-group-text">team members</span>
            </div>
        </div>

        <!-- Continue Button -->
        <button class="btn btn-primary mt-4">Continue to Payment</button>
    </div>
</div>

<script>
    // JavaScript for toggling between Monthly and Yearly buttons
    document.addEventListener('DOMContentLoaded', function () {
        const monthlyButton = document.getElementById('monthly-button');
        const yearlyButton = document.getElementById('yearly-button');

        monthlyButton.addEventListener('click', function () {
            this.classList.add('active');
            this.classList.remove('inactive');
            yearlyButton.classList.remove('active');
            yearlyButton.classList.add('inactive');

            // Update Plan Prices for Monthly (Dynamic Example)
            document.querySelectorAll('.plan-item p').forEach((item, index) => {
                if (index === 0) item.textContent = '$10/mo';
                if (index === 1) item.textContent = '$20/mo';
            });
        });

        yearlyButton.addEventListener('click', function () {
            this.classList.add('active');
            this.classList.remove('inactive');
            monthlyButton.classList.remove('active');
            monthlyButton.classList.add('inactive');

            // Update Plan Prices for Yearly (Dynamic Example)
            document.querySelectorAll('.plan-item p').forEach((item, index) => {
                if (index === 0) item.textContent = '$99/mo';
                if (index === 1) item.textContent = '$249/mo';
            });
        });
    });
</script>

@endsection
