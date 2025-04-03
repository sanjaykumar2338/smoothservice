@extends('admin.dashboard.layout')
    @section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
    <!-- Website Analytics-->
    <div class="col-lg-12 col-md-12 mb-4">
        <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Dashboard</h5>
        </div>
        <div class="card-body pb-2">
            <div class="d-flex justify-content-around align-items-center flex-wrap mb-4">
                
                <div class="user-analytics text-center me-2">
                    <i class="menu-icon tf-icons bx bx-cart"></i>
                    Total Orders
                    <div class="d-flex align-items-center mt-2">
                    <div class="chart-report" data-color="success" data-series=""></div>
                    <h3 class="mb-0">{{ $orders }}</h3>
                    </div>
                </div>

                <div class="user-analytics text-center me-2">
                    <i class="menu-icon tf-icons bx bx-cart"></i>
                    Total Clients
                    <div class="d-flex align-items-center mt-2">
                    <div class="chart-report" data-color="success" data-series=""></div>
                    <h3 class="mb-0">{{ $clients }}</h3>
                    </div>
                </div>

                <div class="user-analytics text-center me-2">
                    <i class="menu-icon tf-icons bx bx-cart"></i>
                    Total Services
                    <div class="d-flex align-items-center mt-2">
                    <div class="chart-report" data-color="success" data-series=""></div>
                    <h3 class="mb-0">{{ $services }}</h3>
                    </div>
                </div>

            </div>
            <div id="analyticsBarChart"></div>
        </div>
        </div>
    </div>
    </div>
    </div>
@endsection
