@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Services /</span> Intake Forms List
    </h4>

    <div class="card">
        <h5 class="card-header">Intake Forms</h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('service.intakeform.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
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
                        <button onclick="window.location.href='{{ route('service.intakeform.add') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Intake Form</span></span>
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
                        <th>Form Name</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($intakeforms->count() > 0)
                        @foreach($intakeforms as $index => $intakeform)
                        <tr>
                            <td scope="row">{{$index + $intakeforms->firstItem()}}</td>
                            <td>{{ $intakeform->form_name }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <a href="{{ route('service.intakeform.edit', $intakeform->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('intakeform.destroy', $intakeform->id) }}" method="POST" style="display:inline-block;">
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
                @if ($intakeforms->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $intakeforms->previousPageUrl() }}" rel="prev">Previous</a></li>
                @endif

                @for ($i = 1; $i <= $intakeforms->lastPage(); $i++)
                    @if ($i == $intakeforms->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $intakeforms->url($i) }}">{{ $i }}</a></li>
                    @endif
                @endfor

                @if ($intakeforms->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $intakeforms->nextPageUrl() }}" rel="next">Next</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                @endif
            </ul>
        </nav>
        </div>
    </div>
</div>

@endsection
