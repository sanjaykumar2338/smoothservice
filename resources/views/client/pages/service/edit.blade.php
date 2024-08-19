@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }

    .custom-input-short{
        width: 11ch;
        /* padding: 0.469rem 0.735rem; */
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

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Services /</span> <span class="text-muted fw-light">Services List /</span> Edit Service
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
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Service Info.</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('client.service.update', $service->id) }}" method="POST" id="serviceForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label" for="service_name">Service Name</label>
                            <input type="text" class="form-control" id="service_name" name="service_name" value="{{ $service->service_name }}" placeholder="Service Name" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="full_editor">Description</label>
                            <div id="full-editor">
                                {!! $service->description !!}
                            </div>
                            <textarea id="editor_content" style="display:none" name="editor_content" class="form-control">
                                {!! $service->description !!}
                            </textarea>
                        </div>

                        
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Basic Package</label>
                            <input class="form-control" type="file" id="formFile" value="Upload Image"/>
                        </div>

                        <div>
                            <h5>Pricing</h5>
                        </div>
                        
                        <div class="mb-3">
                            <div class="nav-align-top mb-4">
                                <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" aria-controls="navs-top-home" aria-selected="true">
                                        One-time service
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" aria-selected="false" tabindex="-1">
                                        Recurring service
                                    </button>
                                </li>
                                </ul>
                                <div class="tab-content">
                                <div class="tab-pane show active" id="navs-top-home" role="tabpanel">
                                    <label class="form-label" for="full_editor">Price</label>
                                    <div class="input-group" style="width: 53ch;">
                                        <input type="text" value="CAD" class="custom-input-short" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <input type="number" value="0.00" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                    </div>

                                    <div class="input-group" style="width: 53ch;">
                                        <div class="demo-inline-spacing mt-3">
                                            <div class="">
                                                <a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex justify-content-between">
                                                    <div class="li-wrapper d-flex justify-content-start align-items-center">
                                                        <div class="list-content">
                                                            <h6 class="">Create multiple pricing options?</h6>
                                                        </div>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex justify-content-between">
                                                    <div class="li-wrapper d-flex justify-content-start align-items-center">
                                                        <div class="list-content">
                                                            <h6 class="mb-1">Create multiple orders?</h6>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="input-group" style="width: 53ch;">
                                        <input type="number" value="2" class="form-control" placeholder="">
                                        <input type="text" readonly value="orders" class="custom-input-short" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        2 new orders will be created when this service is purchased.
                                    </div>

                                </div>

                                <div class="tab-pane" id="navs-top-profile" role="tabpanel">
                                    <label class="form-label" for="full_editor">Price</label>
                                    <div class="input-group" style="">
                                        <input type="text" value="CAD" class="custom-input-short" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <input type="number" value="0.00" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <input type="text" value="every" readonly class="custom-input-short" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <input type="number" value="0.00" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <select name="recurring_pirce" id="recurring_pirce" class="custom-input-short">
                                            <option value="Month"> Month</option>
                                            <option value="Year"> Year</option>
                                            <option value="Week"> Week</option>
                                            <option value="Day"> Day</option>
                                        </select>
                                    </div>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck3" checked="">
                                        <label class="form-check-label" for="defaultCheck3"> With trial or setup fee </label>
                                    </div>

                                    <div class="input-group hidden" style="" id="checked_inputs">
                                        <input type="text" value="CAD" class="custom-input-short" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <input type="number" value="0.00" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <input type="text" value="every" readonly class="custom-input-short" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <input type="number" value="0.00" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                        <select name="recurring_pirce" id="recurring_pirce" class="custom-input-short">
                                            <option value="Month"> Month</option>
                                            <option value="Year"> Year</option>
                                            <option value="Week"> Week</option>
                                            <option value="Day"> Day</option>
                                        </select>
                                    </div>


                                    <div class="col-md mt-3">
                                        <small class="text-light fw-medium">When a recurring payment is received…
                                        </small>

                                        <div class="form-check mt-3">
                                            <input name="default-radio-1" class="form-check-input" type="radio" value="" id="defaultRadio1">
                                            <label class="form-check-label" for="defaultRadio1"> Do nothing
                                            </label><br>
                                            <small class="text-light fw-medium">Order status and due date will not change.
                                            </small>
                                        </div>

                                        <div class="form-check">
                                            <input name="default-radio-1" class="form-check-input" type="radio" value="" id="defaultRadio2" checked="">
                                            <label class="form-check-label" for="defaultRadio2"> Reopen order
                                            </label><br>
                                            <small class="text-light fw-medium">Order will go back into Working status with a new due date.
                                        </small>
                                        </div>

                                        <div class="form-check">
                                            <input name="default-radio-1" class="form-check-input" type="radio" value="" id="defaultRadio33" checked="">
                                            <label class="form-check-label" for="defaultRadio3"> Create 2 new orders
                                            </label><br>
                                            <small class="text-light fw-medium">If you want clients to fill out 2 new intake forms every day.
                                            </small>

                                            <div class="input-group hidden custom_ordering" style="width: 53ch;">
                                                <input type="text" value="CAD" class="custom-input-short" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                                <input type="number" value="0.00" class="form-control" placeholder="" aria-label="Username" aria-describedby="basic-addon11">
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input name="default-radio-1" class="form-check-input" type="radio" value="" id="defaultRadio4" checked="">
                                            <label class="form-check-label" for="defaultRadio4"> Let clients request new orders / tasks as they need them
                                            </label><br>
                                            <small class="text-light fw-medium">If you offer task-based services. Limit total requests? Limit active requests?
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                </div>
                            </div>

                        <div>
                            <h5>Orders of this service</h5>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="addon" name="addon" {{ $service->addon ? 'checked' : '' }}>
                                <label class="form-check-label" for="addon">
                                    This is an add-on, don't create a new order
                                </label>
                            </div>

                            <div class="mb-2">
                                Intake form will be appended to selected services when purchased together.
                            </div>

                            <div class="col-md-6 mb-4 mb-md-0" id="parent_services_container" style="display:none">
                                <div class="select2-dark">
                                    <select id="parent_services" name="parent_services[]" class="select2 form-select" multiple>
                                        <option value="1" {{ isset($service) && $service->parentServices && in_array(1, $service->parentServices->pluck('id')->toArray()) ? 'selected' : '' }}>Test 1</option>
                                        <option value="2" {{ isset($service) && $service->parentServices && in_array(2, $service->parentServices->pluck('id')->toArray()) ? 'selected' : '' }}>Test 2</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="group_multiple" name="group_multiple" {{ $service->group_multiple ? 'checked' : '' }}>
                                <label class="form-check-label" for="group_multiple">
                                    Group multiple quantities of this service into one order
                                </label>
                            </div>
                            <div class="mb-2">
                                By default purchases of multiple quantities are added as separate orders. Different services are always added separately.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="assign_team_member" name="assign_team_member" {{ $service->assign_team_member ? 'checked' : '' }}>
                                <label class="form-check-label" for="assign_team_member">
                                    Assign to a team member
                                </label>
                            </div>
                            <div class="mb-2">
                                Automatically assign orders of this service to a team member.
                            </div>    
                            
                            <div class="col-md-6 mb-4 mb-md-0" id="select_team_container" style="display:none">
                                <div class="select2-dark">
                                    <select id="select_team" name="team_member[]" class="select2 form-select" multiple>
                                        <option value="11" {{ isset($service) && $service->teamMembers && in_array(11, $service->teamMembers->pluck('id')->toArray()) ? 'selected' : '' }}>Test 11</option>
                                        <option value="12" {{ isset($service) && $service->teamMembers && in_array(12, $service->teamMembers->pluck('id')->toArray()) ? 'selected' : '' }}>Test 12</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="set_deadline_check" name="set_deadline_check" {{ $service->set_deadline_check ? 'checked' : '' }}>
                                <label class="form-check-label" for="set_deadline_check">
                                    Set a deadline
                                </label>
                            </div>
                            <div class="mb-2">
                                Helps your team see orders that are due soon, not visible to clients.
                            </div>

                            <div class="col-md-6 mb-4 mb-md-0" id="set_deadline_container" style="display:none">
                                <div class="select2-dark" style="display:inline-flex;">
                                    <input type="number" name="set_a_deadline" class="form-control" id="set_a_deadline" value="{{ $service->set_a_deadline }}" placeholder="Days" />&nbsp;&nbsp;&nbsp;
                                    <select id="set_a_deadline_duration" name="set_a_deadline_duration" class="form-select">
                                        <option value="days" {{ $service->set_a_deadline_duration == 'days' ? 'selected' : '' }}>Days</option>
                                        <option value="hours" {{ $service->set_a_deadline_duration == 'hours' ? 'selected' : '' }}>Hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleDisplay(checkboxId, containerId) {
        const checkbox = document.getElementById(checkboxId);
        const container = document.getElementById(containerId);
        if (checkbox && container) {
            checkbox.addEventListener('change', function () {
                if (checkbox.checked) {
                    container.style.display = 'block';
                } else {
                    container.style.display = 'none';
                }
            });
        }
    }

    function manageCheckboxes(primaryCheckboxId, otherCheckboxIds, otherContainers) {
        const primaryCheckbox = document.getElementById(primaryCheckboxId);

        primaryCheckbox.addEventListener('change', function () {
            if (primaryCheckbox.checked) {
                otherCheckboxIds.forEach(id => document.getElementById(id).disabled = true);
                otherContainers.forEach(id => document.getElementById(id).style.display = 'none');
            } else {
                otherCheckboxIds.forEach(id => document.getElementById(id).disabled = false);
            }
        });

        otherCheckboxIds.forEach((id, index) => {
            const otherCheckbox = document.getElementById(id);
            otherCheckbox.addEventListener('change', function () {
                if (otherCheckbox.checked) {
                    primaryCheckbox.disabled = true;
                    document.getElementById('parent_services_container').style.display = 'none';
                } else {
                    const anyChecked = otherCheckboxIds.some(id => document.getElementById(id).checked);
                    if (!anyChecked) {
                        primaryCheckbox.disabled = false;
                    }
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleDisplay('addon', 'parent_services_container');
        toggleDisplay('assign_team_member', 'select_team_container');
        toggleDisplay('set_deadline_check', 'set_deadline_container');

        manageCheckboxes('addon', ['group_multiple', 'assign_team_member', 'set_deadline_check'], ['select_team_container', 'set_deadline_container']);
    });

    // JavaScript to handle the checkbox behavior
    document.addEventListener('DOMContentLoaded', function() {
        var checkbox = document.getElementById('defaultCheck3');
        var checkedInputs = document.getElementById('checked_inputs');

        // Function to toggle visibility
        function toggleCheckedInputs() {
            if (checkbox.checked) {
                checkedInputs.classList.remove('hidden');
            } else {
                checkedInputs.classList.add('hidden');
            }
        }

        // Initial check
        toggleCheckedInputs();

        // Add event listener to the checkbox
        checkbox.addEventListener('change', toggleCheckedInputs);

        const someCheckbox = document.getElementById('defaultRadio33');
        someCheckbox.addEventListener('change', e => {
            // Get all elements with the class 'custom_ordering'
            const customOrderingElements = document.getElementsByClassName('custom_ordering');

            // Loop through all elements and add/remove the 'hidden' class
            for (let i = 0; i < customOrderingElements.length; i++) {
                if (e.target.checked === true) {
                    customOrderingElements[i].classList.remove('hidden');
                } else {
                    customOrderingElements[i].classList.add('hidden');
                }
            }
        });
    });
</script>

@endsection
