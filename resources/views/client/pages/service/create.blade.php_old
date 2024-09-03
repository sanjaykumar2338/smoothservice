@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Services /</span> <span class="text-muted fw-light">Services List /</span> Add 
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
                    <form action="{{ route('client.service.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label" for="service_name">Service Name</label>
                            <input type="text" class="form-control" id="service_name" name="service_name" placeholder="Service Name" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="full_editor">Description</label>
                            <div id="full-editor">
                               
                            </div>

                            <textarea id="editor_content" style="display:none" name="editor_content" class="form-control">
                                    
                            </textarea>
                        </div>

                        <div>
                            <h5>Orders of this service</h5>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="addon" name="addon">
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
                                        <option id="1">Test 1</option>
                                        <option id="2">Test 2</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="group_multiple" name="group_multiple">
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
                                <input class="form-check-input" type="checkbox" value="1" id="assign_team_member" name="assign_team_member">
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
                                        <option id="11">Test 11</option>
                                        <option id="12">Test 12</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="set_deadline_check" name="set_deadline_check">
                                <label class="form-check-label" for="set_deadline_check">
                                    Set a deadline
                                </label>
                            </div>
                            <div class="mb-2">
                                Helps your team see orders that are due soon, not visible to clients.
                            </div>

                            <div class="col-md-6 mb-4 mb-md-0" id="set_deadline_container" style="display:none">
                                <div class="select2-dark" style="display:inline-flex;">
                                    <input type="number" name="set_a_deadline" class="form-control" id="set_a_deadline" placeholder="Days" />&nbsp;&nbsp;&nbsp;
                                    <select id="set_a_deadline_duration" name="set_a_deadline_duration" class="form-select">
                                        <option value="days">Days</option>
                                        <option value="hours">Hours</option>
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
</script>

@endsection
