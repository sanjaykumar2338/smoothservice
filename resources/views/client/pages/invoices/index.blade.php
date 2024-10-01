@extends('client.client_template')
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
                        <form action="{{ route('invoices.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by invoice name or client" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    @if(checkPermission('add_edit_delete_invoice'))
                    <div class="dt-buttons">
                        &nbsp;
                        <button onclick="window.location.href='{{ route('invoices.create') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Invoice</span></span>
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
                        <th>Service</th>
                        <th>Total</th>
                        <th>Due Date</th>
                        @if(checkPermission('add_edit_delete_invoice'))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($invoices->count() > 0)
                        @foreach($invoices as $invoice)
                        <tr>
                            <th scope="row">{{ $invoice->id }}</th>
                            <td>{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</td>
                            <td>{{ $invoice->service->service_name }}</td>
                            <td>{{ $invoice->total }}</td>
                            <td>{{ $invoice->due_date }}</td>
                            @if(checkPermission('add_edit_delete_invoice'))
                            <td>
                                <!-- Edit Button -->
                                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                <!-- Delete Button -->
                                <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="6" class="text-center">No invoices found.</td></tr>
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
