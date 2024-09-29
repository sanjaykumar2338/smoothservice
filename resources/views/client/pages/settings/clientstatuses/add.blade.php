@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Settings /</span> Add Client Status
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
            
            <form id="client_status_form" method="POST" action="{{ route('statuses.store') }}">
                {{ csrf_field() }}

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Client Status Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status Label -->
                        <div class="mb-3">
                            <label class="form-label" for="label">Label</label>
                            <input type="text" class="form-control" id="label" name="label" value="{{ old('label') }}" placeholder="Enter Status Label" required />
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Enter Status Description">{{ old('description') }}</textarea>
                        </div>

                        <!-- Color Selection -->
                        <div class="mb-3">
                            <label class="form-label" for="color">Color</label>
                            <select class="form-control" id="color" name="color">
                                <option value="gray" {{ old('color') == 'gray' ? 'selected' : '' }}>Gray</option>
                                <option value="yellow" {{ old('color') == 'yellow' ? 'selected' : '' }}>Yellow</option>
                                <option value="blue" {{ old('color') == 'blue' ? 'selected' : '' }}>Blue</option>
                                <option value="green" {{ old('color') == 'green' ? 'selected' : '' }}>Green</option>
                                <option value="red" {{ old('color') == 'red' ? 'selected' : '' }}>Red</option>
                                <option value="indigo" {{ old('color') == 'indigo' ? 'selected' : '' }}>Indigo</option>
                                <option value="purple" {{ old('color') == 'purple' ? 'selected' : '' }}>Purple</option>
                                <option value="pink" {{ old('color') == 'pink' ? 'selected' : '' }}>Pink</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Client Status</button>
            </form>
        </div>
    </div>
</div>

@endsection
