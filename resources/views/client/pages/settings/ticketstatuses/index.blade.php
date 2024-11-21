@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Ticket Statuses /</span> Ticket Statuses List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Ticket Statuses
            <span class="badge bg-primary">{{ $ticketStatuses->total() }} Total Statuses</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('setting.ticketstatuses.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by ticket status" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    <div class="dt-buttons">
                        &nbsp;
                        <button onclick="window.location.href='{{ route('setting.ticketstatuses.create') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Ticket Status</span></span>
                        </button>
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
                        <th>Status Name</th>
                        <th>Description</th>
                        <th>Color</th>
                        <th>Default</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($ticketStatuses->count() > 0)
                        @foreach($ticketStatuses as $status)
                        <tr>
                            <th scope="row">{{ $status->id }}</th>
                            <td>{{ $status->name }}</td>
                            <td>{{ $status->description }}</td>
                            <td>
                                <span style="background-color: {{ $status->color }}; padding: 5px; border-radius: 50%;">&nbsp;</span>
                                {{ ucfirst($status->color) }}
                            </td>
                            <td>{{ $status->is_default == 1 ? 'Yes':'No' }}</td>
                            <td>
                                <!-- Edit Button -->
                                <a href="{{ route('setting.ticketstatuses.edit', $status->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                <!-- Delete Button -->
                                <form action="{{ route('setting.ticketstatuses.delete', $status->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="5" class="text-center">No record found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($ticketStatuses->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $ticketStatuses->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $ticketStatuses->lastPage(); $i++)
                        @if ($i == $ticketStatuses->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $ticketStatuses->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($ticketStatuses->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $ticketStatuses->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
