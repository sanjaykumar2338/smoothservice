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

    <div class="card mb-4">
        <div class="card-body">
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
                <div id="message-list">
                    @foreach ($ticket->replies as $reply)
                        <div class="comment-box">
                            <div class="comment-header">
                                {{ $reply->sender->first_name.' '.$reply->sender->last_name ?? 'Unknown Sender' }} <span class="text-muted">| {{ $reply->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <div class="comment-body">
                                {!! nl2br(e($reply->message)) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Reply Form -->
            <div class="reply-form">
                <div id="reply-editor-section" style="display: block;">
                    <textarea id="reply-editor" class="form-control" rows="5" placeholder="Type your reply..."></textarea>
                    </div>
                    <div class="mt-3 text-end">
                        <button id="send-reply-btn" class="btn btn-danger">Send Message</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
    // Send message to client or team
    $('#send-reply-btn').on('click', function() {
        const message = $('#reply-editor').val();
        const scheduleAt = $('#schedule-datetime').val();
        const cancelOnReply = $('#cancel-on-reply').is(':checked') ? 1 : 0; // Ensure it's a boolean
        const messageType = 'client'; // Set message type

        if (!message) {
            alert('Please enter a reply.');
            return;
        }

        $.ajax({
            url: '/ticket/send-reply', // Using the same route
            method: 'POST',
            data: {
                message: message,
                schedule_at: scheduleAt,
                cancel_if_replied: cancelOnReply,
                ticket_id: '{{ $ticket->id }}',
                client_id: '{{ $ticket->client->id }}',
                message_type: messageType, // Differentiating between client and team messages
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                // Reset the editor after successfully sending the message
                $('#reply-editor').val('');

                // Append the new message dynamically to the message list
                $('#message-list').append(`
                    <div class="comment-box" id="reply${response.reply.id}">
                        <div class="comment-header">
                            ${response.reply.sender_name || 'Unknown Sender'} 
                            <span class="text-muted">| ${new Date(response.reply.created_at).toLocaleString()}</span>
                        </div>
                        <div class="comment-body">
                            ${response.reply.message.replace(/\n/g, '<br>')}
                        </div>
                    </div>
                `);

                // Optional: Scroll to the bottom of the message list to show the new message
                $('#message-list').animate({ scrollTop: $('#message-list')[0].scrollHeight }, 500);
            },
            error: function(xhr, status, error) {
                alert('Failed to send message: ' + xhr.responseText);
            }
        });
    });
</script>

@endsection
