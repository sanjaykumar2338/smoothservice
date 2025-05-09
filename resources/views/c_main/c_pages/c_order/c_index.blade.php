@extends('c_main.c_dashboard')
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
                        <form action="{{ route('portal.orders') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
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
                        <th>Title</th>
                        <th>Created</th>
                        <th>Completed</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($orders->count() > 0)
                        @foreach($orders as $order)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('portal.orders.show', $order->order_no) }}'">
                            <th scope="row"><a href="{{ route('portal.orders.show', $order->order_no) }}">{{ $order->order_no }}</a></th>
                            <td>{{ $order->title }}</td>
                            @php
                                $format = $order->created_at->year === now()->year ? 'M j' : 'M j, Y';
                            @endphp
                            <td>{{ $order->created_at->format($format) }} </td>
                            <td>{{ $order->date_completed ? $order->date_completed->format('M d, Y') : '' }}</td>
                            <td>
                                @if($order->service && $order->service->intake_form && !$order->is_intake_form_data_submitted && $order->invoice_id)
                                    <a href="{{ route('portal.orders.intakeform', ['id' => $order->service->id, 'invoice' => $order->invoice_id, 'order' => $order->id]) }}">
                                        Start Order
                                    </a>
                                @else
                                    {{ ucfirst($order->status) }}
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

@endsection
