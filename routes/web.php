<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Client\IntakeformController;
use App\Http\Controllers\Client\ServiceController;
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
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


