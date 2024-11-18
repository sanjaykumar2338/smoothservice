@extends('c_main.c_dashboard')
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
   .tagify{
   height: auto;
   }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
   <h4 class="py-3 breadcrumb-wrapper mb-4">
      <span class="text-muted fw-light">Orders /</span> Order Details
   </h4>
   <div class="row align-items-center">
      <div class="col-md-6">
         <h5 class="card-action-title mb-4 fs-2 text-black">{{$order->title}}</h5>
      </div>
      <div class="col-md-6 d-flex justify-content-end">
         <ul class="nav nav-pills flex-sm-row mb-4">
            <li class="nav-item dropdown">
               <a class="nav-link active dropdown-toggle" style="background-color: {{ $orderStatus?->color ?? 'defaultColor' }}" href="javascript:void(0);" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
               <span id="selectedStatus">{{ $orderStatus?->name ?: 'Select Status' }}</span>
               </a>
               <ul class="dropdown-menu" aria-labelledby="profileDropdown">
                  @foreach($orderstatus as $status)
                  <li>
                     <a class="dropdown-item" href="javascript:void(0);" 
                        onclick="changeStatus('{{ $status->id }}', '{{ $status->name }}', '{{ $status->color }}')">{{ $status->name }}</a>
                  </li>
                  @endforeach
               </ul>
            </li>
            <li class="nav-item">
               <a class="nav-link {{ $order->notification == 0 ? 'cross-overlay' : '' }}" href="javascript:void(0);" id="notification-icon" onclick="toggleIcon()">
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
                     <li><a class="dropdown-item" href="{{route('order.edit',$order->id)}}">Edit</a></li>
                     <li><a class="dropdown-item" href="{{route('order.project_data',$order->id)}}">Add Project data</a></li>
                     <li><a style="display:none;" class="dropdown-item" href="javascript:void(0);">Create an invoice</a></li>
                     <li>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="duplicateOrder({{ $order->id }})">Duplicate order</a>
                     </li>
                     <li>
                        <a class="dropdown-item" href="javascript:void(0);" onclick="deleteOrder({{ $order->id }})">Delete order</a>
                     </li>
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
         <!-- Projects table -->
         <div class="card mb-4">
            <h5 class="card-header">History</h5>
            <ul id="history-list" class="list-group">
               <!-- The first 5 history messages will be loaded here via JavaScript -->
            </ul>
            <!-- Load More Button -->
            <div id="load-more-container" class="text-center mt-3">
               <button id="load-more-btn" class="btn btn-primary">Load More</button>
            </div>
            <br>
         </div>
         <!--/ Projects table -->
      </div>
      <div class="col-xl-4 col-lg-5 col-md-5">
         <!-- About User -->
         <div class="card mb-4">
            <div class="card-body" style="height: 600px;">
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
                     <span class="fw-medium mx-2">Created</span>
                     <span>
                     {{ $order->date_added ? $order->date_added->format('M d') : 'N/A' }}
                     </span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Started</span>
                     <span>
                     {{ $order->date_started ? $order->date_started->format('M d') : 'N/A' }}
                     </span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Due</span>
                     <span>
                     {{ $order->date_due ? $order->date_due->format('M d') : 'N/A' }}
                     </span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <span class="fw-medium mx-2">Completed</span>
                     <span>
                     {{ $order->date_completed ? $order->date_completed->format('M d') : 'N/A' }}
                     </span>
                  </li>
               </ul>
               <small class="text-uppercase">Select Team Members</small>
               <div>
                  <select
                     id="order_team_member"
                     class="selectpicker w-100"
                     data-style="btn-default"
                     multiple
                     data-max-options="2">
                  @foreach($teamMembers as $team)
                  <option value="{{ $team->id }}"
                  {{-- Mark as selected if this team member is already assigned to the order --}}
                  @if($order->teamMembers->contains($team->id)) selected @endif
                  {{-- Disable the selection if permission logic dictates it --}}
                  @if(
                  (!checkPermission('assign_to_self') && $team->id === getUserID()) || 
                  (!checkPermission('assign_to_others') && $team->id !== getUserID())
                  ) disabled @endif>
                  {{ $team->first_name }} {{ $team->last_name }} 
                  {{-- If already assigned, mark it clearly (optional, for better visibility) --}}
                  @if($order->teamMembers->contains($team->id))
                  (Already Assigned)
                  @endif
                  </option>
                  @endforeach
                  </select>
               </div>
               <small class=" text-uppercase">Select Tags</small>
               <div class="">
                  <input
                     id="TagifyCustomInlineSuggestion"
                     name="TagifyCustomInlineSuggestion"
                     class="form-control"
                     placeholder="select tags"
                     value="{{$existingTagsName}}" />
               </div>
            </div>
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
        url: '/order/' + orderId + '/save-note',
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
     
     // Toggle the cross-overlay class
     if (parent.classList.contains('cross-overlay')) {
        parent.classList.remove('cross-overlay');
        saveNotificationStatus(1);  // Set notification to 1 (on)
     } else {
        parent.classList.add('cross-overlay');
        saveNotificationStatus(0);  // Set notification to 0 (off)
     }
   }
   
   function saveNotificationStatus(status) {
     $.ajax({
        url: "{{ route('order.saveNotification') }}",  // Replace with your route
        method: "POST",
        data: {
              _token: "{{ csrf_token() }}",  // CSRF token for security
              order_id: {{ $order->id }},    // Pass the order ID
              notification: status           // Pass the new notification status (0 or 1)
        },
        success: function(response) {
              console.log('Notification status updated successfully!');
        },
        error: function(error) {
              console.log('Error updating notification status.');
        }
     });
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
        url = `/order/update-task/${editingTaskId}`;
     } else {
        url = '/order/save-task';
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
        url: `/order/get-task/${taskId}`,
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
              url: `/order/delete-task/${taskId}`, // Your controller route
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
        url: `/order/update-task-status/${taskId}`,
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
              url: `/order/tasks/${orderId}`, // Update to pass order_id in the route
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
        url: '/order/save-project-data',
        method: 'POST',
        data: formData,
        headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
              // Use the order ID from $order->id for redirection
              window.location.href = '/order/projectdata/' + {{ $order->id }};
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
   
   $(document).ready(function() {
     let isTeamMessage = false; // Track if sending to the team or client
   
     // Show/hide reply editor for the client
     $('#reply-client-btn').on('click', function() {
        isTeamMessage = false; // Reset to client message mode
        $('#reply-editor-section').show();
        $('#reply-editor').attr('placeholder', 'Type your reply to the client...');
     });
   
     // Show/hide reply editor for the team
     $('#message-team-btn').on('click', function() {
        isTeamMessage = true; // Switch to team message mode
        $('#reply-editor-section').show();
        $('#reply-editor').attr('placeholder', 'Type your message to the team...');
     });
   
     // Toggle the schedule options
     $('#show-schedule').on('click', function() {
        $('#schedule-options').toggle();
     });
   
     // Send message to client or team
     $('#send-reply-btn').on('click', function() {
        const message = $('#reply-editor').val();
        const scheduleAt = $('#schedule-datetime').val();
        const cancelOnReply = $('#cancel-on-reply').is(':checked') ? 1 : 0; // Ensure it's a boolean
        const messageType = isTeamMessage ? 'team' : 'client'; // Set message type
   
        if (!message) {
           alert('Please enter a reply.');
           return;
        }
   
        $.ajax({
           url: '/order/send-reply', // Using the same route
           method: 'POST',
           data: {
                 message: message,
                 schedule_at: scheduleAt,
                 cancel_if_replied: cancelOnReply,
                 order_id: '{{ $order->id }}',
                 client_id: '{{ $order->client->id }}',
                 message_type: messageType, // Differentiating between client and team messages
                 _token: '{{ csrf_token() }}'
           },
           success: function(response) {
                 alert('Message sent successfully.');
                 $('#reply-editor-section').hide();  // Hide editor
                 $('#reply-editor').val('');  // Reset editor
   
                 // Append the new message to the message list dynamically
                 $('#message-list').append(`
                 <li class="mb-3" style="" id="reply${response.reply.id}">
                    <div class="d-flex align-items-start">
                          <!-- Profile Icon or Avatar -->
                          <div class="me-2">
                             ${response.reply.profile_image 
                                ? `<img src="${response.reply.profile_image}" alt="Profile" class="rounded-circle" style="width: 40px; height: 40px;">` 
                                : `<i class="bx bx-user-circle" style="font-size: 40px;"></i>`}
                          </div>
   
                          <!-- Message Content -->
                          <div class="flex-grow-1">
                             <strong>${isTeamMessage ? 'You messaged the team' : 'You replied'}:</strong><br>
                             <span>${response.reply.message}</span><br>
                             <small class="text-muted">${new Date().toLocaleString()}</small>
                          </div>
   
                          <!-- Dropdown Menu -->
                          <div class="dropdown ms-auto">
                             <button class="btn p-0" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bx bx-dots-vertical-rounded"></i>
                             </button>
                             <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item edit-reply" href="#" data-id="${response.reply.id}">Edit</a></li>
                                <li><a class="dropdown-item delete-reply" href="#" data-id="${response.reply.id}">Delete</a></li>
                             </ul>
                          </div>
                    </div>
                 </li>
              `);
           },
           error: function(xhr, status, error) {
                 alert('Failed to send message: ' + xhr.responseText);
           }
        });
     });
   
     // Delete reply content (reset form)
     $('#delete-reply-btn').on('click', function() {
        $('#reply-editor').val('');
        $('#schedule-datetime').val('');
        $('#cancel-on-reply').prop('checked', false);
        $('#reply-editor-section').hide();  // Hide editor
     });
   });
   
   // Toggle the display of the schedule options
   document.getElementById('show-schedule').addEventListener('click', function() {
     const scheduleOptions = document.getElementById('schedule-options');
     scheduleOptions.style.display = scheduleOptions.style.display === 'none' ? 'block' : 'none';
   });
   
   let currentPage = 1; // Track the current page
   let lastPage = false; // Flag to track if we are on the last page
   
   $(document).ready(function() {
     // Function to load history data
     var orderId = '{{ $order->id }}'
     function loadHistory(page) {
        $.ajax({
              url: `/order/${orderId}/history?page=${page}`,
              method: 'GET',
              success: function(response) {
                 const data = response.data;
                 
                 // Append the history messages
                 data.forEach(function(history) {
                    let messageContent = history.action_details;
   
                    // Check if message is valid JSON and format it
                    if (isJson(messageContent)) {
                          messageContent = `<pre>${JSON.stringify(JSON.parse(messageContent), null, 4)}</pre>`;
                    }
   
                    // Append history to the list
                    $('#history-list').append(`
                          <li class="list-group-item">
                             <strong>${history.user.name}:</strong> ${capitalize(history.action_type)} <br>
                             <small class="text-muted">${formatTime(history.created_at)}</small>
                             <p>${messageContent}</p>
                          </li>
                    `);
                 });
   
                 // Check if we are on the last page
                 if (response.current_page >= response.last_page) {
                    lastPage = true;
                    $('#load-more-btn').hide(); // Hide the Load More button if no more pages
                 }
              }
        });
     }
   
     // Function to check if a string is valid JSON
     function isJson(str) {
        try {
              JSON.parse(str);
        } catch (e) {
              return false;
        }
        return true;
     }
   
     // Format time to a readable format
     function formatTime(dateTime) {
        const date = new Date(dateTime);
        return date.toLocaleString();
     }
   
     // Capitalize the first letter of a string
     function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
     }
   
     // Initially load the first 5 messages
     loadHistory(currentPage);
   
     // Load more messages when "Load More" button is clicked
     $('#load-more-btn').on('click', function() {
        if (!lastPage) {
              currentPage++;
              loadHistory(currentPage);
        }
     });
   });
   
   function changeStatus(statusId, statusName, statusColor) {
     const orderId = {{ $order->id }}; // Pass the order ID from the Blade variable
   
     fetch(`/order/update-status/${orderId}`, {
        method: 'POST',
        headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add your CSRF token here
        },
        body: JSON.stringify({ status_id: statusId })
     })
     .then(response => response.json())
     .then(data => {
        if (data.success) {
              // Update the displayed selected status
              document.getElementById('selectedStatus').innerText = statusName;
              document.getElementById('profileDropdown').style.backgroundColor = statusColor; // Change background color
              //alert('Status updated successfully!');
        } else {
              alert('Failed to update status.');
        }
     })
     .catch(error => {
        console.error('Error:', error);
        alert('Error updating status.');
     });
   }
   
   const whitelist = @json($tags->map(function($tag) {
     return ['id' => $tag->id, 'name' => $tag->name];
   }));
   
   const existingTags = @json($existingTags); // Fetch existing tag IDs from the controller
   
   const TagifyCustomInlineSuggestionEl = document.querySelector('#TagifyCustomInlineSuggestion');
   let TagifyCustomInlineSuggestion = new Tagify(TagifyCustomInlineSuggestionEl, {
     whitelist: whitelist.map(tag => tag.name), // Set only names for display
     maxTags: 10,
     dropdown: {
        maxItems: 20,
        classname: 'tags-inline',
        enabled: 0,
        closeOnSelect: false
     }
   });
   
   console.log(existingTags,'existingTags')
   // Initialize Tagify with existing tags
   TagifyCustomInlineSuggestion.addTags(existingTags.map(tagId => {
     const foundTag = whitelist.find(item => item.id === tagId);
     return foundTag ? foundTag.name : null; // Add existing tags by name
   }).filter(tag => tag !== null)); // Filter out null values
   
   // Debounce function to limit the frequency of the AJAX call
   let debounceTimer;
   function saveTagsDebounced() {
     clearTimeout(debounceTimer);
     debounceTimer = setTimeout(() => {
        const selectedTags = TagifyCustomInlineSuggestion.value.map(tag => {
              const foundTag = whitelist.find(item => item.name === tag.value);
              return foundTag ? foundTag.id : null; // Get the ID or null
        }).filter(id => id !== null); // Filter out null values
   
        // AJAX request to save selected tags
        const orderId = {{ $order->id }}; // Assuming you have the order ID available
        fetch(`/order/${orderId}/update-tags`, {
              method: 'POST',
              headers: {
                 'Content-Type': 'application/json',
                 'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token
              },
              body: JSON.stringify({ tags: selectedTags.join(',') }) // Send tags as a comma-separated string
        })
        .then(response => response.json())
        .then(data => {
              if (data.success) {
                 //alert(data.message);
              } else {
                 alert('Failed to update tags.');
              }
        })
        .catch(error => {
              console.error('Error:', error);
              alert('Error updating tags.');
        });
     }, 500); // 500 milliseconds delay
   }
   
   // Listen for changes on Tagify
   TagifyCustomInlineSuggestion.on('change', saveTagsDebounced);
   
   // Listen for changes in the selectpicker
   timer = 500;
   $(document.body).on("change", "#order_team_member", function() {
     clearTimeout(timer); // Clear any previous timer
   
     // Set a timeout of 0.5 seconds (500 milliseconds)
     timer = setTimeout(function() {
        // Get the selected team member IDs
        var selectedTeamMembers = $('#order_team_member').val();
   
        // Check if permissions allow assigning to self or others
        var canAssignToSelf = "{{ checkPermission('assign_to_self') }}"; // Check permission for assign_to_self
        var canAssignToOthers = "{{ checkPermission('assign_to_others') }}"; // Check permission for assign_to_others
        var loggedInUserId = "{{ getUserID() }}"; // Get the logged-in user's ID
   
        // Filter the selected team members based on permissions
        var validSelectedMembers = selectedTeamMembers.filter(function(memberId) {
              // If they have 'assign_to_self' permission, they can select themselves
              if (canAssignToSelf && memberId == loggedInUserId) {
                 return true;
              }
   
              // If they have 'assign_to_others' permission, they can select other members
              if (canAssignToOthers && memberId != loggedInUserId) {
                 return true;
              }
   
              // If no permission, reject the member
              return false;
        });
   
        // Make sure valid members are selected
        if (validSelectedMembers.length > 0 || 1) {
              // Perform AJAX request to save team members
              $.ajax({
                 url: "{{ route('order.saveTeamMembers') }}", // Your route to save team members
                 method: "POST",
                 data: {
                    _token: "{{ csrf_token() }}", // CSRF token for Laravel
                    order_id: {{ $order->id }},   // Pass the order ID
                    team_member_ids: validSelectedMembers // Pass the valid selected team member IDs
                 },
                 success: function(response) {
                    console.log('Team members saved successfully!');
                 },
                 error: function(error) {
                    alert('Error saving team members.');
                 }
              });
        } else {
              alert('You do not have permission to assign these team members.');
              location.reload(true);
        }
     }, 500); // 0.5 second delay before saving
   });
   
