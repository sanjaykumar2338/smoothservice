@extends('client.client_template')
@section('content')

<style>
    .mb-3 {
        margin-left: 15px;
    }

    .card-body{
        margin-left: 30px;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 breadcrumb-wrapper mb-4">
        <span class="text-muted fw-light">Roles /</span> Add Role
    </h4>

    <div class="card">

        
        <h5 class="card-header d-flex justify-content-between align-items-center">
            Add Role
        </h5>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-body">
            <form action="{{ route('client.roles.store') }}" method="POST">
                @csrf

                <!-- Role Name Input -->
                <div class="mb-4">
                    <label class="form-label" for="role_name">Role Name</label>
                    <input type="text" class="form-control" id="role_name" name="role_name" placeholder="Enter Role Name" value="{{ old('role_name') }}" required>
                </div>

                <!-- Permissions -->
                <h5 class="mt-4">Permissions</h5>

                <!-- Order Access -->
                <div class="mb-4">
                    <h6 class="fw-bold">Order Access</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="assigned_orders" name="permissions[]" value="assigned_orders">
                        <label class="form-check-label" for="assigned_orders">Assigned Orders</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="open_orders" name="permissions[]" value="open_orders">
                        <label class="form-check-label" for="open_orders">Open Orders</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="all_orders" name="permissions[]" value="all_orders">
                        <label class="form-check-label" for="all_orders">All Orders</label>
                    </div>
                </div>

                <!-- Order Management -->
                <div class="mb-4">
                    <h6 class="fw-bold">Order Management</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="assign_to_self" name="permissions[]" value="assign_to_self">
                        <label class="form-check-label" for="assign_to_self">Assign to Self</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="assign_to_others" name="permissions[]" value="assign_to_others">
                        <label class="form-check-label" for="assign_to_others">Assign to Others</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_data" name="permissions[]" value="edit_data">
                        <label class="form-check-label" for="edit_data">Edit Data</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="delete_order" name="permissions[]" value="delete_order">
                        <label class="form-check-label" for="delete_order">Delete</label>
                    </div>
                </div>

                <!-- Messaging -->
                <div class="mb-4">
                    <h6 class="fw-bold">Messaging</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="message_team" name="permissions[]" value="message_team">
                        <label class="form-check-label" for="message_team">Message Team</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="message_client" name="permissions[]" value="message_client">
                        <label class="form-check-label" for="message_client">Message Client</label>
                    </div>
                </div>

                <!-- Ticket Access -->
                <div class="mb-4">
                    <h6 class="fw-bold">Ticket Access</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="assigned_tickets" name="permissions[]" value="assigned_tickets">
                        <label class="form-check-label" for="assigned_tickets">Assigned Tickets</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="open_tickets" name="permissions[]" value="open_tickets">
                        <label class="form-check-label" for="open_tickets">Open Tickets</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="all_tickets" name="permissions[]" value="all_tickets">
                        <label class="form-check-label" for="all_tickets">All Tickets</label>
                    </div>
                </div>

                <!-- Ticket Management -->
                <div class="mb-4">
                    <h6 class="fw-bold">Ticket Management</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="assign_ticket_self" name="permissions[]" value="assign_ticket_self">
                        <label class="form-check-label" for="assign_ticket_self">Assign to Self</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="assign_ticket_others" name="permissions[]" value="assign_ticket_others">
                        <label class="form-check-label" for="assign_ticket_others">Assign to Others</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_delete_ticket" name="permissions[]" value="add_delete_ticket">
                        <label class="form-check-label" for="add_delete_ticket">Add / Delete</label>
                    </div>
                </div>

                <!-- Clients -->
                <div class="mb-4">
                    <h6 class="fw-bold">Clients</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="view_clients" name="permissions[]" value="view_clients">
                        <label class="form-check-label" for="view_clients">View</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_edit_clients" name="permissions[]" value="add_edit_clients">
                        <label class="form-check-label" for="add_edit_clients">Add / Edit / Login</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="delete_clients" name="permissions[]" value="delete_clients">
                        <label class="form-check-label" for="delete_clients">Delete</label>
                    </div>
                </div>

                <!-- Invoice and Subscription Access -->
                <div class="mb-4">
                    <h6 class="fw-bold">Invoice and Subscription Access</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="own_invoices" name="permissions[]" value="own_invoices">
                        <label class="form-check-label" for="own_invoices">Own Invoices</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="all_invoices" name="permissions[]" value="all_invoices">
                        <label class="form-check-label" for="all_invoices">All Invoices</label>
                    </div>
                </div>

                <!-- Invoice Management -->
                <div class="mb-4">
                    <h6 class="fw-bold">Invoice Management</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_edit_invoice" name="permissions[]" value="add_edit_invoice">
                        <label class="form-check-label" for="add_edit_invoice">Add / Edit</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="charge_delete_invoice" name="permissions[]" value="charge_delete_invoice">
                        <label class="form-check-label" for="charge_delete_invoice">Charge / Delete</label>
                    </div>
                </div>

                <!-- Coupons -->
                <div class="mb-4">
                    <h6 class="fw-bold">Coupons</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="view_coupons" name="permissions[]" value="view_coupons">
                        <label class="form-check-label" for="view_coupons">View</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_edit_delete_coupons" name="permissions[]" value="add_edit_delete_coupons">
                        <label class="form-check-label" for="add_edit_delete_coupons">Add / Edit / Delete</label>
                    </div>
                </div>

                <!-- Services -->
                <div class="mb-4">
                    <h6 class="fw-bold">Services```php
                <!-- Services -->
                <div class="mb-4">
                    <h6 class="fw-bold">Services</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="view_services" name="permissions[]" value="view_services">
                        <label class="form-check-label" for="view_services">View</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_edit_delete_services" name="permissions[]" value="add_edit_delete_services">
                        <label class="form-check-label" for="add_edit_delete_services">Add / Edit / Delete</label>
                    </div>
                </div>

                <!-- Forms -->
                <div class="mb-4">
                    <h6 class="fw-bold">Forms</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="view_forms" name="permissions[]" value="view_forms">
                        <label class="form-check-label" for="view_forms">View</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_edit_delete_forms" name="permissions[]" value="add_edit_delete_forms">
                        <label class="form-check-label" for="add_edit_delete_forms">Add / Edit / Delete</label>
                    </div>
                </div>

                <!-- Team -->
                <div class="mb-4">
                    <h6 class="fw-bold">Team</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="view_team" name="permissions[]" value="view_team">
                        <label class="form-check-label" for="view_team">View</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_edit_delete_team" name="permissions[]" value="add_edit_delete_team">
                        <label class="form-check-label" for="add_edit_delete_team">Add / Edit / Delete</label>
                    </div>
                </div>

                <!-- Settings -->
                <div class="mb-4">
                    <h6 class="fw-bold">Settings</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="view_settings" name="permissions[]" value="view_settings">
                        <label class="form-check-label" for="view_settings">View</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="add_edit_settings" name="permissions[]" value="add_edit_settings">
                        <label class="form-check-label" for="add_edit_settings">Add / Edit</label>
                    </div>
                </div>

                <!-- Dashboard Access -->
                <div class="mb-4">
                    <h6 class="fw-bold">Dashboard Access</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="view_dashboard_reports" name="permissions[]" value="view_dashboard_reports">
                        <label class="form-check-label" for="view_dashboard_reports">View Dashboard & Reports</label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Add Role</button>
            </form>
        </div>
    </div>
</div>

@endsection
