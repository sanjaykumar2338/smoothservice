@extends('team_member.team_member_tpl')
    @section('content')

    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Welcome {{auth()->user()->first_name}},</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
