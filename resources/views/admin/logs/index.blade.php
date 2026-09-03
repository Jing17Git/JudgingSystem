@extends('layouts.admin')

@section('title', 'Logs Management')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
            <span>Settings</span>
            <span>/</span>
            <span class="text-[var(--green-700)] font-semibold">Logs Management</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Application Logs Management
        </h1>
        <p class="page-subtitle">View, search, filter, and manage application log entries.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.logs.download') }}" class="btn btn-green flex items-center gap-2 shadow-sm text-xs font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download Log
        </a>
        <form method="POST" action="{{ route('admin.logs.clear') }}" onsubmit="return confirm('Are you sure you want to clear the log file? This action cannot be undone.');">
            @csrf
            <button type="submit" class="btn btn-red flex items-center gap-2 shadow-sm text-xs font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Clear Logs
            </button>
        </form>
    </div>
</div>

{{-- Navigation Tabs between Settings Sections --}}
<div class="flex flex-nowrap overflow-x-auto items-center gap-2 mb-6 border-b border-[var(--border-default)] pb-3 hide-scrollbar">
    <a href="{{ route('admin.settings.preliminary') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
        Preliminary Criteria
    </a>
    <a href="{{ route('admin.settings.final') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
        Final Criteria
    </a>
    <a href="{{ route('admin.settings.judge-scores') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
        Judge Score Sheets
    </a>
    <a href="{{ route('admin.settings.audit_record') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
        Audit Record
    </a>
    <a href="{{ route('admin.cache.index') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
        Cache Management
    </a>
    <a href="{{ route('admin.logs.index') }}"
       class="px-4 py-2 rounded-xl text-sm font-bold bg-[var(--green-100)] text-[var(--green-800)] border border-[var(--green-300)] flex items-center gap-2 shadow-sm whitespace-nowrap">
        <span class="w-2.5 h-2.5 rounded-full bg-[var(--green-600)]"></span>
        Logs Management
    </a>
</div>

