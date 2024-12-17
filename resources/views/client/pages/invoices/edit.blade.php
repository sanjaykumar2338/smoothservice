@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
    .hidden-field {
        display: none !important;
    }

    .grid-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .grid-container div {
        display: flex;
        flex-direction: column;
    }

    .actions {
        margin-top: auto;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Invoices /</span> Edit Invoice
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
            
            <form id="invoice_form" method="POST" action="{{ route('invoices.update', $invoice->id) }}">
                @csrf
                @method('PUT')

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Invoice Details</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Client -->
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label" for="client">Client</label>
                                <select class="form-control" id="client" name="client_id">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ $invoice->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->first_name }} {{ $client->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-4">
                                <label class="form-label" for="due_date">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date', $invoice->due_date) }}">
                            </div>
                        </div>

                        <!-- Dynamic Items Section -->
                        <div id="items-wrapper">
                            @foreach($invoice->items as $index => $item)
                            <div class="grid-container item-group mt-4" id="item-{{ $index }}">
                                <!-- Service Dropdown -->
                                <div>
                                    <label class="form-label" for="service_id">Service</label>
                                    <select class="form-control service-select" name="service_id[]">
                                        <option value="">-- No Service --</option>
                                        @foreach($services as $service)
                                            @if($service->service_type == 'recurring')
                                                

                                                @if($service->trial_for!="")
                                                    <option data-type="recurringwithtrail" data-price="{{$service->trial_price}}" value="{{ $service->id }}" {{ $item->service_id == $service->id ? 'selected' : '' }}>
                                                        {{ $service->service_name }} {{$service->trial_currency}} {{$service->trial_price}} for {{$service->trial_for}} {{ $service->trial_for > 1 ? $service->trial_period . 's' : $service->trial_period }}, {{ $service->recurring_service_currency }} 
                                                        ${{ $service->recurring_service_currency_value }}/{{ $service->recurring_service_currency_value_two }} 
                                                        {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}
                                                    </option>
                                                @else
                                            
                                                <option data-type="recurring" data-price="{{$service->recurring_service_currency_value}}" value="{{ $service->id }}" {{ $item->service_id == $service->id ? 'selected' : '' }}>
                                                    {{ $service->service_name }} {{ $service->recurring_service_currency }} 
                                                    ${{ $service->recurring_service_currency_value }} / 
                                                    {{ $service->recurring_service_currency_value_two }} 
                                                    {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}
                                                </option>

                                                @endif


                                            @else
                                                <option data-type="onetime" data-price="{{$service->one_time_service_currency_value}}" value="{{ $service->id }}" {{ $item->service_id == $service->id ? 'selected' : '' }}>
                                                    {{ $service->service_name }} / {{ $service->one_time_service_currency }} ${{ $service->one_time_service_currency_value }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Item Name -->
                                <div class="item-name-container {{ $item->service_id ? 'hidden-field' : '' }}">
                                    <label class="form-label" for="item_name">Item Name</label>
                                    <input type="text" class="form-control" name="item_names[]" value="{{ $item->item_name }}" placeholder="Enter item name">
                                </div>

                                <!-- Description -->
                                <div>
                                    <label class="form-label" for="description">Description</label>
                                    <input type="text" class="form-control" name="descriptions[]" value="{{ $item->description }}" placeholder="Enter description">
                                </div>

                                <!-- Price -->
                                <div>
                                    <label class="form-label" for="price">Price</label>
                                    <input type="number" class="form-control" name="prices[]" value="{{ $item->price }}" placeholder="Enter price">
                                </div>

                                <!-- Quantity -->
                                <div>
                                    <label class="form-label" for="quantity">Quantity</label>
                                    <input type="number" class="form-control" name="quantities[]" value="{{ $item->quantity }}" placeholder="Enter quantity">
                                </div>

                                <div>
                                    <label class="form-label" for="discounts">Discounts</label>
                                    <input type="number" class="form-control" name="discounts[]" value="{{ $item->discount }}" placeholder="Enter discount">
                                    <span class="recurring_discount" style="display:{{$item->service->service_type=='recurring' ? 'block':'none'}}">{{$item->service->service_type=='recurring' ? 'First Payment':'Recurring discount'}}</span>
                                </div>

                                <div class="recurring_discount_next_payment" style="display:{{$item->service->service_type=='recurring' ? 'block':'none'}}">
                                    <label class="form-label" for="discounts">Discounts</label>
                                    <input type="number" class="form-control" name="discountsnextpayment[]" value="{{ $item->discountsnextpayment }}" placeholder="Enter discount">
                                    <span>Next payment</span>
                                </div>

                                <!-- Remove Item Button -->
                                <div class="actions">
                                    <button type="button" class="btn btn-danger remove-item" {{ $index == 0 ? 'style=display:none;' : '' }}>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <hr style="grid-column: span 3;">
                            @endforeach
                        </div>

                        <!-- Add Item Button -->
                        <button type="button" class="btn btn-success mt-2" id="add-item">+ Add Item</button>

                        <!-- Note to Client -->
                        <div class="mt-4">
                            <label class="form-label" for="note">Note to Client</label>
                            <textarea class="form-control" name="note" placeholder="Add a note for the client">{{ $invoice->note }}</textarea>
                        </div>

                        <!-- Additional Options with Dynamic Fields -->
                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="send_email" name="send_email" data-toggle="send_email_field" {{ $invoice->send_email ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_email">Send email notification</label>
                            </div>
                        </div>

                        <!-- Partial Payment -->
                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="partial_payment" name="partial_payment" data-toggle="upfront_payment_field" {{ $invoice->upfront_payment_amount ? 'checked' : '' }}>
                                <label class="form-check-label" for="partial_payment">Partial upfront payment</label>
                            </div>
                            <div class="hidden-field" id="upfront_payment_field">
                                <input type="number" class="form-control mt-2" name="upfront_payment_amount" value="{{ $invoice->upfront_payment_amount }}" placeholder="Enter upfront payment amount">
                            </div>
                        </div>

                        <!-- Custom Billing Date -->
                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="custom_billing_date" name="custom_billing_date" data-toggle="billing_date_field" {{ $invoice->billing_date ? 'checked' : '' }}>
                                <label class="form-check-label" for="custom_billing_date">Custom billing date</label>
                            </div>
                            <div class="hidden-field" id="billing_date_field">
                                <input type="date" class="form-control mt-2" name="billing_date" value="{{ $invoice->billing_date }}">
                            </div>
                        </div>

                        <!-- Custom Currency -->
                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="custom_currency" name="custom_currency" data-toggle="currency_field" {{ $invoice->currency ? 'checked' : '' }}>
                                <label class="form-check-label" for="custom_currency">Custom currency</label>
                            </div>
                            <div class="hidden-field" id="currency_field">
                                <input type="text" class="form-control mt-2" name="currency" value="{{ $invoice->currency }}" placeholder="Enter currency code (e.g., USD, CAD)">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary update_invoice">Update Invoice</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Add click event listener to the "Add Invoice" button
    document.querySelector('.update_invoice').addEventListener('click', function (e) {
        // Prevent default button behavior until validation is passed
        e.preventDefault();

        // Get all service selects
        const serviceSelects = document.querySelectorAll('.service-select');
        const upfrontPaymentInput = document.querySelector('input[name="upfront_payment_amount"]');

        let hasRecurringService = false;

        // Loop through all service selects to check the selected options
        serviceSelects.forEach(select => {
            const selectedOption = select.options[select.selectedIndex];
            const dataType = selectedOption.getAttribute('data-type'); // Get the data-type attribute

            // Check if the selected option has recurring or recurringwithtrail
            if (dataType === 'recurring' || dataType === 'recurringwithtrail') {
                hasRecurringService = true;
            }
        });

        // Check if upfront payment amount is filled
        const upfrontPaymentValue = upfrontPaymentInput.value.trim();

        // Validation: If recurring service and upfront payment are used
        if (hasRecurringService && upfrontPaymentValue !== '') {
            alert('Partial payments cannot be used with recurring services.');
        } else {
            // If validation passes, submit the form
            e.target.closest('form').submit();
        }
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Event delegation: Listen for change events on the parent container
        document.body.addEventListener('change', function (e) {
            // Check if the event target is a service-select dropdown
            if (e.target && e.target.classList.contains('service-select')) {
                const serviceSelect = e.target;

                // Get the selected option
                const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
                const itemNameContainer2 = serviceSelect.closest('.item-group');

                // Retrieve the 'data-type' and 'data-price' attributes
                const dataType = selectedOption.dataset.type;
                const price = selectedOption.dataset.price;

                const priceField = itemNameContainer2.querySelector('[name="prices[]"]'); // Target the price input field

                // Update the price field
                if (priceField) {
                    priceField.value = price;
                }

                console.log('Selected data-type:', dataType);

                // Use the current itemNameContainer for recurring spans
                const recurringDiscountSpan = itemNameContainer2.querySelector('.recurring_discount');
                const recurringDiscountSpan2 = itemNameContainer2.querySelector('.recurring_discount_next_payment');

                if (dataType === 'recurringwithtrail') {
                    if (recurringDiscountSpan) {
                        recurringDiscountSpan.style.display = 'block';
                        recurringDiscountSpan.textContent = "First Discount";
                    }
                    if (recurringDiscountSpan2) {
                        recurringDiscountSpan2.style.display = 'block';
                    }

                    itemNameContainer2.querySelector('[name="discountsnextpayment[]"]').style.display = 'block';

                } else if (dataType === 'recurring') {
                    if (recurringDiscountSpan) {
                        recurringDiscountSpan.style.display = 'block';
                        recurringDiscountSpan.textContent = "Recurring discount";
                    }
                    if (recurringDiscountSpan2) {
                        recurringDiscountSpan2.style.display = 'none';
                        const inputField = recurringDiscountSpan2.querySelector('input');
                        if (inputField) inputField.value = '';
                    }
                } else {
                    if (recurringDiscountSpan) recurringDiscountSpan.style.display = 'none';
                    if (recurringDiscountSpan2) recurringDiscountSpan2.style.display = 'none';
                }
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = {{ count($invoice->items) }}; // Set initial index based on existing items

        // Add new item on button click
        document.getElementById('add-item').addEventListener('click', function () {
            const itemTemplate = document.getElementById('item-0').cloneNode(true);
            itemTemplate.setAttribute('id', 'item-' + itemIndex); // Set unique id for each new item
            itemTemplate.querySelectorAll('input').forEach(input => input.value = ''); // Clear all inputs in the cloned template

            const serviceIdSelect = itemTemplate.querySelector('select[name="service_id[]"]'); 
            serviceIdSelect.selectedIndex = 0;
            
            // Change text for recurring_discount class in itemTemplate
            const recurringDiscount = itemTemplate.querySelector('.recurring_discount');
            if (recurringDiscount) {
                recurringDiscount.textContent = 'Recurring payment'; // Set the new text
                recurringDiscount.style.display = 'none';
            }

            const recurring_discount_next_payment = itemTemplate.querySelector('.recurring_discount_next_payment');
            if (recurring_discount_next_payment) {
                recurring_discount_next_payment.style.display = 'none';
            }

            // Find the 'discountsnextpayment' input field
            const discountsNextPaymentField = itemTemplate.querySelector('[name="discountsnextpayment[]"]');

            // Hide and clear the field if it exists
            if (discountsNextPaymentField) {
                discountsNextPaymentField.style.display = 'none'; // Hide the field
                discountsNextPaymentField.value = ''; // Clear the value
            }

            itemTemplate.querySelector('.remove-item').style.display = 'block'; // Show delete button for cloned items
            // Select the element with the 'item-name-container' class
            const itemNameContainer = itemTemplate.querySelector('.item-name-container');

            // Remove the 'hidden-field' class
            if (itemNameContainer) {
                itemNameContainer.classList.remove('hidden-field');
            }
            document.getElementById('items-wrapper').appendChild(itemTemplate); // Append new item to the DOM
            itemIndex++;
        });

        // Remove item on clicking delete button
        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.closest('.item-group').remove(); // Remove the item
            }
        });

        // Toggle item name field based on service selection
        document.addEventListener('change', function (e) {
            if (e.target && e.target.classList.contains('service-select')) {
                const itemNameContainer = e.target.closest('.item-group').querySelector('.item-name-container');
                if (e.target.value) {
                    itemNameContainer.classList.add('hidden-field');
                } else {
                    itemNameContainer.classList.remove('hidden-field');
                }
            }
        });

        // Handle toggling of additional fields like upfront payment, billing date, and currency
        document.querySelectorAll('.toggle-field').forEach(function (checkbox) {
            const target = document.getElementById(checkbox.dataset.toggle);
            if (target) {
                // Show the field if checkbox is already checked (for pre-filled forms)
                if (checkbox.checked) {
                    target.classList.remove('hidden-field');
                } else {
                    target.classList.add('hidden-field');
                }

                // Add change event listener
                checkbox.addEventListener('change', function () {
                    if (checkbox.checked) {
                        target.classList.remove('hidden-field');
                    } else {
                        target.classList.add('hidden-field');
                    }
                });
            } else {
                console.warn('Target element not found for toggle:', checkbox.dataset.toggle);
            }
        });
    });
</script>

@endsection
