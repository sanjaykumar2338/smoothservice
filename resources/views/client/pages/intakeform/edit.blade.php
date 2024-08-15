@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Services /</span><span class="text-muted fw-light"> Intake Form /</span> Edit 
    </h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-xl">
            <form id="intake_form_edit">
                {{ csrf_field() }}

                <!-- Error messages will be displayed here -->
                <div id="error-messages" class="alert alert-danger" style="display:none;">
                    <ul id="error-list"></ul>
                </div>

                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Intake form details</h5>
                    </div>
                    <div class="card-body">
                        Clients get access to this form after buying your service. Their order will remain Pending until the form is filled out. (?)
                        <br><br>
                        <div class="mb-0">
                            <label class="form-label" for="service_name">Form Name</label>
                            <input type="text" class="form-control" value="{{$intakeform->form_name}}" id="form_name" name="form_name" value="" placeholder="Form Name" />
                        </div>
                        <br>
                        <code>&lt;!-- Custom HTML --&gt;</code>
                        <br><br>
                        <div id="build-wrap-edit"></div>
                        <br><br>
                        
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="checkmark" id="checkmark" 
       {{ $intakeform->checkmark == 1 ? 'checked' : '' }} />

                            <label class="form-check-label" for="defaultCheck3"> Checkmark and submit once you've filled out the above form please. </label>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="mb-3">
                            
                            <input type="hidden" name="onboarding_field_val" id="onboarding_field_val" value="{{$intakeform->onboarding}}">

                            <label for="exampleFormControlSelect1" class="form-label">Onboarding forms
                            </label>

                            <select
                            id="onboarding_field"
                            name="onboarding_field"
                            class="selectpicker w-100"
                            data-style="btn-default"
                            multiple
                            data-icon-base="bx"
                            data-tick-icon="bx-check text-primary">
                            <option>Rocky</option>
                            <option>Pulp Fiction</option>
                            <option>The Godfather</option>
                            </select>

                            If this service requires one or more onboarding forms to be filled out, you can select them here. If the onboarding forms have been filled out once, clients will not be asked to do it again.
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@php
    // Convert the comma-separated string into an array
    $selectedValues = explode(',', $intakeform->onboarding);
@endphp

@endsection