</script>
<script>
   function duplicateOrder(orderId) {
      if (confirm('Are you sure you want to duplicate this order?')) {
         // Make an AJAX POST request to duplicate the order
         fetch(`/orders/${orderId}/duplicate`, {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token for security
               }
         })
         .then(response => response.json())
         .then(data => {
               if (data.success) {
                  alert('Order duplicated successfully!');
                  // Optionally reload the page or update the UI to show the new order
                  window.location.reload();
               } else {
                  alert('Failed to duplicate the order.');
               }
         })
         .catch(error => {
               console.error('Error duplicating order:', error);
               alert('An error occurred.');
         });
      }
   }
   
   function deleteOrder(orderId) {
      if (confirm('Are you sure you want to delete this order?')) {
         // Make an AJAX DELETE request to delete the order
         fetch(`/orders/${orderId}/delete`, {
               method: 'DELETE',
               headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token for security
               }
         })
         .then(response => response.json())
         .then(data => {
               if (data.success) {
                  alert('Order deleted successfully!');
                  // Optionally redirect or update the UI
                  window.location.href = '/client/order/list'; // Redirect to the orders list
               } else {
                  alert('Failed to delete the order.');
               }
         })
         .catch(error => {
               console.error('Error deleting order:', error);
               alert('An error occurred.');
         });
      }
   }
</script>

<script>
    // Edit message
    $(document).on('click', '.edit-reply', function (e) {
        e.preventDefault();
        var replyId = $(this).data('id');
        var replyText = prompt("Edit your message:");
        if (replyText) {
            $.ajax({
                url: '/order/replies/' + replyId + '/edit',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    message: replyText
                },
                success: function (response) {
                    if (response.success) {
                        location.reload();
                    }
                }
            });
        }
    });

    // Delete message
    $(document).on('click', '.delete-reply', function (e) {
        e.preventDefault();
        var replyId = $(this).data('id');
        if (confirm("Are you sure you want to delete this message?")) {
            $.ajax({
                url: '/order/replies/' + replyId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success) {
                        alert(response.message);
                        // Remove the closest li element that contains the delete button
                        $('#reply'+replyId).remove();
                    }
                }.bind(this) // Bind the 'this' context to the success callback
            });
        }
    });

</script>
@endsection