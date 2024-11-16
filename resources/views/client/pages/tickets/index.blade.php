@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Tickets /</span> Ticket List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Tickets
            <span class="badge bg-primary">{{ $tickets->total() }} Total Tickets</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('ticket.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by client name or ticket" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    <div class="dt-buttons">
                        &nbsp;
                        <!-- Button to trigger modal for adding a ticket -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTicketModal" id="addTicketButton">
                            <i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Ticket</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <br>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>#</th>
                        <th>Ticket Title</th>
                        <th>Client</th>
                        <th>Created</th>
                        <th>Last Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($tickets->count() > 0)
                        @foreach($tickets as $ticket)
                        <tr style="cursor: pointer;" onclick="window.location.href='{{ route('ticket.show', $ticket->ticket_no) }}'">
                            <th scope="row"><a href="{{ route('ticket.show', $ticket->id) }}">{{ $ticket->id }}</a></th>
                            <td>{{ $ticket->subject }}</td>
                            <td>{{ $ticket->client->first_name }} {{ $ticket->client->last_name }}</td>
                            <td>{{ $ticket->created_at->format('Y-m-d') }}</td>
                            <td>{{ $ticket->last_message_date ? $ticket->last_message_date->format('Y-m-d') : 'N/A' }}</td>
                            <td>
                                <!-- Edit Button -->
                                <a href="{{ route('ticket.show', $ticket->ticket_no) }}" class="btn btn-sm btn-primary">Edit</a>
                                <!-- Details Button -->
                                <a href="{{ route('ticket.show', $ticket->ticket_no) }}" class="btn btn-sm btn-info">Details</a>

                                <!-- Delete Button -->
                                @if(checkPermission('delete_ticket'))
                                    <form action="{{ route('ticket.destroy', $ticket->id) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="7" class="text-center">No tickets found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($tickets->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $tickets->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $tickets->lastPage(); $i++)
                        @if ($i == $tickets->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $tickets->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($tickets->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $tickets->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal for adding a new ticket -->
<div class="modal" id="addTicketModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('ticket.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addTicketModalLabel">Add Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Client Selection -->
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client</label>
                        <select class="form-control" id="client_id" name="client_id" required>
                            <option value="">-- Select Client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $client->id == $client_id ? 'selected' : '' }}>{{ $client->first_name }} {{ $client->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- CC Selection -->
                    <div class="mb-3">
                        <label for="cc" class="form-label">CC</label>
                        <select class="form-control" id="cc" name="cc[]" multiple>
                            <option value="">-- Select users --</option>
                            @foreach($users as $user)
                                <option value="team_{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject Field -->
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter ticket subject" required>
                    </div>

                    <!-- Related Order Selection -->
                    <div class="mb-3">
                        <label for="order_id" class="form-label">Order (Optional)</label>
                        <select class="form-control" id="order_id" name="reorder_idlated_order">
                            <option value="">-- Select an order --</option>
                            @foreach($orders as $order)
                                <option value="{{ $order->id }}">{{ $order->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Message Field -->
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="4" placeholder="Compose a message..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Replace `{{ $client_id }}` with your server-side variable
        var clientId = "{{ $client_id ?? '' }}";

        // If client ID exists, trigger a click on the button
        if (clientId) {
            document.getElementById("addTicketButton").click();
        }
    });
</script>

@endsection
