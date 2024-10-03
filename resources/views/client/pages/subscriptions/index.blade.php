@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
    /* Make the entire row clickable */
    tr.clickable-row {
        cursor: pointer;
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
                        <form action="{{ route('subscriptions.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by subscription name or client" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    @if(checkPermission('add_edit_delete_subscription'))
                    <div class="dt-buttons">
                        &nbsp;
                        <button onclick="window.location.href='{{ route('subscriptions.create') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Subscription</span></span>
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
                        <th>#</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Due Date</th>
                        @if(checkPermission('add_edit_delete_subscription'))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($subscriptions->count() > 0)
                        @foreach($subscriptions as $subscription)
                        <tr class="clickable-row" data-href="{{ route('subscriptions.show', $subscription->id) }}">
                            <th scope="row">{{ $subscription->id }}</th>
                            <td>{{ $subscription->client->first_name }} {{ $subscription->client->last_name }}</td>
                            <td>{{ $subscription->total }}</td>
                            <td>{{ $subscription->due_date }}</td>
                            @if(checkPermission('add_edit_delete_subscription'))
                            <td>
                                <!-- Edit Button -->
                                <a href="{{ route('subscriptions.edit', $subscription->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <a href="{{ route('subscriptions.deletesubscription', $subscription->id) }}" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="6" class="text-center">No subscriptions found.</td></tr>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Make rows clickable
        const rows = document.querySelectorAll('tr.clickable-row');
        rows.forEach(row => {
            row.addEventListener('click', function() {
                window.location.href = row.dataset.href;
            });
        });
    });
</script>

@endsection
