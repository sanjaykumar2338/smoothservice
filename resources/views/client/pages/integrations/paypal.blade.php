@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .btn-import {
        background-color: #dc3545;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 14px;
    }

    .btn-import:hover {
        background-color: #c82333;
    }

    .integration-info {
        padding: 1rem;
        font-size: 14px;
        color: #6c757d;
        line-height: 1.5;
    }

    .btn-connect, .btn-disconnect {
        display: inline-flex;
        align-items: center;
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        font-size: 16px;
        font-weight: 500;
        margin-top: 10px;
    }

    .btn-connect:hover, .btn-disconnect:hover {
        background-color: #0056b3;
    }

    .btn-disconnect {
        background-color: #dc3545;
    }

    .btn-disconnect:hover {
        background-color: #c82333;
    }

    .btn-connect svg, .btn-disconnect svg {
        margin-right: 8px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Integrations /</span> PayPal
    </h4>

    <div class="card">
        <div class="card-header">
            <h5>PayPal</h5>
            <button class="btn-import hidden">Import Data</button>
        </div>
        <div class="integration-info">
            To start accepting client payments through PayPal, click the button below to securely connect your account. This is the recommended integration option for accepting payments.
            <br>

            @php
                $isConnected = auth()->user()->paypal_connect_account_id ? true : false;
            @endphp

            @if($isConnected)
                <br>
                <p><b>Your PayPal account is connected: {{ auth()->user()->paypal_connect_account_id }}</b></p>
                <a style="text-decoration: none;" href="{{ route('paypal.merchant.disconnect') }}" class="btn-disconnect">
                    <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4ZM10 16L15 12L10 8V16Z" fill="white"/>
                    </svg>
                    Disconnect PayPal Account
                </a>


                <form id="disconnect-paypal-form" action="{{ route('paypal.disconnect') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            @else
                <button class="btn-connect" onclick="window.location.href='{{ route('paypal.onboard') }}'">
                    <svg width="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4ZM10 16L15 12L10 8V16Z" fill="white"/>
                    </svg>
                    Connect with PayPal
                </button>
            @endif
        </div>
    </div>
</div>

@endsection
