@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Tickets /</span> Edit Ticket
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
            
            <form id="edit_ticket_form" method="POST" action="{{ route('tickets.update_info', $ticket->id) }}">
                {{ csrf_field() }}
                {{ method_field('PUT') }}

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit Ticket</h5>
                    </div>
                    <div class="card-body">

                        <!-- Subject -->
                        <div class="mb-3">
                            <label class="form-label" for="subject">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject', $ticket->subject) }}" placeholder="Enter Subject" />
                        </div>

                        <!-- Date Fields -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date_added">Date Added</label>
                                <input type="datetime-local" class="form-control" id="date_added" name="date_added" value="{{ old('date_added', $ticket->date_added) }}" />
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date_closed">Date Closed</label>
                                <input type="datetime-local" class="form-control" id="date_closed" name="date_closed" value="{{ old('date_closed', $ticket->date_closed) }}" />
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="mb-3">
                            <label class="form-label">Metadata</label>
                            <div id="metadata-wrapper">
                                @foreach($ticket->metadata as $meta)
                                    <div class="row mb-2">
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="meta_key[]" value="{{ old('meta_key[]', $meta->key) }}" placeholder="Key">
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control" name="meta_value[]" value="{{ old('meta_value[]', $meta->value) }}" placeholder="Value">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-meta">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-secondary" id="add-meta">Add Metadata</button>
                        </div>

                        <!-- Client and Collaborators -->
                        <div class="mb-3">
                            <label class="form-label" for="client_id">Client</label>
                            <select class="form-control" id="client_id" name="client_id">
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id', $ticket->client_id) == $client->id ? 'selected' : '' }}>{{ $client->first_name }} {{ $client->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="collaborators">Collaborators</label>
                            <select class="form-control" id="collaborators" name="collaborators[]" multiple>
                                @foreach($ticket->ccUsers as $collaborator)
                                    <option value="{{ $collaborator->id }}" {{ in_array($collaborator->id, old('collaborators', $ticket->collaborators->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $collaborator->first_name }} {{ $collaborator->last_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="related_order_id">Related Order</label>
                            <select class="form-control" id="related_order_id" name="related_order_id">
                                <option value="">Select Related Order</option>
                                @foreach($orders as $order)
                                    <option value="{{ $order->id }}" {{ old('related_order_id', $ticket->related_order_id) == $order->id ? 'selected' : '' }}>{{ $order->order_number }}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
        </div>
    </div>
</div>

<script>
    // JavaScript to add/remove metadata fields dynamically
    document.getElementById('add-meta').addEventListener('click', function () {
        let wrapper = document.getElementById('metadata-wrapper');
        let row = document.createElement('div');
        row.className = 'row mb-2';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" class="form-control" name="meta_key[]" placeholder="Key">
            </div>
            <div class="col-md-5">
                <input type="text" class="form-control" name="meta_value[]" placeholder="Value">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-meta">Remove</button>
            </div>`;
        wrapper.appendChild(row);
    });

    document.getElementById('metadata-wrapper').addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-meta')) {
            e.target.closest('.row').remove();
        }
    });
</script>

@endsection
