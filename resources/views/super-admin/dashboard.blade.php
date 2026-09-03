@extends('layouts.super-admin')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="page-title text-2xl font-bold text-[var(--text-primary)]">Super-Admin Dashboard</h1>
                <span class="badge bg-purple-100 text-purple-800 border border-purple-200">System Master</span>
            </div>
            <p class="page-subtitle text-sm text-[var(--text-muted)] mt-1">High-level control, real-time metrics, system health, and security audit logs.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.admins.create') }}" class="btn btn-green shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                <span>Add Admin Account</span>
            </a>
            <a href="{{ route('super-admin.secret-register') }}" class="btn btn-outline border-purple-300 text-purple-700 hover:bg-purple-50">
                <span>🔐 Secret Portal</span>
            </a>
        </div>
    </div>

    {{-- System KPI Metrics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Super Admins --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="stat-label text-purple-700">Super Admins</span>
                <div class="stat-icon bg-purple-100 text-purple-700">
                    👑
                </div>
            </div>
            <div class="mt-4">
                <div class="stat-value text-purple-900">{{ number_format($superAdminCount) }}</div>
                <div class="text-xs text-[var(--text-muted)] mt-1">Master Accounts</div>
            </div>
        </div>

        {{-- Admins --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="stat-label">Admin Accounts</span>
                <div class="stat-icon bg-blue-100 text-blue-700">
                    🛡️
                </div>
            </div>
            <div class="mt-4">
                <div class="stat-value">{{ number_format($adminCount) }}</div>
                <div class="text-xs text-[var(--text-muted)] mt-1">Active Administrators</div>
            </div>
        </div>

        {{-- Judges --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="stat-label">Judges Panel</span>
                <div class="stat-icon bg-indigo-100 text-indigo-700">
                    ⚖️
                </div>
            </div>
            <div class="mt-4">
                <div class="stat-value">{{ number_format($judgeCount) }}</div>
                <div class="text-xs text-[var(--text-muted)] mt-1">Registered Judges</div>
            </div>
        </div>

        {{-- Candidates & Audit --}}
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <span class="stat-label">Candidates & Audit</span>
                <div class="stat-icon bg-amber-100 text-amber-700">
                    📋
                </div>
            </div>
            <div class="mt-4">
                <div class="stat-value">{{ number_format($candidateCount) }}</div>
                <div class="text-xs text-[var(--text-muted)] mt-1">{{ number_format($auditCount) }} Audit Records</div>
            </div>
        </div>
    </div>

    {{-- System Infrastructure Health & Controls --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Infrastructure Health --}}
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title flex items-center gap-2">
                    <span class="text-teal-600">⚡</span> System Infrastructure
                </h3>
                <span class="badge badge-success">Healthy</span>
            </div>
            <div class="panel-body space-y-3">
                <div class="flex items-center justify-between p-3 rounded-lg bg-[var(--surface-100)] border border-[var(--border-default)]">
                    <span class="text-xs font-semibold text-[var(--text-secondary)]">PHP Engine</span>
                    <span class="text-xs font-mono font-bold text-[var(--text-primary)]">{{ $systemHealth['php_version'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-[var(--surface-100)] border border-[var(--border-default)]">
                    <span class="text-xs font-semibold text-[var(--text-secondary)]">Laravel Framework</span>
                    <span class="text-xs font-mono font-bold text-[var(--text-primary)]">v{{ $systemHealth['laravel_version'] }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-[var(--surface-100)] border border-[var(--border-default)]">
                    <span class="text-xs font-semibold text-[var(--text-secondary)]">Database Connection</span>
                    <span class="text-xs font-semibold text-emerald-700 flex items-center gap-1">
                        <span class="live-dot"></span>
                        {{ $systemHealth['db_connection'] }}
                    </span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-[var(--surface-100)] border border-[var(--border-default)]">
                    <span class="text-xs font-semibold text-[var(--text-secondary)]">Cache Driver</span>
                    <span class="text-xs font-mono font-bold text-[var(--text-primary)]">{{ strtoupper($systemHealth['cache_driver']) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg bg-[var(--surface-100)] border border-[var(--border-default)]">
                    <span class="text-xs font-semibold text-[var(--text-secondary)]">Session Driver</span>
                    <span class="text-xs font-mono font-bold text-[var(--text-primary)]">{{ strtoupper($systemHealth['session_driver']) }}</span>
                </div>
            </div>
        </div>

        {{-- Quick Controls --}}
        <div class="lg:col-span-2 panel flex flex-col justify-between">
            <div>
                <div class="panel-header">
                    <h3 class="panel-title flex items-center gap-2">
                        <span class="text-purple-600">⚙️</span> Administrative Actions & Controls
                    </h3>
                </div>
                <div class="panel-body grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('admin.admins.create') }}" class="p-4 rounded-xl border border-[var(--border-default)] hover:border-purple-300 hover:bg-purple-50/50 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-lg bg-purple-100 text-purple-700 font-bold">
                                ➕
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[var(--text-primary)] group-hover:text-purple-700">Add Admin Account</h4>
                                <p class="text-[11px] text-[var(--text-muted)]">Provision new admin user</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.settings.audit_record') }}" class="p-4 rounded-xl border border-[var(--border-default)] hover:border-amber-300 hover:bg-amber-50/50 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-lg bg-amber-100 text-amber-700 font-bold">
                                📜
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[var(--text-primary)] group-hover:text-amber-700">View Audit Logs</h4>
                                <p class="text-[11px] text-[var(--text-muted)]">Audit trail of system edits</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.cache.index') }}" class="p-4 rounded-xl border border-[var(--border-default)] hover:border-teal-300 hover:bg-teal-50/50 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-lg bg-teal-100 text-teal-700 font-bold">
                                ⚡
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[var(--text-primary)] group-hover:text-teal-700">Cache Management</h4>
                                <p class="text-[11px] text-[var(--text-muted)]">Flush views, routes & config</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="p-4 rounded-xl border border-[var(--border-default)] hover:border-indigo-300 hover:bg-indigo-50/50 transition-all group">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 rounded-lg bg-indigo-100 text-indigo-700 font-bold">
                                🛠️
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-[var(--text-primary)] group-hover:text-indigo-700">Criteria Settings</h4>
                                <p class="text-[11px] text-[var(--text-muted)]">Adjust percentage weights</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="px-6 py-3 border-t border-[var(--border-default)] bg-[var(--surface-50)] flex items-center justify-between text-xs text-[var(--text-muted)]">
                <span>Active Super Admin Session</span>
                <span class="font-semibold text-[var(--text-primary)]">Web Guard Authenticated</span>
            </div>
        </div>
    </div>

    {{-- Audit Log Table --}}
    <div class="panel">
        <div class="panel-header">
            <h3 class="panel-title">Recent System Audit Log</h3>
            <a href="{{ route('admin.settings.audit_record') }}" class="btn btn-outline btn-sm">
                Full Audit History &rarr;
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Category / Context</th>
                        <th>Timestamp</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentAuditLogs as $log)
                        <tr>
                            <td class="font-mono font-medium text-[var(--text-muted)]">#{{ $log->id }}</td>
                            <td>
                                <div class="font-bold text-[var(--text-primary)]">{{ $log->user_name ?? 'System' }}</div>
                                <div class="text-xs text-[var(--text-muted)] capitalize">{{ $log->user_role ?? 'N/A' }}</div>
                            </td>
                            <td class="font-semibold text-[var(--text-primary)]">
                                {{ $log->action ?? $log->description ?? 'Record Activity' }}
                            </td>
                            <td>
                                <span class="badge badge-info">
                                    {{ $log->category ?? $log->target_type ?? 'General' }}
                                </span>
                            </td>
                            <td class="text-[var(--text-muted)] text-xs">
                                {{ $log->created_at ? $log->created_at->diffForHumans() : 'Just now' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-[var(--text-muted)]">
                                No recent audit records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
