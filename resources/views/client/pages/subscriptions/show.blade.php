@extends('client.client_template')
@section('content')

<style>
    .subscription-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .subscription-header h4 {
        margin: 0;
    }
    .subscription-info {
        margin-top: 20px;
    }
    .subscription-info-item {
        margin-bottom: 10px;
    }
    .table-borderless th, .table-borderless td {
        border: 0;
    }
    .history-entry {
        margin-top: 10px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Subscriptions /</span> {{ $subscription->id }}
    </h4>

    <div class="card mb-4">
        <div class="card-body">
            <!-- Subscription Header -->
            <div class="subscription-header d-flex justify-content-between align-items-center">
                <div>
                    <h5>Subscription #{{ $subscription->id }}</h5>
                    <p><strong>Status:</strong> {{ $subscription->status }}</p>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-danger me-2" onclick="window.location.href='{{ route('subscriptions.download', $subscription->id) }}'">Download</button>
                    <div class="dropdown">
                        <button
                            type="button"
                            class="btn dropdown-toggle hide-arrow p-0"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="bx bx-dots-vertical-rounded"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#addOrderModal">Share</a></li>
                            <li><a class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#emailInvoiceModal">Email Subscription</a></li>
                            <li><a class="dropdown-item" href="{{ route('subscriptions.edit', $subscription->id) }}">Edit</a></li>
                            <li><a class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#updateAddressModal">Update address</a></li>
                            <li><a class="dropdown-item" href="" data-bs-toggle="modal" data-bs-target="#refundInvoiceModal">Refund</a></li>
                            <li><a class="dropdown-item" href="{{ route('subscriptions.duplicate', $subscription->id) }}">Duplicate</a></li>
                            <li><a class="dropdown-item"  onclick="return confirm('Are you sure?')" href="{{ route('subscriptions.deletesubscription', $subscription->id) }}">Delete</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Subscription Information -->
            <div class="subscription-info mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="subscription-info-item">
                            <h6>{{env('APP_NAME')}}</h6>
                            <p>
                                4014 Kennedy Close SW<br>
                                Edmonton, AB T6W 3B1<br>
                                Canada
                            </p>
                        </div>
                        <div class="subscription-info-item">
                            <h6>Subscribed To:</h6>
                            <p>{{ $subscription->client->first_name }} {{ $subscription->client->last_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tbody>
                                <tr>
                                    <th>Number:</th>
                                    <td>{{ $subscription->id }}</td>
                                </tr>
                                <tr>
                                    <th>Unique ID:</th>
                                    <td>#{{ strtoupper(uniqid()) }}</td>
                                </tr>
                                <tr>
                                    <th>Issued:</th>
                                    <td>{{ $subscription->created_at->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Paid:</th>
                                    <td>{{ $subscription->paid_at ? $subscription->paid_at->format('M d, Y') : 'Unpaid' }}</td>
                                </tr>
                                <tr>
                                    <th>Payment Method:</th>
                                    <td>{{ $subscription->payment_method ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Subscription Items -->
            <div class="table-responsive mt-4">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subscription->items as $item)
                        <tr>
                            <td>{{ $item->service->service_name ?? $item->item_name }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>${{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                            <td>${{ number_format($subscription->total, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>${{ number_format($subscription->total, 2) }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

             <!-- Subscription History -->
             <div class="history mt-4">
                <h6>History</h6>
                <div class="history-entry">
                    <small><strong>{{ $subscription->updated_at->format('M d, Y h:i A') }}</strong></small>
                    <p>System: Subscription updated</p>
                </div>
                <div class="history-entry">
                    <small><strong>{{ $subscription->created_at->format('M d, Y h:i A') }}</strong></small>
                    <p>System: Subscription created</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding new order -->
<div class="modal" id="addOrderModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareInvoiceModalLabel">Get Subscription Links</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                <!-- Private Link Section -->
                <div class="mb-3">
                    <label for="privateLink" class="form-label">Private Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="privateLink" value="{{ route('subscriptions.show', ['id' => $subscription->id]) }}" readonly>
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('privateLink')">Copy Link</button>
                    </div>
                    <small class="text-muted">Client will need to sign in to view the subscription.</small>
                </div>

                <!-- Public Link Section -->
                <div class="mb-3">
                    <label for="publicLink" class="form-label">Public Link</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="publicLink" value="{{ route('subscriptions.public', ['id' => $subscription->id, 'key' => $subscription->public_key]) }}" readonly>
                        <button class="btn btn-outline-secondary" onclick="copyToClipboard('publicLink')">Copy Link</button>
                    </div>
                    <small class="text-muted">Anybody with this link can view and pay the subscription.</small>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Address Modal -->
<div class="modal" id="updateAddressModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('subscriptions.updateAddress', $subscription->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="updateAddressModalLabel">Edit Billing Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- First Name -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="billing_first_name" name="billing_first_name" value="{{ $subscription->billing_first_name }}" required>
                        </div>

                        <!-- Last Name -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="billing_last_name" name="billing_last_name" value="{{ $subscription->billing_last_name }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Address -->
                        <div class="col-md-12 mb-3">
                            <label for="billing_address" class="form-label">Address</label>
                            <input type="text" class="form-control" id="billing_address" name="billing_address" value="{{ $subscription->billing_address }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- City -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_city" class="form-label">City</label>
                            <input type="text" class="form-control" id="billing_city" name="billing_city" value="{{ $subscription->billing_city }}" required>
                        </div>

                        <!-- Country -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="billing_country" name="billing_country" value="{{ $subscription->billing_country }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- State -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_state" class="form-label">State</label>
                            <input type="text" class="form-control" id="billing_state" name="billing_state" value="{{ $subscription->billing_state }}" required>
                        </div>

                        <!-- Postal / Zip Code -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_postal_code" class="form-label">Postal / Zip Code</label>
                            <input type="text" class="form-control" id="billing_postal_code" name="billing_postal_code" value="{{ $subscription->billing_postal_code }}" required>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Company -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_company" class="form-label">Company</label>
                            <input type="text" class="form-control" id="billing_company" name="billing_company" value="{{ $subscription->billing_company }}">
                        </div>

                        <!-- Tax ID -->
                        <div class="col-md-6 mb-3">
                            <label for="billing_tax_id" class="form-label">Tax ID</label>
                            <input type="text" class="form-control" id="billing_tax_id" name="billing_tax_id" value="{{ $subscription->billing_tax_id }}">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Email Invoice Modal -->
<div class="modal" id="emailInvoiceModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailInvoiceLabel">Send Subscription Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="emailRecipient" class="form-label">Select Recipient(s)</label>
                    <select class="form-control select2" id="emailRecipient" name="emailRecipient[]" multiple>
                       
                            @foreach($users as $user)
                                <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                            @foreach($teamMembers as $teamMember)
                                <option value="{{ $teamMember->email }}">{{ $teamMember->name }} ({{ $teamMember->email }})</option>
                            @endforeach
                       
                    </select>
                    <small class="form-text text-muted">You can search and select multiple recipients.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="sendEmailButton">Send Email</button>
            </div>
        </div>
    </div>
</div>

<!-- Refund Subscription Modal -->
<div class="modal" id="refundInvoiceModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="refundForm" action="{{ route('subscriptions.refund', $subscription->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="refundSubscriptionLabel">Refund Subscription #{{ $subscription->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="refund_reason" class="form-label">Reason</label>
                        <input type="text" class="form-control" id="refund_reason" name="refund_reason" placeholder="Reason for refund" required>
                    </div>
                    <div class="mb-3">
                        <label for="refund_amount" class="form-label">Refund amount</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="refund_amount" name="refund_amount" placeholder="0.00" step="0.01" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        if (copyText) {
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            document.execCommand("copy");
            alert("Link copied: " + copyText.value);
        } else {
            alert('Element not found!');
        }
    }

    document.getElementById('sendEmailButton').addEventListener('click', function() {
        let selectedEmails = Array.from(document.getElementById('emailRecipient').selectedOptions).map(option => option.value);
        
        if (selectedEmails.length === 0) {
            alert('Please select at least one recipient.');
            return;
        }

        // AJAX request to send the email to selected recipients
        $.ajax({
            url: '{{ route("subscriptions.sendEmail") }}',  // Your route for sending the subscription
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                emails: selectedEmails,  // Array of selected emails
                subscriptionId: '{{ $subscription->id }}'  // Include the subscription ID
            },
            success: function(response) {
                if (response.success) {
                    alert('Subscription receipt sent successfully to the selected recipients.');
                    $('#emailInvoiceModal').modal('hide');
                } else {
                    alert('Failed to send subscription receipt. Please try again.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });

</script>

@endsection
