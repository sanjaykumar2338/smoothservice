@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Services /</span> Services List
    </h4>

    <div class="card">
        <h5 class="card-header">Services</h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('client.service.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search.." aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    <div class="dt-buttons">
                        &nbsp;
                        <button style="display:none" class="dt-button buttons-collection dropdown-toggle btn btn-label-secondary mx-3" tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog" aria-expanded="false">
                            <span><i class="bx bx-export me-1"></i>Export</span><span class="dt-down-arrow">▼</span>
                        </button>
                        <button onclick="window.location.href='{{ route('client.service.add') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Service</span></span>
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
                        <th>Service Name</th>
                        <th>Addon</th>
                        <th>Group Multiple</th>
                        <th>Assign Team Member</th>
                        <th>Set Deadline</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($services->count() > 0)
                        @foreach($services as $service)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $service->service_name }}</td>
                            <td>{{ $service->addon ? 'Yes' : 'No' }}</td>
                            <td>{{ $service->group_multiple ? 'Yes' : 'No' }}</td>
                            <td>{{ $service->assign_team_member ? 'Yes' : 'No' }}</td>
                            <td>{{ $service->set_deadline_check ? ($service->set_a_deadline . ' ' . $service->set_a_deadline_duration) : 'No' }}</td>
                            <td>
                                <a href="{{ route('client.service.edit', $service->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('client.service.destroy', $service->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td>No record found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
        <nav>
            <ul class="pagination justify-content-center">
                @if ($services->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $services->previousPageUrl() }}" rel="prev">Previous</a></li>
                @endif

                @for ($i = 1; $i <= $services->lastPage(); $i++)
                    @if ($i == $services->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $services->url($i) }}">{{ $i }}</a></li>
                    @endif
                @endfor

                @if ($services->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $services->nextPageUrl() }}" rel="next">Next</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                @endif
            </ul>
        </nav>
        </div>
    </div>
</div>

@endsection
