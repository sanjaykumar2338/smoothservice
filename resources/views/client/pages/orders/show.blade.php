@extends('client.client_template')
@section('content')
<style>
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
        <ul class="nav nav-pills flex-column flex-sm-row mb-4">
            <li class="nav-item">
                <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages-profile-teams.html"><i class="bx bx-group me-1"></i> Teams</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages-profile-projects.html"><i class="bx bx-grid-alt me-1"></i> Projects</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="pages-profile-connections.html"><i class="bx bx-link-alt me-1"></i> Connections</a>
            </li>
        </ul>
    </div>
    </div>


   <div class="row">
      <div class="col-xl-8 col-lg-7 col-md-7">
         <!-- serice heading -->
         <div class="card card-action mb-4">
            <div class="card-body">
               <div class="">
                  <label class="form-label" for="full_editor">Description</label>
                  <div id="full-editor">
                  </div>
                  <textarea id="editor_content" style="display:none" name="editor_content" class="form-control">
            </textarea>
               </div>
               <div class="card-footer d-flex justify-content-end">
                  <button class="btn btn-label-primary p-1 btn-sm"><i class="bx bx-save"></i> Save Note</button>
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
               <p class="card-text text-uppercase">About</p>
               <ul class="list-unstyled mb-4">
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bx-user bx-xs"></i><span class="fw-medium mx-2">Full Name:</span>
                     <span>John Doe</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bx-check bx-xs"></i><span class="fw-medium mx-2">Status:</span>
                     <span>Active</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bx-star bx-xs"></i><span class="fw-medium mx-2">Role:</span>
                     <span>Developer</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bx-flag bx-xs"></i><span class="fw-medium mx-2">Country:</span> <span>USA</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bx-detail bx-xs"></i><span class="fw-medium mx-2">Languages:</span>
                     <span>English</span>
                  </li>
               </ul>
               <p class="card-text text-uppercase">Contacts</p>
               <ul class="list-unstyled mb-4">
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bx-phone bx-xs"></i><span class="fw-medium mx-2">Contact:</span>
                     <span>(123) 456-7890</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bx-chat bx-xs"></i><span class="fw-medium mx-2">Skype:</span>
                     <span>john.doe</span>
                  </li>
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bx-envelope bx-xs"></i><span class="fw-medium mx-2">Email:</span>
                     <span>john.doe@example.com</span>
                  </li>
               </ul>
               <small class="text-muted text-uppercase">Teams</small>
               <ul class="list-unstyled mt-3 mb-0">
                  <li class="d-flex align-items-center mb-3">
                     <i class="bx bxl-github text-primary me-2"></i>
                     <div class="d-flex flex-wrap">
                        <span class="fw-medium me-2">Backend Developer</span><span>(126 Members)</span>
                     </div>
                  </li>
                  <li class="d-flex align-items-center">
                     <i class="bx bxl-react text-info me-2"></i>
                     <div class="d-flex flex-wrap">
                        <span class="fw-medium me-2">React Developer</span><span>(98 Members)</span>
                     </div>
                  </li>
               </ul>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection