@extends('admin.dashboard.layout')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Tickets /</span> Ticket List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Tickets
            <span class="badge bg-primary">{{ $tickets->total() }} Total Tickets</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('admin.tickets') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by client name or service" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
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
                        <th>ID</th>
                        <th>Ticket No.</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($tickets->count() > 0)
                        @foreach($tickets as $ticket)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('admin.ticketshow', $ticket->id) }}'">
                            <th scope="row"><a href="{{ route('admin.ticketshow', $ticket->id) }}">{{ $ticket->id }}</a></th>
                            <td>{{ $ticket->ticket_no }}</td>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ ucfirst($ticket->ticket_status?->name) }}</td>
                            <td>{{ $ticket->created_at->format('M d, Y') }}</td>
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
                    @if ($tickets->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $tickets->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $tickets->lastPage(); $i++)
                        @if ($i == $tickets->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $tickets->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($tickets->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $tickets->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
