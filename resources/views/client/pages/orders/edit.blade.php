@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Orders /</span> Edit Order
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
            
            <form id="edit_order_form" method="POST" action="{{ route('client.order.update', $order->id) }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Order</h5>
                    </div>
                    <div class="card-body">

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label" for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $order->title) }}" placeholder="Enter Title" />
                        </div>

                        <!-- Date Fields -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date_added">Date Added</label>
                                <input type="datetime-local" class="form-control" id="date_added" name="date_added" value="{{ old('date_added', $order->date_added) }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date_due">Date Due</label>
                                <input type="datetime-local" class="form-control" id="date_due" name="date_due" value="{{ old('date_due', $order->date_due) }}" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date_started">Date Started</label>
                                <input type="datetime-local" class="form-control" id="date_started" name="date_started" value="{{ old('date_started', $order->date_started) }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date_completed">Date Completed</label>
                                <input type="datetime-local" class="form-control" id="date_completed" name="date_completed" value="{{ old('date_completed', $order->date_completed) }}" />
                            </div>
                        </div>

                        <!-- Client -->
                        <div class="mb-3">
                            <label class="form-label" for="client_id">Client</label>
                            <select class="form-control" id="client_id" name="client_id">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $order->client_id) == $client->id ? 'selected' : '' }}>{{ $client->first_name }} {{ $client->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Service -->
                        <div class="mb-3">
                            <label class="form-label" for="service_id">Service</label>
                            <select class="form-control" id="service_id" name="service_id">
                                <option value="">Select Service</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id', $order->service_id) == $service->id ? 'selected' : '' }}>{{ $service->service_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Amount -->
                        <div class="mb-3">
                            <label class="form-label" for="amount">Amount ($)</label>
                            <input type="number" step="0.01" class="form-control" id="amount" name="amount" value="{{ old('amount', $order->amount) }}" placeholder="Enter Amount" />
                        </div>

                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
        </div>
    </div>
</div>

@endsection
