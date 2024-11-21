@extends('c_main.c_dashboard')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Tickets /</span> Add Ticket
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
        <!-- Personal Information Section -->
        <div class="col-xl-12">
            <form id="client_form" method="POST" action="{{ route('portal.tickets.store') }}">
                {{ csrf_field() }}

                <!-- Error messages will be displayed here -->
                <div id="error-messages" class="alert alert-danger" style="display:none;">
                    <ul id="error-list"></ul>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Email (Required) -->
                        <div class="mb-3">
                            <label class="form-label" for="email">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Enter Subject" required />
                        </div>

                        <!-- First Name (Optional) -->
                        <div class="mb-3">
                            <label class="form-label" for="first_name">Related order (Optional)</label>
                            <select id="collapsible-order" name="order" class="select2 form-select select-order-ticket" data-allow-clear="true">
                                <option value="">Select Order</option>
                                @foreach($orders as $order)
                                    <option value="{{$order->id}}">{{$order->title}}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label" for="full_editor">Message</label>
                            <div id="full-editor">
                               
                            </div>

                            <textarea id="editor_content" style="display:none" name="editor_content" class="form-control">
                                    
                            </textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Add Ticket</button>
                    </div>
                </div>
        </div>
    </div>
</div>

@endsection
