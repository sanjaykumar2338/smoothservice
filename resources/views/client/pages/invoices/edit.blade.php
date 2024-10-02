@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
    .hidden-field {
        display: none;
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
        <span class="text-muted fw-light">Invoices /</span> Edit 
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
                        

                        <div class="row">
                        <!-- Client -->
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
                                <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date',$invoice->due_date) }}">
                            </div>
                        </div>
                    

                        <!-- Dynamic Items Section -->
                        <div id="items-wrapper">
                            @foreach($invoice->items as $index => $item)
                            <div class="grid-container item-group mt-4" id="item-{{ $index }}">
                                <!-- Service Dropdown -->
                                <div>
                                    <label class="form-label" for="service_id">Service</label>
                                    <select class="form-control service-select" name="service_id[]" id="service-select">
                                        <option value="">-- No Service --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ $item->service_id == $service->id ? 'selected' : '' }}>
                                                {{ $service->service_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Item Name -->
                                <div class="item-name-container {{ $item->service_id ? 'hidden-field' : '' }}" id="item-name-container">
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
                                <input class="form-check-input toggle-field" type="checkbox" id="send_email" name="send_email" {{ $invoice->send_email ? 'checked' : '' }}>
                                <label class="form-check-label" for="send_email">Send email notification</label>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="partial_payment" name="partial_payment" data-toggle="upfront_payment_field" {{ $invoice->upfront_payment_amount ? 'checked' : '' }}>
                                <label class="form-check-label" for="partial_payment">Partial upfront payment</label>
                            </div>
                            <div class="hidden-field" id="upfront_payment_field">
                                <input type="number" class="form-control mt-2" name="upfront_payment_amount" value="{{ $invoice->upfront_payment_amount }}" placeholder="Enter upfront payment amount">
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input toggle-field" type="checkbox" id="custom_billing_date" name="custom_billing_date" data-toggle="billing_date_field" {{ $invoice->billing_date ? 'checked' : '' }}>
                                <label class="form-check-label" for="custom_billing_date">Custom billing date</label>
                            </div>
                            <div class="hidden-field" id="billing_date_field">
                                <input type="date" class="form-control mt-2" name="billing_date" value="{{ $invoice->billing_date }}">
                            </div>
                        </div>

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
                <button type="submit" class="btn btn-primary">Update Invoice</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = {{ count($invoice->items) }};
        const itemTemplate = document.getElementById('item-0').cloneNode(true); // Clone the first item as a template

        document.getElementById('add-item').addEventListener('click', function () {
            const newItem = itemTemplate.cloneNode(true);
            newItem.setAttribute('id', 'item-' + itemIndex);
            newItem.querySelectorAll('input').forEach(input => input.value = ''); // Clear inputs
            newItem.querySelector('select').selectedIndex = 0;
            document.getElementById('items-wrapper').appendChild(newItem);
            itemIndex++;
        });

        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.closest('.item-group').remove();
            }
        });

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

        document.querySelectorAll('.toggle-field').forEach(function (checkbox) {
            const target = document.getElementById(checkbox.dataset.toggle);

            // Check if target element exists
            if (target) {
                const inputElement = target.querySelector('input, select, textarea'); // Check if the target has a field

                // Logic to show field if there's a pre-existing value in the field or the checkbox is checked
                if (checkbox.checked || (inputElement && inputElement.value)) {
                    target.classList.remove('hidden-field');
                } else {
                    target.classList.add('hidden-field');
                }

                // Attach the event listener for checkbox change
                checkbox.addEventListener('change', function () {
                    if (checkbox.checked) {
                        target.classList.remove('hidden-field');
                    } else {
                        target.classList.add('hidden-field');
                    }
                });
            } else {
                console.warn(`Target element with ID ${checkbox.dataset.toggle} not found`);
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // Function to toggle item name visibility based on service selection
        function toggleItemName(serviceSelect) {
            const itemNameContainer = serviceSelect.closest('.item-group').querySelector('.item-name-container');
            if (serviceSelect.value) {
                // Hide the item name if a service is selected
                itemNameContainer.style.display = 'none';
            } else {
                // Show the item name if no service is selected
                itemNameContainer.style.display = 'block';
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
