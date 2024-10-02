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
            <div class="subscription-header">
                <div>
                    <h5>Subscription #{{ $subscription->id }}</h5>
                    <p><strong>Status:</strong> {{ $subscription->status }}</p>
                </div>
                <button class="btn btn-danger">Download</button>
            </div>
            
            <!-- Subscription Information -->
            <div class="subscription-info mt-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="subscription-info-item">
                            <h6>Up Digital</h6>
                            <p>
                                4014 Kennedy Close SW<br>
                                Edmonton, AB T6W 3B1<br>
                                Canada
                            </p>
                        </div>
                        <div class="subscription-info-item">
                            <h6>Invoiced To:</h6>
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
                                    <td>#{{ strtoupper(uniqid()) }}</td> <!-- Example of a unique ID -->
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
                            <td>{{ $item->item_name }}</td>
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

@endsection
