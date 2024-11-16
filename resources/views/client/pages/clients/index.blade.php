@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Clients /</span> Client List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Clients
            <span class="badge bg-primary">{{ $clients->total() }} Total Clients</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('client.list') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by name or email" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
                    </div>
                    @if(checkPermission('add_edit_login_clients'))
                    <div class="dt-buttons">
                        &nbsp;
                        <button onclick="window.location.href='{{ route('client.add') }}'" class="dt-button add-new btn btn-primary ms-n1" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span><i class="bx bx-plus me-0 me-lg-2"></i><span class="d-none d-lg-inline-block">Add Client</span></span>
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
                        <th>Name</th>
                        <th>Company</th>
                        <th>Created On</th>
                        <th>Status</th>
                        @if(checkPermission('add_edit_login_clients') || checkPermission('delete_clients'))
                        <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($clients->count() > 0)
                        @foreach($clients as $client)
                        <tr>
                            <th scope="row">{{ $client->id }}</th>
                            <td>{{ $client->first_name }} {{ $client->last_name }}<br>{{ $client->email }}</td>
                            <td>{{ $client->company }}</td>
                            <td>{{ $client->created_at->format('M d, Y') }}</td>
                            <td>{{ $client->client_status?->label ?? 'No Status' }}</td>
                            <td>
                                <div class="dropdown" style="display: inline;">
                                    <button class="btn btn-sm btn-light p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i> <!-- Three vertical dots icon -->
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        @if(checkPermission('add_edit_login_clients'))
                                            <li>
                                                <a class="dropdown-item" href="{{ route('client.edit', $client->id) }}">Edit</a>
                                            </li>
                                        @endif

                                        <li>
                                            <a class="dropdown-item" href="{{ route('client.sign_in_as_client', $client->id) }}">Sign in as user</a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ route('invoices.create', $client->id) }}">New invoice</a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item" href="{{ route('ticket.list', $client->id) }}">New ticket</a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item open_client_merge" data-id="{{$client->id}}" href="javascript:void(0);">Merge</a>
                                        </li>


                                        @if(checkPermission('delete_clients'))
                                            <li>
                                                <form action="{{ route('client.destroy', $client->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure?')" style="display: block;
    padding: 3px 20px;
    clear: both;
    font-weight: 400;
    line-height: 1.42857143;
    color: #333;
    white-space: nowrap;">Delete</button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="8" class="text-center">No clients found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($clients->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $clients->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $clients->lastPage(); $i++)
                        @if ($i == $clients->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $clients->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($clients->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $clients->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>


{{-- for client merge modal --}}
<div class="modal" id="ClientModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ClientModal">Merge Client</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="" name="mergeticket" action="{{route('tickets.merge')}}" method="post">
          @csrf  
          <div class="mb-2">
            <label for="field_type" class="form-label">Select Client</label>
            <select class="form-select" id="target_client_id" name="target_client_id" required>
                @foreach($clients as $rec)
                    <option value="{{$rec->id}}">{{$rec->first_name}} {{$rec->last_name}} ({{$rec->email}})</option>
                @endforeach
            </select>
            All orders, messages, invoices, and tickets from pk 2 will be moved to the selected account.
          </div>
          <input type="hidden" name="source_client_id" id="source_client_id" value="">
          <button type="submit" class="btn btn-primary" id="">Merge</button>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<script>
    let removedOption; // Variable to store the removed option

    $(document).on('click', '.open_client_merge', function() {
        let client_id = $(this).attr('data-id'); // Get the client ID from the data attribute
        $('#source_client_id').val(client_id); // Set the client ID in the source input field

        // Save the removed option in a variable
        removedOption = $(`#target_client_id option[value="${client_id}"]`).detach();

        $('#ClientModal').modal('show'); // Show the modal
    });

    // When the modal is closed, re-add the removed option
    $('#ClientModal').on('hidden.bs.modal', function() {
        if (removedOption) {
            $('#target_client_id').append(removedOption); // Add the option back
            removedOption = null; // Reset the variable
        }
    });
</script>


@endsection
