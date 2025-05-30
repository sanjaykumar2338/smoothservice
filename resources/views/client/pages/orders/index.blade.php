@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Orders /</span> Order List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Orders
            <span class="badge bg-primary">{{ $orders->total() }} Total Orders</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('order.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by client name or service" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    <div class="dt-buttons">
                        &nbsp;
                        <!-- Button to trigger modal for adding an order -->
                        @if (getUserType() == 'web')
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addOrderModal">
                                <i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Order</span>
                            </button>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr class="text-nowrap">
                        <th style="display:none"><input type="checkbox" id="select-all"></th>
                        <th>ID</th>
                        <th>Order Title</th>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('order.show', $order->order_no) }}'">
                            <td style="display:none"><input type="checkbox" class="order-checkbox" value="{{ $order->id }}"></td>
                            <th scope="row"><a href="{{ route('order.show', $order->order_no) }}">{{ $order->order_no }}</a></th>
                            <td>{{ $order->title }}</td>
                            <td>{{ $order->client->first_name }} {{ $order->client->last_name }}</td>
                            <td>{{ $order->service?->service_name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($order->status) }}</td>
                            @php
                                $format = $order->created_at->year === now()->year ? 'M j' : 'M j, Y';
                            @endphp
                            <td>{{ $order->created_at->format($format) }}</td>
                            <td>
                                <!-- Edit Button -->
                                <a style="display:none;" href="{{ route('order.edit', $order->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                <a href="{{ route('order.show', $order->order_no) }}" class="btn btn-sm btn-info">Details</a>

                                <!-- Delete Button -->
                                @if(checkPermission('delete_order'))
                                    <form action="{{ route('order.destroy', $order->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="7" class="text-center">No orders found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($orders->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $orders->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $orders->lastPage(); $i++)
                        @if ($i == $orders->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $orders->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($orders->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $orders->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal for adding new order -->
<div class="modal" id="addOrderModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('order.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addOrderModalLabel">Add Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Client Selection -->
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Select Client</label>
                        <select class="form-control" id="client_id" name="client_id" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->first_name }} {{ $client->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Service Selection -->
                    <div class="mb-3">
                        <label for="service_id" class="form-label">Select Service</label>
                        <select class="form-control" id="service_id" name="service_id" required>
                            <option value="">-- Select Service --</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->service_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Add a note for your team -->
                    <div class="mb-3">
                        <label for="note" class="form-label">Add a Note for Your Team</label>
                        <textarea class="form-control" id="note" name="note" rows="3" placeholder="Add a note for your team..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Order</button>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
