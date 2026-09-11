@extends('layouts.admin')

@section('title', 'Data & Score Reset Center')

@push('styles')
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(14px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.4); }
        50%      { box-shadow: 0 0 0 12px rgba(220, 38, 38, 0); }
    }
    @keyframes greenGlow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.35); }
        50%      { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    }

    .anim-1 { animation: fadeUp .35s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .anim-2 { animation: fadeUp .35s .08s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .anim-3 { animation: fadeUp .35s .16s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .anim-4 { animation: fadeUp .35s .24s cubic-bezier(0.16, 1, 0.3, 1) both; }

    .reset-container {
        max-width: 76rem;
        margin: 0 auto;
        padding-bottom: 4rem;
    }

    /* Hero Banner */
    .reset-hero {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid var(--border-default, #e2e8f0);
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }
    .reset-hero::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    /* Stat Cards */
    .metric-card {
        background: #ffffff;
        border: 1px solid var(--border-default, #e2e8f0);
        border-radius: 1rem;
        padding: 1.25rem;
        transition: all .2s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }
    .metric-card:hover {
        transform: translateY(-2px);
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.08);
    }

    /* Category Cards */
    .reset-card {
        background: #ffffff;
        border: 1px solid var(--border-default, #e2e8f0);
        border-radius: 1.15rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: all .22s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }
    .reset-card:hover {
        transform: translateY(-3px);
        border-color: #94a3b8;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
    }

    .reset-card-head {
        padding: 1.15rem 1.25rem 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border-default, #f1f5f9);
    }

    .reset-card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        flex: 1;
        gap: 1.25rem;
    }

    .btn-action-reset {
        width: 100%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        padding: .65rem 1rem;
        border-radius: .75rem;
        font-size: .8125rem;
        font-weight: 700;
        transition: all .18s ease;
        cursor: pointer;
        border: 1px solid transparent;
    }
    .btn-action-reset.btn-danger {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fecaca;
    }
    .btn-action-reset.btn-danger:hover:not(:disabled) {
        background: #fee2e2;
        color: #991b1b;
        border-color: #fca5a5;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.15);
    }
    .btn-action-reset.btn-amber {
        background: #fffbeb;
        color: #b45309;
        border-color: #fde68a;
    }
    .btn-action-reset.btn-amber:hover:not(:disabled) {
        background: #fef3c7;
        color: #92400e;
        border-color: #fcd34d;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.15);
    }
    .btn-action-reset:disabled {
        opacity: .5;
        cursor: not-allowed;
        background: #f8fafc;
        color: #94a3b8;
        border-color: #e2e8f0;
    }

    /* Master Reset / Danger Zone */
    .master-danger-panel {
        background: linear-gradient(135deg, #fff5f5 0%, #fff1f2 50%, #ffffff 100%);
        border: 1.5px solid #fca5a5;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 12px 36px -8px rgba(225, 29, 72, 0.12);
    }
    .master-danger-header {
        padding: 1.5rem 1.75rem;
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .master-input {
        width: 100%;
        background: #ffffff;
        border: 2px solid #cbd5e1;
        border-radius: .75rem;
        padding: .85rem 1.15rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: .95rem;
        font-weight: 700;
        color: #0f172a;
        transition: all .2s;
    }
    .master-input:focus {
        border-color: #dc2626;
        outline: none;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.15);
    }

    .btn-master-submit {
        width: 100%;
        padding: .9rem 1.5rem;
        border-radius: .75rem;
        font-size: .95rem;
        font-weight: 800;
        letter-spacing: .02em;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .6rem;
        transition: all .22s;
        border: none;
    }
    .btn-master-submit.locked {
        background: #f1f5f9;
        color: #94a3b8;
        border: 1.5px solid #e2e8f0;
        cursor: not-allowed;
    }
    .btn-master-submit.unlocked {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        color: #ffffff;
        cursor: pointer;
        box-shadow: 0 8px 24px -4px rgba(220, 38, 38, 0.45);
        animation: pulseGlow 2s infinite;
    }
    .btn-master-submit.unlocked:hover {
        background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        transform: translateY(-1px);
        box-shadow: 0 12px 30px -4px rgba(220, 38, 38, 0.55);
    }

    /* Modal */
    .reset-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity .2s ease;
    }
    .reset-modal-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }
    .reset-modal-box {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        border-radius: 1.25rem;
        max-width: 28rem;
        width: 100%;
        overflow: hidden;
        transform: scale(.95);
        transition: transform .22s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    .reset-modal-overlay.active .reset-modal-box {
        transform: scale(1);
    }
</style>
@endpush

@section('content')
<div class="reset-container space-y-7">

    {{-- Hero Section --}}
    <div class="reset-hero anim-1">
        <div class="flex items-start gap-4">
            <div class="w-13 h-13 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center shrink-0 shadow-sm p-3">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        Admin Data Management
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                        Irreversible Actions
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Score Data Reset Center</h1>
                <p class="text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed">
                    Manually purge specific scoring categories, contestants, or judges — or execute an audited master clean sweep to prepare the system for a fresh pageant event.
                </p>
            </div>
        </div>

        <div class="hidden lg:flex flex-col items-end gap-2.5 shrink-0">
            <a href="{{ route('admin.categories.management') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 hover:border-slate-400 transition-all shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Category Management
            </a>
            <span class="text-[11px] text-slate-500 font-medium">Operator: <strong class="text-slate-700">{{ auth()->user()->name }}</strong></span>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
    <div class="anim-1 flex items-start gap-3 bg-emerald-50 border border-emerald-300 rounded-xl px-4 py-3.5 shadow-xs">
        <div class="p-1 rounded-lg bg-emerald-600 text-white shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-emerald-950">Action Executed Successfully</p>
            <p class="text-xs font-medium text-emerald-800 mt-0.5">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="anim-1 flex items-start gap-3 bg-rose-50 border border-rose-300 rounded-xl px-4 py-3.5 shadow-xs">
        <div class="p-1 rounded-lg bg-rose-600 text-white shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-rose-950">System Notice</p>
            <p class="text-xs font-medium text-rose-800 mt-0.5">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    {{-- Summary Metric Strip --}}
    <div class="anim-2 space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">System Record Metrics</h2>
            </div>
            <span class="text-xs text-slate-500 font-medium">Real-time counts across active tables</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Scores --}}
            <div class="metric-card border-l-4 border-emerald-500 bg-gradient-to-br from-white to-emerald-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Total Scores</span>
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">📊</span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-black text-slate-900">{{ number_format($totalScores) }}</div>
                    <span class="text-xs text-slate-500 mt-0.5 block">Across all 6 criteria</span>
                </div>
            </div>

            {{-- Candidates Count --}}
            <div class="metric-card border-l-4 border-blue-500 bg-gradient-to-br from-white to-blue-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Candidates</span>
                    <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm">🌟</span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-black text-slate-900">{{ number_format($candidateCount ?? 0) }}</div>
                    <span class="text-xs text-slate-500 mt-0.5 block">Enrolled contestants</span>
                </div>
            </div>

            {{-- Judges Count --}}
            <div class="metric-card border-l-4 border-indigo-500 bg-gradient-to-br from-white to-indigo-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-700">Judges Panel</span>
                    <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-sm">⚖️</span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-black text-slate-900">{{ number_format($judgeCount ?? 0) }}</div>
                    <span class="text-xs text-slate-500 mt-0.5 block">Active evaluators</span>
                </div>
            </div>

            {{-- Active Categories --}}
            <div class="metric-card border-l-4 border-amber-500 bg-gradient-to-br from-white to-amber-50/20">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-700">Scoring Stages</span>
                    <span class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-sm">🏆</span>
                </div>
                <div class="mt-2">
                    <div class="text-3xl font-black text-slate-900">{{ count($counts) }}</div>
                    <span class="text-xs text-slate-500 mt-0.5 block">Criteria categories</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 1: Manual Reset Data for Judges & Candidates --}}
    <div class="anim-2 space-y-3.5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                <h2 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">Manual Roster &amp; Entity Reset</h2>
            </div>
            <p class="text-xs text-slate-500 font-medium">Selectively purge participant lists and their corresponding scores</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Candidate Roster Reset Card --}}
            <div class="reset-card border-t-4 border-blue-500">
                <div class="reset-card-head bg-blue-50/40">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-lg shadow-2xs font-bold">
                            👤
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Contestants &amp; Candidates Roster</h3>
                            <span class="text-[11px] text-blue-700 font-semibold">Roster &amp; Profile Records</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-blue-100 text-blue-800">
                        {{ number_format($candidateCount ?? 0) }} Enrolled
                    </span>
                </div>
                <div class="reset-card-body">
                    <div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Purges all registered candidates, profile data, contestant numbers, and candidate photo references. 
                            Dependent scoring records across all categories will also be wiped to maintain relational integrity.
                        </p>
                        <div class="mt-3 p-2.5 rounded-lg bg-blue-50/60 border border-blue-100 text-[11px] text-blue-900 font-medium flex items-center gap-2">
                            <span class="text-blue-600 font-bold">ℹ️ Impact:</span>
                            <span>Deletes {{ $candidateCount ?? 0 }} candidate(s) and associated votes.</span>
                        </div>
                    </div>

                    <button type="button"
                            onclick="openManualModal('candidates', 'Candidate Roster', {{ $candidateCount ?? 0 }}, 'CANDIDATES', 'This will delete all contestants and clear their scores.')"
                            class="btn-action-reset btn-amber"
                            @if(($candidateCount ?? 0) === 0) disabled title="No candidates in database" @endif>
                        <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ ($candidateCount ?? 0) === 0 ? 'No Candidates Recorded' : 'Reset Candidates Roster' }}
                    </button>
                </div>
            </div>

            {{-- Judges Panel Reset Card --}}
            <div class="reset-card border-t-4 border-indigo-500">
                <div class="reset-card-head bg-indigo-50/40">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg shadow-2xs font-bold">
                            ⚖️
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">Judges Evaluator Panel</h3>
                            <span class="text-[11px] text-indigo-700 font-semibold">Judge User Accounts</span>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 rounded-lg text-xs font-extrabold bg-indigo-100 text-indigo-800">
                        {{ number_format($judgeCount ?? 0) }} Evaluators
                    </span>
                </div>
                <div class="reset-card-body">
                    <div>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Deletes all registered judge evaluator user accounts (leaving administrator accounts intact). 
                            All scores submitted by judges will be cleared so a completely fresh panel can be assigned.
                        </p>
                        <div class="mt-3 p-2.5 rounded-lg bg-indigo-50/60 border border-indigo-100 text-[11px] text-indigo-900 font-medium flex items-center gap-2">
                            <span class="text-indigo-600 font-bold">ℹ️ Impact:</span>
                            <span>Deletes {{ $judgeCount ?? 0 }} judge account(s) and their submitted score sheets.</span>
                        </div>
                    </div>

                    <button type="button"
                            onclick="openManualModal('judges', 'Judges Panel', {{ $judgeCount ?? 0 }}, 'JUDGES', 'This will remove all judge evaluator accounts and all submitted score entries.')"
                            class="btn-action-reset btn-amber"
                            @if(($judgeCount ?? 0) === 0) disabled title="No judges in database" @endif>
                        <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ ($judgeCount ?? 0) === 0 ? 'No Judges Recorded' : 'Reset Judges Panel' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: Category-by-Category Selective Reset --}}
    @php
    $categoryMeta = [
        'production'         => ['label' => 'Production Stage',       'icon' => '🎭', 'badge' => 'Pre-Judging'],
        'fitness'            => ['label' => 'Fitness & Physique',     'icon' => '💪', 'badge' => 'Pre-Judging'],
        'traditional_attire' => ['label' => 'Traditional Attire',     'icon' => '👘', 'badge' => 'Pre-Judging'],
        'indigenous_attire'  => ['label' => 'Indigenous Attire',      'icon' => '🪬', 'badge' => 'Pre-Judging'],
        'qa'                 => ['label' => 'Final Question & Answer', 'icon' => '🎤', 'badge' => 'Final Stage'],
        'custom'             => ['label' => 'Custom Categories',      'icon' => '✨', 'badge' => 'Custom Criteria'],
    ];
    @endphp

    <div class="anim-3 space-y-3.5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                <h2 class="text-xs font-extrabold text-slate-800 uppercase tracking-widest">Selective Category Score Reset</h2>
            </div>
            <p class="text-xs text-slate-500 font-medium">Wipes scores for one individual category without affecting any other</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($counts as $key => $count)
            @php $m = $categoryMeta[$key] ?? ['label' => ucfirst($key), 'icon' => '📋', 'badge' => 'Category']; @endphp
            <div class="reset-card">
                <div class="reset-card-head">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center text-base border border-slate-200">
                            {{ $m['icon'] }}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm leading-tight">{{ $m['label'] }}</h3>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $m['badge'] }}</span>
                        </div>
                    </div>
                    <span class="px-2 py-0.5 rounded-md text-[11px] font-mono font-bold {{ $count > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500' }}">
                        {{ number_format($count) }}
                    </span>
                </div>

                <div class="reset-card-body">
                    <div class="space-y-1">
                        <div class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($count) }}</div>
                        <p class="text-xs text-slate-500">
                            {{ $count === 1 ? '1 active score entry' : number_format($count) . ' active score entries' }}
                        </p>
                    </div>

                    <button type="button"
                            onclick="openManualModal('{{ $key }}', '{{ $m['label'] }}', {{ $count }}, '{{ strtoupper(str_replace([' ', '&', '-', '_'], '', $m['label'])) }}', 'All submitted scores for this category will be deleted.')"
                            class="btn-action-reset btn-danger"
                            @if($count === 0) disabled title="No score records to reset" @endif>
                        <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ $count === 0 ? 'No Data Recorded' : 'Reset ' . $m['label'] }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- SECTION 3: Master Reset / Danger Zone --}}
    <div class="anim-4 master-danger-panel">
        <div class="master-danger-header">
            <div class="flex items-center gap-3.5">
                <div class="w-11 h-11 rounded-xl bg-white/15 border border-white/25 flex items-center justify-center text-xl shrink-0 shadow-xs">
                    ⚠️
                </div>
                <div>
                    <h2 class="text-white font-extrabold text-base tracking-tight leading-tight">Master Reset &amp; Multi-Scope Purge</h2>
                    <p class="text-rose-100 text-xs mt-0.5">Wipe all scores or run a complete clean sweep of the entire judging database</p>
                </div>
            </div>
            <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-[11px] font-extrabold bg-black/25 text-white border border-white/20 uppercase tracking-wider">
                Full Security Audit
            </span>
        </div>

        <div class="p-6 sm:p-7 space-y-6 bg-white/70">
            {{-- Warning Banner --}}
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-950 text-xs space-y-2">
                <div class="flex items-center gap-2 font-bold text-rose-900 text-sm">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    CRITICAL OPERATION SAFEGUARDS
                </div>
                <p class="leading-relaxed">
                    This operation is <strong>strictly irreversible</strong>. Deleted records cannot be restored. Every purge is cryptographically logged to the system's Immutable Audit Ledger with your Admin Account ID, IP Address, and timestamp.
                </p>
            </div>

            {{-- Reset All Form --}}
            <form method="POST" action="{{ route('admin.categories.reset.confirm') }}" id="reset-all-form" onsubmit="return handleResetAllSubmit(event)" class="space-y-5">
                @csrf

                {{-- Scope Selector --}}
                <div class="space-y-2">
                    <label class="block text-xs font-extrabold text-slate-800 uppercase tracking-wider">
                        Select Purge Scope
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 cursor-pointer transition-all">
                            <input type="radio" name="scope" value="all" checked class="mt-1 text-rose-600 focus:ring-rose-500">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">Full Clean Sweep (All)</span>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">Wipes all scores, contestants, and judge accounts.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 cursor-pointer transition-all">
                            <input type="radio" name="scope" value="scores_only" class="mt-1 text-rose-600 focus:ring-rose-500">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">Scores Only</span>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">Clears all criteria scores; keeps candidates &amp; judges.</span>
                            </div>
                        </label>

                        <label class="flex items-start gap-3 p-3.5 rounded-xl border border-slate-200 bg-white hover:border-slate-300 cursor-pointer transition-all">
                            <input type="radio" name="scope" value="candidates" class="mt-1 text-rose-600 focus:ring-rose-500">
                            <div>
                                <span class="font-bold text-slate-900 text-xs block">Candidates + Scores</span>
                                <span class="text-[11px] text-slate-500 mt-0.5 block">Purges candidate roster and all associated marks.</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Confirmation Input --}}
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider" for="reset-all-input">
                        Type <code class="px-2 py-0.5 rounded bg-slate-900 text-white font-mono text-xs font-bold">RESET ALL DATA</code> to unlock
                    </label>
                    <input type="text"
                           id="reset-all-input"
                           name="confirmation"
                           placeholder="Type RESET ALL DATA exactly as shown..."
                           autocomplete="off"
                           class="master-input"
                           oninput="checkResetAll()">
                </div>

                @error('confirmation')
                    <p class="text-xs text-rose-600 font-bold">{{ $message }}</p>
                @enderror

                <button type="submit" id="reset-all-btn" disabled class="btn-master-submit locked">
                    <svg class="w-5 h-5 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span id="reset-all-label">Locked — Type Confirmation to Proceed</span>
                </button>
            </form>
        </div>
    </div>

