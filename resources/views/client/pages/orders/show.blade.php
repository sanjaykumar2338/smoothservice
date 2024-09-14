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
                           <button
                              type="button"
                              class="btn dropdown-toggle hide-arrow p-0"
                              data-bs-toggle="dropdown"
                              aria-expanded="false">
                           <i class="bx bx-dots-vertical-rounded"></i>
                           </button>
                           <ul class="dropdown-menu dropdown-menu-end">
                              <li><a class="dropdown-item" href="javascript:void(0);">Share connections</a></li>
                              <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                              <li>
                                 <hr class="dropdown-divider" />
                              </li>
                              <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
                           </ul>
                        </div>
                     </div>
                  </div>
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
                              <li><a class="dropdown-item" href="javascript:void(0);">Share connections</a></li>
                              <li><a class="dropdown-item" href="javascript:void(0);">Suggest edits</a></li>
                              <li>
                                 <hr class="dropdown-divider" />
                              </li>
                              <li><a class="dropdown-item" href="javascript:void(0);">Report bug</a></li>
                           </ul>
                        </div>
                     </div>
                  </div>
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
                        <td>no record.<td>
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
</script>

@endsection