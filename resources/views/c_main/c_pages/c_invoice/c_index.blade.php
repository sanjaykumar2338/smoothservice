@extends('c_main.c_dashboard')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Invoices /</span> Invoice List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Invoices
            <span class="badge bg-primary">{{ $invoices->total() }} Total Invoices</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('portal.invoices') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by note, billing first name or last name" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
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
                        <th>Date</th>
                        <th>Total</th>
                        <th>Payment Status</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($invoices->count() > 0)
                        @foreach($invoices as $invoice)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('portal.invoices.show', $invoice->id) }}'">
                            <th scope="row"><a href="{{ route('portal.invoices.show', $invoice->id) }}">{{ $invoice->invoice_no }}</a></th>
                            <td>{{ $invoice->created_at->format('M d, Y') }}</td>
                            <td>${{ number_format($invoice->total, 2) }}</td>
                            <td>{{ $invoice->paid_at ? 'Paid On '.$invoice->paid_at->format('M d, Y') : 'Unpaid' }}</td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="7" class="text-center">No invoices found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($invoices->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $invoices->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $invoices->lastPage(); $i++)
                        @if ($i == $invoices->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $invoices->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($invoices->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $invoices->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
