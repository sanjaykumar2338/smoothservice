@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Landing Pages /</span> Landing Pages List
    </h4>

    <div class="card">
        <h5 class="card-header">Landing Pages</h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('landingpage.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search.." aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    @if(checkPermission('add_edit_delete_landing_pages'))
                        <div class="dt-buttons">
                            &nbsp;
                            <button onclick="window.location.href='{{ route('landingpage.add') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                                <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Landing Page</span></span>
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
                        <th>Title</th>
                        <th>Slug</th>
                        <th>Visible</th>
                        <th>Show in Sidebar</th>
                        <th>Show Coupon Field</th>
                        <th>Link</th>
                        @if(checkPermission('add_edit_delete_landing_pages'))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($landingPages->count() > 0)
                        @foreach($landingPages as $landingPage)
                        <tr>
                            <th scope="row">{{ $landingPage->id }}</th>
                            <td>{{ Str::limit($landingPage->title, 50, '...') }}</td>
                            <td>{{ $landingPage->slug }}</td>
                            <td>{{ $landingPage->is_visible ? 'Yes' : 'No' }}</td>
                            <td>{{ $landingPage->show_in_sidebar ? 'Yes' : 'No' }}</td>
                            <td>{{ $landingPage->show_coupon_field ? 'Yes' : 'No' }}</td>
                            
                            <td>
                                <a href="{{ url('/order/payment/' . $landingPage->landing_no) }}" target="_blank">
                                    Preview Or Link
                                </a>
                            </td>

                            @if(checkPermission('add_edit_delete_landing_pages'))
                            <td>
                                <a href="{{ route('landingpage.design', $landingPage->slug) }}" class="btn btn-sm btn-primary" target="_blank">Design Page</a>
                                <a href="{{ route('landingpage.edit', $landingPage->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('landingpage.destroy', $landingPage->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="7">No record found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
        <nav>
            <ul class="pagination justify-content-center">
                @if ($landingPages->onFirstPage())
                    <li class="page-item disabled"><span class="page-link">Previous</span></li>
                @else
                    <li class="page-item"><a class="page-link" href="{{ $landingPages->previousPageUrl() }}" rel="prev">Previous</a></li>
                @endif

                @for ($i = 1; $i <= $landingPages->lastPage(); $i++)
                    @if ($i == $landingPages->currentPage())
                        <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $landingPages->url($i) }}">{{ $i }}</a></li>
                    @endif
                @endfor

                @if ($landingPages->hasMorePages())
                    <li class="page-item"><a class="page-link" href="{{ $landingPages->nextPageUrl() }}" rel="next">Next</a></li>
                @else
                    <li class="page-item disabled"><span class="page-link">Next</span></li>
                @endif
            </ul>
        </nav>
        </div>
    </div>
</div>

@endsection
