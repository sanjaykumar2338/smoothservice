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
        <span class="text-muted fw-light">Invoices /</span> Add 
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
            
            <form id="invoice_form" method="POST" action="{{ route('invoices.store') }}">
                {{ csrf_field() }}

                <input type="hidden" name="order_id" value="{{isset($order->id)}}">

                <!-- Error messages will be displayed here -->
                <div id="error-messages" class="alert alert-danger" style="display:none;">
                    <ul id="error-list"></ul>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Invoice Details</h5>
                    </div>
                    <div class="card-body">
                        
                    <div class="row">
                        <!-- Client -->
                        
                        <div class="col-md-8">
                            <label class="form-label" for="client">Client</label>
                            <select class="form-control" id="client" name="client_id">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $client->id == $client_id ? 'selected' : '' }}>{{ $client->first_name }} {{ $client->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Due Date -->
                        <div class="col-md-4">
                            <label class="form-label" for="due_date">Due Date</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
                        </div>
                    </div>

                    <!-- Template for the item (hidden by default) -->
                    <template id="item-template">
                        <div class="grid-container item-group mt-4">
                            <!-- Service Dropdown -->
                            <div>
                                <label class="form-label" for="service_id">Service</label>
                                <select class="form-control service-select" name="service_id[]">
                                    <option value="">-- No Service --</option>
                                    @foreach($services as $service)
                                        @if($service->service_type == 'recurring')
                                            @if($order=="")
                                                @if($service->trial_for!="")
                                                    <option data-parent-services="{{ $service->parent_services ?? '' }}"  data-type="recurringwithtrail" data-price="{{$service->trial_price}}" value="{{ $service->id }}">
                                                        {{ $service->service_name }} {{$service->trial_currency}} {{$service->trial_price}} for {{$service->trial_for}} {{ $service->trial_for > 1 ? $service->trial_period . 's' : $service->trial_period }}, {{ $service->recurring_service_currency }} 
                                                        ${{ $service->recurring_service_currency_value }}/{{ $service->recurring_service_currency_value_two }} 
                                                        {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}
                                                    </option>
                                                @else
                                                    <option data-parent-services="{{ $service->parent_services ?? '' }}" data-type="recurring" data-price="{{$service->recurring_service_currency_value}}" value="{{ $service->id }}">
                                                        {{ $service->service_name }} {{ $service->recurring_service_currency }} 
                                                        ${{ $service->recurring_service_currency_value }} / 
                                                        {{ $service->recurring_service_currency_value_two }} 
                                                        {{ $service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type }}
                                                    </option>
                                                @endif
                                            @endif
                                        @else
                                            <option data-parent-services="{{ $service->parent_services ?? '' }}" data-type="onetime" data-price="{{$service->one_time_service_currency_value}}" value="{{ $service->id }}">
                                                {{ $service->service_name }} / {{ $service->one_time_service_currency }} ${{ $service->one_time_service_currency_value }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                @if(isset($order->id)) Note: Subscription not allowed in existing order @endif
                            </div>

                            <!-- Item Name -->
                            <div class="item-name-container">
                                <label class="form-label" for="item_name">Item Name</label>
                                <input type="text" class="form-control" name="item_names[]" placeholder="Enter item name">
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="form-label" for="description">Description</label>
                                <input type="text" class="form-control" name="descriptions[]" placeholder="Enter description">
                            </div>

                            <!-- Price -->
                            <div>
                                <label class="form-label" for="price">Price</label>
                                <input type="number" class="form-control" name="prices[]" placeholder="Enter price">
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="form-label" for="quantity">Quantity</label>
                                <input type="number" class="form-control" name="quantities[]" value="1" placeholder="Enter quantity">
                            </div>

                            <div>
                                <label class="form-label" for="discounts">Discounts</label>
                                <input type="number" class="form-control" name="discounts[]" value="" placeholder="Enter discount">
                                <span class="recurring_discount" style="display:none;">Recurring discount</span>
                            </div>

                            <div class="recurring_discount_next_payment" style="display:none;">
                                <label class="form-label" for="discounts">Discounts</label>
                                <input type="number" class="form-control" name="discountsnextpayment[]" value="" placeholder="Enter discount">
                                <span>Next payment</span>
                            </div>

                            <!-- Remove Item Button -->
                            <div class="actions">
                                <button type="button" class="btn btn-danger remove-item" style="display: none;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            <hr style="grid-column: span 3;">
                        </div>
                    </template>

                    <!-- Dynamic Items Section -->
                    <div id="items-wrapper"></div>

                    <!-- Add Item Button -->
                    <button type="button" class="btn btn-success mt-2" id="add-item">+ Add Item</button>



                        <!-- Note to Client -->
                        <div class="mt-4">
                            <label class="form-label" for="note">Note to Client</label>
                            <textarea class="form-control" name="note" placeholder="Add a note for the client"></textarea>
                        </div>

                        <!-- Additional Options with Dynamic Fields -->
                        <!-- Send Email Notification -->
                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="send_email" name="send_email">
                                <label class="form-check-label" for="send_email">Send email notification</label>
                            </div>
                        </div>

                        <!-- Partial Payment -->
                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="partial_payment" name="partial_payment" data-toggle="upfront_payment_field">
                                <label class="form-check-label" for="partial_payment">Partial upfront payment</label>
                            </div>
                            <div class="hidden-field" id="upfront_payment_field">
                                <input type="number" class="form-control mt-2" name="upfront_payment_amount" placeholder="Enter upfront payment amount">
                            </div>
                        </div>

                        <!-- Custom Billing Date -->
                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="custom_billing_date" name="custom_billing_date" data-toggle="billing_date_field">
                                <label class="form-check-label" for="custom_billing_date">Custom billing date</label>
                            </div>
                            <div class="hidden-field" id="billing_date_field">
                                <input type="date" class="form-control mt-2" name="billing_date">
                                <p>If you want the Stripe subscription to start at a specific date. Current date will be used if the invoice is not paid before this billing date.</p>
                            </div>
                        </div>

                        <!-- Custom Currency -->
                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="custom_currency" name="custom_currency" data-toggle="currency_field">
                                <label class="form-check-label" for="custom_currency">Custom currency</label>
                            </div>
                            <div class="hidden-field" id="currency_field">
                                <input type="text" class="form-control mt-2" name="currency" placeholder="Enter currency code (e.g., USD, CAD)">
                            </div>
                        </div>



                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary add_invoice">Add Invoice</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add click event listener to the "Add Invoice" button
        document.querySelector('.add_invoice').addEventListener('click', function (e) {
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
                const parentList = selectedOption.getAttribute('data-parent-services')?.trim();
                console.log('parentList', parentList, select.value);

                // Check if the selected option has recurring or recurringwithtrail
                if (dataType === 'recurring' || dataType === 'recurringwithtrail') {
                    hasRecurringService = true;
                }
            });

            // Check if upfront payment amount is filled
            const upfrontPaymentValue = upfrontPaymentInput.value.trim();

            // Validation: If recurring service and upfront payment are used
            if (hasRecurringService && upfrontPaymentValue !== '') {
                notify.error("Partial payments cannot be used with recurring services.!");
            } else {

                // 1. collect all selected service IDs
                const selectedIds = Array.from(serviceSelects)
                .map(sel => sel.value)
                .filter(v => v);

                // 2. check each addon for at least one parent in selectedIds
                let addonValid = true;
                serviceSelects.forEach(sel => {
                    const opt = sel.options[sel.selectedIndex];
                    const parentList = opt.getAttribute('data-parent-services')?.trim();
                    if (!parentList) return;         // not an addon, skip

                    // split into an array of parent IDs
                    const parents = parentList.split(',').map(id => id.trim());

                    // if none of those parents are in selectedIds → invalid
                    if (!parents.some(pid => selectedIds.includes(pid))) {
                        addonValid = false;
                    }
                });

                if (!addonValid) {
                    e.preventDefault();
                    notify.error("One or more addons require at least one of their parent services to be selected");
                    return;
                }

                e.target.closest('form').submit();
            }
        });
    });
