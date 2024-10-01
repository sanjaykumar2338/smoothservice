<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\IntakeformController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\TeamController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\SettingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Client\TagController;
use App\Http\Controllers\Client\ClientStatusController;
use App\Http\Controllers\Client\RoleController;
use App\Http\Controllers\Client\InvoiceController;
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

    // Order statuses routes
    Route::get('/orderstatuses/list', [SettingController::class, 'index'])->name('setting.orderstatuses.list');
    Route::get('/orderstatuses/create', [SettingController::class, 'create'])->name('setting.orderstatuses.create');
    Route::post('/orderstatuses/store', [SettingController::class, 'store'])->name('setting.orderstatuses.store');
    Route::get('/orderstatuses/edit/{id}', [SettingController::class, 'edit'])->name('setting.orderstatuses.edit');
    Route::post('/orderstatuses/update/{id}', [SettingController::class, 'update'])->name('setting.orderstatuses.update');
    Route::delete('/orderstatuses/delete/{id}', [SettingController::class, 'destroy'])->name('setting.orderstatuses.delete');

    // Tags routes
    Route::get('/tags/list', [TagController::class, 'index'])->name('tags.list');
    Route::get('/tags/create', [TagController::class, 'create'])->name('tags.create');
    Route::post('/tags/store', [TagController::class, 'store'])->name('tags.store');
    Route::get('/tags/edit/{id}', [TagController::class, 'edit'])->name('tags.edit');
    Route::put('/tags/update/{id}', [TagController::class, 'update'])->name('tags.update');
    Route::delete('/tags/delete/{id}', [TagController::class, 'destroy'])->name('tags.delete');

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
    Route::delete('invoices/{id}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
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
