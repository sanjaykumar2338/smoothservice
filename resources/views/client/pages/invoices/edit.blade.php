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
                                            <option value="{{ $service->id }}" {{ $item->service_id == $service->id ? 'selected' : '' }}>
                                                {{ $service->service_name }}
                                            </option>
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
                <button type="submit" class="btn btn-primary">Update Invoice</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = {{ count($invoice->items) }}; // Set initial index based on existing items

        // Add new item on button click
        document.getElementById('add-item').addEventListener('click', function () {
            const itemTemplate = document.getElementById('item-0').cloneNode(true);
            itemTemplate.setAttribute('id', 'item-' + itemIndex); // Set unique id for each new item
            itemTemplate.querySelectorAll('input').forEach(input => input.value = ''); // Clear all inputs in the cloned template
            itemTemplate.querySelector('.remove-item').style.display = 'block'; // Show delete button for cloned items
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
