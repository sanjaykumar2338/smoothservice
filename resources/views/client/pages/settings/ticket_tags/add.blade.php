@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Settings /</span> Add Ticket Tag
    </h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl">
            
            <form id="tag_form" method="POST" action="{{ route('tickettags.store') }}">
                {{ csrf_field() }}

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Tag Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Tag Name -->
                        <div class="mb-3">
                            <label class="form-label" for="name">Tag Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter Tag Name" required />
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Tag</button>
            </form>
        </div>
    </div>
</div>

@endsection
