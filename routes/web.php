<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\CacheController;
use App\Http\Controllers\Admin\CandidateManagementController;
use App\Http\Controllers\Admin\CategoryManagementController;
use App\Http\Controllers\Admin\CustomCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinalOverallController;
use App\Http\Controllers\Admin\FitnessController;
use App\Http\Controllers\Admin\IndigenousAttireController;
use App\Http\Controllers\Admin\JudgeManagementController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\OverallController;
use App\Http\Controllers\Admin\ProductionController;
use App\Http\Controllers\Admin\QaController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TraditionalAttireController;
use App\Http\Controllers\Auth\SuperAdminRegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Judge\JudgeCustomCategoryController;
use App\Http\Controllers\Judge\JudgeDashboardController;
use App\Http\Controllers\Judge\JudgeProfileController;
use App\Http\Controllers\Judge\JudgeScoringController;
use App\Http\Controllers\SuperAdmin\SuperAdminCategoryController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\WelcomeController;
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

// Secret Super Admin Registration Portal
Route::get('/super-admin/secret-register', [SuperAdminRegisterController::class, 'showRegister'])->name('super-admin.secret-register');
Route::post('/super-admin/secret-register', [SuperAdminRegisterController::class, 'register'])->name('super-admin.secret-register.store');

// Password Reset
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Super Admin routes
Route::prefix('super-admin')->middleware(['auth', 'super-admin'])->name('super-admin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    
    // Category Management
    Route::get('/categories/management', [SuperAdminCategoryController::class, 'index'])->name('categories.management');
    Route::post('/categories/management', [SuperAdminCategoryController::class, 'store'])->name('categories.management.store');
    Route::put('/categories/management/{setting}', [SuperAdminCategoryController::class, 'update'])->name('categories.management.update');
    Route::delete('/categories/management/{setting}', [SuperAdminCategoryController::class, 'destroy'])->name('categories.management.destroy');
    Route::post('/categories/management/percentages', [SuperAdminCategoryController::class, 'updatePercentages'])->name('categories.management.percentages');
});

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
    Route::get('/overall/candidate/{candidate}', [OverallController::class, 'candidateVotes'])->name('overall.candidate-votes');

    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::get('/settings/categories', [CategoryManagementController::class, 'index'])->name('settings.categories');
    Route::post('/settings/categories', [CategoryManagementController::class, 'store'])->name('settings.categories.store');
    Route::put('/settings/categories/{setting}', [CategoryManagementController::class, 'update'])->name('settings.categories.update');
    Route::delete('/settings/categories/{setting}', [CategoryManagementController::class, 'destroy'])->name('settings.categories.destroy');
    Route::post('/settings/categories/percentages', [CategoryManagementController::class, 'updatePercentages'])->name('settings.categories.percentages');
    Route::get('/categories/management', [CategoryManagementController::class, 'index'])->name('categories.management');
    Route::get('/settings/preliminary', [SettingsController::class, 'preliminary'])->name('settings.preliminary');
    Route::get('/settings/final', [SettingsController::class, 'final'])->name('settings.final');
    Route::get('/settings/judge-scores', [SettingsController::class, 'judgeScores'])->name('settings.judge-scores');
    Route::get('/settings/audit_record', [SettingsController::class, 'auditRecord'])->name('settings.audit_record');
    Route::get('/settings/audit-record', [SettingsController::class, 'auditRecord']);
    Route::post('/settings/audit_record/clear', [SettingsController::class, 'clearAuditRecord'])->name('settings.audit_record.clear');
    Route::get('/settings/audit_record/export', [SettingsController::class, 'exportAuditRecord'])->name('settings.audit_record.export');
    Route::get('/settings/audit_record/candidate_history', [SettingsController::class, 'candidateHistory'])->name('settings.audit_record.candidate_history');
    Route::post('/settings/audit_record/{record}/review', [SettingsController::class, 'reviewAuditRecord'])->name('settings.audit_record.review');
    Route::post('/settings/audit_record/{record}/flag', [SettingsController::class, 'flagAuditRecord'])->name('settings.audit_record.flag');
    Route::get('/settings/cache', [CacheController::class, 'index'])->name('settings.cache');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

    // Cache Management
    Route::get('/cache', [CacheController::class, 'index'])->name('cache.index');
    Route::post('/cache/clear-all', [CacheController::class, 'clearAll'])->name('cache.clear-all');
    Route::post('/cache/clear-app', [CacheController::class, 'clearApp'])->name('cache.clear-app');
    Route::post('/cache/clear-route', [CacheController::class, 'clearRoute'])->name('cache.clear-route');
    Route::post('/cache/clear-config', [CacheController::class, 'clearConfig'])->name('cache.clear-config');
    Route::post('/cache/clear-view', [CacheController::class, 'clearView'])->name('cache.clear-view');
    Route::post('/cache/clear-logs', [CacheController::class, 'clearLogs'])->name('cache.clear-logs');
    Route::post('/cache/optimize', [CacheController::class, 'optimize'])->name('cache.optimize');

    // Logs Management
    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    Route::post('/logs/clear', [LogController::class, 'clear'])->name('logs.clear');
    Route::get('/logs/download', [LogController::class, 'download'])->name('logs.download');

    // Account Settings
    Route::get('/account', [AccountController::class, 'index'])->name('account.index');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
    Route::put('/account/change-password', [AccountController::class, 'changePassword'])->name('account.change-password');

    // Dynamic custom categories (added via Super Admin)
    Route::get('/category/{key}', [CustomCategoryController::class, 'index'])->name('category.index');
    Route::post('/category/score', [CustomCategoryController::class, 'saveScore'])->name('category.save-score');
    Route::post('/category/reset', [CustomCategoryController::class, 'resetScore'])->name('category.reset-score');
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

    // Dynamic custom categories (added via Super Admin)
    Route::get('/category/{key}', [JudgeCustomCategoryController::class, 'index'])->name('category.index');

    // Account Profile
    Route::get('/profile', [JudgeProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [JudgeProfileController::class, 'update'])->name('profile.update');
});
