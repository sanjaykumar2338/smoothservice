<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            // Order Access
            ['name' => 'assigned_orders', 'category' => 'Order Access'],
            ['name' => 'open_orders', 'category' => 'Order Access'],
            ['name' => 'all_orders', 'category' => 'Order Access'],
            // Order Management
            ['name' => 'assign_to_self', 'category' => 'Order Management'],
            ['name' => 'assign_to_others', 'category' => 'Order Management'],
            ['name' => 'edit_data', 'category' => 'Order Management'],
            ['name' => 'delete_order', 'category' => 'Order Management'],
            // Messaging
            ['name' => 'message_team', 'category' => 'Messaging'],
            ['name' => 'message_client', 'category' => 'Messaging'],
            // Ticket Access
            ['name' => 'assigned_tickets', 'category' => 'Ticket Access'],
            ['name' => 'open_tickets', 'category' => 'Ticket Access'],
            ['name' => 'all_tickets', 'category' => 'Ticket Access'],
            // Ticket Management
            ['name' => 'assign_to_self_tickets', 'category' => 'Ticket Management'],
            ['name' => 'assign_to_others_tickets', 'category' => 'Ticket Management'],
            ['name' => 'add_delete_tickets', 'category' => 'Ticket Management'],
            // Clients
            ['name' => 'view_clients', 'category' => 'Clients'],
            ['name' => 'add_edit_login_clients', 'category' => 'Clients'],
            ['name' => 'delete_clients', 'category' => 'Clients'],
            // Invoices and Subscription
            ['name' => 'own_invoices', 'category' => 'Invoices and Subscriptions'],
            ['name' => 'all_invoices', 'category' => 'Invoices and Subscriptions'],
            // Invoice Management
            ['name' => 'add_edit_invoices', 'category' => 'Invoice Management'],
            ['name' => 'charge_delete_invoices', 'category' => 'Invoice Management'],
            // Coupons
            ['name' => 'view_add_edit_delete_coupons', 'category' => 'Coupons'],
            // Services
            ['name' => 'view_add_edit_delete_services', 'category' => 'Services'],
            // Forms
            ['name' => 'view_add_edit_delete_forms', 'category' => 'Forms'],
            // Team
            ['name' => 'view_add_edit_delete_team', 'category' => 'Team'],
            // Settings
            ['name' => 'view_add_edit_settings', 'category' => 'Settings'],
            // Dashboard
            ['name' => 'view_dashboard_reports', 'category' => 'Dashboard Access']
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
}