</div>

{{-- Unified Confirmation Modal --}}
<div class="reset-modal-overlay" id="unified-modal" role="dialog" aria-modal="true">
    <div class="reset-modal-box">
        <div class="p-5 bg-gradient-to-r from-rose-600 to-rose-700 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center font-bold">
                    ⚠️
                </div>
                <div>
                    <h3 class="font-bold text-sm" id="modal-target-title">Confirm Reset</h3>
                    <p class="text-xs text-rose-100 mt-0.5" id="modal-target-sub">Action cannot be reversed</p>
                </div>
            </div>
            <button type="button" onclick="closeManualModal()" class="text-white/80 hover:text-white text-lg font-bold">&times;</button>
        </div>

        <div class="p-6 space-y-4">
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-900 leading-relaxed" id="modal-impact-text">
                All records for this item will be permanently wiped.
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider" for="modal-typed-confirm">
                    Type <code class="px-1.5 py-0.5 rounded bg-slate-100 text-rose-700 font-mono font-bold" id="modal-code-hint"></code> to confirm:
                </label>
                <input type="text"
                       id="modal-typed-confirm"
                       class="w-full px-3.5 py-2.5 rounded-xl border-2 border-slate-200 font-mono text-sm focus:border-rose-600 focus:outline-none"
                       placeholder="Type confirmation text..."
                       autocomplete="off"
                       oninput="validateModalInput()">
            </div>

            <div class="pt-2 space-y-2">
                <button type="button"
                        id="modal-submit-btn"
                        disabled
                        onclick="executeModalReset()"
                        class="w-full py-2.5 px-4 rounded-xl font-bold text-sm bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed transition-all">
                    Confirm &amp; Delete Records
                </button>
                <button type="button"
                        onclick="closeManualModal()"
                        class="w-full py-2 px-4 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                    Cancel — Keep Data
                </button>
            </div>

            <form id="action-reset-form" method="POST" action="" style="display:none">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const RESET_ALL_CODE = 'RESET ALL DATA';