{{-- Flash Messages --}}
@if(session('success'))
    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 mb-6 text-sm flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 mb-6 text-sm flex items-center gap-2 shadow-sm">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- Log Stats Overview Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    {{-- Log File Size --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">File Size</span>
            <span class="p-2 rounded-lg bg-blue-50 text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
            </span>
        </div>
        <div>
            <div class="text-2xl font-black text-gray-900">
                @if($logExists)
                    @if($logSize >= 1048576)
                        {{ number_format($logSize / 1048576, 2) }} <span class="text-sm font-normal text-[var(--text-muted)]">MB</span>
                    @else
                        {{ number_format($logSize / 1024, 1) }} <span class="text-sm font-normal text-[var(--text-muted)]">KB</span>
                    @endif
                @else
                    N/A
                @endif
            </div>
            <div class="text-xs text-[var(--text-muted)] mt-1">
                {{ $logExists ? 'storage/logs/laravel.log' : 'Log file not found' }}
            </div>
        </div>
    </div>

    {{-- Total Entries --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Total Entries</span>
            <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </span>
        </div>
        <div>
            <div class="text-2xl font-black text-gray-900">{{ number_format($totalEntries) }}</div>
            <div class="text-xs text-[var(--text-muted)] mt-1">Parsed log entries</div>
        </div>
    </div>

    {{-- Errors --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Errors</span>
            <span class="p-2 rounded-lg bg-red-50 text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </span>
        </div>
        <div>
            @php $errorTotal = $levelCounts['error'] + $levelCounts['critical'] + $levelCounts['emergency'] + $levelCounts['alert']; @endphp
            <div class="text-2xl font-black {{ $errorTotal > 0 ? 'text-red-600' : 'text-emerald-600' }}">{{ $errorTotal }}</div>
            <div class="text-xs text-[var(--text-muted)] mt-1">
                Error, critical, emergency, alert
            </div>
        </div>
    </div>

    {{-- Warnings --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Warnings</span>
            <span class="p-2 rounded-lg bg-amber-50 text-amber-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>
        <div>
            <div class="text-2xl font-black {{ $levelCounts['warning'] > 0 ? 'text-amber-600' : 'text-gray-900' }}">{{ $levelCounts['warning'] }}</div>
            <div class="text-xs text-[var(--text-muted)] mt-1">Warning level entries</div>
        </div>
    </div>

    {{-- Info / Debug --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Info / Debug</span>
            <span class="p-2 rounded-lg bg-cyan-50 text-cyan-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>
        <div>
            <div class="text-2xl font-black text-gray-900">{{ $levelCounts['info'] + $levelCounts['debug'] + $levelCounts['notice'] }}</div>
            <div class="text-xs text-[var(--text-muted)] mt-1">Info, debug, notice</div>
        </div>
    </div>
</div>

{{-- Filter & Search Bar --}}
<div class="panel bg-white border border-[var(--border-default)] rounded-2xl p-5 shadow-sm mb-6">
    <form method="GET" action="{{ route('admin.logs.index') }}" class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <div class="flex-1">
            <label class="text-xs font-semibold text-[var(--text-muted)] mb-1.5 block">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search log entries..."
                class="w-full px-3 py-2 text-sm border border-[var(--border-default)] rounded-lg bg-[var(--bg-primary)] text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--green-500)] focus:border-transparent">
        </div>
        <div class="sm:w-48">
            <label class="text-xs font-semibold text-[var(--text-muted)] mb-1.5 block">Log Level</label>
            <select name="level" class="w-full px-3 py-2 text-sm border border-[var(--border-default)] rounded-lg bg-[var(--bg-primary)] text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--green-500)]">
                <option value="all" {{ request('level', 'all') === 'all' ? 'selected' : '' }}>All Levels</option>
                <option value="emergency" {{ request('level') === 'emergency' ? 'selected' : '' }}>🔴 Emergency</option>
                <option value="alert" {{ request('level') === 'alert' ? 'selected' : '' }}>🔴 Alert</option>
                <option value="critical" {{ request('level') === 'critical' ? 'selected' : '' }}>🔴 Critical</option>
                <option value="error" {{ request('level') === 'error' ? 'selected' : '' }}>🟠 Error</option>
                <option value="warning" {{ request('level') === 'warning' ? 'selected' : '' }}>🟡 Warning</option>
                <option value="notice" {{ request('level') === 'notice' ? 'selected' : '' }}>🔵 Notice</option>
                <option value="info" {{ request('level') === 'info' ? 'selected' : '' }}>🟢 Info</option>
                <option value="debug" {{ request('level') === 'debug' ? 'selected' : '' }}>⚪ Debug</option>
            </select>
        </div>
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button type="submit" class="btn btn-green text-xs font-bold px-4 py-2 flex-1 sm:flex-none flex justify-center items-center gap-1.5 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Filter
            </button>
            @if(request('search') || (request('level') && request('level') !== 'all'))
                <a href="{{ route('admin.logs.index') }}" class="btn btn-outline text-xs font-semibold px-4 py-2 flex-1 sm:flex-none text-center text-gray-600 hover:bg-gray-50 border-gray-300">Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Log Entries Table --}}
<div class="panel bg-white border border-[var(--border-default)] rounded-2xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-[var(--border-default)] bg-gray-50 flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-gray-900">Log Entries</h2>
            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                Showing {{ count($logEntries) }} of {{ number_format($totalEntries) }} entries
                @if(request('search'))
                    — filtered by "<strong>{{ request('search') }}</strong>"
                @endif
                @if(request('level') && request('level') !== 'all')
                    — level: <strong>{{ request('level') }}</strong>
                @endif
            </p>
        </div>
        @if($totalPages > 1)
            <div class="flex items-center gap-2 text-xs">
                @if($page > 1)
                    <a href="{{ route('admin.logs.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
                       class="btn btn-outline text-xs px-2.5 py-1 font-semibold text-gray-600 hover:bg-gray-100 border-gray-300">&laquo; Prev</a>
                @endif
                <span class="text-[var(--text-muted)] font-medium">Page {{ $page }}/{{ $totalPages }}</span>
                @if($page < $totalPages)
                    <a href="{{ route('admin.logs.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
                       class="btn btn-outline text-xs px-2.5 py-1 font-semibold text-gray-600 hover:bg-gray-100 border-gray-300">Next &raquo;</a>
                @endif
            </div>
        @endif
    </div>

    @if(!$logExists)
        <div class="px-6 py-16 text-center text-[var(--text-muted)]">
            <div class="p-4 rounded-2xl bg-gray-50 inline-block mb-4">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">No log file found</p>
            <p class="text-xs text-gray-400 mt-1">The laravel.log file does not exist yet.</p>
        </div>
    @elseif(empty($logEntries))
        <div class="px-6 py-16 text-center text-[var(--text-muted)]">
            <div class="p-4 rounded-2xl bg-emerald-50 inline-block mb-4">
                <svg class="w-12 h-12 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-sm font-semibold text-gray-500">
                @if(request('search') || (request('level') && request('level') !== 'all'))
                    No log entries match your filter
                @else
                    Log file is empty
                @endif
            </p>
            <p class="text-xs text-gray-400 mt-1">
                @if(request('search') || (request('level') && request('level') !== 'all'))
                    Try adjusting your search or level filter.
                @else
                    No entries have been recorded yet.
                @endif
            </p>
        </div>
    @else
        <div class="divide-y divide-gray-100">
            @foreach($logEntries as $entry)
                <div class="px-5 py-4 hover:bg-gray-50/50 transition-colors cursor-pointer log-entry-row" onclick="this.querySelector('.log-detail').classList.toggle('hidden')">
                    <div class="flex items-start gap-3">
                        {{-- Level Badge --}}
                        @php
                            $badgeClasses = match($entry['level']) {
                                'emergency', 'alert', 'critical' => 'bg-red-100 text-red-800 border-red-200',
                                'error' => 'bg-red-50 text-red-700 border-red-200',
                                'warning' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'notice' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'info' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'debug' => 'bg-gray-100 text-gray-600 border-gray-200',
                                default => 'bg-gray-100 text-gray-600 border-gray-200',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider flex-shrink-0 mt-0.5 border {{ $badgeClasses }}">
                            {{ $entry['level'] }}
                        </span>

                        <div class="flex-1 min-w-0">
                            {{-- Message preview (first line) --}}
                            <p class="text-sm text-gray-900 truncate font-mono leading-relaxed">
                                {{ \Illuminate\Support\Str::limit(strtok($entry['message'], "\n"), 150) }}
                            </p>
                            <p class="text-[11px] text-[var(--text-muted)] mt-1.5 font-mono flex items-center gap-2">
                                <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $entry['timestamp'] }}
                                <span class="text-gray-300">·</span>
                                {{ $entry['environment'] }}
                            </p>

                            {{-- Expandable detail --}}
                            <div class="log-detail hidden mt-3">
                                <pre class="text-xs bg-gray-900 text-gray-100 rounded-xl p-4 overflow-x-auto max-h-72 whitespace-pre-wrap break-words font-mono leading-relaxed">{{ $entry['message'] }}</pre>
                            </div>
                        </div>

                        {{-- Expand icon --}}
                        <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Bottom Pagination --}}
        @if($totalPages > 1)
            <div class="px-6 py-4 border-t border-[var(--border-default)] bg-gray-50 flex items-center justify-between text-xs">
                <span class="text-[var(--text-muted)] font-medium">
                    Showing {{ (($page - 1) * $perPage) + 1 }}–{{ min($page * $perPage, $totalEntries) }} of {{ number_format($totalEntries) }} entries
                </span>
                <div class="flex items-center gap-2">
                    @if($page > 1)
                        <a href="{{ route('admin.logs.index', array_merge(request()->query(), ['page' => $page - 1])) }}"
                           class="btn btn-outline text-xs px-2.5 py-1 font-semibold text-gray-600 hover:bg-gray-100 border-gray-300">&laquo; Prev</a>
                    @endif
                    <span class="text-[var(--text-muted)] font-medium">Page {{ $page }}/{{ $totalPages }}</span>
                    @if($page < $totalPages)
                        <a href="{{ route('admin.logs.index', array_merge(request()->query(), ['page' => $page + 1])) }}"
                           class="btn btn-outline text-xs px-2.5 py-1 font-semibold text-gray-600 hover:bg-gray-100 border-gray-300">Next &raquo;</a>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
