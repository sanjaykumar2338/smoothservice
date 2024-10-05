@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Marketing /</span> Coupons / Add Coupon
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
        <div class="col-xl">
            <form id="coupon_form" method="POST" action="{{ route('coupon.store') }}">
                {{ csrf_field() }}

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Add Coupon</h5>
                    </div>
                    <div class="card-body">

                        <!-- Coupon Code -->
                        <div class="mb-3">
                            <label class="form-label" for="coupon_code">Coupon Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="coupon_code" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="E.G. 25OFF" />
                                <button type="button" class="btn btn-secondary" id="generate_code">Generate</button>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Optional"></textarea>
                        </div>

                        <!-- Discount Type -->
                        <div class="mb-3">
                            <label class="form-label">Discount Type</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="discount_type" id="fixed_amount" value="Fixed" checked>
                                    <label class="form-check-label" for="fixed_amount">
                                        Fixed Amount
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="discount_type" id="percentage" value="Percentage">
                                    <label class="form-check-label" for="percentage">
                                        Percentage
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Discount Duration -->
                        <div class="mb-3">
                            <label class="form-label">Discount Duration</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="discount_duration" id="forever" value="Forever" checked>
                                    <label class="form-check-label" for="forever">
                                        Forever
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="discount_duration" id="first_payment" value="First Payment">
                                    <label class="form-check-label" for="first_payment">
                                        First Payment
                                    </label>
                                </div>
                            </div>
                        </div>


                        <!-- Applies to (Multiple Services Selection) and Discount (Cloneable) -->
                        <div class="discount-wrapper">
                            
                            <div class="mb-3 discount-clone d-flex align-items-center">
                                <!-- Applies to (Dropdown) -->
                                <div class="flex-grow-1 me-3">
                                    <label class="form-label" for="applies_to">Applies to</label>
                                    <select class="form-control applies_to" name="applies_to[]">
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Discount Input -->
                                <div class="flex-grow-1 me-3">
                                    <label class="form-label" for="discount">Discount</label>
                                    <input type="text" class="form-control" name="discount[]" value="{{ old('discount') }}" placeholder="e.g. $10">
                                </div>

                                <!-- Delete Button -->
                                <div class="align-self-end">
                                    <button type="button" class="btn btn-danger remove-discount">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Button to add discount, floated to the right -->
                        <div class="mb-3 text-end">
                            <button type="button" class="btn btn-secondary" id="add_discount">
                                <i class="fas fa-plus"></i> Add Discount
                            </button>
                        </div>

                        <!-- Redemption Limits -->
                        <div class="mb-3">
                            <label class="form-label">Redemption Limits</label>
                            
                            <!-- Limit to one use per customer -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="limit_to_one" id="limit_to_one" value="1">
                                <label class="form-check-label" for="limit_to_one">
                                    Limit to one use per customer
                                </label>
                            </div>

                            <!-- Limit to new customers only -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="limit_to_new_customers" id="limit_to_new_customers" value="1">
                                <label class="form-check-label" for="limit_to_new_customers">
                                    Limit to new customers only
                                </label>
                            </div>

                            <!-- Set expiry date (checkbox and date input) -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="set_expiry" id="set_expiry" value="1">
                                <label class="form-check-label" for="set_expiry">Set expiry date</label>
                            </div>
                            <!-- Expiry date input (hidden initially) -->
                            <div class="mt-2" id="expiry_date_field" style="display: none;">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="date" id="expiry_date" name="expiry_date" class="form-control">
                            </div>

                            <!-- Require a minimum cart amount (checkbox and input for price) -->
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="min_cart_amount" id="min_cart_amount" value="1">
                                <label class="form-check-label" for="min_cart_amount">Require a minimum cart amount</label>
                            </div>
                            <!-- Minimum cart amount input (hidden initially) -->
                            <div class="mt-2" id="min_cart_amount_field" style="display: none;">
                                <label for="min_cart_amount_value">Minimum Cart Amount</label>
                                <input type="number" id="min_cart_amount_value" name="min_cart_amount_value" class="form-control" placeholder="Enter minimum cart amount">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Coupon</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Generate random coupon code
    document.getElementById('generate_code').addEventListener('click', function() {
        let randomString = Math.random().toString(36).substring(2, 10).toUpperCase();
        document.getElementById('coupon_code').value = randomString;
    });

    // Function to add a new discount field
    document.getElementById('add_discount').addEventListener('click', function() {
        let discountClone = document.querySelector('.discount-clone').cloneNode(true);
        discountClone.querySelector('input').value = '';  // Clear the input value
        document.querySelector('.discount-wrapper').appendChild(discountClone);
        $("select.applies_to").select2();
        let $discountClone = $(discountClone);
        $discountClone.find('select.applies_to').select2().val([]);

        // Find the last select2 container within discountClone and remove it
        $discountClone.find('.select2-container:last').remove();
            console.log(discountClone,'discountClone')
    });

    // Function to remove a discount field
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-discount')) {
            e.target.closest('.discount-clone').remove();
        }
    });

    document.getElementById('set_expiry').addEventListener('change', function () {
        document.getElementById('expiry_date_field').style.display = this.checked ? 'block' : 'none';
    });

    document.getElementById('min_cart_amount').addEventListener('change', function () {
        document.getElementById('min_cart_amount_field').style.display = this.checked ? 'block' : 'none';
    });
</script>

@endsection
