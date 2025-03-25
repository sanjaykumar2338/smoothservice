<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>Feedback Form | {{ env('APP_NAME') }}</title>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    
    <!-- FormBuilder -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/formBuilder/3.6.1/form-render.min.css">
    
    @php
        $company_settings = App\Models\CompanySetting::where('user_id', auth()->id())->first();
    @endphp

    @if($company_settings && $company_settings->favicon && file_exists(public_path('storage/' . $company_settings->favicon)))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $company_settings->favicon) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('/assets/img/favicon/favicon.ico') }}" />
    @endif
    
    <style>
        body {
            background-color: #283144;
        }
        
        .feedback-container {
            max-width: 600px;
            margin: 50px auto;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            padding: 15px;
            border-radius: 10px 10px 0 0;
        }

        .form-container {
            padding: 20px;
        }

        .btn-submit {
            background-color: #28a745;
            color: white;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
        }

        .btn-submit:hover {
            background-color: #218838;
        }

        .error-message {
            color: red;
            font-size: 14px;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container feedback-container">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-comments"></i> {{$intake_form->form_name ?? 'Feedback Form'}}
            </div>
            <div class="form-container">
                <form id="feedback_form" action="{{route('storeFeedback')}}" name="feedback_form" method="post" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="invoice_id" value="{{$invoice_no}}">
                    <input type="hidden" name="landing_page" value="{{$landing_page}}">

                    <div id="render-form"></div>
                    <div class="text-center p-3">
                        <button type="submit" class="btn btn-primary d-grid w-100">Submit Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://formbuilder.online/assets/js/form-render.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            var formData = @json($intake_form->form_fields ?? '[]');

            try {
                if (typeof formData === "string") {
                    formData = JSON.parse(formData);
                }

                // Remove submit buttons
                formData = formData.filter(field => field.type !== "button" && field.type !== "submit");

                // Ensure file inputs are properly handled
                formData.forEach(field => {
                    if (field.type === "file") {
                        field.className = "form-control"; // Apply Bootstrap styling
                    }
                });

            } catch (error) {
                console.error("Error parsing form JSON:", error);
                formData = [];
            }

            var formRenderOpts = {
                dataType: 'json',
                formData: formData
            };

            var renderedForm = $('#render-form');
            renderedForm.formRender(formRenderOpts);

            console.log(renderedForm.html()); // Debugging

            // Form submission logic
            $("#feedback_form").on("submit", function (e) {
                e.preventDefault();
                
                let formArray = [];
                let hasError = false;
                let fileInputs = [];

                $(".rendered-form .form-control").each(function () {
                    let fieldName = $(this).siblings("label").text();
                    let fieldLabel = $(this).siblings("label").text();
                    let fieldType = $(this).attr("type") || "text";
                    let fieldValue = $(this).val();

                    if ($(this).prop("required") && fieldValue.trim() === "") {
                        $(this).addClass("is-invalid");
                        hasError = true;
                    } else {
                        $(this).removeClass("is-invalid");
                    }

                    if (fieldType === "file" && this.files.length > 0) {
                        fileInputs.push({ input: this, name: fieldName, label: fieldLabel, type: fieldType });
                    } else {
                        formArray.push({
                            name: fieldName,
                            label: fieldLabel,
                            type: fieldType,
                            value: fieldValue,
                        });
                    }
                });

                if (hasError) {
                    alert("Please fill all required fields.");
                    return;
                }

                if (fileInputs.length > 0) {
                    processFileInputs(fileInputs, formArray);
                } else {
                    submitForm(formArray);
                }
            });

            function processFileInputs(fileInputs, formArray) {
                let processedCount = 0;

                fileInputs.forEach(fileInputObj => {
                    let file = fileInputObj.input.files[0];
                    let reader = new FileReader();

                    reader.onloadend = function () {
                        formArray.push({
                            name: fileInputObj.name,
                            label: fileInputObj.label,
                            type: fileInputObj.type,
                            value: reader.result, // Convert file to base64
                        });

                        processedCount++;

                        if (processedCount === fileInputs.length) {
                            submitForm(formArray);
                        }
                    };

                    reader.readAsDataURL(file);
                });
            }

            function submitForm(formData) {
                $.ajax({
                    url: "{{ route('storeFeedback') }}",
                    type: "POST",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        landing_page: "{{$landing_page}}",
                        invoice_id: "{{$invoice_no}}",
                        form_data: formData,
                    },
                    success: function (response) {
                        alert("Feedback submitted successfully!");
                        window.location.href = '{{ route("portal.invoices.show", ["id" => $invoice_no]) }}';
                        //window.location.reload();
                    },
                    error: function (xhr) {
                        alert("Error submitting feedback: " + xhr.responseJSON.message);
                    },
                });
            }
        });

    </script>

</body>
</html>
