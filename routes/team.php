<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamMember\TeamMemberController;

// Team member routes protected by the 'team' guard
Route::middleware(['auth:team'])->group(function () {
    Route::get('/team/dashboard', [TeamMemberController::class, 'dashboard'])->name('team.dashboard');
    Route::get('/team/profile', [TeamMemberController::class, 'profile'])->name('team.profile');
});
