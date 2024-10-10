@extends('client.client_template')
@section('content')
<style>
  .col-form-label{
    width: 13%;
  } 
</style>
<div class="container-xxl flex-grow-1 container-p-y">
   <h4 class="py-3 breadcrumb-wrapper mb-4">
      <span class="text-muted fw-light">Tickets /</span> Ticket Details
   </h4>
   <div class="row align-items-center">
      <!--/ project data -->
      <div class="row">
         <div class="col-lg-12 col-xl-12">
            <div class="card card-action mb-4">
               <div class="card-header align-items-center">
                  <h5 class="card-action-title mb-0">Edit Fields data</h5>
                  
                  <a style="color: #fff;" onclick="window.location.href='{{ route('ticket.show', $ticket->ticket_no) }}'" class="btn btn-secondary mb-3">
                    Back to Order
                  </a>
                   &nbsp; 
                  <a href="#" class="btn btn-info mb-3 open_data_project">
                    Add Field
                  </a>
               </div>

               <div class="card-body">
               <form id="project-data-save-form" action="{{ route('ticket.save_project_data', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf  
                @foreach($project_data as $field)
                    @if($field && isset($field->field_type) && isset($field->field_name))
                        <div class="form-group mb-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center w-100">

                                @if($field->field_type == 'file_upload')
                                    <label style="width: 14%;" for="field_{{ $field->id }}" class="me-3 col-form-label">{{ $field->field_name }}:</label>
                                @else
                                    <label style="" for="field_{{ $field->id }}" class="me-3 col-form-label">{{ $field->field_name }}:</label>
                                @endif

                                @if($field->field_type == 'single_line')
                                    <input type="text" class="form-control" name="field_{{ $field->id }}" id="field_{{ $field->field_name }}" placeholder="Enter {{ $field->field_name }}" value="{{ $field->field_value ?? '' }}">
                                
                                @elseif($field->field_type == 'multiple_line')
                                    <textarea class="form-control" name="field_{{ $field->id }}" id="field_{{ $field->field_name }}" rows="3" placeholder="Enter {{ $field->field_name }}">{{ $field->field_value ?? '' }}</textarea>
                                
                                @elseif($field->field_type == 'checkbox')
                                    <input type="checkbox" class="form-check-input" name="field_{{ $field->id }}" id="field_{{ $field->field_name }}" {{ $field->field_value ? 'checked' : '' }}>
                                
                                @elseif($field->field_type == 'hidden_field')
                                    <input type="hidden" name="field_{{ $field->id }}" id="field_{{ $field->field_name }}" value="{{ $field->field_value ?? 'hidden_value' }}">    
                                @elseif($field->field_type == 'file_upload')
                                    
                                    <input type="file" class="form-control" name="field_{{ $field->id }}" id="field_{{ $field->field_name }}">

                                    @if($field->field_value)
                                        &nbsp;&nbsp;
                                        <a href="{{ asset('storage/' . $field->field_value) }}" target="_blank" class="me-3">View</a>
                                    @endif
                                @endif
                            </div>

                            <!-- Remove Icon Button on the right -->
                            <button type="button" class="btn btn-danger btn-sm ms-3 remove-field" data-id="{{ $field->id }}">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    @else
                        <p>Invalid field data</p>
                    @endif
                @endforeach

                <div class="mt-4 center">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
            </div>
            </div>
         </div>
      </div>
      <!--/ project data -->
   </div>
</div>
<div class="modal" id="addDataModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
   <div class="modal-dialog">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="addDataModalLabel">Add Fields</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body">
            <form id="project-data-form">
               <div class="mb-3">
                  <label for="field_name" class="form-label">Field Name</label>
                  <input type="text" class="form-control" id="field_name" name="field_name" required>
               </div>
               <div class="mb-3">
                  <label for="field_type" class="form-label">Field Type</label>
                  <select class="form-select" id="field_type" name="field_type" required>
                     <option value="single_line">Single Line Text</option>
                     <option value="multiple_line">Multiple Line Text</option>
                     <option value="checkbox">Checkbox</option>
                     <option value="file_upload">File Upload</option>
                     <option value="hidden_field">Hidden Field</option>
                  </select>
               </div>
               <input type="hidden" name="ticket_id" value="{{$ticket->id}}">
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="save-project-data">Continue</button>
         </div>
      </div>
   </div>
</div>
<script>
   $(document).on('click', '.open_data_project', function() {
      $('#addDataModal').modal('show');
   });
   
   $('#save-project-data').on('click', function() {
      var formData = $('#project-data-form').serialize();
   
      $.ajax({
         url: '/ticket/save-project-data',  // Define your route for saving data
         method: 'POST',
         data: formData,
         headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Adding the CSRF token
         },
         success: function(response) {
               // Close modal and refresh the page or update the list dynamically
               $('#addDataModal').modal('hide');
               // You can refresh the project data section dynamically
               window.location.reload();
         }
      });
   });

   $(document).on('click', '.remove-field', function() {
    var fieldId = $(this).data('id');
    
    // Make an AJAX call to remove the field from the database
    $.ajax({
        url: '/ticket/remove-project-field/' + fieldId,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            // Remove the field from the form after successful deletion
            $('button[data-id="' + fieldId + '"]').closest('.form-group').remove();
        }
    });
    });
   
</script>
@endsection