@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Clients /</span> Client List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Clients
            <span class="badge bg-primary">{{ $clients->total() }} Total Clients</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('client.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by name or email" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    @if(checkPermission('add_edit_login_clients'))
                    <div class="dt-buttons">
                        &nbsp;
                        <button onclick="window.location.href='{{ route('client.add') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Client</span></span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        <br>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Billing Address</th>
                        <th>Country</th>
                        @if(checkPermission('add_edit_login_clients') || checkPermission('delete_clients'))
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($clients->count() > 0)
                        @foreach($clients as $client)
                        <tr>
                            <th scope="row">{{ $client->id }}</th>
                            <td>{{ $client->first_name }}</td>
                            <td>{{ $client->last_name }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->company }}</td>
                            <td>{{ $client->billing_address }}</td>
                            <td>{{ $client->country }}</td>
                            <td>
                                <!-- Edit Button -->
                                @if(checkPermission('add_edit_login_clients'))
                                <a href="{{ route('client.edit', $client->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                @endif

                                @if(checkPermission('delete_clients'))
                                <!-- Delete Button -->
                                <form action="{{ route('client.destroy', $client->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                                @endif

                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No clients found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($clients->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $clients->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $clients->lastPage(); $i++)
                        @if ($i == $clients->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $clients->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($clients->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $clients->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
