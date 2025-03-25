<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="/assets/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>{{env('APP_NAME')}}</title>

    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <!-- Favicon -->
    @php
        $company_settings = App\Models\CompanySetting::where('user_id', auth()->id())->first();
    @endphp

    @if($company_settings && $company_settings->favicon && file_exists(public_path('storage/' . $company_settings->favicon)))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $company_settings->favicon) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('/assets/img/favicon/favicon.ico') }}" />
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('/assets/vendor/fonts/boxicons.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/fonts/fontawesome.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/fonts/flag-icons.css')}}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('/assets/vendor/css/rtl/core.css')}}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/css/rtl/theme-default.css')}}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{asset('/assets/css/demo.css')}}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/typeahead-js/typeahead.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/select2/select2.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/tagify/tagify.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/typeahead-js/typeahead.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/apex-charts/apex-charts.css')}}" />

    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/quill/typography.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/quill/katex.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/vendor/libs/quill/editor.css')}}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" />

    <script src="{{asset('/assets/vendor/js/helpers.js')}}"></script>
    <script src="{{asset('/assets/vendor/js/template-customizer.js')}}"></script>
    <script src="{{asset('/assets/js/config.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{asset('/assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/tagify/tagify.js')}}"></script>
  </head>

  @include('client.custom_settings')

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->


        @if(getUserType()=='web')
          @include('client.sidebar')
        @else
          @include('client.teamsidebar')
        @endif
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          <nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="container-xxl">
              <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                  <i class="bx bx-menu bx-sm"></i>
                </a>
              </div>

              @include('client.topbar')

              <!-- Search Small Screens -->
              <div class="navbar-search-wrapper search-input-wrapper container-xxl d-none">
                <input
                  type="text"
                  class="form-control search-input border-0"
                  placeholder="Search..."
                  aria-label="Search..." />
                <i class="bx bx-x bx-sm search-toggler cursor-pointer"></i>
              </div>
            </div>
          </nav>

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            @if(session('success'))
              <div class="alert alert-success alert-dismissible d-flex align-items-center" role="alert">
                <i class="bx bx-xs bx-desktop me-2"></i>
                {{session('success')}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif  

            @if(session('error'))
              <div class="alert alert-danger alert-dismissible d-flex align-items-center" role="alert">
                <i class="bx bx-xs bx-store me-2"></i>
                {{session('error')}}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif  

            @yield('content')
            
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                  ©
                  <script>
                    document.write(new Date().getFullYear());
                  </script>
                  ,
                  <a href="https://pixinvent.com" target="_blank" class="footer-link fw-medium">{{env('APP_NAME')}}</a>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>

      <!-- Drag Target Area To SlideIn Menu On Small Screens -->
      <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{asset('/assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{asset('/assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/hammer/hammer.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/i18n/i18n.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
    <script src="{{asset('/assets/vendor/js/menu.js')}}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{asset('/assets/vendor/libs/moment/moment.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/select2/select2.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/cleavejs/cleave.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
    <script src="{{asset('/assets/js/forms-typeahead.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/quill/katex.js')}}"></script>
    <script src="{{asset('/assets/vendor/libs/quill/quill.js')}}"></script>

    <!-- Main JS -->
    <script src="{{asset('/assets/js/main.js')}}"></script>

    <script src="{{asset('/assets/js/forms-selects.js')}}"></script>

    <script src="{{asset('/assets/js/forms-editors.js')}}"></script>

    <!-- Page JS -->
    <script src="{{asset('/assets/js/app-user-list.js')}}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
    <script src="https://formbuilder.online/assets/js/form-builder.min.js"></script>
    <script src="https://formbuilder.online/assets/js/form-render.min.js"></script>
    <script src="{{asset('/assets/js/custom.js')}}?v={{time()}}"></script>

    <script>
      $(document).ready(function(){  
        //for creating usage
        if($('#intake_form_save').length > 0){
          jQuery($ => {
              const fbTemplate = document.getElementById('build-wrap');
              const options = {
                disableFields: ['roles','access'], // Assuming 'roles' is the control you want to remove
              };
              const formBuilder = $(fbTemplate).formBuilder(options);

              console.log('formBuilder', formBuilder);

              $('#intake_form_save').on('submit', function(e) {
                  e.preventDefault(); // Prevent the default form submission

                  // Clear any previous errors
                  $('#error-list').empty();
                  $('#error-messages').hide();

                  // Get the form data
                  const formData = formBuilder.actions.getData('json');

                  // Collect additional data if needed
                  const formName = $('#form_name').val();
                  const checkmark = $('#checkmark').is(':checked') ? 1 : 0;
                  const onboardingField = $('#onboarding_field').val();

                  // Send the data to the backend
                  $.ajaxSetup({
                      headers: {
                          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                      }
                  });

                  $.ajax({
                      url: '{{ route("intakeform.store") }}', // Backend route
                      method: 'POST',
                      data: {
                          form_name: formName,
                          form_fields: formData, // JSON data from form builder
                          checkmark: checkmark,
                          onboarding_field: onboardingField
                      },
                      success: function(response) {
                          alert('Form saved successfully!');
                          window.location.href = '{{ route("service.intakeform.list") }}'; // Redirect
                      },
                      error: function(xhr) {
                          $('#error-list').html(''); // Clear old errors
                          if (xhr.status === 422) {
                              const errors = xhr.responseJSON.errors;
                              for (let field in errors) {
                                  if (errors.hasOwnProperty(field)) {
                                      $('#error-list').append('<li>' + errors[field][0] + '</li>');
                                  }
                              }
                              $('#error-messages').show();
                          } else {
                              console.error('Error saving form:', xhr);
                              alert('There was an error saving the form.');
                          }
                      }
                  });

              });
          });
        }

        if ($('#intake_form_edit').length > 0) {
            jQuery($ => {
                var fields = $('#intake_frm_details').val();
                const existingFormData = fields;
                const id = $('#iding').val();

                const fbTemplate = document.getElementById('build-wrap-edit');
                const optionsedit = {
                    formData: existingFormData
                };

                const formBuilder = $(fbTemplate).formBuilder(optionsedit);

                $('#intake_form_edit').on('submit', function(e) {
                    e.preventDefault(); // Prevent the default form submission

                    // Clear any previous errors
                    $('#error-list').empty();
                    $('#error-messages').hide();

                    // Get the form data
                    const formData = formBuilder.actions.getData('json');

                    // Collect additional data if needed
                    const formName = $('#form_name').val();
                    const checkmark = $('#checkmark').is(':checked') ? 1 : 0;
                    const onboardingField = $('#onboarding_field').val();

                    // Send the data to the backend
                    $.ajax({
                        url: '{{ route("service.intakeform.update_intake") }}', // Your updated backend route
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF token for Laravel
                            form_name: formName,
                            form_fields: formData, // JSON data from form builder
                            checkmark: checkmark,
                            onboarding_field: onboardingField,
                            id: id
                        },
                        success: function(response) {
                            alert('Form saved successfully!');
                            window.location.href = '{{ route("service.intakeform.list") }}'; // Updated route
                        },
                        error: function(xhr) {
                            // Parse and display the errors
                            if (xhr.status === 422) { // Laravel validation error status
                                const errors = xhr.responseJSON.errors;
                                for (let field in errors) {
                                    if (errors.hasOwnProperty(field)) {
                                        $('#error-list').append('<li>' + errors[field][0] + '</li>');
                                    }
                                }
                                $('#error-messages').show();
                            } else {
                                console.error('Error saving form:', xhr);
                                alert('There was an error saving the form.');
                            }
                        }
                    });
                });

                // Initialize select2 and set selected values
                var selectedtxt = $('#onboarding_field_val').val();
                if(selectedtxt!=""){
                  const selectedValues = selectedtxt.split(',');

                  if (selectedValues && selectedValues.length > 0) {
                      $('#onboarding_field').select2();
                      $('#onboarding_field').val(selectedValues).trigger('change');
                  } else {
                      console.log('No selected values to apply.');
                  }
                }
            });
        }

        setInterval(function() {
          $('.access-wrap').remove();
        }, 1000);
      })  
      </script>

  </body>
</html>
