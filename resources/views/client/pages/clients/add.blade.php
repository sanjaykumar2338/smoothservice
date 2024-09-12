@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Clients /</span> Add Client
    </h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <!-- Personal Information Section -->
        <div class="col-xl-6">
            <form id="client_form" method="POST" action="{{ route('client.store') }}">
                {{ csrf_field() }}

                <!-- Error messages will be displayed here -->
                <div id="error-messages" class="alert alert-danger" style="display:none;">
                    <ul id="error-list"></ul>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Email (Required) -->
                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter Email" required />
                        </div>

                        <!-- First Name (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="first_name">First Name (Optional)</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" placeholder="Enter First Name" />
                        </div>

                        <!-- Last Name (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="last_name">Last Name (Optional)</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" placeholder="Enter Last Name" />
                        </div>

                        <!-- Phone (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="phone">Phone (Optional)</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter Phone Number" />
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="password">Password (Optional)</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password (Optional)" />
                        </div>

                        <!-- Send Welcome Email (Checkbox) -->
                        <div class="mb-3">
                            <label class="form-label" for="send_welcome_email">Send Welcome Email</label>
                            <input type="checkbox" id="send_welcome_email" name="send_welcome_email" />
                        </div>

                        <button type="submit" class="btn btn-primary">Add Client</button>
                    </div>
                </div>
        </div>

        <!-- Company Information Section -->
        <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Billing Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Company -->
                        <div class="mb-3">
                            <label class="form-label" for="company">Company</label>
                            <input type="text" class="form-control" id="company" name="company" value="{{ old('company') }}" placeholder="Enter Company" required />
                        </div>

                        <!-- Tax ID -->
                        <div class="mb-3">
                            <label class="form-label" for="tax_id">Tax ID</label>
                            <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id') }}" placeholder="Enter Tax ID" required />
                        </div>

                        <!-- Billing Address -->
                        <div class="mb-3">
                            <label class="form-label" for="billing_address">Billing Address</label>
                            <input type="text" class="form-control" id="billing_address" name="billing_address" value="{{ old('billing_address') }}" placeholder="Enter Billing Address" required />
                        </div>

                        <!-- Country -->
                        <div class="mb-3">
                            <label class="form-label" for="country">Country</label>
                            <select class="form-control" id="country" name="country" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->code }}" {{ old('country') == $country->code ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- State / Province / Region -->
                        <div class="mb-3">
                            <label class="form-label" for="state">State / Province / Region</label>
                            <input type="text" class="form-control" id="state" name="state" value="{{ old('state') }}" placeholder="Enter State / Province / Region" required />
                        </div>

                        <!-- Postal / Zip Code -->
                        <div class="mb-3">
                            <label class="form-label" for="postal_code">Postal / Zip Code</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" placeholder="Enter Postal / Zip Code" required />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
