<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\JudgeManagementController;
use App\Http\Controllers\Admin\CandidateManagementController;
use App\Http\Controllers\Admin\ProductionController;
use App\Http\Controllers\Admin\FitnessController;
use App\Http\Controllers\Admin\IndigenousAttireController;
use App\Http\Controllers\Admin\TraditionalAttireController;
use App\Http\Controllers\Admin\OverallController;
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

    // Candidate Management
    Route::resource('candidates', CandidateManagementController::class);

    // Production Scoring
    Route::get('/production', [ProductionController::class, 'index'])->name('production.index');
    Route::post('/production/score', [ProductionController::class, 'saveScore'])->name('production.save-score');

    // Fitness Scoring
    Route::get('/fitness', [FitnessController::class, 'index'])->name('fitness.index');
    Route::post('/fitness/score', [FitnessController::class, 'saveScore'])->name('fitness.save-score');

    // Indigenous Attire Scoring
    Route::get('/indigenous-attire', [IndigenousAttireController::class, 'index'])->name('indigenous-attire.index');
    Route::post('/indigenous-attire/score', [IndigenousAttireController::class, 'saveScore'])->name('indigenous-attire.save-score');

    // Traditional Attire Scoring
    Route::get('/traditional-attire', [TraditionalAttireController::class, 'index'])->name('traditional-attire.index');
    Route::post('/traditional-attire/score', [TraditionalAttireController::class, 'saveScore'])->name('traditional-attire.save-score');

    // Overall Tabulation
    Route::get('/overall', [OverallController::class, 'index'])->name('overall.index');
});

// Judge routes (placeholder for future implementation)
Route::prefix('judge')->middleware('judge')->name('judge.')->group(function () {
    Route::get('/dashboard', function () {
        return view('judge.dashboard');
    })->name('dashboard');
});
