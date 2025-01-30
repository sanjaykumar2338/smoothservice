@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }

    .custom-input-short{
        width: 11ch;
        font-size: 0.9375rem;
        font-weight: 400;
        line-height: 1.4;
        color: #677788;
        appearance: none;
        background-color: #fff;
        background-clip: padding-box;
        border: var(--bs-border-width) solid #d4d8dd;
        border-radius: var(--bs-border-radius);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        background-color: #fff;
    }

    /* Modal overlay */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    /* Modal content */
    .modal-content {
        background: white;
        padding: 20px;
        border-radius: 8px;
        max-width: 890px;
        width: 100%;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        position: relative;
        max-height: 800px;
        overflow-y: scroll;
    }

    /* Close button */
    .modal-close {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        font-size: 20px;
        background: none;
        border: none;
    }

    .option-input {
        margin-bottom: 10px;
        display: flex;
    }

    .option-menu {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
    }

    .add-option-menu {
        cursor: pointer;
        color: #007bff;
        text-decoration: underline;
    }

    .option-header {
        display: flex;
        align-items: center;
    }

    .option-header input {
        flex-grow: 1;
    }
</style>

<link href="https://unpkg.com/grapesjs/dist/css/grapes.min.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://malsup.github.io/jquery.blockUI.js"></script>
<script src="https://unpkg.com/grapesjs"></script>
<script src="https://unpkg.com/grapesjs-preset-webpage@1.0.2"></script>
<script src="https://unpkg.com/grapesjs-plugin-forms@2.0.5"></script>
<script src="https://unpkg.com/grapesjs-blocks-basic@1.0.1"></script>

<div class="container-xxl flex-grow-1 container-p-y" id="app">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Landing Pages /</span> Add Landing Page
    </h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Landing Page Info</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('landingpage.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Landing Page Title -->
                        <div class="mb-3">
                            <label class="form-label" for="title">Landing Page Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Enter description" rows="4" required></textarea>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label class="form-label" for="image">Upload Image</label>
                            <input class="form-control" type="file" id="image" name="image" accept="image/*" required>
                        </div>

                        <!-- Fields -->
                        <div class="mb-3">
                            <label class="form-label" for="fields">Additional Fields (Optional)</label>
                            <textarea class="form-control" id="fields" name="fields" placeholder="Enter additional fields as JSON"></textarea>
                        </div>

                        <!-- Checkbox for Show in Sidebar -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_sidebar" name="show_in_sidebar">
                                <label class="form-check-label" for="show_in_sidebar">
                                    Show in portal sidebar
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                You can add a link to this form to your client portal's sidebar.
                            </small>
                        </div>

                        <!-- Checkbox for Show Coupon Field -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_coupon_field" name="show_coupon_field">
                                <label class="form-check-label" for="show_coupon_field">
                                    Show coupon field
                                </label>
                            </div>
                        </div>

                        <!-- Visibility -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_visible" name="is_visible">
                                <label class="form-check-label" for="is_visible"> Make this landing page visible</label>
                            </div>
                        </div>

                      
                            
                      
                        
                    </form>

                    <div id="gjs" style=""></div>
                    <br>
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <a href="{{ route('landingpage.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var editor = grapesjs.init({
      container : '#gjs',
      plugins: ['grapesjs-preset-webpage','grapesjs-plugin-forms','gjs-blocks-basic'],
      pluginsOpts: {
        'grapesjs-preset-webpage': {
        },
        'grapesjs-plugin-forms': {
        },
        "gjs-blocks-basic": {
        
        },     
    }
    });
</script>

@endsection
