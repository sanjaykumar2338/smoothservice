<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\IntakeformController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\TeamController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\SettingController;
use App\Http\Controllers\Client\TicketStatusController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Client\TagController;
use App\Http\Controllers\Client\ClientStatusController;
use App\Http\Controllers\Client\RoleController;
use App\Http\Controllers\Client\InvoiceController;
use App\Http\Controllers\Client\SubscriptionController;
use App\Http\Controllers\Client\CouponController;
use App\Http\Controllers\Client\TicketController;
use App\Http\Controllers\Client\TicketTagController;
use App\Http\Middleware\CheckWebOrTeam;

//for team members
//require __DIR__.'/team.php';

//Route for login , register
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('register', [LoginController::class, 'register'])->name('register');
Route::post('register', [LoginController::class, 'create_account'])->name('register');
Route::get('forget', [LoginController::class, 'forget'])->name('forget');

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// For user login
Route::middleware(CheckWebOrTeam::class)->group(function () {
    Route::get('dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
    
    // Service routes
    Route::get('/service/list', [ServiceController::class, 'index'])->name('service.list');
    Route::get('/service/add', [ServiceController::class, 'create'])->name('service.add');
    Route::post('/service/store', [ServiceController::class, 'store'])->name('service.store');
    Route::get('/service/{service}/edit', [ServiceController::class, 'edit'])->name('service.edit');
    Route::put('/service/{service}', [ServiceController::class, 'update'])->name('service.update');
    Route::delete('/service/{service}', [ServiceController::class, 'destroy'])->name('service.destroy');
    Route::post('/save-options', [ServiceController::class, 'saveOptions'])->name('service.saveOptions');
    Route::get('/get-options/{id}', [ServiceController::class, 'getOptions'])->name('service.getOptions');

    // Intake form routes
    Route::get('/service/intakeform/list', [IntakeformController::class, 'index'])->name('service.intakeform.list');
    Route::get('/service/intakeform/add', [IntakeformController::class, 'create'])->name('service.intakeform.add');
    Route::get('/service/intakeform/{id}/edit', [IntakeformController::class, 'edit'])->name('service.intakeform.edit');
    Route::delete('/service/intakeform/{id}', [IntakeformController::class, 'destroy'])->name('intakeform.destroy');
    Route::post('/service/intakeform/store', [IntakeformController::class, 'store'])->name('intakeform.store');
    Route::post('/service/intakeform/update_intake', [IntakeFormController::class, 'update_intake'])->name('service.intakeform.update_intake');

    // Team member routes
    Route::get('/team/list', [TeamController::class, 'index'])->name('team.list');
    Route::get('/team/add', [TeamController::class, 'create'])->name('team.add');
    Route::post('/team/store', [TeamController::class, 'store'])->name('team.store');
    Route::get('/team/{teamMember}/edit', [TeamController::class, 'edit'])->name('team.edit');
    Route::put('/team/{teamMember}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/{teamMember}', [TeamController::class, 'destroy'])->name('team.destroy');

    // Client routes
    Route::get('/client/list', [ClientController::class, 'index'])->name('client.list');
    Route::get('/client/add', [ClientController::class, 'create'])->name('client.add');
    Route::post('/client/store', [ClientController::class, 'store'])->name('client.store');
    Route::get('/client/edit/{id}', [ClientController::class, 'edit'])->name('client.edit');
    Route::put('/client/update/{id}', [ClientController::class, 'update'])->name('client.update');
    Route::delete('/client/destroy/{id}', [ClientController::class, 'destroy'])->name('client.destroy');

    // Order routes
    Route::get('/order/detail/{id}', [OrderController::class, 'show'])->name('order.show');
    Route::get('/order/projectdata/{id}', [OrderController::class, 'project_data'])->name('order.project_data');
    Route::get('/order/list', [OrderController::class, 'index'])->name('order.list');
    Route::get('/order/add', [OrderController::class, 'create'])->name('order.add');
    Route::post('/order/store', [OrderController::class, 'store'])->name('order.store');
    Route::get('/order/edit/{id}', [OrderController::class, 'edit'])->name('order.edit');
    Route::put('/order/update/{id}', [OrderController::class, 'update'])->name('order.update');
    Route::delete('/order/delete/{id}', [OrderController::class, 'destroy'])->name('order.destroy');
    Route::post('/order/{id}/save-note', [OrderController::class, 'saveNote']);
    Route::post('/order/save-task', [OrderController::class, 'saveTask']);
    Route::get('/order/get-task/{taskId}', [OrderController::class, 'getTask']);
    Route::get('/order/delete-task/{taskId}', [OrderController::class, 'deleteTask']);
    Route::post('/order/update-task/{taskId}', [OrderController::class, 'updateTask']);
    Route::post('/order/update-task-status/{taskId}', [OrderController::class, 'updateTaskStatus']);
    Route::get('/order/tasks/{id}', [OrderController::class, 'getTasksByStatus']);
    Route::post('/order/save-project-data', [OrderController::class, 'saveProjectData']);
    Route::post('/order/save-project-data/{id}', [OrderController::class, 'save_project_data'])->name('order.save_project_data');
    Route::post('/order/remove-project-field/{id}', [OrderController::class, 'removeProjectField'])->name('order.remove_project_field');
    Route::get('/order/export-data/{id}', [OrderController::class, 'exportData'])->name('order.export_data');
    Route::get('/order/download-files/{id}', [OrderController::class, 'downloadFiles'])->name('order.download_files');
    Route::delete('/order/delete-data/{id}', [OrderController::class, 'deleteData'])->name('order.delete_data');
    Route::post('/order/send-reply', [OrderController::class, 'saveReply'])->name('order.send_reply');
    Route::get('/order/{orderId}/history', [OrderController::class, 'getOrderHistory']);
    Route::post('/order/update-status/{id}', [OrderController::class, 'updateStatus']);
    Route::post('/order/{id}/update-tags', [OrderController::class, 'updateTags'])->name('order.updateTags');
    Route::post('/order/save-team-members', [OrderController::class, 'saveTeamMembers'])->name('order.saveTeamMembers');
    Route::post('/order/save-notification', [OrderController::class, 'saveNotification'])->name('order.saveNotification');
    Route::delete('/orders/{order}/delete', [OrderController::class, 'deleteOrder'])->name('orders.delete');
    Route::post('/orders/{order}/duplicate', [OrderController::class, 'duplicateOrder'])->name('orders.duplicate');
    Route::post('/order/replies/{reply}/edit', [OrderController::class, 'replies_edit'])->name('order.reply.edit');
    Route::delete('/order/replies/{reply}', [OrderController::class, 'replies_destroy'])->name('order.reply.destroy');

    // Order statuses routes
    Route::get('/orderstatuses/list', [SettingController::class, 'index'])->name('setting.orderstatuses.list');
    Route::get('/orderstatuses/create', [SettingController::class, 'create'])->name('setting.orderstatuses.create');
    Route::post('/orderstatuses/store', [SettingController::class, 'store'])->name('setting.orderstatuses.store');
    Route::get('/orderstatuses/edit/{id}', [SettingController::class, 'edit'])->name('setting.orderstatuses.edit');
    Route::post('/orderstatuses/update/{id}', [SettingController::class, 'update'])->name('setting.orderstatuses.update');
    Route::delete('/orderstatuses/delete/{id}', [SettingController::class, 'destroy'])->name('setting.orderstatuses.delete');

     // ticket statuses routes
     Route::get('/ticketstatuses/list', [TicketStatusController::class, 'index'])->name('setting.ticketstatuses.list');
    Route::get('/ticketstatuses/create', [TicketStatusController::class, 'create'])->name('setting.ticketstatuses.create');
    Route::post('/ticketstatuses/store', [TicketStatusController::class, 'store'])->name('setting.ticketstatuses.store');
    Route::get('/ticketstatuses/edit/{id}', [TicketStatusController::class, 'edit'])->name('setting.ticketstatuses.edit');
    Route::post('/ticketstatuses/update/{id}', [TicketStatusController::class, 'update'])->name('setting.ticketstatuses.update');
    Route::delete('/ticketstatuses/delete/{id}', [TicketStatusController::class, 'destroy'])->name('setting.ticketstatuses.delete');


    // Tags routes
    Route::get('/tags/list', [TagController::class, 'index'])->name('tags.list');
    Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('/tags/store', [TagController::class, 'store'])->name('tags.store');
    Route::get('/tags/edit/{id}', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/tags/update/{id}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/delete/{id}', [TagController::class, 'destroy'])->name('tags.delete');

    //Ticket tags routes
    Route::get('/tickettags/list', [TicketTagController::class, 'index'])->name('tickettags.list');
    Route::get('/tickettags/create', [TicketTagController::class, 'create'])->name('tickettags.create');
    Route::post('/tickettags/store', [TicketTagController::class, 'store'])->name('tickettags.store');
    Route::get('/tickettags/edit/{id}', [TicketTagController::class, 'edit'])->name('tickettags.edit');
    Route::put('/tickettags/update/{id}', [TicketTagController::class, 'update'])->name('tickettags.update');
    Route::delete('/tickettags/delete/{id}', [TicketTagController::class, 'destroy'])->name('tickettags.delete');

    // Client statuses routes
    Route::get('/clientstatuses/list', [ClientStatusController::class, 'index'])->name('statuses.list');
    Route::get('/clientstatuses/create', [ClientStatusController::class, 'create'])->name('statuses.create');
    Route::post('/clientstatuses/store', [ClientStatusController::class, 'store'])->name('statuses.store');
    Route::get('/clientstatuses/edit/{id}', [ClientStatusController::class, 'edit'])->name('statuses.edit');
    Route::put('/clientstatuses/update/{id}', [ClientStatusController::class, 'update'])->name('statuses.update');
    Route::delete('/clientstatuses/delete/{id}', [ClientStatusController::class, 'destroy'])->name('statuses.delete');

    // Roles routes
    Route::get('/roles/list', [RoleController::class, 'index'])->name('roles.list');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/update/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/delete/{id}', [RoleController::class, 'destroy'])->name('roles.delete');

    //profile
    Route::get('/profile', [ClientController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [ClientController::class, 'updateprofile'])->name('profile.update');
    Route::post('/profile/update-image', [ClientController::class, 'updateImage'])->name('profile.updateImage');
    Route::post('/profile/delete-image', [ClientController::class, 'deleteImage'])->name('profile.deleteImage');

    //invoice
    Route::get('invoices', [InvoiceController::class, 'index'])->name('invoices.list');
    Route::get('invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('invoices/{id}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('invoices/{id}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::get('invoices/deleteinvoice/{id}', [InvoiceController::class, 'destroy'])->name('invoices.deleteinvoice');
    Route::get('invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('invoices/{id}/download', [InvoiceController::class, 'downloadInvoice'])->name('invoices.download');
    Route::get('invoices/{id}/duplicate', [InvoiceController::class, 'duplicate'])->name('invoices.duplicate');
    Route::get('invoices/{id}/public', [InvoiceController::class, 'publicShow'])->name('invoices.public');
    Route::post('/invoices/{id}/update-address', [InvoiceController::class, 'updateAddress'])->name('invoices.updateAddress');
    Route::post('/invoices/send-email', [InvoiceController::class, 'sendEmail'])->name('invoices.sendEmail');
    Route::post('/invoices/{invoice}/refund', [InvoiceController::class, 'refund'])->name('invoices.refund');

    // Subscription Routes
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.list');
    Route::get('subscriptions/create', [SubscriptionController::class, 'create'])->name('subscriptions.create');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::get('subscriptions/{id}/edit', [SubscriptionController::class, 'edit'])->name('subscriptions.edit');
    Route::put('subscriptions/{id}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::get('subscriptions/deletesubscription/{id}', [SubscriptionController::class, 'destroy'])->name('subscriptions.deletesubscription');
    Route::get('subscriptions/{id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
    Route::get('subscriptions/{id}/download', [SubscriptionController::class, 'downloadSubscription'])->name('subscriptions.download');
    Route::get('subscriptions/{id}/duplicate', [SubscriptionController::class, 'duplicate'])->name('subscriptions.duplicate');
    Route::get('subscriptions/{id}/public', [SubscriptionController::class, 'publicShow'])->name('subscriptions.public');
    Route::post('/subscriptions/{id}/update-address', [SubscriptionController::class, 'updateAddress'])->name('subscriptions.updateAddress');
    Route::post('/subscriptions/send-email', [SubscriptionController::class, 'sendEmail'])->name('subscriptions.sendEmail');
    Route::post('/subscriptions/{subscription}/refund', [SubscriptionController::class, 'refund'])->name('subscriptions.refund');

    //for coupon
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupon.list');
    Route::get('/coupons/add', [CouponController::class, 'create'])->name('coupon.add');
    Route::post('/coupons/save', [CouponController::class, 'store'])->name('coupon.store');
    Route::get('/coupons/edit/{coupon}', [CouponController::class, 'edit'])->name('coupon.edit');
    Route::put('/coupons/update/{coupon}', [CouponController::class, 'update'])->name('coupon.update');
    Route::delete('/coupons/remove/{coupon}', [CouponController::class, 'destroy'])->name('coupon.destroy');

    // Routes for Tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('ticket.list');
    Route::get('/tickets/add', [TicketController::class, 'create'])->name('ticket.add');
    Route::post('/tickets/save', [TicketController::class, 'store'])->name('ticket.store');
    Route::get('/tickets/edit/{ticket}', [TicketController::class, 'edit'])->name('ticket.edit');
    Route::put('/tickets/update/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
    Route::delete('/tickets/remove/{ticket}', [TicketController::class, 'destroy'])->name('ticket.destroy');
    Route::get('/tickets/show/{id}', [TicketController::class, 'show'])->name('ticket.show');
    Route::post('/tickets/save-history/{ticket}', [TicketController::class, 'saveHistory'])->name('ticket.save.history');
    Route::post('/tickets/save-team-members', [TicketController::class, 'saveTeamMembers'])->name('ticket.save.team');
    Route::post('/tickets/save-notification', [TicketController::class, 'saveNotification'])->name('ticket.saveNotification');
    Route::post('/tickets/save-team-members', [TicketController::class, 'saveTeamMembers'])->name('ticket.saveTeamMembers');
    Route::post('/ticket/{id}/save-note', [TicketController::class, 'saveNote']);
    Route::post('/ticket/save-project-data', [TicketController::class, 'saveProjectData']);
    Route::get('/ticket/projectdata/{id}', [TicketController::class, 'project_data'])->name('ticket.project_data');
    Route::post('/ticket/save-project-data/{id}', [TicketController::class, 'save_project_data'])->name('ticket.save_project_data');
    Route::post('/ticket/remove-project-field/{id}', [TicketController::class, 'removeProjectField'])->name('ticket.remove_project_field');
    Route::get('/ticket/export-data/{id}', [TicketController::class, 'exportData'])->name('ticket.export_data');
    Route::get('/ticket/download-files/{id}', [TicketController::class, 'downloadFiles'])->name('ticket.download_files');
    Route::delete('/ticket/delete-data/{id}', [TicketController::class, 'deleteData'])->name('ticket.delete_data');
    Route::post('/ticket/update-status/{id}', [TicketController::class, 'updateStatus']);
    Route::post('/ticket/{id}/update-tags', [TicketController::class, 'updateTags'])->name('ticket.updateTags');
    Route::get('/tickets/edit-info/{id}', [TicketController::class, 'edit_info'])->name('tickets.edit_info');
    Route::put('/tickets/update-info/{id}', [TicketController::class, 'update_info'])->name('tickets.update_info');
    Route::post('/ticket/send-reply', [TicketController::class, 'saveReply'])->name('ticket.send_reply');
    Route::post('/tickets/merge', [TicketController::class, 'mergeTickets'])->name('tickets.merge');
    Route::post('/ticket/replies/{reply}/edit', [TicketController::class, 'replies_edit'])->name('ticket.reply.edit');
    Route::delete('/ticket/replies/{reply}', [TicketController::class, 'replies_destroy'])->name('ticket.reply.destroy');
});

Route::get('logout', function() {
    if (Auth::guard('web')->check()) {
        Auth::guard('web')->logout();
    }
    if (Auth::guard('team')->check()) {
        Auth::guard('team')->logout();
    }
    return redirect('/');
})->name('logout');
