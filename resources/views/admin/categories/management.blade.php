@extends('layouts.admin')

@section('content')
<div x-data="{ activeStage: 'preliminary', showAddModal: false, editModal: false, activeEdit: {}, expandedLocks: {} }" class="space-y-6">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="panel p-4 bg-emerald-50/70 border-emerald-200 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition>
            <span class="text-emerald-600 text-lg">✅</span>
            <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
            <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-700 text-lg cursor-pointer">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="panel p-4 bg-red-50/70 border-red-200 flex items-center gap-3" x-data="{ show: true }" x-show="show" x-transition>
            <span class="text-red-600 text-lg">❌</span>
            <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
            <button @click="show = false" class="ml-auto text-red-400 hover:text-red-700 text-lg cursor-pointer">&times;</button>
        </div>
    @endif
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="page-title text-2xl font-bold text-[var(--text-primary)]">Manage Judging Categories</h1>
                <span class="badge bg-green-100 text-green-800 border border-green-200">Admin Control</span>
            </div>
            <p class="page-subtitle text-sm text-[var(--text-muted)] mt-1">Configure pageant judging categories, percentage weights, and scoring criteria stages.</p>
        </div>

        <div class="flex items-center gap-3">
            <button @click="showAddModal = true" class="btn btn-green shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Category</span>
            </button>
        </div>
    </div>

    {{-- Stage Selector Tabs --}}
    <div class="flex border-b border-[var(--border-default)]">
        <button @click="activeStage = 'preliminary'" 
                :class="activeStage === 'preliminary' ? 'border-green-600 text-green-700 font-bold border-b-2' : 'text-[var(--text-muted)] font-medium'"
                class="px-6 py-3 text-sm flex items-center gap-2 transition-colors cursor-pointer">
            <span>✨ Preliminary Stage</span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800 font-mono font-bold">{{ $preliminaryTotal }}%</span>
        </button>

        <button @click="activeStage = 'final'" 
                :class="activeStage === 'final' ? 'border-green-600 text-green-700 font-bold border-b-2' : 'text-[var(--text-muted)] font-medium'"
                class="px-6 py-3 text-sm flex items-center gap-2 transition-colors cursor-pointer">
            <span>🏆 Final Stage</span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-800 font-mono font-bold">{{ $finalTotal }}%</span>
        </button>

        <button @click="activeStage = 'categories_table'" 
                :class="activeStage === 'categories_table' ? 'border-green-600 text-green-700 font-bold border-b-2' : 'text-[var(--text-muted)] font-medium'"
                class="px-6 py-3 text-sm flex items-center gap-2 transition-colors cursor-pointer">
            <span>🗄️ Categories DB Table</span>
            <span class="px-2 py-0.5 text-xs rounded-full bg-emerald-100 text-emerald-800 font-mono font-bold">{{ $dbCategories->count() }} Records</span>
        </button>
    </div>

    {{-- Preliminary Stage Panel --}}
    <div x-show="activeStage === 'preliminary'" class="space-y-6">
        {{-- Total Weight Status Banner --}}
        <div class="panel p-4 flex flex-col sm:flex-row items-center justify-between gap-4 {{ abs($preliminaryTotal - 100) < 0.01 ? 'bg-emerald-50/70 border-emerald-200' : 'bg-amber-50/70 border-amber-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg {{ abs($preliminaryTotal - 100) < 0.01 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ abs($preliminaryTotal - 100) < 0.01 ? '✓' : '⚠️' }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-[var(--text-primary)]">Preliminary Total Weight: <span class="font-mono text-base">{{ $preliminaryTotal }}%</span></h4>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">
                        {{ abs($preliminaryTotal - 100) < 0.01 ? 'Preliminary category weights equal exactly 100%.' : 'Total must equal 100% for balanced score tabulations.' }}
                    </p>
                </div>
            </div>
            <div class="w-full sm:w-48">
                <div class="progress-bar">
                    <div class="progress-fill {{ abs($preliminaryTotal - 100) < 0.01 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ min($preliminaryTotal, 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Hidden Form for Preliminary Percentages (prevents nested forms inside the table) --}}
        <form id="preliminaryPercentagesForm" action="{{ route('admin.categories.management.percentages') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="stage" value="preliminary">
        </form>

        {{-- Categories Table Panel --}}
        <div class="panel">
            <div class="panel-header">
                <h3 class="panel-title">Preliminary Judging Categories</h3>
                <button type="submit" form="preliminaryPercentagesForm" class="btn btn-green btn-sm">Save Percentages</button>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Category Name</th>
                            <th>Key Identifier</th>
                            <th>Voting Status</th>
                            <th>Percentage Weight</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($preliminarySettings as $setting)
                            @php
                                $catSlug = strtolower(str_replace('_', '-', $setting->key));
                                $catKey = strtolower(str_replace('-', '_', $setting->key));
                                $lockedCount = 0;
                                foreach($judges as $j) {
                                    if (!empty($submissionsMap[$j->id . '_' . $setting->key]) ||
                                        !empty($submissionsMap[$j->id . '_' . $catSlug]) ||
                                        !empty($submissionsMap[$j->id . '_' . $catKey])) {
                                        $lockedCount++;
                                    }
                                }
                                $totalJudges = $judges->count();
                            @endphp
                            <tr class="{{ !$setting->is_enabled ? 'opacity-60 bg-rose-50/20' : '' }}">
                                <td class="font-mono font-medium text-[var(--text-muted)]">#{{ $setting->sort_order }}</td>
                                <td>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-[var(--text-primary)]">{{ $setting->name }}</span>
                                        <button type="button" 
                                                @click="expandedLocks['{{ $setting->id }}'] = !expandedLocks['{{ $setting->id }}']" 
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-indigo-600 hover:text-indigo-800 text-left mt-0.5 cursor-pointer">
                                            <span>Judges: <strong>{{ $lockedCount }}/{{ $totalJudges }} locked</strong></span>
                                            <span class="text-[10px]" x-text="expandedLocks['{{ $setting->id }}'] ? '▲' : '▼'"></span>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info font-mono text-[11px]">{{ $setting->key }}</span>
                                </td>
                                <td>
                                    <div class="flex flex-col gap-1.5 items-start">
                                        @if($setting->is_enabled)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300" title="Category voting is globally enabled.">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>Voting Unlocked</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300" title="Category voting is globally locked.">
                                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                <span>Voting Locked</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <input type="number" 
                                               form="preliminaryPercentagesForm"
                                               name="percentages[{{ $setting->key }}]" 
                                               value="{{ (int) $setting->percentage }}" 
                                               min="0" max="100" 
                                               class="form-input w-24 font-mono font-bold text-center py-1">
                                        <span class="text-xs text-[var(--text-muted)] font-bold">%</span>
                                    </div>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Per-Judge Locks toggle drawer button --}}
                                        <button type="button" 
                                                @click="expandedLocks['{{ $setting->id }}'] = !expandedLocks['{{ $setting->id }}']" 
                                                class="btn btn-outline btn-sm font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer transition-colors"
                                                :class="expandedLocks['{{ $setting->id }}'] ? 'bg-indigo-100 text-indigo-800 border-indigo-300' : 'text-indigo-700 border-indigo-200 hover:bg-indigo-50'"
                                                title="Open judge lock management for {{ $setting->name }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            <span>Judges ({{ $lockedCount }}/{{ $totalJudges }})</span>
                                        </button>

                                        {{-- Lock / Unlock voting toggle button (Global) --}}
                                        <form action="{{ route('admin.categories.management.toggle', $setting->id) }}" method="POST">
                                            @csrf
                                            @if($setting->is_enabled)
                                                <button type="submit" 
                                                        class="btn btn-outline btn-sm text-rose-600 border-rose-300 hover:bg-rose-50 hover:border-rose-400 font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer" 
                                                        title="Globally lock judges from voting in {{ $setting->name }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    <span>Lock All</span>
                                                </button>
                                            @else
                                                <button type="submit" 
                                                        class="btn btn-outline btn-sm text-emerald-700 border-emerald-300 hover:bg-emerald-50 hover:border-emerald-400 font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer" 
                                                        title="Globally unlock judges to allow voting in {{ $setting->name }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                    <span>Unlock All</span>
                                                </button>
                                            @endif
                                        </form>
                                        <button type="button" 
                                                @click="editModal = true; activeEdit = { id: '{{ $setting->id }}', name: '{{ $setting->name }}', percentage: '{{ $setting->percentage }}', sort_order: '{{ $setting->sort_order }}' }"
                                                class="btn btn-outline btn-sm text-blue-600 border-blue-200 hover:bg-blue-50">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.categories.management.destroy', $setting->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete category {{ $setting->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Expandable Per-Judge Lock Management Drawer --}}
                            <tr x-show="expandedLocks['{{ $setting->id }}']" x-cloak class="bg-indigo-50/30 border-y border-indigo-100">
                                <td colspan="6" class="p-4 sm:p-5">
                                    <div class="rounded-xl border border-indigo-200/80 bg-white p-4 shadow-sm space-y-4">
                                        {{-- Header: Category Title + Simultaneous Controls --}}
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                                    ⚖️
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="text-sm font-bold text-[var(--text-primary)]">Per-Judge Lock Controls: {{ $setting->name }}</h4>
                                                        <span class="badge {{ $lockedCount === $totalJudges && $totalJudges > 0 ? 'bg-rose-100 text-rose-800' : ($lockedCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }} font-mono text-xs">
                                                            {{ $lockedCount }}/{{ $totalJudges }} Judges Locked
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-[var(--text-muted)] mt-0.5">Control scoring pad access individually per judge, or lock/unlock all judges simultaneously.</p>
                                                </div>
                                            </div>

                                            {{-- Simultaneous Controls --}}
                                            <div class="flex items-center gap-2">
                                                <form action="{{ route('admin.categories.management.lock-all-judges', $setting->id) }}" method="POST" onsubmit="return confirm('Lock scoring pad for ALL {{ $totalJudges }} judges in {{ $setting->name }}?');">
                                                    @csrf
                                                    <input type="hidden" name="action" value="lock">
                                                    <button type="submit" class="btn btn-sm btn-outline text-rose-700 border-rose-300 hover:bg-rose-50 flex items-center gap-1.5 font-semibold text-xs py-1.5 px-3 cursor-pointer shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                        <span>Lock All Judges</span>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.categories.management.lock-all-judges', $setting->id) }}" method="POST" onsubmit="return confirm('Unlock scoring pad for ALL {{ $totalJudges }} judges in {{ $setting->name }}?');">
                                                    @csrf
                                                    <input type="hidden" name="action" value="unlock">
                                                    <button type="submit" class="btn btn-sm btn-outline text-emerald-700 border-emerald-300 hover:bg-emerald-50 flex items-center gap-1.5 font-semibold text-xs py-1.5 px-3 cursor-pointer shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                        <span>Unlock All Judges</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Grid of Judges --}}
                                        @if($judges->isEmpty())
                                            <p class="text-xs text-slate-400 italic py-2">No evaluator judge accounts found.</p>
                                        @else
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                @foreach($judges as $judge)
                                                    @php
                                                        $isJudgeLocked = !empty($submissionsMap[$judge->id . '_' . $setting->key]) ||
                                                                         !empty($submissionsMap[$judge->id . '_' . $catSlug]) ||
                                                                         !empty($submissionsMap[$judge->id . '_' . $catKey]);
                                                    @endphp
                                                    <div class="p-3 rounded-lg border flex items-center justify-between gap-3 {{ $isJudgeLocked ? 'bg-rose-50/70 border-rose-200 shadow-2xs' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300' }}">
                                                        <div class="min-w-0">
                                                            <div class="flex items-center gap-1.5">
                                                                <span class="text-xs font-bold text-slate-800 truncate">
                                                                    Judge #{{ $judge->judge_number ?? $loop->iteration }}
                                                                </span>
                                                                @if($isJudgeLocked)
                                                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-300">
                                                                        Locked
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-300">
                                                                        Open
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <p class="text-[11px] text-slate-500 truncate" title="{{ $judge->name }}">{{ $judge->name }}</p>
                                                        </div>

                                                        <form action="{{ route('admin.categories.management.lock-judge', [$setting->id, $judge->id]) }}" method="POST">
                                                            @csrf
                                                            @if($isJudgeLocked)
                                                                <button type="submit" 
                                                                        class="btn btn-sm btn-outline text-emerald-700 border-emerald-300 hover:bg-emerald-100 px-2.5 py-1 text-xs font-bold flex items-center gap-1 shadow-2xs cursor-pointer"
                                                                        title="Click to unlock scoring pad for {{ $judge->name }}">
                                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                                    <span>Unlock</span>
                                                                </button>
                                                            @else
                                                                <button type="submit" 
                                                                        class="btn btn-sm btn-outline text-rose-700 border-rose-300 hover:bg-rose-100 px-2.5 py-1 text-xs font-bold flex items-center gap-1 shadow-2xs cursor-pointer"
                                                                        title="Click to lock scoring pad for {{ $judge->name }}">
                                                                    <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                                    <span>Lock</span>
                                                                </button>
                                                            @endif
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-[var(--text-muted)]">
                                    No preliminary categories configured.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Final Stage Panel --}}
    <div x-show="activeStage === 'final'" class="space-y-6">
        {{-- Total Weight Status Banner --}}
        <div class="panel p-4 flex flex-col sm:flex-row items-center justify-between gap-4 {{ abs($finalTotal - 100) < 0.01 ? 'bg-emerald-50/70 border-emerald-200' : 'bg-amber-50/70 border-amber-200' }}">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg {{ abs($finalTotal - 100) < 0.01 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ abs($finalTotal - 100) < 0.01 ? '✓' : '⚠️' }}
                </div>
                <div>
                    <h4 class="text-sm font-bold text-[var(--text-primary)]">Final Stage Total Weight: <span class="font-mono text-base">{{ $finalTotal }}%</span></h4>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">
                        {{ abs($finalTotal - 100) < 0.01 ? 'Final stage category weights equal exactly 100%.' : 'Total must equal 100% for final tabulation.' }}
                    </p>
                </div>
            </div>
            <div class="w-full sm:w-48">
                <div class="progress-bar">
                    <div class="progress-fill {{ abs($finalTotal - 100) < 0.01 ? 'bg-emerald-500' : 'bg-amber-500' }}" style="width: {{ min($finalTotal, 100) }}%"></div>
                </div>
            </div>
        </div>


        {{-- ══ Final Judging Categories (scoreable categories with per-judge locks) ══ --}}
        <div class="panel">
            <div class="panel-header">
                <div>
                    <h3 class="panel-title">🏆 Final Judging Categories</h3>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">Scoreable categories judges evaluate during the Final Stage (e.g. Q&amp;A). Control locking per judge.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Category Name</th>
                            <th>Key Identifier</th>
                            <th>Voting Status</th>
                            <th>Judge Lock Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($finalJudgingSettings as $setting)
                            @php
                                $catSlug = strtolower(str_replace('_', '-', $setting->key));
                                $catKey  = strtolower(str_replace('-', '_', $setting->key));
                                $lockedCount = 0;
                                foreach($judges as $j) {
                                    if (!empty($submissionsMap[$j->id . '_' . $setting->key]) ||
                                        !empty($submissionsMap[$j->id . '_' . $catSlug]) ||
                                        !empty($submissionsMap[$j->id . '_' . $catKey])) {
                                        $lockedCount++;
                                    }
                                }
                                $totalJudges = $judges->count();
                            @endphp
                            <tr class="{{ !$setting->is_enabled ? 'opacity-60 bg-rose-50/20' : '' }}">
                                <td class="font-mono font-medium text-[var(--text-muted)]">#{{ $setting->sort_order }}</td>
                                <td>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-[var(--text-primary)]">{{ $setting->name }}</span>
                                        <button type="button"
                                                @click="expandedLocks['fj_{{ $setting->id }}'] = !expandedLocks['fj_{{ $setting->id }}']"
                                                class="inline-flex items-center gap-1 text-[11px] font-medium text-indigo-600 hover:text-indigo-800 text-left mt-0.5 cursor-pointer">
                                            <span>Judges: <strong>{{ $lockedCount }}/{{ $totalJudges }} locked</strong></span>
                                            <span class="text-[10px]" x-text="expandedLocks['fj_{{ $setting->id }}'] ? '▲' : '▼'"></span>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info font-mono text-[11px]">{{ $setting->key }}</span>
                                </td>
                                <td>
                                    <div class="flex flex-col gap-1.5 items-start">
                                        @if($setting->is_enabled)
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300" title="Category voting is globally enabled.">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                <span>Voting Unlocked</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300" title="Category voting is globally locked.">
                                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                <span>Voting Locked</span>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $lockedCount === $totalJudges && $totalJudges > 0 ? 'bg-rose-100 text-rose-800' : ($lockedCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }} font-mono text-xs">
                                        {{ $lockedCount }}/{{ $totalJudges }} Locked
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Per-Judge Locks toggle drawer button --}}
                                        <button type="button"
                                                @click="expandedLocks['fj_{{ $setting->id }}'] = !expandedLocks['fj_{{ $setting->id }}']"
                                                class="btn btn-outline btn-sm font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer transition-colors"
                                                :class="expandedLocks['fj_{{ $setting->id }}'] ? 'bg-indigo-100 text-indigo-800 border-indigo-300' : 'text-indigo-700 border-indigo-200 hover:bg-indigo-50'"
                                                title="Open judge lock management for {{ $setting->name }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                            <span>Judges ({{ $lockedCount }}/{{ $totalJudges }})</span>
                                        </button>

                                        {{-- Global Lock / Unlock toggle --}}
                                        <form action="{{ route('admin.categories.management.toggle', $setting->id) }}" method="POST">
                                            @csrf
                                            @if($setting->is_enabled)
                                                <button type="submit"
                                                        class="btn btn-outline btn-sm text-rose-600 border-rose-300 hover:bg-rose-50 hover:border-rose-400 font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer"
                                                        title="Globally lock judges from voting in {{ $setting->name }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    <span>Lock All</span>
                                                </button>
                                            @else
                                                <button type="submit"
                                                        class="btn btn-outline btn-sm text-emerald-700 border-emerald-300 hover:bg-emerald-50 hover:border-emerald-400 font-semibold flex items-center gap-1.5 shadow-2xs cursor-pointer"
                                                        title="Globally unlock judges to allow voting in {{ $setting->name }}">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                    <span>Unlock All</span>
                                                </button>
                                            @endif
                                        </form>

                                        <button type="button" 
                                                @click="editModal = true; activeEdit = { id: '{{ $setting->id }}', name: '{{ $setting->name }}', percentage: '{{ $setting->percentage }}', sort_order: '{{ $setting->sort_order }}' }"
                                                class="btn btn-outline btn-sm text-blue-600 border-blue-200 hover:bg-blue-50"
                                                title="Edit category details">
                                            Edit
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            {{-- Expandable Per-Judge Lock Management Drawer (uses 'fj_' prefix to avoid ID collision) --}}
                            <tr x-show="expandedLocks['fj_{{ $setting->id }}']" x-cloak class="bg-indigo-50/30 border-y border-indigo-100">
                                <td colspan="6" class="p-4 sm:p-5">
                                    <div class="rounded-xl border border-indigo-200/80 bg-white p-4 shadow-sm space-y-4">
                                        {{-- Header + Simultaneous Controls --}}
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">
                                                    🏆
                                                </div>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="text-sm font-bold text-[var(--text-primary)]">Per-Judge Lock Controls: {{ $setting->name }}</h4>
                                                        <span class="badge {{ $lockedCount === $totalJudges && $totalJudges > 0 ? 'bg-rose-100 text-rose-800' : ($lockedCount > 0 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }} font-mono text-xs">
                                                            {{ $lockedCount }}/{{ $totalJudges }} Judges Locked
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-[var(--text-muted)] mt-0.5">Control scoring pad access individually per judge, or lock/unlock all judges simultaneously.</p>
                                                </div>
                                            </div>

                                            {{-- Simultaneous Controls --}}
                                            <div class="flex items-center gap-2">
                                                <form action="{{ route('admin.categories.management.lock-all-judges', $setting->id) }}" method="POST" onsubmit="return confirm('Lock scoring pad for ALL {{ $totalJudges }} judges in {{ $setting->name }}?');">
                                                    @csrf
                                                    <input type="hidden" name="action" value="lock">
                                                    <button type="submit" class="btn btn-sm btn-outline text-rose-700 border-rose-300 hover:bg-rose-50 flex items-center gap-1.5 font-semibold text-xs py-1.5 px-3 cursor-pointer shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                        <span>Lock All Judges</span>
                                                    </button>
                                                </form>

                                                <form action="{{ route('admin.categories.management.lock-all-judges', $setting->id) }}" method="POST" onsubmit="return confirm('Unlock scoring pad for ALL {{ $totalJudges }} judges in {{ $setting->name }}?');">
                                                    @csrf
                                                    <input type="hidden" name="action" value="unlock">
                                                    <button type="submit" class="btn btn-sm btn-outline text-emerald-700 border-emerald-300 hover:bg-emerald-50 flex items-center gap-1.5 font-semibold text-xs py-1.5 px-3 cursor-pointer shadow-2xs">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2z"/></svg>
                                                        <span>Unlock All Judges</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Grid of Judges --}}
                                        @if($judges->isEmpty())
                                            <p class="text-xs text-slate-400 italic py-2">No evaluator judge accounts found.</p>
                                        @else
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                @foreach($judges as $judge)
                                                    @php
                                                        $isJudgeLocked = !empty($submissionsMap[$judge->id . '_' . $setting->key]) ||
                                                                         !empty($submissionsMap[$judge->id . '_' . $catSlug]) ||
                                                                         !empty($submissionsMap[$judge->id . '_' . $catKey]);
                                                    @endphp
                                                    <div class="p-3 rounded-lg border flex items-center justify-between gap-3 {{ $isJudgeLocked ? 'bg-rose-50/70 border-rose-200 shadow-2xs' : 'bg-slate-50/70 border-slate-200 hover:border-slate-300' }}">
                                                        <div class="min-w-0">
                                                            <div class="flex items-center gap-1.5">
                                                                <span class="text-xs font-bold text-slate-800 truncate">
                                                                    Judge #{{ $judge->judge_number ?? $loop->iteration }}
                                                                </span>
                                                                @if($isJudgeLocked)
                                                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-100 text-rose-700 border border-rose-300">Locked</span>
                                                                @else
                                                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-300">Open</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-[11px] text-slate-500 truncate" title="{{ $judge->name }}">{{ $judge->name }}</p>
                                                        </div>

                                                        <form action="{{ route('admin.categories.management.lock-judge', [$setting->id, $judge->id]) }}" method="POST">
                                                            @csrf
                                                            @if($isJudgeLocked)
                                                                <button type="submit"
                                                                        class="btn btn-sm btn-outline text-emerald-700 border-emerald-300 hover:bg-emerald-100 px-2.5 py-1 text-xs font-bold flex items-center gap-1 shadow-2xs cursor-pointer"
                                                                        title="Click to unlock scoring pad for {{ $judge->name }}">
                                                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                                    <span>Unlock</span>
                                                                </button>
                                                            @else
                                                                <button type="submit"
                                                                        class="btn btn-sm btn-outline text-rose-700 border-rose-300 hover:bg-rose-100 px-2.5 py-1 text-xs font-bold flex items-center gap-1 shadow-2xs cursor-pointer"
                                                                        title="Click to lock scoring pad for {{ $judge->name }}">
                                                                    <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                                    <span>Lock</span>
                                                                </button>
                                                            @endif
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-[var(--text-muted)]">
                                    No final judging categories configured. Add categories with the <strong>Final</strong> stage.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Categories DB Table Panel --}}
    <div x-show="activeStage === 'categories_table'" class="panel space-y-4">
        <div class="panel-header">
            <div>
                <h3 class="panel-title">Categories Table (`categories`)</h3>
                <p class="text-xs text-[var(--text-muted)] mt-0.5">Live records in the `categories` database table.</p>
            </div>
            <span class="badge badge-success font-mono font-bold">{{ $dbCategories->count() }} Total Records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pageant ID</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Weight (%)</th>
                        <th>Sort Order</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dbCategories as $catItem)
                        <tr>
                            <td class="font-mono text-xs">#{{ $catItem->id }}</td>
                            <td class="font-mono text-xs">{{ $catItem->pageant_id }}</td>
                            <td class="font-bold text-[var(--text-primary)]">{{ $catItem->name }}</td>
                            <td class="text-xs text-[var(--text-muted)]">{{ $catItem->description ?? 'N/A' }}</td>
                            <td>
                                <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-green-100 text-green-800">
                                    {{ (int) $catItem->weight_percentage }}%
                                </span>
                            </td>
                            <td class="font-mono text-xs">#{{ $catItem->sort_order }}</td>
                            <td class="text-xs text-[var(--text-muted)]">{{ $catItem->created_at?->format('M d, Y H:i') ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-[var(--text-muted)]">
                                No records found in the `categories` database table.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Category Modal --}}
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" style="display: none;">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[var(--border-default)]">
            <h3 class="text-lg font-bold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                <span>➕</span> Add New Category
            </h3>
            
            <form action="{{ route('admin.categories.management.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" placeholder="e.g. Swimwear &amp; Fitness" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Judging Stage</label>
                    <input type="hidden" name="stage" value="preliminary">
                    <div class="flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 border border-green-200">
                        <svg class="w-4 h-4 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm font-semibold text-green-800">Pre-Judging (Preliminary)</span>
                        <span class="ml-auto text-[11px] text-green-500">Always default</span>
                    </div>
                </div>

                <div>
                    <label class="form-label">Initial Weight Percentage (%)</label>
                    <input type="number" name="percentage" min="0" max="100" value="10" class="form-input font-mono font-bold" required>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button type="button" @click="showAddModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-green">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Category Modal --}}
    <div x-show="editModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs" style="display: none;">
        <div class="bg-white rounded-2xl p-6 w-full max-w-md shadow-2xl border border-[var(--border-default)]">
            <h3 class="text-lg font-bold text-[var(--text-primary)] mb-4 flex items-center gap-2">
                <span>✏️</span> Edit Category
            </h3>
            
            <form :action="'{{ url('/admin/categories/management') }}/' + activeEdit.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" x-model="activeEdit.name" class="form-input" required>
                </div>

                <div>
                    <label class="form-label">Weight Percentage (%)</label>
                    <input type="number" name="percentage" min="0" max="100" x-model="activeEdit.percentage" class="form-input font-mono font-bold" required>
                </div>

                <div>
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" min="1" x-model="activeEdit.sort_order" class="form-input font-mono">
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button type="button" @click="editModal = false" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-green">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
