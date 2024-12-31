@extends('c_main.c_dashboard')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Subscriptions /</span> Subscription List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Subscriptions
            <span class="badge bg-primary">{{ $subscriptions->total() }} Total Subscriptions</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('portal.invoice.subscription') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by client name or note" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
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
                        <th>Invoice</th>
                        <th>Amount</th>
                        <th>Interval</th>
                        <th>Payment Method</th>
                        <th>Starts At</th>
                        <th>Ends At</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($subscriptions->count() > 0)
                        @foreach($subscriptions as $subscription)
                        <tr>
                            <td><a href="{{ route('portal.invoices.show', $subscription->invoice_id) }}">{{ $subscription->invoice->invoice_no }}</a></td>
                            <td>${{ number_format($subscription->amount, 2) }} {{ strtoupper($subscription->currency) }}</td>
                            <td>{{ ucfirst($subscription->intervel) }}</td>
                            <td>{{ ucfirst($subscription->payment_by) }}</td>
                            <td>{{ $subscription->starts_at ? \Carbon\Carbon::parse($subscription->starts_at)->format('M d, Y') : 'N/A' }}</td>
                            <td>{{ $subscription->ends_at ? \Carbon\Carbon::parse($subscription->ends_at)->format('M d, Y') : 'Ongoing' }}</td>
                            <td class="text-end">
                                @if(!$subscription->cancelled_at)
                                    
                                    @if($subscription->payment_by=='stripe' || $subscription->payment_by=="")
                                        <form action="{{ route('portal.subscriptions.cancel', $subscription->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this subscription?')">Cancel</button>
                                        </form>
                                    @endif

                                    @if($subscription->payment_by=='paypal')
                                        <form action="{{ route('portal.paypal.cancel.subscription', $subscription->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this subscription?')">Cancel</button>
                                        </form>
                                    @endif

                                @else
                                <span class="badge bg-secondary">Cancelled on {{ \Carbon\Carbon::parse($subscription->cancelled_at)->format('M d, Y') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No subscriptions found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($subscriptions->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $subscriptions->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $subscriptions->lastPage(); $i++)
                        @if ($i == $subscriptions->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $subscriptions->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($subscriptions->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $subscriptions->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
