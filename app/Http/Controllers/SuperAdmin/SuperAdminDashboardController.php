<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AuditRecord;
use App\Models\Candidate;
use App\Models\Pageant;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    /**
     * Display the Super-Admin Dashboard.
     */
    public function index()
    {
        $superAdminCount = User::whereIn('role', ['super-admin', 'super_admin'])->count();
        $adminCount = User::where('role', 'admin')->count();
        $judgeCount = User::where('role', 'judge')->count();
        $totalUsers = User::count();
        $candidateCount = class_exists(Candidate::class) ? Candidate::count() : 0;
        $pageantCount = class_exists(Pageant::class) ? Pageant::count() : 0;

        $auditCount = 0;
        $recentAuditLogs = collect();
        if (class_exists(AuditRecord::class)) {
            try {
                $auditCount = AuditRecord::count();
                $recentAuditLogs = AuditRecord::latest()->take(8)->get();
            } catch (\Exception $e) {
                // Table might not exist or error fetching audit logs
            }
        }

        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();

        // System Health & Metrics
        $dbConnected = true;
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbConnected = false;
        }

        $systemHealth = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'db_connection' => $dbConnected ? 'Healthy' : 'Disconnected',
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
            'queue_driver' => config('queue.default'),
        ];

        return view('super-admin.dashboard', compact(
            'superAdminCount',
            'adminCount',
            'judgeCount',
            'totalUsers',
            'candidateCount',
            'pageantCount',
            'auditCount',
            'recentAuditLogs',
            'recentUsers',
            'systemHealth'
        ));
    }
}
