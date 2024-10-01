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

                <!-- Error messages will be displayed here -->
                <div id="error-messages" class="alert alert-danger" style="display:none;">
                    <ul id="error-list"></ul>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Invoice Details</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Client -->
                        <div class="mb-0">
                            <label class="form-label" for="client">Client</label>
                            <select class="form-control" id="client" name="client_id">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Items Section -->
                        <div id="items-wrapper">
                            <div class="grid-container item-group mt-4" id="item-template">
                                <!-- Service Dropdown -->
                                <div>
                                    <label class="form-label" for="service_id">Service</label>
                                    <select class="form-control service-select" name="service_id">
                                        <option value="">-- No Service --</option> <!-- Optional empty option -->
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                                        @endforeach
                                    </select>
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

                                <!-- Remove Item Button -->
                                <div class="actions">
                                <button type="button" class="btn btn-danger remove-item" style="display:none">
    <i class="fas fa-trash"></i>
</button>

                                </div>
                            </div>

                            <hr style="grid-column: span 3;">
                        </div>

                        <!-- Add Item Button -->
                        <button type="button" class="btn btn-success mt-2" id="add-item">+ Add Item</button>

                        <!-- Note to Client -->
                        <div class="mt-4">
                            <label class="form-label" for="note">Note to Client</label>
                            <textarea class="form-control" name="note" placeholder="Add a note for the client"></textarea>
                        </div>

                        <!-- Additional Options with Dynamic Fields -->
                        <div class="mt-4">
                            <input type="checkbox" id="send_email" name="send_email">
                            <label for="send_email">Send email notification</label>
                        </div>

                        <div class="mt-4">
                            <input type="checkbox" id="partial_payment" name="partial_payment" class="toggle-field" data-toggle="upfront_payment_field">
                            <label for="partial_payment">Partial upfront payment</label>
                            <div class="hidden-field" id="upfront_payment_field">
                                <input type="number" class="form-control mt-2" name="upfront_payment_amount" placeholder="Enter upfront payment amount">
                            </div>
                        </div>

                        <div class="mt-4">
                            <input type="checkbox" id="custom_billing_date" name="custom_billing_date" class="toggle-field" data-toggle="billing_date_field">
                            <label for="custom_billing_date">Custom billing date</label>
                            <div class="hidden-field" id="billing_date_field">
                                <input type="date" class="form-control mt-2" name="billing_date">
                            </div>
                        </div>

                        <div class="mt-4">
                            <input type="checkbox" id="custom_currency" name="custom_currency" class="toggle-field" data-toggle="currency_field">
                            <label for="custom_currency">Custom currency</label>
                            <div class="hidden-field" id="currency_field">
                                <input type="text" class="form-control mt-2" name="currency" placeholder="Enter currency code (e.g., USD, CAD)">
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Add Invoice</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = 1; // To track number of items
        const itemTemplate = document.getElementById('item-template').cloneNode(true);
        document.getElementById('item-template').querySelector('.remove-item').style.display = 'none'; // Hide the delete button on the original

        // Add new item on button click
        document.getElementById('add-item').addEventListener('click', function () {
            const newItem = itemTemplate.cloneNode(true);
            newItem.setAttribute('id', 'item-' + itemIndex); // Set unique id for each new item
            newItem.querySelector('.remove-item').style.display = 'block'; // Show delete button for cloned items
            document.getElementById('items-wrapper').appendChild(newItem);
            itemIndex++;
        });

        // Remove item on clicking remove button
        document.addEventListener('click', function (e) {
            if (e.target && e.target.classList.contains('remove-item')) {
                e.target.closest('.item-group').remove();
            }
        });

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
</script>

@endsection
