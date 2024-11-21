@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
    .color-circle {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 10px;
    }
    .color-option {
        display: flex;
        align-items: center;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Settings /</span> Edit Ticket Status
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
            
            <form id="ticket_status_form" method="POST" action="{{ route('setting.ticketstatuses.update', $status->id) }}">
                @csrf
                @method('POST')

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Ticket Status Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Status Name -->
                        <div class="mb-3">
                            <label class="form-label" for="name">Label</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $status->name) }}" placeholder="Enter Status Name" required />
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Enter description">{{ old('description', $status->description) }}</textarea>
                        </div>

                        <!-- Status Color -->
                        <div class="mb-3">
                            <label class="form-label" for="color">Color</label>
                            <select class="form-control" id="color" name="color">
                                <option value="gray" class="color-option" {{ $status->color == 'gray' ? 'selected' : '' }}><span class="color-circle" style="background-color: #808080;"></span>Gray</option>
                                <option value="yellow" class="color-option" {{ $status->color == 'yellow' ? 'selected' : '' }}><span class="color-circle" style="background-color: #FFFF00;"></span>Yellow</option>
                                <option value="blue" class="color-option" {{ $status->color == 'blue' ? 'selected' : '' }}><span class="color-circle" style="background-color: #0000FF;"></span>Blue</option>
                                <option value="green" class="color-option" {{ $status->color == 'green' ? 'selected' : '' }}><span class="color-circle" style="background-color: #008000;"></span>Green</option>
                                <option value="red" class="color-option" {{ $status->color == 'red' ? 'selected' : '' }}><span class="color-circle" style="background-color: #FF0000;"></span>Red</option>
                                <option value="indigo" class="color-option" {{ $status->color == 'indigo' ? 'selected' : '' }}><span class="color-circle" style="background-color: #4B0082;"></span>Indigo</option>
                                <option value="purple" class="color-option" {{ $status->color == 'purple' ? 'selected' : '' }}><span class="color-circle" style="background-color: #800080;"></span>Purple</option>
                                <option value="pink" class="color-option" {{ $status->color == 'pink' ? 'selected' : '' }}><span class="color-circle" style="background-color: #FFC0CB;"></span>Pink</option>
                            </select>
                        </div>

                        <!-- Checkboxes for additional settings -->
                        <div class="mb-3 form-check">
                            <div>
                                <input type="checkbox" class="form-check-input" id="lock_completed" name="lock_completed" value="1" {{ $status->lock_completed_orders ? 'checked' : '' }}>
                                <label class="form-check-label" for="lock_completed">Lock completed tickets</label>
                            </div>
                            <small class="form-text text-muted">Clients will need to click a "Request revision" button to post a message.</small>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="change_status_on_message" name="change_status_on_message" value="1" {{ $status->change_status_on_revision ? 'checked' : '' }}>
                            <label class="form-check-label" for="change_status_on_message">Change status of completed tickets when a client sends a message</label>
                        </div>

                        <div class="mb-3 form-check">
                            <div>
                                <input type="checkbox" class="form-check-input" id="enable_ratings" name="enable_ratings" value="1" {{ $status->enable_ratings ? 'checked' : '' }}>
                                <label class="form-check-label" for="enable_ratings">Enable ratings</label>
                            </div>
                            <small class="form-text text-muted">Clients will be able to leave a rating for completed tickets.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Default</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="is_default_yes" name="is_default" value="yes" 
                                        {{ $status->is_default == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default_yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="is_default_no" name="is_default" value="no" 
                                        {{ $status->is_default != 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default_no">No</label>
                                </div>
                            </div>
                            <small class="form-text text-muted">Initial status for a new ticket</small>
                        </div>


                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Ticket Status</button>
            </form>
        </div>
    </div>
</div>

@endsection
