@extends('layouts.super-admin')

@section('title', 'Super-Admin Command Center')

@section('content')
<div class="space-y-6">
    {{-- Command Center Hero Header --}}
    <div class="panel border-l-4 border-purple-600 bg-gradient-to-r from-purple-500/5 via-transparent to-transparent">
        <div class="panel-body py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-600/10 text-purple-600 flex items-center justify-center flex-shrink-0 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2.5">
                        <h1 class="page-title text-2xl font-bold text-[var(--text-primary)]">Super-Admin Command Center</h1>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 border border-purple-200">
                            👑 Master Control
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Monitoring
                        </span>
                    </div>
                    <p class="page-subtitle text-sm text-[var(--text-muted)] mt-1">
                        Comprehensive system orchestration, real-time infrastructure diagnostics, security logs, and user authority.
                    </p>
                </div>
            </div>

            {{-- Live Status & Clock Card --}}
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 rounded-xl bg-[var(--surface-50)] border border-[var(--border-default)] shadow-xs flex items-center gap-3">
                    <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></div>
                    <div class="text-right">
                        <div class="text-[11px] font-semibold text-[var(--text-muted)] uppercase tracking-wider">System Clock</div>
                        <div class="text-xs font-mono font-bold text-[var(--text-primary)]">
                            {{ now()->format('M d, Y • h:i A') }}
                        </div>
                    </div>
                    <button type="button" onclick="window.location.reload()" title="Refresh Dashboard" class="p-1.5 text-[var(--text-muted)] hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- System KPI Metrics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Super Admins --}}
        <div class="stat-card border-t-4 border-purple-600 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-700">Super Admins</span>
                    <div class="text-3xl font-extrabold text-[var(--text-primary)] mt-1">{{ number_format($superAdminCount) }}</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center text-xl shadow-xs">
                    👑
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[var(--border-default)] flex items-center justify-between text-xs">
                <span class="text-[var(--text-muted)]">Full Master Authority</span>
                <span class="px-2 py-0.5 rounded-md bg-purple-50 text-purple-700 font-semibold text-[11px]">Root</span>
            </div>
        </div>

        {{-- Admins --}}
        <div class="stat-card border-t-4 border-blue-600 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Admins</span>
                    <div class="text-3xl font-extrabold text-[var(--text-primary)] mt-1">{{ number_format($adminCount) }}</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shadow-xs">
                    🛡️
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[var(--border-default)] flex items-center justify-between text-xs">
                <span class="text-[var(--text-muted)]">Active Administrators</span>
                <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 font-semibold text-[11px]">Operators</span>
            </div>
        </div>

        {{-- Judges --}}
        <div class="stat-card border-t-4 border-indigo-600 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Judges Panel</span>
                    <div class="text-3xl font-extrabold text-[var(--text-primary)] mt-1">{{ number_format($judgeCount) }}</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-xl shadow-xs">
                    ⚖️
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[var(--border-default)] flex items-center justify-between text-xs">
                <span class="text-[var(--text-muted)]">Registered Evaluators</span>
                <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 font-semibold text-[11px]">Scorers</span>
            </div>
        </div>

        {{-- Contestants & Audit --}}
        <div class="stat-card border-t-4 border-amber-500 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Contestants</span>
                    <div class="text-3xl font-extrabold text-[var(--text-primary)] mt-1">{{ number_format($candidateCount) }}</div>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center text-xl shadow-xs">
                    🌟
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-[var(--border-default)] flex items-center justify-between text-xs">
                <span class="text-[var(--text-muted)]">{{ number_format($auditCount) }} Security Events</span>
                <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 font-semibold text-[11px]">Tracked</span>
            </div>
        </div>
    </div>

    {{-- System Diagnostics & Recent User Directory Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Infrastructure Health Diagnostics --}}
        <div class="panel flex flex-col justify-between shadow-xs">
            <div>
                <div class="panel-header border-b border-[var(--border-default)] pb-4">
                    <div class="flex items-center justify-between">
                        <h3 class="panel-title flex items-center gap-2 text-sm font-bold text-[var(--text-primary)]">
                            <span class="p-1.5 rounded-lg bg-teal-50 text-teal-600">⚡</span>
                            Infrastructure Health
                        </h3>
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Operational
                        </span>
                    </div>
                </div>
                <div class="panel-body space-y-3 pt-4">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-[var(--surface-100)] border border-[var(--border-default)]">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">🐘</span>
                            <span class="text-xs font-semibold text-[var(--text-secondary)]">PHP Engine</span>
                        </div>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-md bg-white border border-[var(--border-default)] text-[var(--text-primary)]">
                            {{ $systemHealth['php_version'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-[var(--surface-100)] border border-[var(--border-default)]">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">🔴</span>
                            <span class="text-xs font-semibold text-[var(--text-secondary)]">Laravel Framework</span>
                        </div>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-md bg-white border border-[var(--border-default)] text-[var(--text-primary)]">
                            v{{ $systemHealth['laravel_version'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-[var(--surface-100)] border border-[var(--border-default)]">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">🗄️</span>
                            <span class="text-xs font-semibold text-[var(--text-secondary)]">Database Link</span>
                        </div>
                        <span class="text-xs font-semibold text-emerald-700 flex items-center gap-1.5 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            {{ $systemHealth['db_connection'] }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-[var(--surface-100)] border border-[var(--border-default)]">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">⚡</span>
                            <span class="text-xs font-semibold text-[var(--text-secondary)]">Cache Storage</span>
                        </div>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-md bg-white border border-[var(--border-default)] text-[var(--text-primary)]">
                            {{ strtoupper($systemHealth['cache_driver']) }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-[var(--surface-100)] border border-[var(--border-default)]">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm">🛡️</span>
                            <span class="text-xs font-semibold text-[var(--text-secondary)]">Session Driver</span>
                        </div>
                        <span class="text-xs font-mono font-bold px-2 py-0.5 rounded-md bg-white border border-[var(--border-default)] text-[var(--text-primary)]">
                            {{ strtoupper($systemHealth['session_driver']) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-5 py-3 border-t border-[var(--border-default)] bg-[var(--surface-50)] rounded-b-2xl flex items-center justify-between text-xs text-[var(--text-muted)]">
                <span class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Ready for Live Intramurals
                </span>
                <span class="font-mono text-[11px] font-semibold text-purple-700">Security Tier 1</span>
            </div>
        </div>

        {{-- Recent Accounts & Identity Roster --}}
        <div class="lg:col-span-2 panel flex flex-col justify-between shadow-xs">
            <div>
                <div class="panel-header border-b border-[var(--border-default)] pb-4 flex items-center justify-between">
                    <h3 class="panel-title flex items-center gap-2 text-sm font-bold text-[var(--text-primary)]">
                        <span class="p-1.5 rounded-lg bg-purple-50 text-purple-600">👥</span>
                        User Accounts & Access Directory
                    </h3>
                    <div class="flex items-center gap-2 text-xs text-[var(--text-muted)]">
                        <span class="font-bold text-[var(--text-primary)]">{{ $totalUsers }}</span> Total Registered
                    </div>
                </div>

                <div class="panel-body divide-y divide-[var(--border-default)]">
                    @forelse($recentUsers as $user)
                        <div class="py-3.5 first:pt-1 last:pb-1 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm text-white flex-shrink-0 shadow-xs
                                    {{ in_array($user->role, ['super-admin', 'super_admin']) ? 'bg-gradient-to-br from-purple-600 to-indigo-700' : ($user->role === 'admin' ? 'bg-gradient-to-br from-blue-600 to-cyan-700' : 'bg-gradient-to-br from-emerald-600 to-teal-700') }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-[var(--text-primary)] truncate flex items-center gap-2">
                                        <span>{{ $user->name }}</span>
                                        @if(in_array($user->role, ['super-admin', 'super_admin']))
                                            <span class="text-[10px] font-extrabold px-1.5 py-0.2 rounded bg-purple-100 text-purple-800 border border-purple-200">SUPER</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-[var(--text-muted)] truncate flex items-center gap-2 mt-0.5">
                                        <span>{{ $user->email }}</span>
                                        <span>•</span>
                                        <span class="font-mono">@<span>{{ $user->username ?? 'user' }}</span></span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 flex-shrink-0">
                                @if(in_array($user->role, ['super-admin', 'super_admin']))
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        Super-Admin
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        Admin
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Judge
                                    </span>
                                @endif

                                <span class="hidden sm:inline text-[11px] text-[var(--text-muted)]">
                                    {{ $user->created_at ? $user->created_at->diffForHumans() : 'Active' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-sm text-[var(--text-muted)]">
                            No registered users found.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Role Breakdown Distribution Bar --}}
            <div class="px-6 py-3 border-t border-[var(--border-default)] bg-[var(--surface-50)] rounded-b-2xl">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                    <span class="text-[var(--text-muted)] font-medium">Role Ratio Composition</span>
                    <div class="flex items-center gap-4 text-[11px]">
                        <span class="flex items-center gap-1.5 text-purple-700 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-purple-600"></span> Super-Admins: {{ $superAdminCount }}
                        </span>
                        <span class="flex items-center gap-1.5 text-blue-700 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span> Admins: {{ $adminCount }}
                        </span>
                        <span class="flex items-center gap-1.5 text-emerald-700 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span> Judges: {{ $judgeCount }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- System Security & Audit Log Table --}}
    @php
        $formatCategory = function($cat) {
            $cat = strtolower(trim($cat ?? ''));
            return match($cat) {
                'qa', 'qanda' => ['label' => 'Q&A Final', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
                'production' => ['label' => 'Production', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
                'fitness' => ['label' => 'Fitness', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                'traditional_attire', 'traditional-attire' => ['label' => 'Traditional Attire', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
                'indigenous_attire', 'indigenous-attire' => ['label' => 'Indigenous Attire', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
                'talent_portion', 'talent-portion' => ['label' => 'Talent Portion', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
                'photogenic' => ['label' => 'Photogenic', 'class' => 'bg-cyan-100 text-cyan-800 border-cyan-200'],
                'security', 'auth' => ['label' => 'Security & Auth', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
                default => ['label' => ucwords(str_replace(['_', '-'], ' ', $cat ?: 'General')), 'class' => 'bg-gray-100 text-gray-800 border-gray-200'],
            };
        };
    @endphp

    <div class="panel shadow-xs overflow-hidden">
        <div class="panel-header border-b border-[var(--border-default)] py-4 px-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[var(--surface-50)]">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 flex items-center justify-center text-lg flex-shrink-0">
                    📜
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="panel-title text-base font-bold text-[var(--text-primary)]">
                            System Audit Trail & Security Ledger
                        </h3>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            {{ number_format($auditCount) }} Events
                        </span>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">Real-time immutable log of judge score entries, administrative changes, and security events.</p>
                </div>
            </div>
            <a href="{{ route('super-admin.settings.audit_record') }}" class="btn btn-outline btn-sm hover:border-purple-300 hover:text-purple-700 flex items-center gap-1.5 self-start sm:self-auto shadow-2xs">
                <span>View Full Audit History</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr class="bg-[var(--surface-100)] text-[var(--text-muted)] text-[11px] uppercase tracking-wider border-b border-[var(--border-default)]">
                        <th class="py-3 px-4 w-20 text-left font-bold">Event ID</th>
                        <th class="py-3 px-4 text-left font-bold w-48">Actor / User</th>
                        <th class="py-3 px-4 text-left font-bold">Action & Score Summary</th>
                        <th class="py-3 px-4 text-left font-bold w-40">Category / Stage</th>
                        <th class="py-3 px-4 text-left font-bold w-32">Status</th>
                        <th class="py-3 px-4 text-right font-bold w-44">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-default)] bg-white text-xs">
                    @forelse($recentAuditLogs as $log)
                        @php
                            $details = is_string($log->details) ? json_decode($log->details, true) : (is_array($log->details) ? $log->details : []);
                            $scoreVal = $log->new_score ?? ($details['score'] ?? null);
                            $oldScoreVal = $log->old_score ?? ($details['old_score'] ?? null);
                            $categoryKey = $log->category ?? ($details['category'] ?? 'general');
                            $catInfo = $formatCategory($categoryKey);
                            $actionType = $details['action'] ?? $log->event_type ?? ($log->action ?? 'saved');
                            $candNum = $log->candidate_number ?? ($details['candidate_number'] ?? null);
                            $candName = $log->candidate_name ?? ($details['candidate_name'] ?? null);
                        @endphp
                        <tr class="hover:bg-purple-50/20 transition-colors">
                            {{-- Event ID --}}
                            <td class="py-3.5 px-4 font-mono font-bold text-[var(--text-muted)] align-middle">
                                <span class="px-2 py-1 rounded-md bg-[var(--surface-100)] border border-[var(--border-default)] text-[11px]">
                                    #{{ $log->id }}
                                </span>
                            </td>

                            {{-- Actor User --}}
                            <td class="py-3.5 px-4 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs
                                        {{ in_array($log->user_role, ['super-admin', 'super_admin']) ? 'bg-purple-100 text-purple-700 border border-purple-200' :
                                           ($log->user_role === 'admin' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200') }}">
                                        {{ strtoupper(substr($log->user_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-sm text-[var(--text-primary)] truncate">
                                            {{ $log->user_name ?? 'System Event' }}
                                        </div>
                                        <div class="text-[11px] text-[var(--text-muted)] capitalize flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $log->user_role === 'judge' ? 'bg-emerald-500' : ($log->user_role === 'admin' ? 'bg-blue-500' : 'bg-purple-500') }}"></span>
                                            {{ $log->user_role ?? 'Automated' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Event Description & Score Details --}}
                            <td class="py-3.5 px-4 align-middle">
                                <div class="flex flex-col gap-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($actionType === 'reset' || $actionType === 'score_reset')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Score Reset
                                            </span>
                                        @elseif($oldScoreVal !== null && $scoreVal !== null && $oldScoreVal != $scoreVal)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Score Updated
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Score Saved
                                            </span>
                                        @endif

                                        @if($candNum)
                                            <span class="text-xs font-semibold text-[var(--text-secondary)] flex items-center gap-1">
                                                Candidate <span class="px-1.5 py-0.5 rounded bg-purple-50 text-purple-800 font-bold border border-purple-200">#{{ $candNum }}</span>
                                                @if($candName)
                                                    <span class="text-[var(--text-muted)] font-normal truncate max-w-xs">({{ $candName }})</span>
                                                @endif
                                            </span>
                                        @endif

                                        {{-- Score Pill --}}
                                        @if($scoreVal !== null)
                                            <div class="inline-flex items-center gap-1.5 ml-1">
                                                @if($oldScoreVal !== null && $oldScoreVal != $scoreVal)
                                                    <span class="text-[var(--text-muted)] line-through font-mono text-xs">{{ number_format((float)$oldScoreVal, 2) }}</span>
                                                    <span class="text-[var(--text-muted)]">&rarr;</span>
                                                @endif
                                                <span class="px-2 py-0.5 rounded-md font-mono font-black text-xs bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs">
                                                    {{ number_format((float)$scoreVal, 2) }} / 10
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    @if(!empty($log->action_description))
                                        <div class="text-[11px] text-[var(--text-muted)] mt-0.5">
                                            {{ $log->action_description }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Category / Context --}}
                            <td class="py-3.5 px-4 align-middle">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $catInfo['class'] }}">
                                    {{ $catInfo['label'] }}
                                </span>
                            </td>

                            {{-- Status & Threat Indicator --}}
                            <td class="py-3.5 px-4 align-middle">
                                @if($log->is_suspicious || $log->risk_level === 'critical')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Critical
                                    </span>
                                @elseif($log->risk_level === 'warning')
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Warning
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[11px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified
                                    </span>
                                @endif
                            </td>

                            {{-- Timestamp --}}
                            <td class="py-3.5 px-4 text-right align-middle font-mono">
                                <div class="font-bold text-xs text-[var(--text-primary)]">
                                    {{ $log->created_at ? $log->created_at->diffForHumans() : 'Just now' }}
                                </div>
                                <div class="text-[10px] text-[var(--text-muted)] mt-0.5">
                                    {{ $log->created_at ? $log->created_at->format('M d, Y • H:i:s') : '' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-[var(--text-muted)]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-[var(--text-muted)] opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-sm font-semibold">No recent audit records available</span>
                                    <span class="text-xs">Judge scoring entries and system activities will appear here in real-time.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
