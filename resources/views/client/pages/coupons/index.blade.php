@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Coupons /</span> Coupon List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Coupons
            <span class="badge bg-primary">{{ $coupons->total() }} Total Coupons</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('coupon.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by coupon code or description" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    @if(checkPermission('add_edit_delete_coupon'))
                    <div class="dt-buttons">
                        &nbsp;
                        <button onclick="window.location.href='{{ route('coupon.add') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Coupon</span></span>
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
                        <th>Coupon Code</th>
                        <th>Description</th>
                        <th>Discount Type</th>
                        <th>Discount Duration</th>
                        @if(checkPermission('add_edit_delete_coupon'))
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($coupons->count() > 0)
                        @foreach($coupons as $coupon)
                        <tr>
                            <th scope="row">{{ $coupon->id }}</th>
                            <td>{{ $coupon->coupon_code }}</td>
                            <td>{{ $coupon->description }}</td>
                            <td>{{ $coupon->discount_type == 'Fixed' ? 'Fixed Amount' : 'Percentage' }}</td>
                            <td>{{ $coupon->discount_duration }}</td>
                            @if(checkPermission('add_edit_delete_coupon'))
                            <td>
                                <!-- Edit Button -->
                                <a href="{{ route('coupon.edit', $coupon->id) }}" class="btn btn-sm btn-primary">Edit</a>

                                <!-- Delete Button -->
                                <form action="{{ route('coupon.destroy', $coupon->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="7" class="text-center">No coupons found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($coupons->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $coupons->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $coupons->lastPage(); $i++)
                        @if ($i == $coupons->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $coupons->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($coupons->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $coupons->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

@endsection
