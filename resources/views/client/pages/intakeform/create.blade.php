@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Services /</span><span class="text-muted fw-light"> Intake Form /</span> Add 
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
                    <h5 class="mb-0">Intake form details</h5>
                </div>
                <div class="card-body">
                    <div id="build-wrap"></div>
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
