@extends('client.client_template')
@section('content')

<style>
    /* Create the cross overlay */
    .cross-overlay::before {
      content: "\00d7";
      position: absolute;
      color: #000511;
      font-size: 1.2em;
      top: 47%;
      left: 47%;
      transform: translate(-50%, -50%);
    }

    .nav-link {
        position: relative;
    }

   table th:last-child, 
   table td:last-child {
      text-align: right; /* Align text to the right */
   }

   .completed-task {
      opacity: 0.5;
   }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
   <h4 class="py-3 breadcrumb-wrapper mb-4">
      <span class="text-muted fw-light">Orders /</span> Order Details
   </h4>

   <div class="row align-items-center">
    <div class="col-md-6">
        <h5 class="card-action-title mb-4 fs-2 text-black">{{$order->service->service_name}}</h5>
    </div>
    <div class="col-md-6 d-flex justify-content-end">
         <ul class="nav nav-pills flex-sm-row mb-4">
            <li class="nav-item dropdown">
                  <a class="nav-link active dropdown-toggle" href="javascript:void(0);" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Status
                  </a>
                  <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                     <li><a class="dropdown-item" href="#">Submitted</a></li>
                     <li><a class="dropdown-item" href="#">Working</a></li>
                     <li><a class="dropdown-item" href="#">Completed</a></li>
                     <li><a class="dropdown-item" href="#">Canceled</a></li>
                  </ul>
            </li>
            
            <li class="nav-item">
                  <a class="nav-link" href="pages-profile-teams.html"><i class="bx bx-group me-1"></i> Teams</a>
            </li>

            <li class="nav-item">
               <a class="nav-link" href="javascript:void(0);" id="notification-icon" onclick="toggleIcon()">
                  <i class="bx bx-bell me-1" id="icon"></i>
               </a>
            </li>

            <li class="nav-item">
               <div class="dropdown" style="padding-top: 6px;">
                  <button
                     type="button"
                     class="btn dropdown-toggle hide-arrow p-0"
                     data-bs-toggle="dropdown"
                     aria-expanded="false">
                  <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                     <li><a class="dropdown-item" href="javascript:void(0);">Edit</a></li>
                     <li><a class="dropdown-item" href="javascript:void(0);">Add Project data</a></li>
                     <li><a class="dropdown-item" href="javascript:void(0);">Create an invoice</a></li>
                     <li><a class="dropdown-item" href="javascript:void(0);">Duplicate order</a></li>
                     <li><a class="dropdown-item" href="javascript:void(0);">Delete order</a></li>
                  </ul>
               </div>
            </li>
         </ul>
      </div>
    </div>


   <div class="row">
      <div class="col-xl-8 col-lg-7 col-md-7">
         <!-- serice heading -->
         <div class="card card-action mb-4">
            <div class="card-body">
               <!-- Display the note -->
               <div id="display-note" style="{{ $order->note ? '' : 'display:none;' }}">
                  <div id="note-content">{!! $order->note !!}</div>
                  <button class="btn btn-label-primary p-1 btn-sm" id="edit-note-btn"><i class="bx bx-edit"></i> Edit Note</button>
               </div>

               <!-- Editor for editing the note, hidden by default -->
               <div id="note-editor" style="{{ $order->note ? 'display:none;' : '' }}">
                  <label class="form-label" for="full_editor">Add a note for your team...</label>
                  <div id="full-editor" style="">{!! $order->note !!}</div>
                  <textarea id="editor_content" style="display:none;" name="editor_content" class="form-control">{!! $order->note !!}</textarea>

                  <div class="card-footer d-flex justify-content-end">
                        <button class="btn btn-label-primary p-1 btn-sm" id="save-note-btn"><i class="bx bx-save"></i> Save Note</button>
                  </div>
               </div>
            </div>
         </div>
         <!--/ tasks data -->

         <div class="row">
            <div class="col-lg-12 col-xl-12">
               <div class="card card-action mb-4">
                     <div class="card-header align-items-center">
                        <h5 class="card-action-title mb-0">Tasks</h5>
                        <div class="card-action-element btn-pinned">
                           <div class="dropdown">
                                 <button type="button" class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                 </button>
                                 <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a data-order-id="{{$order->id}}" class="dropdown-item show_task_by_status" href="javascript:void(0);">Show Completed Tasks</a></li>
                                 </ul>
                           </div>
                        </div>
                     </div>

                     <div class="card-body">
                        <!-- Task List Table -->
                        <table class="table" id="task-list">
                           <thead>
                              <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Actions</th>
                              </tr>
                           </thead>
                           <tbody>
                              @if($order->tasks->count() > 0 )
                                 @foreach($order->tasks as $task)
                                    <tr data-id="{{ $task->id }}">
                                          <td><input type="checkbox" class="task-status" data-id="{{ $task->id }}" {{ $task->status == 1 ? 'checked' : '' }}></td>
                                          <td>
                                             <strong>{{ $task->name }}</strong> <br>
                                             <span>{{ $task->description }}</span> <br>
                                             <small class="text-muted">Due: 
                                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y H:i') : 'No due date' }}
                                             </small>
                                          </td>
                                          <td>
                                             <button class="btn btn-sm btn-primary edit-task" data-id="{{ $task->id }}">
                                                <i class="bx bx-edit"></i>
                                             </button>
                                             <button class="btn btn-sm btn-danger delete-task" data-id="{{ $task->id }}">
                                                <i class="bx bx-trash"></i>
                                             </button>
                                          </td>
                                    </tr>
                                 @endforeach
                              @else
                                 <tr class="no_record"> 
                                    <td colspan="3" style="text-align: center;">No tasks found.</td>
                                 </tr>
                              @endif
                           </tbody>
                        </table>

                        <form id="task-form" style="display:none;" class="mt-3">
                              <input type="hidden" name="_token" value="{{ csrf_token() }}">
                              <div class="mb-3">
                                    <label for="task-name" class="form-label">Task Name</label>
                                    <input type="text" class="form-control" id="task-name" name="name" required>
                              </div>

                              <div class="mb-3">
                                    <label for="task-desc" class="form-label">Description</label>
                                    <textarea class="form-control" id="task-desc" name="description"></textarea>
                              </div>

                              <div class="mb-3">
                                 <label for="task-assign" class="form-label">Assign To</label>
                                 <select id="task-assign" class="select2 form-select" name="assigned_to[]" multiple>
                                    @foreach($team_members as $team)
                                          <option value="{{ $team->id }}"
                                             @if(isset($task) && in_array($team->id, $task->members->pluck('id')->toArray())) selected @endif>
                                             {{ $team->first_name }} {{ $team->last_name }}
                                          </option>
                                    @endforeach
                                 </select>
                              </div>

                              <div class="mb-3">
                                 <label for="due-date-type" class="form-label">Due</label>
                                 <select class="form-control" id="due-date-type" name="due_type">
                                    <option value="">--Select--</option>
                                    <option value="fixed">Fixed Date</option>
                                    <option value="previous">From Previous Task</option>
                                 </select>
                              </div>

                              <!-- Fixed Date Picker -->
                              <div id="fixed-date-section" class="mb-3" style="display:none;">
                                    <label for="fixed-due-date" class="form-label">Select Due Date</label>
                                    <input type="datetime-local" class="form-control" id="fixed-due-date" name="due_date" value="">
                              </div>

                              <!-- Previous Task Section -->
                              <div id="previous-task-section" class="mb-3" style="display:none;">
                                    <div class="mb-2">
                                       <label for="due-period-value" class="form-label"></label>
                                       <input type="number" class="form-control" id="due-period-value" name="due_period_value">
                                    </div>

                                    <div class="mb-2">
                                       <label for="due-period-type" class="form-label"></label>
                                       <select class="form-control" id="due-period-type" name="due_period_type">
                                          <option value="days">Days</option>
                                          <option value="hours">Hours</option>
                                       </select>
                                    </div>
                              </div>

                              <input type="hidden" name="order_id" value="{{$order->id}}">
                              <button type="submit" id="save_task_order" class="btn btn-primary">Save Task</button>
                        </form>
                     </div>

                     <div class="card-footer d-flex justify-content-end">
                        <button class="btn btn-label-primary p-1 btn-sm" id="add-task-btn"><i class="bx bx-plus"></i> Add Task</button>&nbsp;
                        <button style="display:none;" class="btn btn-label-primary p-1 btn-sm" id="cancel-task-btn">Cancel</button>
                     </div>
               </div>
            </div>
         </div>

         <!--/ project data -->
         <div class="row">
            <div class="col-lg-12 col-xl-12">
               <div class="card card-action mb-4">
                  <div class="card-header align-items-center">
                     <h5 class="card-action-title mb-0">Project Data</h5>
                     <div class="card-action-element btn-pinned">
                        <div class="dropdown">
                           <button
                              type="button"
                              class="btn dropdown-toggle hide-arrow p-0"
                              data-bs-toggle="dropdown"
                              aria-expanded="false">
                           <i class="bx bx-dots-vertical-rounded"></i>
                           </button>
                           <ul class="dropdown-menu dropdown-menu-end">
                              <li><a class="dropdown-item open_data_project" href="javascript:void(0);">Add data</a></li>
                              <li><a class="dropdown-item" href="{{ route('client.order.project_data', $order->id) }}">Edit data</a></li>
                              <li><a class="dropdown-item" href="{{ route('client.order.export_data', $order->id) }}">Export data</a></li>
                              <li><a class="dropdown-item" href="{{ route('client.order.download_files', $order->id) }}">Download files</a></li>
                              <li><a class="dropdown-item" href="javascript:void(0);" onclick="deleteData({{ $order->id }})">Delete data</a></li>
                           </ul>

                        </div>
                     </div>
                  </div>

                  <div class="card-body">
                     <ul class="list-unstyled mb-0">
                        @foreach($project_data as $data)
                           <li class="mb-3">
                              <div class="d-flex align-items-start">
                                 <div class="d-flex align-items-start">
                                    <div class="me-2">
                                       <strong>{{ $data->field_name }}:</strong> 
                                       
                                       @if($data->field_type === 'file_upload' && $data->field_value)
                                          <a href="{{ asset('storage/' . $data->field_value) }}" target="_blank">View File</a>
                                       @else
                                          <span>{{ $data->field_value ?? 'No value provided' }}</span>
                                       @endif
                                    </div>
                                 </div>
                              </div>
                           </li>
                        @endforeach
                     </ul>
                  </div>



               </div>
            </div>
         </div>
         <!--/ project data -->
         
         <div class="row">
            <div class="col-lg-12 col-xl-12">
               <div class="card card-action mb-4">
                  <div class="card-body">
                     <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                           <div class="d-flex align-items-start">
                              <div class="d-flex align-items-start">
                                 <div class="me-2">
                                    <h6 class="mb-0">Cecilia Payne</h6>
                                    <small class="text-muted">45 Connections</small>
                                 </div>
                              </div>
                           </div>
                        </li>
                     </ul>
                  </div>
                  <div class="card-footer d-flex justify-content-end">
                     <button class="btn btn-label-primary p-1 btn-sm"><i class="bx bx-reply"></i> Reply to Client</button>&nbsp;
                     <button class="btn btn-label-primary p-1 btn-sm"><i class="bx bx-plus"></i> Message Team</button>
                  </div>
               </div>
            </div>
         </div>
         <!-- Projects table -->
         <div class="card mb-4">
            <h5 class="card-header">History</h5>
            <div class="table-responsive mb-3">
               <table class="table datatable-project">
                  <thead class="">
                     <tr>
                        <th></th>
                        <th></th>
                        <th>Project</th>
                        <th class="text-nowrap">Total Task</th>
                        <th>Progress</th>
                        <th>Hours</th>
                     </tr>
                  </thead>
                  <tbody>
                    <tr>
                        <td style="text-align:center" colspan="6">No record.<td>
                    </tr>
                  </tbody>
               </table>
            </div>
         </div>
         <!--/ Projects table -->
      </div>
      <div class="col-xl-4 col-lg-5 col-md-5">
         <!-- About User -->
         <div class="card mb-4">
            <div class="card-body">
               <ul class="list-unstyled mb-4">
                  <li class="d-flex align-items-center mb-3">
                    <span class="fw-medium mx-2">{{$order->order_no}}</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Service</span>
                     <span>{{$order->service->service_name}}</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Client</span>
                     <span>{{$order->client->first_name}} {{$order->client->last_name}}</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Created</span> <span>{{ $order->created_at->format('M d') }}
                     </span>
                  </li>

                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Created</span> <span>{{ $order->created_at->format('M d') }}
                     </span>
                  </li>

                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Started</span> <span>--
                     </span>
                  </li>

                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Due</span> <span>--
                     </span>
                  </li>

                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Completed</span> <span>--
                     </span>
                  </li>
               </ul>
               <small class="text-muted text-uppercase">Tags</small>
               
               <ul class="list-unstyled mt-3 mb-0" style="display: -webkit-box;">
                  <li class="d-flex align-items-center mb-3">
                     <div class="d-flex flex-wrap">
                        <span class="fw-medium me-2">TEST</span>
                     </div>
                  </li>
                  <li>
                     <button class="btn" style="padding-top: 1px;padding-left: 0px;"><i class="bx bx-plus"></i> Add</button>
                  </li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</div>

