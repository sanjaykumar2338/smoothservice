@extends('admin.dashboard.layout')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }

    /* Add pointer cursor to the clickable rows */
    .clickable-row {
        cursor: pointer;
    }

    /* Optional: Highlight the row when hovering */
    .clickable-row:hover {
        background-color: #f5f5f5;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Invoices /</span> Invoice List
    </h4>

    <div class="card">
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Invoices
            <span class="badge bg-primary">{{ $invoices->total() }} Total Invoices</span>
        </h5>

        <div class="row mx-2">
            <div class="col-md-12">
                <div class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                        <form action="{{ route('admin.invoices') }}" method="GET" class="dataTables_filter" id="DataTables_Table_0_filter">
                            <label>
                                <input type="search" name="search" class="form-control" placeholder="Search by invoice name or client" aria-controls="DataTables_Table_0" value="{{ request()->get('search') }}">
                            </label>
                        </form>
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
                        <th>Client</th>
                        <th>Total</th>
                        <th>Due Date</th>
                        <th>Intake Form Feedback</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @if($invoices->count() > 0)
                        @foreach($invoices as $invoice)
                        <tr>
                            <th scope="row">{{ $invoice->id }}</th>
                            <td>{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</td>
                            <td>{{$invoice->currency}} {{ $invoice->total }}</td>
                            <td>{{ $invoice->due_date }}</td>
                            <td>
                                @php
                                    $feedback = \App\Models\FeedbackEntry::where('invoice_id', $invoice->id)->latest()->first();
                                @endphp

                                @if($feedback)

                                <button
                                    class="btn btn-sm btn-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#feedbackModal"
                                    data-feedback='@json($feedback->form_data)'>
                                    View Feedback
                                </button>
                                @else
                                    <span class="text-muted">No Feedback</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.invoiceshow', $invoice->id) }}" class="btn btn-sm btn-primary">Details</a>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="6" class="text-center">No invoices found.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            <nav>
                <ul class="pagination justify-content-center">
                    @if ($invoices->onFirstPage())
                        <li class="page-item disabled"><span class="page-link">Previous</span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $invoices->previousPageUrl() }}" rel="prev">Previous</a></li>
                    @endif

                    @for ($i = 1; $i <= $invoices->lastPage(); $i++)
                        @if ($i == $invoices->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $i }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $invoices->url($i) }}">{{ $i }}</a></li>
                        @endif
                    @endfor

                    @if ($invoices->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $invoices->nextPageUrl() }}" rel="next">Next</a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link">Next</span></li>
                    @endif
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="modal" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feedback Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="feedbackBody">
                <!-- Feedback data will be inserted here -->
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const feedbackModal = document.getElementById('feedbackModal');
        const feedbackBody = document.getElementById('feedbackBody');

        // When modal is triggered
        document.querySelectorAll('[data-feedback]').forEach(button => {
            button.addEventListener('click', function () {
                const feedback = JSON.parse(this.dataset.feedback);

                let html = '';
                feedback.forEach(item => {
                    if (item.type === 'file') {
                        html += `<p><strong>${item.name}:</strong><br><a href="${item.value}" target="_blank"><img src="${item.value}" width="100" /></a></p>`;
                    } else {
                        html += `<p><strong>${item.name}:</strong> ${item.value}</p>`;
                    }
                });

                feedbackBody.innerHTML = html;
            });
        });
    });
</script>

<script>
    // Add click event to each row to redirect to the show route
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.clickable-row');
        rows.forEach(row => {
            row.addEventListener('click', function() {
                window.location = this.dataset.href;
            });
        });
    });
</script>

@endsection
