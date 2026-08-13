<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\JudgeManagementController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/live-stats', [DashboardController::class, 'liveStats'])->name('dashboard.live-stats');

    // Admin Management
    Route::resource('admins', AdminManagementController::class)->except(['show']);

    // Judge Management
    Route::patch('/judges/{judge}/toggle-status', [JudgeManagementController::class, 'toggleStatus'])->name('judges.toggle-status');
    Route::resource('judges', JudgeManagementController::class);
});

// Judge routes (placeholder for future implementation)
Route::prefix('judge')->middleware('judge')->name('judge.')->group(function () {
    Route::get('/dashboard', function () {
        return view('judge.dashboard');
    })->name('dashboard');
});
