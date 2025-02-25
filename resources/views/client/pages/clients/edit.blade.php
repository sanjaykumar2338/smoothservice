@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Clients /</span> Edit Client
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

    <form id="client_form" method="POST" action="{{ route('client.update', $client->id) }}">
        @csrf
        @method('PUT')

    <div class="row">
        <!-- Personal Information Section -->
        <div class="col-xl-6">
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
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $client->email) }}" placeholder="Enter Email" required />
                        </div>

                        <!-- First Name (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="first_name">First Name (Optional)</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $client->first_name) }}" placeholder="Enter First Name" />
                        </div>

                        <!-- Last Name (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="last_name">Last Name (Optional)</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $client->last_name) }}" placeholder="Enter Last Name" />
                        </div>

                        <!-- Phone (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="phone">Phone (Optional)</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $client->phone) }}" placeholder="Enter Phone Number" />
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="password">Password (Optional)</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password (Optional)" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Stripe customer ID</label>
                            <input type="text" class="form-control" id="stripe_customer_id" name="stripe_customer_id" value="{{ old('stripe_customer_id', $client->stripe_customer_id) }}" placeholder="cust_" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="account_balance">Account Balance</label>
                            <div class="input-group">
                                <span class="input-group-text" style="padding-bottom: 4px;">$</span> <!-- Change "$" to your preferred currency symbol -->
                                <input type="number" class="form-control" id="account_balance" name="account_balance" 
                                    value="{{ old('account_balance', $client->account_balance) }}" 
                                    placeholder="0.00" step="0.01">
                            </div>
                        </div>


                        <div class="mb-3">
                            <label for="order_id" class="form-label">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">-- Select Status --</option>
                                @foreach($client_statues as $status)
                                    <option {{$status->id==$client->status?'selected':''}} value="{{ $status->id }}">{{ $status->label }}</option>
                                @endforeach
                            </select>
                        </div>
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
                            <input type="text" class="form-control" id="company" name="company" value="{{ old('company', $client->company) }}" placeholder="Enter Company" required />
                        </div>

                        <!-- Tax ID -->
                        <div class="mb-3">
                            <label class="form-label" for="tax_id">Tax ID</label>
                            <input type="text" class="form-control" id="tax_id" name="tax_id" value="{{ old('tax_id', $client->tax_id) }}" placeholder="Enter Tax ID" required />
                        </div>

                        <!-- Billing Address -->
                        <div class="mb-3">
                            <label class="form-label" for="billing_address">Billing Address</label>
                            <input type="text" class="form-control" id="billing_address" name="billing_address" value="{{ old('billing_address', $client->billing_address) }}" placeholder="Enter Billing Address" required />
                        </div>

                        <!-- Country -->
                        <div class="mb-3">
                            <label class="form-label" for="country">Country</label>
                            <select class="form-control" id="country" name="country" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->code }}" {{ old('country', $client->country) == $country->code ? 'selected' : '' }}>{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- State / Province / Region -->
                        <div class="mb-3">
                            <label class="form-label" for="state">State / Province / Region</label>
                            <input type="text" class="form-control" id="state" name="state" value="{{ old('state', $client->state) }}" placeholder="Enter State / Province / Region" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="city">City</label>
                            <input type="text" class="form-control" id="city" name="city" value="{{ old('city', $client->city) }}" placeholder="Enter City" required />
                        </div>

                        <!-- Postal / Zip Code -->
                        <div class="mb-3">
                            <label class="form-label" for="postal_code">Postal / Zip Code</label>
                            <input type="text" class="form-control" id="postal_code" name="postal_code" value="{{ old('postal_code', $client->postal_code) }}" placeholder="Enter Postal / Zip Code" required />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="state">Single line of text</label>
                            <input type="text" class="form-control" id="single_line_of_text" name="single_line_of_text" value="{{ old('single_line_of_text', $client->single_line_of_text) }}" placeholder="" />
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="reset_password_welcome_email" name="reset_password_welcome_email">
                                <label class="form-check-label" for="reset_password_welcome_email">Reset password and send welcome email</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="send_email" name="send_email">
                                <label class="form-check-label" for="send_email">Change password</label>
                            </div>

                            <div class="hidden-field" id="password_field" style="display: none;">
                                <input type="password" class="form-control mt-2" name="new_password" placeholder="New Password">
                            </div>
                        </div>


                        <button type="submit" class="btn btn-primary">Update Client</button>
                    </div>
                </div>
        </div>
    </div>

    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const sendEmailCheckbox = document.getElementById('send_email');
        const passwordField = document.getElementById('password_field');

        // Toggle visibility based on checkbox state
        sendEmailCheckbox.addEventListener('change', function() {
            if (this.checked) {
                passwordField.style.display = 'block';
            } else {
                passwordField.style.display = 'none';
            }
        });
    });
</script>

@endsection