<div class="modal" id="addDataModal" tabindex="-1" aria-modal="true" role="dialog" style="padding-left: 0px;">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addDataModalLabel">Add Project Data</h5>
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

          <input type="hidden" name="order_id" value="{{$order->id}}">

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
    document.getElementById('edit-note-btn').addEventListener('click', function() {
        // Hide the display area and show the editor
        document.getElementById('display-note').style.display = 'none';
        document.getElementById('note-editor').style.display = 'block';
    });

    document.getElementById('save-note-btn').addEventListener('click', function() {
      var orderId = {{ $order->id }}; // Assuming you're passing the order ID into the view

      // Get the value from the hidden textarea (already updated by Quill's 'text-change' event)
      var noteContent = document.getElementById('editor_content').value;

      // Make an AJAX request to save the note
      $.ajax({
         url: '/client/order/' + orderId + '/save-note',
         method: 'POST',
         data: {
               note: noteContent, // Send the content from the textarea
               _token: '{{ csrf_token() }}' // Include CSRF token
         },
         success: function(response) {
               // Update the displayed note content on the page
               document.getElementById('note-content').innerHTML = noteContent;

               // Hide the editor and show the display area
               document.getElementById('note-editor').style.display = 'none';
               document.getElementById('display-note').style.display = 'block';
         },
         error: function(error) {
               console.error('Error saving the note:', error);
         }
      });
   });

    function toggleIcon() {
        var icon = document.getElementById("icon");
        var parent = document.getElementById("notification-icon");
        
        // Toggle the cross overlay
        if (parent.classList.contains('cross-overlay')) {
            parent.classList.remove('cross-overlay');
        } else {
            parent.classList.add('cross-overlay');
        }
    }

   document.getElementById('add-task-btn').addEventListener('click', function() {
      document.getElementById('task-form').style.display = 'block';
      document.getElementById('cancel-task-btn').style.display = 'block';
   });

   // Hide the form when "Cancel" is clicked
   document.getElementById('cancel-task-btn').addEventListener('click', function() {
      document.getElementById('task-form').style.display = 'none';
      document.getElementById('cancel-task-btn').style.display = 'none';
   });

    // Show appropriate fields based on the "Due" selection
    document.getElementById('due-date-type').addEventListener('change', function() {
        var dueType = this.value;
        if (dueType === 'fixed') {
            document.getElementById('fixed-date-section').style.display = 'block';
            document.getElementById('previous-task-section').style.display = 'none';
        } else if (dueType === 'previous') {
            document.getElementById('fixed-date-section').style.display = 'none';
            document.getElementById('previous-task-section').style.display = 'block';
        } else {
            document.getElementById('fixed-date-section').style.display = 'none';
            document.getElementById('previous-task-section').style.display = 'none';
        }
    });

   // Track whether the task is being edited
   var editingTaskId = null;

   $('#task-form').submit(function(e) {
      e.preventDefault();
      var url;
      if (editingTaskId) {
         url = `/client/order/update-task/${editingTaskId}`;
      } else {
         url = '/client/order/save-task';
      }

      $.ajax({
         url: url, 
         method: 'POST',
         data: $(this).serialize(),
         success: function(response) {
               $('#task-list tbody').find('tr.no_record').remove();

               if (editingTaskId) {
                  // Update task in the table
                  var row = $(`tr[data-id="${editingTaskId}"]`);
                  row.find('td:nth-child(2) strong').text(response.task.name);  // Update task name
                  row.find('td:nth-child(2) span').text(response.task.description);  // Update task description
                  row.find('td:nth-child(2) small').text(`Due: ${response.task.due_date ? response.task.due_date : 'No due date'}`);  // Update due date
               } else {
                  // Append the new task to the table
                  $('#task-list tbody').append(`
                     <tr data-id="${response.task.id}">
                           <td><input type="checkbox" class="task-status" data-id="${response.task.id}"></td>
                           <td>
                              <strong>${response.task.name}</strong><br>
                              <span>${response.task.description}</span><br>
                              <small class="text-muted">Due: ${response.task.due_date ? response.task.due_date : 'No due date'}</small>
                           </td>
                           <td>
                              <button class="btn btn-sm btn-primary edit-task" data-id="${response.task.id}">
                                 <i class="bx bx-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger delete-task" data-id="${response.task.id}">
                                 <i class="bx bx-trash"></i>
                              </button>
                           </td>
                     </tr>
                  `);
               }

               // Reset form and hide it
               $('#task-form')[0].reset();
               $('#task-form').hide();
               document.getElementById('cancel-task-btn').style.display = 'none';
               editingTaskId = null;
               $('#save_task_order').text('Save Task');  // Reset button text
         }
      });
   });

   // Edit Task
   $(document).on('click', '.edit-task', function() {
      var taskId = $(this).data('id');
      editingTaskId = taskId;
      document.getElementById('cancel-task-btn').style.display = 'block';

      $.ajax({
         url: `/client/order/get-task/${taskId}`,
         method: 'GET',
         success: function(response) {
               $('#task-name').val(response.task.name);
               $('#task-desc').val(response.task.description);

               // Set assigned members in select2 dropdown
               var assignedMembers = response.task.members.map(member => member.id);
               $('#task-assign').val(assignedMembers).trigger('change');

               // Set due type
               $('#due-date-type').val(response.task.due_type).trigger('change');

               // Show and set due date or period fields based on due_type
               if (response.task.due_type === 'fixed') {
                  $('#fixed-due-date').val(response.task.due_date);  // Set due date
                  $('#fixed-date-section').show();  // Show fixed date section
                  $('#previous-task-section').hide();  // Hide previous task section
               } else if (response.task.due_type === 'previous') {
                  $('#due-period-value').val(response.task.due_period_value);  // Set due period value
                  $('#due-period-type').val(response.task.due_period_type);  // Set due period type (days, hours)
                  $('#previous-task-section').show();  // Show previous task section
                  $('#fixed-date-section').hide();  // Hide fixed date section
               } else {
                  $('#fixed-date-section').hide();
                  $('#previous-task-section').hide();
               }

               $('#task-form').show();
               $('#save_task_order').text('Update Task');  // Change button text to 'Update Task'
         }
      });
   });

   // Delete Task
   $(document).on('click', '.delete-task', function() {
      var taskId = $(this).data('id');

      if(confirm('Are you sure?')) {
         $.ajax({
               url: `/client/order/delete-task/${taskId}`, // Your controller route
               method: 'GET',
               success: function() {
                  // Remove the task row
                  $(`tr[data-id="${taskId}"]`).remove();

                  // Check if there are no more task rows left
                  if ($('#task-list tbody tr').length === 0 || $('#task-list tbody tr:visible').length === 0) {
                     // Append "No tasks found" message if no tasks are left
                     $('#task-list tbody').append(`
                          <tr class="no_record">
                              <td colspan="3" style="text-align: center;">No tasks found.</td>
                           </tr>
                     `);
                  }
               }
         });
      }
   });

   $(document).ready(function() {
      $('#due-date-type').on('change', function() {
         var dueType = $(this).val();
         if (dueType === 'fixed') {
               $('#fixed-date-section').show();
               $('#previous-task-section').hide();
         } else if (dueType === 'previous') {
               $('#fixed-date-section').hide();
               $('#previous-task-section').show();
         } else {
               $('#fixed-date-section').hide();
               $('#previous-task-section').hide();
         }
      });

      // Trigger the change event on page load if editing a task
      $('#due-date-type').trigger('change');
   });

   $(document).on('change', '.task-status', function() {
      var taskId = $(this).data('id');
      var isCompleted = $(this).is(':checked') ? 1 : 0;

      $.ajax({
         url: `/client/order/update-task-status/${taskId}`,
         method: 'POST',
         data: {
               _token: '{{ csrf_token() }}',
               status: isCompleted
         },
         success: function(response) {
               if (isCompleted === 1) {
                  $(`tr[data-id="${taskId}"]`).remove();
               }

               // Check if all tasks are removed and show "No tasks found" message
               if ($('#task-list tbody tr').length === 0) {
                  $('#task-list tbody').append(`
                     <tr class="no_record">
                           <td colspan="3" style="text-align: center;">No tasks found.</td>
                     </tr>
                  `);
               }
         },
         error: function(error) {
               console.error('Error updating task status:', error);
         }
      });
   });

   $(document).ready(function() {
      $(document).on('click', '.show_task_by_status', function() {
         var showCompleted = $(this).text() === 'Show Completed Tasks';
         var orderId = $(this).data('order-id'); // Get order ID from the dropdown

         // Toggle the button text
         $(this).text(showCompleted ? 'Show Incomplete Tasks' : 'Show Completed Tasks');

         // Make AJAX request to fetch tasks based on order_id and status
         $.ajax({
               url: `/client/order/tasks/${orderId}`, // Update to pass order_id in the route
               method: 'GET',
               data: {
                  status: showCompleted ? 1 : 0 // 1 for completed, 0 for incomplete
               },
               success: function(response) {
                  // Clear the current task list
                  $('#task-list tbody').empty();

                  if (response.tasks.length === 0) {
                     // Show no tasks found message
                     $('#task-list tbody').append(`
                           <tr class="no_record">
                              <td colspan="3" style="text-align: center;">No tasks found.</td>
                           </tr>
                     `);
                  } else {
                     // Append the tasks to the table
                     response.tasks.forEach(function(task) {
                           let taskClass = task.status === 1 ? 'completed-task' : '';
                           let actionButtons = task.status === 0 ? `
                              <button class="btn btn-sm btn-primary edit-task" data-id="${task.id}">
                                 <i class="bx bx-edit"></i>
                              </button>
                              <button class="btn btn-sm btn-danger delete-task" data-id="${task.id}">
                                 <i class="bx bx-trash"></i>
                              </button>` : ''; // Remove actions for completed tasks

                           $('#task-list tbody').append(`
                              <tr data-id="${task.id}" class="${taskClass}">
                                 <td>${task.status === 0 ? `<input type="checkbox" class="task-status" data-id="${task.id}">` : ''}</td> <!-- Remove checkbox for completed tasks -->
                                 <td>
                                       <strong>${task.name}</strong><br>
                                       <span>${task.description}</span><br>
                                       <small class="text-muted">Due: ${task.due_date ? task.due_date : 'No due date'}</small>
                                 </td>
                                 <td>${actionButtons}</td> <!-- Show action buttons only for incomplete tasks -->
                              </tr>
                           `);
                     });
                  }
               }
         });
      });
   });

   $(document).on('click', '.open_data_project', function() {
      $('#addDataModal').modal('show');
   });

   $('#save-project-data').on('click', function() {
      var formData = $('#project-data-form').serialize();

      $.ajax({
         url: '/client/order/save-project-data',
         method: 'POST',
         data: formData,
         headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
         },
         success: function(response) {
               // Use the order ID from $order->id for redirection
               window.location.href = '/client/order/projectdata/' + {{ $order->id }};
         }
      });
   });

   function deleteData(orderId) {
      if (confirm('Are you sure you want to delete this data?')) {
         $.ajax({
               url: `/order/delete-data/${orderId}`,
               method: 'DELETE',
               headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
               success: function(response) {
                  location.reload();  // Reload the page after deletion
               }
         });
      }
   }
</script>

@endsection