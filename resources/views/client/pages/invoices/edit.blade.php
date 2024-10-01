@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
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

                <!-- Error messages will be displayed here -->
                <div id="error-messages" class="alert alert-danger" style="display:none;">
                    <ul id="error-list"></ul>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Invoice Details</h5>
                    </div>
                    <div class="card-body">
                        
                        <!-- Client -->
                        <div class="mb-3">
                            <label class="form-label" for="client">Client</label>
                            <select class="form-control" id="client" name="client_id">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ $invoice->client_id == $client->id ? 'selected' : '' }}>{{ $client->first_name }} {{ $client->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dynamic Items Section -->
                        <div id="items-wrapper">
                            @foreach($invoice->items as $index => $item)
                            <div class="mb-3 item-group" id="item-{{ $index }}">
                                <label class="form-label">Item</label>
                                
                                <!-- Service Dropdown -->
                                <div class="mb-3">
                                    <label class="form-label" for="service_id">Service</label>
                                    <select class="form-control service-select" name="services[]">
                                        <option value="">-- No Service --</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ $item->service_id == $service->id ? 'selected' : '' }}>{{ $service->service_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Item Name (hidden if service selected) -->
                                <div class="mb-3 item-name-container {{ $item->service_id ? 'hidden-field' : '' }}">
                                    <label class="form-label" for="item_name">Item Name</label>
                                    <input type="text" class="form-control" name="item_names[]" value="{{ $item->item_name }}" placeholder="Enter item name">
                                </div>

                                <!-- Description -->
                                <div class="mb-3">
                                    <label class="form-label" for="description">Description</label>
                                    <input type="text" class="form-control" name="descriptions[]" value="{{ $item->description }}" placeholder="Enter description">
                                </div>

                                <!-- Price -->
                                <div class="mb-3">
                                    <label class="form-label" for="price">Price</label>
                                    <input type="number" class="form-control" name="prices[]" value="{{ $item->price }}" placeholder="Enter price">
                                </div>

                                <!-- Quantity -->
                                <div class="mb-3">
                                    <label class="form-label" for="quantity">Quantity</label>
                                    <input type="number" class="form-control" name="quantities[]" value="{{ $item->quantity }}" placeholder="Enter quantity">
                                </div>

                                <!-- Remove Item Button -->
                                <button type="button" class="btn btn-danger remove-item" {{ $index == 0 ? 'style=display:none;' : '' }}>Remove Item</button>
                                <hr>
                            </div>
                            @endforeach
                        </div>

                        <!-- Add Item Button -->
                        <button type="button" class="btn btn-success mt-2" id="add-item">+ Add Item</button>

                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Invoice</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let itemIndex = {{ count($invoice->items) }}; // To track number of items based on existing ones
        const itemTemplate = document.getElementById('item-0').cloneNode(true); // Clone the first item as template
        document.getElementById('item-0').querySelector('.remove-item').style.display = 'none'; // Hide the delete button on the original

        // Add new item on button click
        document.getElementById('add-item').addEventListener('click', function () {
            const newItem = itemTemplate.cloneNode(true);
            newItem.setAttribute('id', 'item-' + itemIndex); // Set unique id for each new item
            newItem.querySelector('.remove-item').style.display = 'block'; // Show delete button for cloned items

            // Clear the input fields in the new cloned item
            newItem.querySelectorAll('input').forEach(input => input.value = '');
            newItem.querySelector('select').selectedIndex = 0;

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
    });
</script>

@endsection
