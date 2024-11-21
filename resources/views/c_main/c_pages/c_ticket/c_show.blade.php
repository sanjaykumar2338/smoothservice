@extends('c_main.c_dashboard')
@section('content')

<style>
    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .ticket-header h4 {
        margin: 0;
    }
    .ticket-info {
        margin-top: 20px;
    }
    .ticket-info-item {
        margin-bottom: 10px;
    }
    .comment-box {
        margin-top: 20px;
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
    }
    .comment-header {
        font-weight: bold;
    }
    .comment-body {
        margin-top: 10px;
    }
    .reply-form {
        margin-top: 20px;
    }
    .reply-form textarea {
        resize: none;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Ticket Header -->
    <h4 class="py-3 breadcrumb-wrapper mb-4">
      <span class="text-muted fw-light">Tickets /</span> Ticket Detail
    </h4>

    <div class="row align-items-center">
        <div class="ticket-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4>{{ $ticket->subject }}</h4>
                <p><strong>Ticket Number:</strong> {{ $ticket->ticket_no }}</p>
            </div>
            <div>
                <p><strong>Status:</strong> <span class="badge bg-success">{{ optional($ticket->ticket_status)->name }}</span></p>
            </div>
        </div>

        <!-- Ticket Details -->
        <div class="ticket-info mb-4">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Created:</strong> {{ $ticket->created_at->format('M d, Y') }}</p>
                    <p><strong>Closed:</strong> {{ $ticket->closed_at ? $ticket->closed_at->format('M d, Y') : '--' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Owner:</strong> </p>
                    <p><strong>Team:</strong></p>
                    <p><strong>Email:</strong> </p>
                </div>
            </div>
        </div>

        <!-- Comments/Replies -->
        <div class="comments-section mb-4">
            <h5>Replies</h5>
            @foreach ($ticket->replies as $reply)
                <div class="comment-box">
                    <div class="comment-header">
                        {{ $reply->sender->name ?? 'Unknown Sender' }} <span class="text-muted">| {{ $reply->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="comment-body">
                        {!! nl2br(e($reply->message)) !!}
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Reply Form -->
        <div class="reply-form">
            <form action="" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="message" class="form-label">Your Reply</label>
                    <textarea class="form-control" id="message" name="message" rows="5" placeholder="Type your reply here..."></textarea>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <p class="mb-0">
                        <strong>CC:</strong>
                    </p>
                    <button type="submit" class="btn btn-danger">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