function checkResetAll() {
    const input = document.getElementById('reset-all-input');
    const btn   = document.getElementById('reset-all-btn');
    const label = document.getElementById('reset-all-label');
    const match = input.value.trim() === RESET_ALL_CODE;

    btn.disabled  = !match;
    btn.className = 'btn-master-submit ' + (match ? 'unlocked' : 'locked');
    label.textContent = match ? '⚡ EXECUTE MASTER PURGE NOW' : 'Locked — Type Confirmation to Proceed';
}

function handleResetAllSubmit(e) {
    if (document.getElementById('reset-all-input').value.trim() !== RESET_ALL_CODE) {
        e.preventDefault();
        return false;
    }
    const scope = document.querySelector('input[name="scope"]:checked')?.value || 'all';
    return confirm('FINAL SYSTEM WARNING:\nYou are about to purge records with scope [' + scope.toUpperCase() + '].\nThis action is permanent and cannot be undone.\n\nAre you absolutely sure?');
}

let _targetKey = '', _requiredMatch = '';

function openManualModal(key, title, count, requiredWord, impact) {
    _targetKey = key;
    _requiredMatch = requiredWord;

    document.getElementById('modal-target-title').textContent = 'Reset ' + title;
    document.getElementById('modal-target-sub').textContent   = count + ' record(s) currently stored';
    document.getElementById('modal-impact-text').textContent  = impact;
    document.getElementById('modal-code-hint').textContent    = requiredWord;

    const input = document.getElementById('modal-typed-confirm');
    input.value = '';
    input.placeholder = 'Type ' + requiredWord + '...';

    const btn = document.getElementById('modal-submit-btn');
    btn.disabled = true;
    btn.className = 'w-full py-2.5 px-4 rounded-xl font-bold text-sm bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed transition-all';
    btn.textContent = 'Confirm & Reset ' + title;

    document.getElementById('unified-modal').classList.add('active');
    setTimeout(() => input.focus(), 150);
}

function closeManualModal() {
    document.getElementById('unified-modal').classList.remove('active');
}

function validateModalInput() {
    const typed = document.getElementById('modal-typed-confirm').value.trim().toUpperCase();
    const btn   = document.getElementById('modal-submit-btn');
    const match = typed === _requiredMatch.toUpperCase();

    btn.disabled = !match;
    if (match) {
        btn.className = 'w-full py-2.5 px-4 rounded-xl font-bold text-sm bg-rose-600 hover:bg-rose-700 text-white shadow-md cursor-pointer transition-all';
    } else {
        btn.className = 'w-full py-2.5 px-4 rounded-xl font-bold text-sm bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed transition-all';
    }
}

function executeModalReset() {
    const form = document.getElementById('action-reset-form');
    form.action = '/admin/categories/reset/' + _targetKey;
    form.submit();
}

document.getElementById('unified-modal').addEventListener('click', function(e) {
    if (e.target === this) closeManualModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeManualModal();
});
</script>
@endpush
