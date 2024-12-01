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

<div class="container-xxl flex-grow-1 container-p-y" id="app">
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
                    <form action="{{ route('service.store') }}" method="POST">
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
                                        <input type="text" value="" class="custom-input-short" placeholder="CAD" aria-label="Username" aria-describedby="basic-addon11" name="one_time_service_currency">
                                        <input type="number" value="" class="form-control" placeholder="0.00" aria-label="Username" aria-describedby="basic-addon11" name="one_time_service_currency_value">
                                    </div>

                                    <!-- for price option -->
                                    <div class="pricing_option" v-show="isPricingOptionVisible">
                                        <br>
                                        <label class="form-label" for="full_editor">Pricing options</label>
                                        <div class="input-group" id="">
                                            
                                            <select style="height: 37px;border: 0px;" name="pricing_option_data" id="pricing_option_data" class="form-control" v-model="selectedRecurringPrice">
                                                <option value="" disabled>No pricing options set up yet...</option>
                                                <option v-for="(option, index) in recurringPriceOptions" :key="index" :value="option">
                                                    @{{ option }}
                                                </option>
                                            </select>

                                        <button @click="showModal = true" type="button" style="border-left: 0px;" class="btn btn-label-secondary">Edit</button><br>
                                        </div>
                                        <br>
                                        <span style="cursor:pointer;" class="revert_to_simple_pricing" @click="revertToSimplePricing">Revert to simple pricing</span>
                                    </div>

                                    <div class="input-group" style="width: 53ch;">
                                        <div class="demo-inline-spacing mt-3">
                                            <div>
                                                <a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex justify-content-between" @click="togglePricingOption">
                                                    <div class="li-wrapper d-flex justify-content-start align-items-center">
                                                        <div class="list-content">
                                                            <h6 class="create_multiple_pricing_option">Create multiple pricing options?</h6>
                                                        </div>
                                                    </div>
                                                </a>
                                                <a href="javascript:void(0);" class="list-group-item list-group-item-action d-flex justify-content-between" @click="toggleOrdersDiv">
                                                    <div class="li-wrapper d-flex justify-content-start align-items-center">
                                                        <div class="list-content">
                                                            <h6 class="mb-1 create_multiple_orders">Create multiple orders?</h6>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="input-group create_multiple_orders_div" v-show="isOrdersDivVisible">
                                        <input type="number" value="" v-model.number="orderValue" class="form-control" placeholder="" name="multiple_orders">
                                    </div>
                                    <div v-show="isOrdersDivVisible">
                                        <span>@{{ orderValue }}</span> new orders will be created when this service is purchased.
                                    </div>
                                </div>

                                <div class="tab-pane" id="navs-top-profile" role="tabpanel">
                                    <label class="form-label" for="full_editor">Price</label>
                                    <div class="input-group" style="">
                                        <input type="text" value="CAD" class="custom-input-short" placeholder="" aria-label="Username" name="recurring_service_currency" aria-describedby="basic-addon11">

                                        <input type="number" value="" class="form-control" placeholder="" aria-label="Username" name="recurring_service_currency_value" aria-describedby="basic-addon11">
                                        
                                        <input type="text" value="Every" readonly class="custom-input-short" placeholder="" name="recurring_service_currency_every" aria-label="Username" aria-describedby="basic-addon11">
                                        
                                        <input type="number" value="" class="form-control" placeholder="" aria-label="Username" name="recurring_service_currency_value_two" aria-describedby="basic-addon11">
                                        
                                        <select name="recurring_service_currency_value_two_type" id="recurring_pirce" class="custom-input-short">
                                            <option value="Month"> Month</option>
                                            <option value="Year"> Year</option>
                                            <option value="Week"> Week</option>
                                            <option value="Day"> Day</option>
                                        </select>
                                    </div>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" name="with_trial_or_setup_fee" type="checkbox" value="1" id="defaultCheck3">
                                        <label class="form-check-label" for="defaultCheck3">With trial or setup fee</label>
                                    </div>

                                    <div class="col-md mt-3">
                                        <small class="text-light fw-medium">When a recurring payment is received…
                                        </small>

                                        <div class="form-check mt-3">
                                            <input class="form-check-input" type="radio" id="defaultRadio1" name="when_recurring_payment_received" value="Do nothing">
                                            <label class="form-check-label" for="defaultRadio1"> Do nothing</label><br>
                                            <small class="text-light fw-medium">Order status and due date will not change.</small>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="defaultRadio2" name="when_recurring_payment_received" value="Reopen order"
                                            >
                                            <label class="form-check-label" for="defaultRadio2"> Reopen order</label><br>
                                            <small class="text-light fw-medium">Order will go back into <a href="{{route('setting.orderstatuses.list')}}" target="_blank">Working</a> status with a new due date.</small>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="defaultRadio33" name="when_recurring_payment_received" value="Create 2 new orders"
                                            >
                                            <label class="form-check-label" for="defaultRadio3"> Create a new order / task</label><br>
                                            <small class="text-light fw-medium">If you want clients to fill out a new intake form every day. <a href="#" class="create_a_new_order">Create multiple orders?</a> </small>

                                           
                                            <div class="input-group custom_ordering hidden" style="width: 53ch;">
                                                <input type="text" name="when_recurring_payment_received_two_order_currency" class="custom-input-short" placeholder="CAD" aria-label="Username" aria-describedby="basic-addon11">

                                                <input type="number" name="when_recurring_payment_received_two_order_currency_value" class="form-control" placeholder="0.00" aria-label="Username" aria-describedby="basic-addon11">

                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" id="defaultRadio4" name="when_recurring_payment_received" value="Let clients request new orders / tasks as they need them">
                                            <label class="form-check-label" for="defaultRadio4"> Let clients request up to <span class="requests_limit">2</span> new orders / tasks as they need them</label><br>
                                            <small class="text-light fw-medium">
                                                If you offer task-based services. 
                                                <a class="limit_total_requests" href="javascript:void(0);">Limit total requests?</a> 
                                                <a class="limit_active_requests" href="javascript:void(0);">Limit active requests?</a>
                                            </small>
                                        </div>

                                        <div class="input-group custom_ordering total_requests_field hidden" style="margin-bottom: 7px;">
                                            <input type="number" value="2" class="form-control" placeholder="Enter total requests" aria-label="Total Requests" name="total_requests" aria-describedby="basic-addon11">
                                            <span class="input-group-text">requests</span>
                                        </div>

                                        <div class="input-group custom_ordering active_requests_field hidden">
                                            <input type="number" value="1" class="form-control" placeholder="Enter active requests" aria-label="Active Requests" name="active_requests" aria-describedby="basic-addon11">
                                            <span class="input-group-text">active requests</span>
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
                                        <option value="1" {{ isset($service) && $service->parentServices && in_array(1, $service->parentServices->pluck('id')->toArray()) ? 'selected' : '' }}>Test 1</option>
                                        <option value="2" {{ isset($service) && $service->parentServices && in_array(2, $service->parentServices->pluck('id')->toArray()) ? 'selected' : '' }}>Test 2</option>
                                    </select>
                                </div>
                            </div>
                            <br>
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
                            <br>
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

                            <div class="col-md-6 mb-4 mb-md-0" id="select_team_container" style="display:none;">
                                <div class="select2-dark">
                                    <select id="select_team" name="team_member[]" class="select2 form-select" multiple>
                                        <option value="" disabled>Select Team Members</option>
                                        @if($team_members)
                                            @foreach($team_members as $member)
                                                <option value="{{$member->id}}">{{$member->first_name}} {{$member->last_name}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <br>
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
                                    <input type="number" name="set_a_deadline" class="form-control" id="set_a_deadline" value="" placeholder="Days" />&nbsp;&nbsp;&nbsp;
                                    <select id="set_a_deadline_duration" name="set_a_deadline_duration" class="form-select">
                                        <option value="days">Days</option>
                                        <option value="hours">Hours</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h5>Visibility</h5>
                        </div>

                        <div class="mb-3">
                            
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="show_in_the_service_page" name="show_in_the_service_page">
                                <label class="form-check-label" for="set_deadline_check">
                                    Show in services page <a href="" target="_blank">(?)</a>
                                </label>
                            </div>
                            <div class="mb-2">
                                Choose whether to list this service in your Client Portal's services page. Service can still be used in order forms.
                            </div>
                        </div>

                        <input type="hidden" name="price_options" id="price_options">
                        <input type="hidden" name="combinations" id="combinations">

                        <button type="submit" class="btn btn-primary">Submit</button>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Modal Component -->
    <custom-modal v-if="showModal" @close="showModal = false">
                            <template v-slot:header>
                                <h5>Create Options</h5>
                            </template>

                            <template v-slot:body>
                                <!-- Loop through each option menu -->
                                <div v-for="(menu, menuIndex) in optionMenus" :key="menuIndex" class="option-menu">
                                    <div class="option-header">
                                        <h6 class="mb-2" style="padding-bottom: 75px;">Option @{{ menuIndex + 1 }}:</h6><br><br>
                                        <input style="width:65px;" type="text" class="form-control mb-2" placeholder="Turnaround Time" v-model="menu.optionTitle">
                                        <!-- Show remove button only for cloned option menus -->
                                        &nbsp;<button v-if="menuIndex > 0" class="btn btn-danger btn-sm remove-option-menu" @click="removeOptionMenu(menuIndex)">&times;</button>
                                    </div>
                                    <p class="text-muted">This is a drop-down menu where customers can select one of the options below.</p>

                                    <div v-for="(option, index) in menu.options" :key="index" class="option-input">
                                        <input type="text" class="form-control" v-model="option.value" :placeholder="option.placeholder">
                                        <button class="btn btn-danger btn-sm" @click="removeOption(menuIndex, index)">&times;</button>
                                    </div>

                                    <button class="btn btn-secondary" @click="addOption(menuIndex)">+ Add value</button>
                                </div>

                                <!-- Button to add another option menu -->
                                <div>
                                    <span class="add-option-menu" @click="addOptionMenu">+ Add another option menu</span>
                                </div>

                            <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th v-for="(menu, menuIndex) in optionMenus" :key="menuIndex">
                                                @{{ menu.optionTitle }}
                                            </th>
                                            <th>Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(combination, combinationIndex) in combinations" :key="combinationIndex">
                                            <td>
                                                <input type="checkbox" v-model="combination.checked" :key="`checkbox-${combinationIndex}`">
                                            </td>
                                            <td v-for="(option, optionIndex) in combination.options" :key="`option-${optionIndex}-${combinationIndex}`">
                                                @{{ option }}
                                            </td>
                                            <td>
                                                <input type="number" class="form-control mb-2" v-model="combination.price" 
                                                    placeholder="Enter price" 
                                                    :key="`price-${combinationIndex}`">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </template>

                            <template v-slot:footer>
                                <button @click="showModal = false" class="btn btn-secondary">Close</button>
                                <button class="btn btn-primary" @click="saveOptions($event)">Save options</button>
                                <span v-if="statusMessage" class="status-message" :class="statusClass">@{{ statusMessage }}</span>
                            </template>
                        </custom-modal>
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
       
        const elements = document.querySelectorAll('.custom_ordering');
        if (elements.length > 0) {
            elements.forEach((element) => {
                element.style.display = 'none';
            });
        } else {
            console.error('No elements found with class "custom_ordering".');
        }


        // Function to toggle visibility
        function toggleCheckedInputs() {
            if(checkedInputs){
                if (checkbox.checked) {
                    checkedInputs.classList.remove('hidden');
                } else {
                    checkedInputs.classList.add('hidden');
                }
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
                    //customOrderingElements[i].classList.remove('hidden');
                    //customOrderingElements[i].style.display = 'flex';
                } else {
                    //customOrderingElements[i].classList.add('hidden');
                    //customOrderingElements[i].style.display = 'none';
                }
            }
        });

        $('.create_a_new_order').on('click', function (e) {
            e.preventDefault(); // Prevent default anchor link behavior

            const customOrderingElement = document.querySelector('.custom_ordering'); // Assuming only one element with this class

            if (customOrderingElement) {
                if (customOrderingElement.classList.contains('hidden')) {
                    customOrderingElement.classList.remove('hidden');
                    customOrderingElement.style.display = 'flex';
                } else {
                    customOrderingElement.classList.add('hidden');
                    customOrderingElement.style.display = 'none';
                }
            }
        });
    });
