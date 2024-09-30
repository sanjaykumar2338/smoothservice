@extends('client.client_template')
@section('content')
<style>
   .mb-3 {
   margin-left: 15px;
   }
</style>
<div class="container-xxl flex-grow-1 container-p-y">
   <h4 class="py-3 breadcrumb-wrapper mb-4">
      <span class="text-muted fw-light">Account Settings /</span> Account
   </h4>
   <div class="row">
      <div class="col-md-12">
         <ul class="nav nav-pills flex-column flex-md-row mb-3">
            <li class="nav-item">
               <a class="nav-link active" href="javascript:void(0);"><i class="bx bx-user me-1"></i> Account</a>
            </li>
            <li class="nav-item hidden">
               <a class="nav-link" href="#"
                  ><i class="bx bx-detail me-1"></i> Billing & Plans</a
                  >
            </li>
            <li class="nav-item hidden">
               <a class="nav-link" href="#"
                  ><i class="bx bx-bell me-1"></i> Notifications</a
                  >
            </li>
         </ul>
         <div class="card mb-4">
            <h5 class="card-header">Profile Details</h5>
            <!-- Account -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card-body">
                <div class="d-flex align-items-start align-items-sm-center gap-4">
                    <img
                        src="{{ getAuthenticatedUser()->profile_image ? asset(getAuthenticatedUser()->profile_image) : asset('assets/img/avatars/1.png') }}"
                        alt="user-avatar"
                        class="d-block rounded"
                        height="100"
                        width="100"
                        id="uploadedAvatar" />
                    <div class="button-wrapper">
                        <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                            <span class="d-none d-sm-block">Upload new photo</span>
                            <i class="bx bx-upload d-block d-sm-none"></i>
                            <input
                                type="file"
                                id="upload"
                                class="account-file-input"
                                hidden
                                accept="image/png, image/jpeg"
                                name="profile_image"
                                onchange="uploadImage(event)" />
                        </label>
                        <label class="me-2 mt-2" tabindex="0" onclick="deleteImage()">
                            <button type="reset" style="margin-bottom:24px;" class="account-image-reset btn btn-label-secondary">Delete Photo</button>
                        </label>
                        <p class="mb-0">Allowed JPG, GIF, or PNG.</p>
                    </div>
                </div>
            </div>
            <hr class="my-0" />
            <div class="card-body">
               <form id="formAccountSettings" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <!-- Full Name -->

                     @if(getUserType()=='web')
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Name</label>
                            <input class="form-control" type="text" id="firstName" name="firstName" value="{{ getAuthenticatedUser()->name }}" autofocus />
                        </div>
                     @else
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Name</label>
                            <input class="form-control" type="text" id="firstName" name="firstName" value="{{ getAuthenticatedUser()->first_name }}" autofocus />
                        </div>
                     @endif

                     @if(getUserType()=='team')
                        <div class="col-md-6">
                            <label for="firstName" class="form-label">Last Name</label>
                            <input class="form-control" type="text" id="lastName" name="lastName" value="{{ getAuthenticatedUser()->last_name }}" autofocus />
                        </div>
                     @endif

                     <!-- Email -->
                     <div class="col-md-6">
                        <label for="email" class="form-label">E-mail</label>
                        <input class="form-control" type="email" id="email" name="email" value="{{ getAuthenticatedUser()->email }}" />
                     </div>
                     <!-- Password -->
                     <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password" />
                     </div>
                     <!-- Phone Number -->
                     <div class="col-md-6">
                        <label for="phoneNumber" class="form-label">Phone Number</label>
                        <input class="form-control" type="text" id="phoneNumber" name="phoneNumber" value="{{ getAuthenticatedUser()->phone_number }}" />
                     </div>
                     <!-- Timezone -->
                     <div class="col-md-6">
                        <label for="timeZones" class="form-label">Timezone</label>
                        <select id="timeZones" name="timezone" class="form-select">
                           <option value="">Select Timezone</option>
                           <option value="America/New_York" {{ getAuthenticatedUser()->timezone == 'America/New_York' ? 'selected' : '' }}>America/New_York</option>
                           <option value="Europe/London" {{ getAuthenticatedUser()->timezone == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                           <!-- Add more options as needed -->
                        </select>
                     </div>
                  </div>
                  <div class="row mt-2">
                     <div class="col-sm-6 mt-2">
                        <label class="switch">
                        <input type="checkbox" class="switch-input" name="push_notification" {{ getAuthenticatedUser()->push_notification ? 'checked' : '' }} />
                        <span class="switch-toggle-slider">
                        <span class="switch-on"></span>
                        <span class="switch-off"></span>
                        </span>
                        <span class="switch-label">Push notifications</span>
                        </label>
                     </div>
                  </div>
                  <!-- Submit and Reset Buttons -->
                  <div class="mt-2">
                     <button type="submit" class="btn btn-primary me-2">Save changes</button>
                     <button type="reset" class="btn btn-label-secondary">Cancel</button>
                  </div>
               </form>
            </div>
            <!-- /Account -->
         </div>
      </div>
   </div>
</div>

<script>
    function uploadImage(event) {
        let formData = new FormData();
        let file = event.target.files[0];

        formData.append('profile_image', file);
        formData.append('_token', '{{ csrf_token() }}'); // Add CSRF token for Laravel

        // AJAX request
        $.ajax({
            url: '{{ route('profile.updateImage') }}', // Route to handle the image upload
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Update the avatar preview with the new image
                    document.getElementById('uploadedAvatar').src = response.image_url;
                    alert('Profile image updated successfully.');
                } else {
                    alert('Image upload failed. Please try again.');
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please try again.');
            }
        });
    }

    function deleteImage() {
        // AJAX request to delete the image
        $.ajax({
            url: '{{ route("profile.deleteImage") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    // Replace the image with the default avatar
                    document.getElementById('uploadedAvatar').src = '{{ asset("assets/img/avatars/1.png") }}';
                    alert('Profile image deleted successfully.');
                } else {
                    alert('Image deletion failed. Please try again.');
                }
            },
            error: function(xhr) {
                alert('An error occurred. Please try again.');
            }
        });
    }
</script>
@endsection