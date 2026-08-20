@extends('layouts.admin')

@section('title', 'Cache Management')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
            <span>Settings</span>
            <span>/</span>
            <span class="text-[var(--green-700)] font-semibold">Cache Management</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            System Cache &amp; Performance Management
        </h1>
        <p class="page-subtitle">Inspect system cache states, view storage usage, optimize performance, and safely clear application caches.</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <form method="POST" action="{{ route('admin.cache.clear-all') }}" onsubmit="return confirm('Are you sure you want to clear all system caches?');">
            @csrf
            <button type="submit" class="btn btn-red flex items-center gap-2 shadow-sm text-xs font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Clear All Caches
            </button>
        </form>

        <form method="POST" action="{{ route('admin.cache.optimize') }}">
            @csrf
            <button type="submit" class="btn btn-green flex items-center gap-2 shadow-sm text-xs font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                {{ $isOptimized ? 'Re-Optimize Application' : 'Optimize Application' }}
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
    <a href="{{ route('admin.cache.index') }}"
       class="px-4 py-2 rounded-xl text-sm font-bold bg-[var(--green-100)] text-[var(--green-800)] border border-[var(--green-300)] flex items-center gap-2 shadow-sm whitespace-nowrap">
        <span class="w-2.5 h-2.5 rounded-full bg-[var(--green-600)]"></span>
        Cache Management
    </a>
    <a href="{{ route('admin.logs.index') }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
        <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
        Logs Management
    </a>
</div>

