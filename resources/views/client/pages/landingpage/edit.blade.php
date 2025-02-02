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
</style>

<div class="container-xxl flex-grow-1 container-p-y" id="app">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Landing Pages /</span> Edit Landing Page
    </h4>

    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Landing Page Info</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('landingpage.update', $landingPage->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <!-- Landing Page Title -->
                        <div class="mb-3">
                            <label class="form-label" for="title">Landing Page Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $landingPage->title) }}" placeholder="Enter title" required>
                        </div>

                        <!-- Description -->
                        <div class="mb-3" style="display:none;">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" placeholder="Enter description" rows="4" required>{{ old('description', $landingPage->description) }}</textarea>
                        </div>

                       <!-- Image -->
                        <div class="mb-3" style="display:none;">
                            <label class="form-label" for="image">Upload Image</label>

                            <!-- Show current image if exists -->
                            @if(!empty($landingPage->image) && \Storage::exists('public/' . $landingPage->image))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $landingPage->image) }}" alt="Current Image" style="max-width: 200px; max-height: 150px; display: block;">
                                </div>

                                <!-- Checkbox to remove the current image -->
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">Remove current image</label>
                                </div>
                            @endif

                            <!-- File input for uploading new image -->
                            <input class="form-control" type="file" id="image" name="image" accept="image/*">
                            <small class="form-text text-muted">Leave blank to keep the current image.</small>
                        </div>


                        <!-- Fields -->
                        <div class="mb-3" style="display:none;">
                            <label class="form-label" for="fields">Additional Fields (Optional)</label>
                            <textarea class="form-control" id="fields" name="fields" placeholder="Enter additional fields as JSON">{{ old('fields', $landingPage->fields) }}</textarea>
                        </div>

                        <!-- Checkbox for Show in Sidebar -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_in_sidebar" name="show_in_sidebar" {{ $landingPage->show_in_sidebar ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_in_sidebar">
                                    Show in portal sidebar
                                </label>
                            </div>
                        </div>

                        <!-- Checkbox for Show Coupon Field -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="show_coupon_field" name="show_coupon_field" {{ $landingPage->show_coupon_field ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_coupon_field">
                                    Show coupon field
                                </label>
                            </div>
                        </div>

                        <!-- Visibility -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="is_visible" name="is_visible" {{ $landingPage->is_visible ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_visible"> Make this landing page visible</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('landingpage.list') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