</script>

<script>

    // Define the custom modal component
    Vue.component('custom-modal', {
        template: `
            <div class="modal-overlay" @click.self="$emit('close')">
                <div class="modal-content">
                    <button class="modal-close" @click="$emit('close')">&times;</button>
                    <header class="modal-header">
                        <slot name="header"></slot>
                    </header>
                    <section class="modal-body">
                        <slot name="body"></slot>
                    </section>
                    <footer class="modal-footer">
                        <slot name="footer"></slot>
                    </footer>
                </div>
            </div>
        `
    });

    new Vue({
        el: '#app',
        data: {
            isOrdersDivVisible: false,
            isPricingOptionVisible: false,
            orderValue: "{{ $service->multiple_orders ?? '' }}",
            showModal: false,
            maxOptionMenus: 3,
            serviceId: '', // Pass the service ID from Blade
            optionMenus: [],
            combinations: [], // To store the combinations data
            recurringPriceOptions: [], // To store the pricing options
            selectedRecurringPrice: '', // To store the selected recurring price option
            statusMessage: '',
            statusClass: '',
        },
        computed: {
            maxOptionFields() {
                return Math.max(...this.optionMenus.map(menu => menu.options.length));
            },
            allOptionCombinations() {
                const combinations = this.generateCombinations(
                    this.optionMenus.map(menu => menu.options.map(option => option.value))
                );
                this.combinations = combinations.map((comb, index) => {
                    return {
                        options: comb,
                        checked: this.combinations[index]?.checked || false,
                        price: this.combinations[index]?.price || ''
                    };
                });
                return this.combinations;
            }
        },
        watch: {
            showModal(newValue) {
                if (newValue) {
                    //this.fetchSavedOptions();
                }
            },
            optionMenus: {
                handler(newValue) {
                    this.allOptionCombinations; // Force update of combinations
                },
                deep: true // Watch deeply for changes within the optionMenus array
            }
        },
        methods: {
            togglePricingOption() {
                this.isPricingOptionVisible = !this.isPricingOptionVisible;
            },
            toggleOrdersDiv() {
                this.isOrdersDivVisible = !this.isOrdersDivVisible;
            },
            revertToSimplePricing() {
                this.isPricingOptionVisible = false;
            },
            addOption(menuIndex) {
                this.optionMenus[menuIndex].options.push({ value: '', placeholder: 'Extra Fast', price: '' });
                this.allOptionCombinations; // Update combinations after adding an option
            },
            removeOption(menuIndex, optionIndex) {
                this.optionMenus[menuIndex].options.splice(optionIndex, 1);
                this.allOptionCombinations; // Update combinations after removing an option
            },
            addOptionMenu() {
                if (this.optionMenus.length < this.maxOptionMenus) {
                    this.optionMenus.push({
                        optionTitle: `Turnaround Time ${this.optionMenus.length + 1}`,
                        options: [
                            { value: '', placeholder: 'Regular', price: '' },
                            { value: '', placeholder: 'Fast', price: '' },
                            { value: '', placeholder: 'Extra Fast', price: '' }
                        ]
                    });
                    this.allOptionCombinations; // Recalculate combinations after adding a menu
                } else {
                    alert("You can only add up to 3 option menus.");
                }
            },
            removeOptionMenu(menuIndex) {
                this.optionMenus.splice(menuIndex, 1);
                this.allOptionCombinations; // Recalculate combinations after removing a menu
            },
            saveOptions(event) {
                event.preventDefault();

                const rows = document.querySelectorAll('tbody tr');
                const combinationsData = [];

                rows.forEach((row) => {
                    const checkbox = row.querySelector('input[type="checkbox"]');
                    const isChecked = checkbox ? checkbox.checked : false;

                    const priceInput = row.querySelector('input[type="number"]');
                    const priceValue = priceInput ? priceInput.value : '';

                    const optionValues = Array.from(row.querySelectorAll('td:not(:first-child):not(:last-child)'))
                        .map(td => td.innerText.trim());

                    const rowData = {
                        options: optionValues,
                        checked: isChecked,
                        price: priceValue
                    };

                    combinationsData.push(rowData);
                });

                // Update combinations data
                this.combinations = combinationsData;

                // Clear the existing recurringPriceOptions
                this.recurringPriceOptions = [];

                // Loop through optionMenus and push optionTitle into recurringPriceOptions
                this.optionMenus.forEach(menu => {
                    if (menu.optionTitle && !this.recurringPriceOptions.includes(menu.optionTitle)) {
                        this.recurringPriceOptions.push(menu.optionTitle);
                    }
                });

                // Update hidden fields with serialized data
                document.getElementById('price_options').value = JSON.stringify(this.optionMenus);
                document.getElementById('combinations').value = JSON.stringify(combinationsData);

                // Close the modal
                this.showModal = false;
            },

            generateCombinations(arrays, prefix = []) {
                if (arrays.length === 0) {
                    return [prefix];
                } else {
                    const result = [];
                    const firstArray = arrays[0];
                    const remainingArrays = arrays.slice(1);
                    for (let i = 0; i < firstArray.length; i++) {
                        const newPrefix = prefix.concat(firstArray[i]);
                        result.push(...this.generateCombinations(remainingArrays, newPrefix));
                    }
                    return result;
                }
            }
        },
        mounted() {
            //this.fetchSavedOptions(); // Fetch options when the component is mounted
            // Ensure at least one option menu is present
            if (this.optionMenus.length === 0) {
                this.addOptionMenu();
            }
        }
    });

    // Assume $service->pricing_option_data is available in the JavaScript context
    const savedOption = "";

    setTimeout(() => {
        const selectElement = document.getElementById('pricing_option_data');
        
        if (selectElement && savedOption) {
            // Iterate over options to find the one that matches the saved value
            for (let i = 0; i < selectElement.options.length; i++) {
                if (selectElement.options[i].value === savedOption) {
                    selectElement.selectedIndex = i;
                    break;
                }
            }
        }
    }, 2000);

    document.addEventListener('DOMContentLoaded', function () {
        const totalRequestsLink = document.querySelector('.limit_total_requests');
        const activeRequestsLink = document.querySelector('.limit_active_requests');
        const totalRequestsField = document.querySelector('.total_requests_field');
        const activeRequestsField = document.querySelector('.active_requests_field');

        // Toggle visibility for "Limit total requests"
        totalRequestsLink.addEventListener('click', function (e) {
            e.preventDefault();
            if (totalRequestsField.classList.contains('hidden')) {
                totalRequestsField.classList.remove('hidden');
                totalRequestsField.style.display = 'flex';
                totalRequestsLink.textContent = 'Unlimited requests?';
            } else {
                totalRequestsField.classList.add('hidden');
                totalRequestsField.style.display = 'none';
                totalRequestsLink.textContent = 'Limit total requests?';
            }
        });

        // Toggle visibility for "Limit active requests"
        activeRequestsLink.addEventListener('click', function (e) {
            e.preventDefault();
            if (activeRequestsField.classList.contains('hidden')) {
                activeRequestsField.classList.remove('hidden');
                activeRequestsField.style.display = 'flex';
                activeRequestsLink.textContent = 'Unlimited active requests?';
            } else {
                activeRequestsField.classList.add('hidden');
                activeRequestsField.style.display = 'none';
                activeRequestsLink.textContent = 'Limit active requests?';
            }
        });
    });
</script>
@endsection