{{-- Optimization Status Hero Banner --}}
<div class="mb-6 p-5 rounded-2xl border {{ $isOptimized ? 'border-emerald-200 bg-emerald-50/70 text-emerald-950' : 'border-amber-200 bg-amber-50/70 text-amber-950' }} shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div class="flex items-start gap-3.5">
        <div class="p-2.5 rounded-xl {{ $isOptimized ? 'bg-emerald-600 text-white' : 'bg-amber-500 text-white' }} shadow-sm mt-0.5">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
        </div>
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h2 class="text-base font-bold">{{ $isOptimized ? 'Application is in Optimized Mode' : 'Application is in Dynamic (Dev) Mode' }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isOptimized ? 'bg-emerald-200 text-emerald-900' : 'bg-amber-200 text-amber-900' }}">
                    {{ $isOptimized ? 'Production Cached' : 'Dynamic / Non-Cached' }}
                </span>
            </div>
            <p class="text-xs {{ $isOptimized ? 'text-emerald-800' : 'text-amber-800' }} mt-1 max-w-2xl">
                @if($isOptimized)
                    Your configuration settings, routes, and Blade templates are compiled and cached into bootstrap files for maximum execution speed and zero overhead.
                @else
                    Configuration files and routes are parsed dynamically per request. Click "Optimize Application" to compile routes, config, and templates for optimal performance.
                @endif
            </p>
        </div>
    </div>
    <div class="flex items-center gap-2 flex-shrink-0 w-full md:w-auto justify-end">
        <form method="POST" action="{{ route('admin.cache.optimize') }}">
            @csrf
            <button type="submit" class="btn {{ $isOptimized ? 'btn-outline border-emerald-400 bg-white text-emerald-800 hover:bg-emerald-100' : 'btn-green' }} text-xs font-bold py-2 px-4 flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                {{ $isOptimized ? 'Re-Optimize Now' : 'Optimize Application' }}
            </button>
        </form>
        @if($isOptimized)
            <form method="POST" action="{{ route('admin.cache.clear-all') }}" onsubmit="return confirm('Clear optimization and switch back to dynamic development mode?');">
                @csrf
                <button type="submit" class="btn btn-outline border-gray-300 bg-white text-gray-700 hover:bg-gray-50 text-xs font-semibold py-2 px-3">
                    Switch to Dev Mode
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Overview Metric Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Application Cache Driver Card --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Cache Driver</span>
            <span class="p-2 rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/>
                </svg>
            </span>
        </div>
        <div>
            <div class="text-2xl font-black text-gray-900 capitalize">{{ $cacheDriver }}</div>
            <div class="text-xs text-[var(--text-muted)] mt-1 flex items-center justify-between">
                <span>{{ $cacheDriver === 'database' ? ($dbCacheCount . ' cached rows') : ($cacheDirStats['count'] . ' files (' . $cacheDirStats['size'] . ')') }}</span>
                <span class="text-emerald-700 font-semibold">Active</span>
            </div>
        </div>
    </div>

    {{-- Route Cache Status --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Route Cache</span>
            <span class="p-2 rounded-lg {{ $routesCached ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
            </span>
        </div>
        <div>
            <div class="text-2xl font-black {{ $routesCached ? 'text-emerald-700' : 'text-amber-700' }}">
                {{ $routesCached ? 'Cached' : 'Not Cached' }}
            </div>
            <div class="text-xs text-[var(--text-muted)] mt-1">
                {{ $routesCached ? 'Routes compiled for speed' : 'Dynamic route registration' }}
            </div>
        </div>
    </div>

    {{-- Configuration Cache Status --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Config Cache</span>
            <span class="p-2 rounded-lg {{ $configCached ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                </svg>
            </span>
        </div>
        <div>
            <div class="text-2xl font-black {{ $configCached ? 'text-emerald-700' : 'text-amber-700' }}">
                {{ $configCached ? 'Cached' : 'Not Cached' }}
            </div>
            <div class="text-xs text-[var(--text-muted)] mt-1">
                {{ $configCached ? 'Settings cached into single file' : 'Reading config files per request' }}
            </div>
        </div>
    </div>

    {{-- Compiled Views Storage --}}
    <div class="panel p-5 border border-[var(--border-default)] rounded-xl bg-white shadow-sm flex flex-col justify-between">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Compiled Views</span>
            <span class="p-2 rounded-lg bg-blue-50 text-blue-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </span>
        </div>
        <div>
            <div class="text-2xl font-black text-gray-900">{{ $viewsDirStats['count'] }} <span class="text-sm font-normal text-[var(--text-muted)]">templates</span></div>
            <div class="text-xs text-[var(--text-muted)] mt-1 flex items-center justify-between">
                <span>Disk: {{ $viewsDirStats['size'] }}</span>
                <span class="text-blue-600 font-semibold">Blade Cache</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- Cache Actions & Operations Table --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="panel bg-white border border-[var(--border-default)] rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-[var(--border-default)] bg-gray-50 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Cache Controls &amp; Optimization Operations</h2>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">Select an operation below to optimize or invalidate specific storage buffers.</p>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                {{-- Master Optimize Application --}}
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-emerald-50/30 hover:bg-emerald-50/60 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 rounded-xl bg-emerald-600 text-white mt-0.5 shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-gray-900">Optimize Application (Full Framework Cache)</h3>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $isOptimized ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $isOptimized ? 'Optimized' : 'Ready to Optimize' }}
                                </span>
                            </div>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                Caches configuration, routes, events, and compiles Blade views into single files for fastest production performance.
                            </p>
                            <span class="inline-block mt-1.5 text-[11px] px-2 py-0.5 bg-white border border-emerald-200 text-emerald-800 rounded font-medium">
                                Runs: config:cache, route:cache, event:cache, view:cache
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <form method="POST" action="{{ route('admin.cache.optimize') }}">
                            @csrf
                            <button type="submit" class="btn btn-green text-xs px-3.5 py-2 font-bold shadow-sm flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                {{ $isOptimized ? 'Re-Optimize' : 'Optimize' }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Application Data Cache --}}
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Application Data Cache</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                Clears transient data cached by application logic (queries, models, temporary values).
                            </p>
                            <span class="inline-block mt-1.5 text-[11px] px-2 py-0.5 bg-gray-100 text-gray-600 rounded font-mono">
                                Driver: {{ $cacheDriver }} {{ $cacheDriver === 'database' ? "({$dbCacheCount} entries)" : "({$cacheDirStats['size']})" }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.cache.clear-app') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline text-xs px-3 py-1.5 font-semibold text-emerald-700 hover:bg-emerald-50 border-emerald-300">
                                Flush App Cache
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Route Cache --}}
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 rounded-xl bg-blue-50 text-blue-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Route Cache</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                Flushes cached route definitions. Necessary if you added new routes or changed controller methods.
                            </p>
                            <span class="inline-block mt-1.5 text-[11px] px-2 py-0.5 {{ $routesCached ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }} rounded font-medium">
                                Status: {{ $routesCached ? 'Cached' : 'Not cached' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.cache.clear-route') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline text-xs px-3 py-1.5 font-semibold text-blue-700 hover:bg-blue-50 border-blue-300">
                                Clear Route Cache
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Configuration Cache --}}
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 rounded-xl bg-purple-50 text-purple-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Configuration Cache</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                Flushes cached environment and configuration values so fresh `.env` settings take effect immediately.
                            </p>
                            <span class="inline-block mt-1.5 text-[11px] px-2 py-0.5 {{ $configCached ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }} rounded font-medium">
                                Status: {{ $configCached ? 'Cached' : 'Not cached' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.cache.clear-config') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline text-xs px-3 py-1.5 font-semibold text-purple-700 hover:bg-purple-50 border-purple-300">
                                Clear Config Cache
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Blade Compiled Views --}}
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 rounded-xl bg-amber-50 text-amber-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Blade Compiled Views</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                Recompiles all Blade templates from disk so UI updates render without stale cached markup.
                            </p>
                            <span class="inline-block mt-1.5 text-[11px] px-2 py-0.5 bg-gray-100 text-gray-600 rounded font-mono">
                                Storage: {{ $viewsDirStats['size'] }} ({{ $viewsDirStats['count'] }} compiled files)
                            </span>
                        </div>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.cache.clear-view') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline text-xs px-3 py-1.5 font-semibold text-amber-700 hover:bg-amber-50 border-amber-300">
                                Clear Views Cache
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Application Log Files --}}
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-gray-50/50 transition-colors">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 rounded-xl bg-red-50 text-red-600 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Application Logs</h3>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                Cleans up `storage/logs/laravel.log` and archives to free up server disk space.
                            </p>
                            <span class="inline-block mt-1.5 text-[11px] px-2 py-0.5 bg-gray-100 text-gray-600 rounded font-mono">
                                Size: {{ $logsDirStats['size'] }} ({{ $logsDirStats['count'] }} log files)
                            </span>
                        </div>
                    </div>
                    <div>
                        <form method="POST" action="{{ route('admin.cache.clear-logs') }}" onsubmit="return confirm('Clear application logs?');">
                            @csrf
                            <button type="submit" class="btn btn-outline text-xs px-3 py-1.5 font-semibold text-red-700 hover:bg-red-50 border-red-300">
                                Clear System Logs
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- System Environment & OPcache Information --}}
    <div class="space-y-6">
        {{-- System Environment Details --}}
        <div class="panel bg-white border border-[var(--border-default)] rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                </svg>
                Environment &amp; Server
            </h3>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                    <span class="text-[var(--text-muted)]">PHP Version</span>
                    <span class="font-bold text-gray-900 font-mono">{{ $systemInfo['php_version'] }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                    <span class="text-[var(--text-muted)]">Laravel Version</span>
                    <span class="font-bold text-emerald-700 font-mono">{{ $systemInfo['laravel_version'] }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                    <span class="text-[var(--text-muted)]">Database Driver</span>
                    <span class="font-semibold text-gray-900 uppercase font-mono">{{ $dbConnection }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                    <span class="text-[var(--text-muted)]">Session Driver</span>
                    <span class="font-semibold text-gray-900 uppercase font-mono">{{ $sessionDriver }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                    <span class="text-[var(--text-muted)]">Active Sessions</span>
                    <span class="font-bold text-gray-900 font-mono">{{ $sessionDriver === 'database' ? $dbSessionsCount : $sessionsDirStats['count'] }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                    <span class="text-[var(--text-muted)]">Memory Limit</span>
                    <span class="font-mono text-gray-900 font-medium">{{ $systemInfo['memory_limit'] }}</span>
                </div>
                <div class="flex items-center justify-between py-1.5">
                    <span class="text-[var(--text-muted)]">Max Execution Time</span>
                    <span class="font-mono text-gray-900 font-medium">{{ $systemInfo['max_execution_time'] }}</span>
                </div>
            </div>
        </div>

        {{-- OPcache Status (if available) --}}
        <div class="panel bg-white border border-[var(--border-default)] rounded-2xl p-5 shadow-sm">
            <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                Zend OPcache
            </h3>

            @if($opcacheInfo && $opcacheInfo['enabled'])
                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                        <span class="text-[var(--text-muted)]">Status</span>
                        <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-semibold text-[11px]">Active</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                        <span class="text-[var(--text-muted)]">Hit Rate</span>
                        <span class="font-bold text-emerald-700 font-mono">{{ $opcacheInfo['hit_rate'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-b border-gray-100">
                        <span class="text-[var(--text-muted)]">Memory Used</span>
                        <span class="font-mono text-gray-900">{{ $opcacheInfo['memory_used'] }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5">
                        <span class="text-[var(--text-muted)]">Cached Scripts</span>
                        <span class="font-mono text-gray-900">{{ number_format($opcacheInfo['cached_scripts']) }}</span>
                    </div>
                </div>
            @else
                <div class="p-3 bg-gray-50 border border-gray-100 rounded-xl text-xs text-[var(--text-muted)] flex items-start gap-2">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>OPcache extension is not currently enabled on this PHP runtime.</span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
