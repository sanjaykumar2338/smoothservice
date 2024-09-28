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

// For client after login
Route::prefix('client')->middleware(CheckWebOrTeam::class)->group(function () {
    Route::get('dashboard', [ClientController::class, 'index'])->name('client.dashboard');
    
    // Service routes
    Route::get('/service/list', [ServiceController::class, 'index'])->name('client.service.list');
    Route::get('/service/add', [ServiceController::class, 'create'])->name('client.service.add');
    Route::post('/service/store', [ServiceController::class, 'store'])->name('client.service.store');
    Route::get('/service/{service}/edit', [ServiceController::class, 'edit'])->name('client.service.edit');
    Route::put('/service/{service}', [ServiceController::class, 'update'])->name('client.service.update');
    Route::delete('/service/{service}', [ServiceController::class, 'destroy'])->name('client.service.destroy');
    Route::post('/save-options', [ServiceController::class, 'saveOptions'])->name('client.service.saveOptions');
    Route::get('/get-options/{id}', [ServiceController::class, 'getOptions'])->name('client.service.getOptions');

    //for intake form
    Route::get('/service/intakeform/list', [IntakeformController::class, 'index'])->name('client.service.intakeform.list');
    Route::get('/service/intakeform/add', [IntakeformController::class, 'create'])->name('client.service.intakeform.add');
    Route::get('/service/intakeform/{id}/edit', [IntakeformController::class, 'edit'])->name('client.service.intakeform.edit');
    Route::delete('/service/intakeform/{id}', [IntakeformController::class, 'destroy'])->name('client.intakeform.destroy');
    Route::post('/service/intakeform/store', [IntakeformController::class, 'store'])->name('client.intakeform.store');
    Route::post('/service/intakeform/update_intake', [IntakeFormController::class, 'update_intake'])->name('client.service.intakeform.update_intake');

    // Team member routes
    Route::get('/team/list', [TeamController::class, 'index'])->name('client.team.list');
    Route::get('/team/add', [TeamController::class, 'create'])->name('client.team.add');
    Route::post('/team/store', [TeamController::class, 'store'])->name('client.team.store');
    Route::get('/client/team/{teamMember}/edit', [TeamController::class, 'edit'])->name('client.team.edit');
    Route::put('/client/team/{teamMember}', [TeamController::class, 'update'])->name('client.team.update');
    Route::delete('/client/team/{teamMember}', [TeamController::class, 'destroy'])->name('client.team.destroy');

    // Client routes
    Route::get('/list', [ClientController::class, 'index'])->name('client.list');
    Route::get('/add', [ClientController::class, 'create'])->name('client.add');
    Route::post('/store', [ClientController::class, 'store'])->name('client.store');
    Route::get('/edit/{id}', [ClientController::class, 'edit'])->name('client.edit');
    Route::put('/update/{id}', [ClientController::class, 'update'])->name('client.update');
    Route::delete('/destroy/{id}', [ClientController::class, 'destroy'])->name('client.destroy');

    // Order routes
    Route::get('/order/detail/{id}', [OrderController::class, 'show'])->name('client.order.show');
    Route::get('/order/projectdata/{id}', [OrderController::class, 'project_data'])->name('client.order.project_data');
    Route::get('/order/list', [OrderController::class, 'index'])->name('client.order.list');
    Route::get('/order/add', [OrderController::class, 'create'])->name('client.order.add');
    Route::post('/order/store', [OrderController::class, 'store'])->name('client.order.store');        
    Route::get('/order/edit/{id}', [OrderController::class, 'edit'])->name('client.order.edit');
    Route::put('/order/update/{id}', [OrderController::class, 'update'])->name('client.order.update');
    Route::delete('/order/delete/{id}', [OrderController::class, 'destroy'])->name('client.order.destroy');
    Route::post('/order/{id}/save-note', [OrderController::class, 'saveNote']);
    Route::post('/order/save-task', [OrderController::class, 'saveTask']);
    Route::get('/order/get-task/{taskId}', [OrderController::class, 'getTask']);
    Route::get('/order/delete-task/{taskId}', [OrderController::class, 'deleteTask']);
    Route::post('/order/update-task/{taskId}', [OrderController::class, 'updateTask']);
    Route::post('/order/update-task-status/{taskId}', [OrderController::class, 'updateTaskStatus']);
    Route::get('/order/tasks/{id}', [OrderController::class, 'getTasksByStatus']);
    Route::post('/order/save-project-data', [OrderController::class, 'saveProjectData']);
    Route::post('/order/save-project-data/{id}', [OrderController::class, 'save_project_data'])->name('client.order.save_project_data');
    Route::post('/order/remove-project-field/{id}', [OrderController::class, 'removeProjectField'])->name('client.order.remove_project_field');
    Route::get('/order/export-data/{id}', [OrderController::class, 'exportData'])->name('client.order.export_data');
    Route::get('/order/download-files/{id}', [OrderController::class, 'downloadFiles'])->name('client.order.download_files');
    Route::delete('/order/delete-data/{id}', [OrderController::class, 'deleteData'])->name('client.order.delete_data');
    Route::post('/order/send-reply', [OrderController::class, 'saveReply'])->name('client.order.send_reply');
    Route::get('/order/{orderId}/history', [OrderController::class, 'getOrderHistory']);
    Route::post('/order/update-status/{id}', [OrderController::class, 'updateStatus']);
    Route::post('/order/{id}/update-tags', [OrderController::class, 'updateTags'])->name('order.updateTags');
    Route::post('/order/save-team-members', [OrderController::class, 'saveTeamMembers'])->name('order.saveTeamMembers');
    Route::post('/order/save-notification', [OrderController::class, 'saveNotification'])->name('order.saveNotification');
    Route::delete('/orders/{order}/delete', [OrderController::class, 'deleteOrder'])->name('orders.delete');
    Route::post('/orders/{order}/duplicate', [OrderController::class, 'duplicateOrder'])->name('orders.duplicate');

    //order statuses
    Route::get('/orderstatuses/list', [SettingController::class, 'index'])->name('setting.orderstatuses.list');
    Route::get('/orderstatuses/create', [SettingController::class, 'create'])->name('setting.orderstatuses.create');
    Route::post('/orderstatuses/store', [SettingController::class, 'store'])->name('setting.orderstatuses.store');
    Route::get('/orderstatuses/edit/{id}', [SettingController::class, 'edit'])->name('setting.orderstatuses.edit');
    Route::post('/orderstatuses/update/{id}', [SettingController::class, 'update'])->name('setting.orderstatuses.update');
    Route::delete('/orderstatuses/delete/{id}', [SettingController::class, 'destroy'])->name('setting.orderstatuses.delete');

    Route::get('/tags/list', [TagController::class, 'index'])->name('client.tags.list');
    Route::get('/tags/create', [TagController::class, 'create'])->name('client.tags.create');
    Route::post('/tags/store', [TagController::class, 'store'])->name('client.tags.store');
    Route::get('/tags/edit/{id}', [TagController::class, 'edit'])->name('client.tags.edit');
    Route::put('/tags/update/{id}', [TagController::class, 'update'])->name('client.tags.update');
    Route::delete('/tags/delete/{id}', [TagController::class, 'destroy'])->name('client.tags.delete');

    Route::get('/clientstatuses/list', [ClientStatusController::class, 'index'])->name('client.statuses.list');
    Route::get('/clientstatuses/create', [ClientStatusController::class, 'create'])->name('client.statuses.create');
    Route::post('/clientstatuses/store', [ClientStatusController::class, 'store'])->name('client.statuses.store');
    Route::get('/clientstatuses/edit/{id}', [ClientStatusController::class, 'edit'])->name('client.statuses.edit');
    Route::put('/clientstatuses/update/{id}', [ClientStatusController::class, 'update'])->name('client.statuses.update');
    Route::delete('/clientstatuses/delete/{id}', [ClientStatusController::class, 'destroy'])->name('client.statuses.delete');

    Route::get('/roles/list', [RoleController::class, 'index'])->name('client.roles.list');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('client.roles.create');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('client.roles.store');
    Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])->name('client.roles.edit');
    Route::put('/roles/update/{id}', [RoleController::class, 'update'])->name('client.roles.update');
    Route::delete('/roles/delete/{id}', [RoleController::class, 'destroy'])->name('client.roles.delete');
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
