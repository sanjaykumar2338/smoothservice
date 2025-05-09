@extends('c_main.c_dashboard')

@section('title', $intake_form->form_name ?? 'Feedback Form')

@section('content')

<div class="container mt-5">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <a href="{{ route('portal.orders') }}" class="text-muted fw-light">Orders</a> /
        <span>Intake Form</span>
    </h4>

    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold">Your project information</h3>
            <p class="text-muted mb-0">{{ $intake_form->form_name ?? 'Mixed Order' }}</p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form id="feedback_form" action="{{ route('portal.orders.storeIntakeForm') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="invoice_id" value="{{ $invoice_no }}">
                <input type="hidden" name="landing_page" value="{{ $landing_page }}">
                <input type="hidden" name="order_id" value="{{ $order }}">

                <div id="render-form" class="rendered-form"></div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Required Libraries -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/formBuilder/3.6.1/form-render.min.css" rel="stylesheet">

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://formbuilder.online/assets/js/form-render.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    let formData = @json($intake_form->form_fields ?? '[]');

    try {
        if (typeof formData === "string") {
            formData = JSON.parse(formData);
        }

        formData = formData.filter(field => field.type !== "button" && field.type !== "submit");

        formData.forEach(field => {
            if (field.type === "file") {
                field.className = "form-control";
            }
        });
    } catch (error) {
        console.error("Error parsing form JSON:", error);
        formData = [];
    }

    $('#render-form').formRender({
        dataType: 'json',
        formData: formData
    });

    $("#feedback_form").on("submit", function (e) {
        e.preventDefault();

        let formArray = [];
        let hasError = false;
        let fileInputs = [];

        $(".rendered-form .form-control").each(function () {
            let label = $(this).siblings("label").text();
            let type = $(this).attr("type") || "text";
            let value = $(this).val();

            if ($(this).prop("required") && value.trim() === "") {
                $(this).addClass("is-invalid");
                hasError = true;
            } else {
                $(this).removeClass("is-invalid");
            }

            if (type === "file" && this.files.length > 0) {
                fileInputs.push({ input: this, name: label, label, type });
            } else {
                formArray.push({ name: label, label, type, value });
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

        fileInputs.forEach(obj => {
            let file = obj.input.files[0];
            let reader = new FileReader();

            reader.onloadend = function () {
                formArray.push({
                    name: obj.name,
                    label: obj.label,
                    type: obj.type,
                    value: reader.result
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
            url: "{{ route('portal.orders.storeIntakeForm') }}",
            method: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                landing_page: "{{ $landing_page }}",
                invoice_id: "{{ $invoice_no }}",
                order_id: "{{ $order }}",
                form_data: formData
            },
            success: function () {
                alert("Feedback submitted successfully!");
                window.location.href = '{{ route("portal.orders") }}';
            },
            error: function (xhr) {
                alert("Error: " + (xhr.responseJSON?.message || 'Please try again.'));
            }
        });
    }
});
</script>
@endsection
