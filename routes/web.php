<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\IntakeformController;
use App\Http\Controllers\Client\ServiceController;
use App\Http\Controllers\Client\TeamController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

//Route for login , register
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('register', [LoginController::class, 'register'])->name('register');
Route::post('register', [LoginController::class, 'create_account'])->name('register');
Route::get('forget', [LoginController::class, 'forget'])->name('forget');
Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [ForgotPasswordController::class,'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// For client after login
Route::prefix('client')->middleware('auth')->group(function () {
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
    Route::post('/order/update/{id}', [OrderController::class, 'update'])->name('client.order.update');
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
});

Route::group(['middleware' => 'auth:team_members'], function () {
    Route::get('/dashboard', [TeamController::class, 'index'])->name('team.dashboard');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


