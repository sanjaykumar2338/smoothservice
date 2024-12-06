@extends('client.client_template')
@section('content')

<style>
    .integration-container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .integration-tabs {
        display: flex;
        justify-content: flex-start;
        border-bottom: 1px solid #e0e0e0;
        margin-bottom: 20px;
    }

    .integration-tabs button {
        background: none;
        border: none;
        font-size: 1rem;
        padding: 10px 20px;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
    }

    .integration-tabs button.active {
        font-weight: bold;
        color: #007bff;
        border-bottom: 2px solid #007bff;
    }

    .integration-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .integration-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    .integration-item:last-child {
        border-bottom: none;
    }

    .integration-icon {
        font-size: 24px;
        color: #007bff;
        margin-right: 15px;
    }

    .integration-name {
        font-size: 1rem;
        font-weight: bold;
        color: #333;
    }

    .integration-description {
        font-size: 0.9rem;
        color: #666;
    }

    .integration-settings {
        background-color: #dc3545;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 0.9rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .integration-settings:hover {
        background-color: #c82333;
    }

    .integration-activate {
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 4px;
        padding: 5px 10px;
        font-size: 0.9rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .integration-activate:hover {
        background-color: #0056b3;
    }

    .integration-category {
        display: none;
    }

    .integration-category.active {
        display: block;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Integrations</span>
    </h4>

    <div class="card integration-container">
        <h5 class="card-header integration-header">Integrations</h5>

        <!-- Tabs -->
        <div class="integration-tabs">
            <button class="active" data-tab="all">All</button>
            <button data-tab="payment">Payment</button>
            <button data-tab="analytics">Analytics</button>
            <button data-tab="email">Email</button>
            <button data-tab="reports">Reports</button>
            <button data-tab="partners">Partners</button>
        </div>

        <!-- Integration Categories -->
        <div class="integration-category active" id="all">
            <ul class="integration-list">
                <li class="integration-item">
                    <div class="d-flex align-items-center">
                        <i class="bx bxl-stripe integration-icon"></i>
                        <div>
                            <p class="integration-name">Stripe</p>
                            <p class="integration-description">Collect payments for your services.</p>
                        </div>
                    </div>
                    <button onclick="window.location.href='{{ route('integrations.stripe.connect') }}'" class="integration-settings">Settings</button>
                </li>
                <li class="integration-item">
                    <div class="d-flex align-items-center">
                        <i class="bx bxl-paypal integration-icon"></i>
                        <div>
                            <p class="integration-name">PayPal</p>
                            <p class="integration-description">Get paid via PayPal Payments Standard.</p>
                        </div>
                    </div>
                    <button onclick="window.location.href='{{ route('integrations.paypal') }}'" class="integration-settings">Settings</button>
                </li>
            </ul>
        </div>

        <div class="integration-category" id="payment">
            <ul class="integration-list">
                <li class="integration-item">
                    <div class="d-flex align-items-center">
                        <i class="bx bxl-stripe integration-icon"></i>
                        <div>
                            <p class="integration-name">Stripe</p>
                            <p class="integration-description">Collect payments for your services.</p>
                        </div>
                    </div>
                    <button onclick="window.location.href='{{ route('integrations.stripe.connect') }}'" class="integration-settings">Settings</button>
                </li>
                <li class="integration-item">
                    <div class="d-flex align-items-center">
                        <i class="bx bxl-paypal integration-icon"></i>
                        <div>
                            <p class="integration-name">PayPal</p>
                            <p class="integration-description">Get paid via PayPal Payments Standard.</p>
                        </div>
                    </div>
                    <button onclick="window.location.href='{{ route('integrations.paypal') }}'" class="integration-settings">Settings</button>
                </li>
            </ul>
        </div>

        <div class="integration-category" id="analytics">
            <ul class="integration-list">
                <li class="integration-item">
                    <div class="d-flex align-items-center">
                        <i class="bx bxl-google integration-icon"></i>
                        <div>
                            <p class="integration-name">Google Tag Manager</p>
                            <p class="integration-description">Send event and purchase data to Google Tag Manager.</p>
                        </div>
                    </div>
                    <button class="integration-settings">Settings</button>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.integration-tabs button');
        const categories = document.querySelectorAll('.integration-category');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                const target = tab.getAttribute('data-tab');
                categories.forEach(category => {
                    category.classList.remove('active');
                    if (category.id === target) {
                        category.classList.add('active');
                    }
                });
            });
        });
    });
</script>

@endsection
