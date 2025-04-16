@extends('client.client_template')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Notifications /</span> All Notifications
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            All Notifications
            <span class="badge bg-primary">{{ $notifications->total() }} Total</span>
        </h5>

        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr class="text-nowrap">
                        <th>#</th>
                        <th>Type</th>
                        <th>Message</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($notifications as $index => $note)
                    @php
                        $prefixes = [
                            'order_message' => 'Order message ',
                            'order_note' => 'Order note saved with the following data: ',
                            'ticket_created' => 'Ticket created with the following data: ',
                            'order_created' => 'Order created with the following data: ',
                            'order_updated' => 'Order updated with the following data: ',
                        ];

                        $action = $note->action_type;
                        $raw = $note->action_details;
                        $prefix = $prefixes[$action] ?? '';
                        $json = str_replace($prefix, '', $raw);
                        $details = json_decode($json, true);

                        $messageText = $details['message'] ?? ($details['note'] ?? ($details['subject'] ?? 'N/A'));
                        $orderLink = isset($details['order_id']) ? url('/admin/order/' . $details['order_id']) : null;
                    @endphp

                    <tr>
                        <td>{{ $index + 1 + ($notifications->currentPage() - 1) * $notifications->perPage() }}</td>
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $action)) }}</strong></td>
                        <td>
                            @if ($orderLink)
                                Order Message: <a href="{{ $orderLink }}">{{ $messageText }}</a>
                            @else
                                {{ $messageText }}
                            @endif
                        </td>
                        <td>{{ $note->created_at->diffForHumans() }}</td>
                        <td>
                            <form action="{{ route('notifications.destroy', $note->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this notification?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">X</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">No notifications found.</td></tr>
                @endforelse

                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($notifications->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $notifications->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $notifications->lastPage(); $i++)
                        @if ($i == $notifications->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $notifications->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($notifications->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $notifications->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection
