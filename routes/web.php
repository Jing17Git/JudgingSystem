<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\JudgeManagementController;
use App\Http\Controllers\Admin\CandidateManagementController;
use App\Http\Controllers\Admin\ProductionController;
use App\Http\Controllers\Admin\FitnessController;
use App\Http\Controllers\Admin\IndigenousAttireController;
use App\Http\Controllers\Admin\TraditionalAttireController;
use App\Http\Controllers\Admin\QaController;
use App\Http\Controllers\Admin\OverallController;
use App\Http\Controllers\Admin\FinalOverallController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Judge\JudgeDashboardController;
use App\Http\Controllers\Judge\JudgeScoringController;
use App\Http\Controllers\Judge\JudgeProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Landing page
Route::get('/', [WelcomeController::class, 'index'])->name('home');

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

    // Q&A Final Judging
    Route::get('/qa', [QaController::class, 'index'])->name('qa.index');
    Route::post('/qa/score', [QaController::class, 'saveScore'])->name('qa.save-score');
    Route::post('/qa/questions', [QaController::class, 'storeQuestion'])->name('qa.store-question');
    Route::put('/qa/questions/{question}', [QaController::class, 'updateQuestion'])->name('qa.update-question');
    Route::delete('/qa/questions/{question}', [QaController::class, 'destroyQuestion'])->name('qa.destroy-question');

    // Overall Tabulations
    Route::get('/overall', [OverallController::class, 'index'])->name('overall.index');
    Route::get('/overall-final', [FinalOverallController::class, 'index'])->name('overall.final');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/preliminary', [SettingsController::class, 'preliminary'])->name('settings.preliminary');
    Route::get('/settings/final', [SettingsController::class, 'final'])->name('settings.final');
    Route::get('/settings/judge-scores', [SettingsController::class, 'judgeScores'])->name('settings.judge-scores');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
});

// Judge routes
Route::prefix('judge')->middleware('judge')->name('judge.')->group(function () {
    // Judge Dashboard
    Route::get('/dashboard', [JudgeDashboardController::class, 'index'])->name('dashboard');

    // Scoring Categories
    Route::get('/production', [JudgeScoringController::class, 'production'])->name('production.index');
    Route::get('/fitness', [JudgeScoringController::class, 'fitness'])->name('fitness.index');
    Route::get('/traditional-attire', [JudgeScoringController::class, 'traditionalAttire'])->name('traditional-attire.index');
    Route::get('/indigenous-attire', [JudgeScoringController::class, 'indigenousAttire'])->name('indigenous-attire.index');
    Route::get('/qa', [JudgeScoringController::class, 'qa'])->name('qa.index');
    Route::get('/qanda', [JudgeScoringController::class, 'qa'])->name('qanda.index');

    // Scoring Actions
    Route::post('/save-score', [JudgeScoringController::class, 'saveScore'])->name('save-score');
    Route::post('/reset-score', [JudgeScoringController::class, 'resetScore'])->name('reset-score');

    // Account Profile
    Route::get('/profile', [JudgeProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [JudgeProfileController::class, 'update'])->name('profile.update');
});