</script>

<script>

    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 0; // To keep track of item IDs

        // Function to add a new item
        function addItem(defaultItem = false) {
            const template = document.getElementById('item-template').content.cloneNode(true);

            // Give the new item a unique ID
            const newItem = document.createElement('div');
            newItem.classList.add('item-wrapper');
            newItem.setAttribute('id', 'item-' + itemIndex); // Set unique id for each new item
            newItem.appendChild(template); // Append the cloned template

            // If it's not the default item, show the remove button
            if (!defaultItem) {
                newItem.querySelector('.remove-item').style.display = 'block';
            }

            // Append the new item to the wrapper
            document.getElementById('items-wrapper').appendChild(newItem);

            // Add event listener for removing the item
            newItem.querySelector('.remove-item').addEventListener('click', function () {
                newItem.remove(); // Remove the item
            });

            itemIndex++; // Increment the index for unique IDs
        }

        // Add default item when the page loads (without remove button)
        addItem(true);

        // Add new item on button click
        document.getElementById('add-item').addEventListener('click', function () {
            addItem(); // Call function to add new item
        });
    });

    document.addEventListener('DOMContentLoaded', function () {

        // Toggle item name field based on service selection
        document.addEventListener('change', function (e) {
            if (e.target && e.target.classList.contains('service-select')) {
                const itemNameContainer = e.target.closest('.item-group').querySelector('.item-name-container');
                if (e.target.value) {
                    // If a service is selected, hide the item name field
                    itemNameContainer.classList.add('hidden-field');
                } else {
                    // If no service is selected, show the item name field
                    itemNameContainer.classList.remove('hidden-field');
                }
            }
        });

        // Toggle additional fields based on checkbox selection
        document.querySelectorAll('.toggle-field').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const target = document.getElementById(checkbox.dataset.toggle);
                if (checkbox.checked) {
                    target.classList.remove('hidden-field');
                } else {
                    target.classList.add('hidden-field');
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Function to toggle item name visibility based on service selection
        function toggleItemName(serviceSelect) {
            const itemNameContainer = serviceSelect.closest('.item-group').querySelector('.item-name-container');
            const itemNameContainer2 = serviceSelect.closest('.item-group');

            if (serviceSelect.value) {
                // Hide the item name if a service is selected
                itemNameContainer.style.display = 'none';
            } else {
                // Show the item name if no service is selected
                itemNameContainer.style.display = 'block';
            }

            const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
            const dataType = selectedOption.dataset.type;
            const price = selectedOption.dataset.price;
            const recurringDiscountSpan = itemNameContainer2.querySelector('.recurring_discount');
            const recurringDiscountSpan2 = itemNameContainer2.querySelector('.recurring_discount_next_payment');
            const priceField = itemNameContainer2.querySelector('[name="prices[]"]'); // Target the price input field

            // Update the price field
            if (priceField) {
                priceField.value = price;
            }

            if(dataType=='recurringwithtrail'){
                recurringDiscountSpan.style.display = 'block';
                recurringDiscountSpan2.style.display = 'block';
                recurringDiscountSpan.textContent = "First Discount";
            } else if (dataType === 'recurring') {
                recurringDiscountSpan2.style.display = 'none';
                recurringDiscountSpan.style.display = 'block';
                recurringDiscountSpan.textContent = "Recurring discount";

                const inputField = recurringDiscountSpan2.querySelector('input');
                if (inputField) {
                    inputField.value = '';
                }
            } else {
                recurringDiscountSpan2.style.display = 'none';
                recurringDiscountSpan.style.display = 'none';
            }
        }

        // Apply to all current service selects
        function addEventListeners() {
            document.querySelectorAll('.service-select').forEach(function (serviceSelect) {
                serviceSelect.addEventListener('change', function () {
                    toggleItemName(serviceSelect);
                });

                // Initial check for each service select on page load
                toggleItemName(serviceSelect);
            });
        }

        // Run event listeners for existing items
        addEventListeners();

        // Handle the addition of new items dynamically
        const addItemButton = document.getElementById('add-item');
        const itemTemplate = document.getElementById('item-template').cloneNode(true);
        let itemIndex = 1;

        addItemButton.addEventListener('click', function () {
            const newItem = itemTemplate.cloneNode(true);
            newItem.id = 'item-' + itemIndex;
            document.getElementById('items-wrapper').appendChild(newItem);
            itemIndex++;

            // Apply event listeners to the newly added item
            addEventListeners();
        });
    });


</script>

@endsection
