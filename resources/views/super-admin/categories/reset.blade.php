@extends('layouts.super-admin')

@section('title', 'Score Data Reset Center')

@push('styles')
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes lightgreenPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
        50%      { box-shadow: 0 0 0 14px rgba(16, 185, 129, 0); }
    }
    @keyframes subtleGlow {
        0%, 100% { border-color: rgba(52, 211, 153, 0.45); }
        50%      { border-color: rgba(16, 185, 129, 0.85); }
    }

    .anim-1 { animation: fadeUp .45s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .anim-2 { animation: fadeUp .45s .08s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .anim-3 { animation: fadeUp .45s .16s cubic-bezier(0.16, 1, 0.3, 1) both; }
    .anim-4 { animation: fadeUp .45s .24s cubic-bezier(0.16, 1, 0.3, 1) both; }

    .lightgreen-glow { animation: lightgreenPulse 2.2s ease-in-out infinite; }

    .reset-page {
        max-width: 62rem;
        margin: 0 auto;
        padding-bottom: 3.5rem;
    }

    /* Hero Banner */
    .hero-banner {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 40%, #ecfdf5 100%);
        border: 1.5px solid #86efac;
        border-radius: 1.5rem;
        padding: 2rem 2.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.5rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 12px 32px -8px rgba(16, 185, 129, 0.12), 0 4px 12px rgba(0, 0, 0, 0.03);
    }
    .hero-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse 70% 90% at 90% 20%, rgba(110, 231, 183, 0.25) 0%, transparent 70%);
        pointer-events: none;
    }
    .hero-left {
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        position: relative;
        z-index: 1;
    }
    .hero-icon {
        width: 3.75rem;
        height: 3.75rem;
        background: #ffffff;
        border: 1.5px solid #6ee7b7;
        border-radius: 1.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 8px 18px -4px rgba(16, 185, 129, 0.2);
    }
    .hero-title {
        font-size: 1.625rem;
        font-weight: 800;
        color: #064e3b;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }
    .hero-sub {
        font-size: .875rem;
        color: #047857;
        margin-top: .4rem;
        line-height: 1.55;
    }
    .hero-tag {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: #ffffff;
        color: #059669;
        border: 1px solid #a7f3d0;
        padding: .3rem .75rem;
        border-radius: 9999px;
        font-size: .75rem;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.08);
    }

    /* Stat Cards Strip */
    .stat-strip {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(9.5rem, 1fr));
        gap: .85rem;
    }
    .stat-chip {
        background: #ffffff;
        border: 1.5px solid #dcfce7;
        border-radius: 1.15rem;
        padding: 1rem 1.15rem;
        transition: all .22s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.04);
    }
    .stat-chip:hover {
        transform: translateY(-3px);
        border-color: #86efac;
        box-shadow: 0 10px 24px -4px rgba(16, 185, 129, 0.15);
    }
    .stat-chip .chip-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .5rem;
    }
    .stat-chip .chip-icon-box {
        width: 2.1rem;
        height: 2.1rem;
        border-radius: .7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        background: #f0fdf4;
        border: 1px solid #dcfce7;
    }
    .stat-chip .chip-label {
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #065f46;
    }
    .stat-chip .chip-count {
        font-size: 1.75rem;
        font-weight: 900;
        line-height: 1.1;
        color: #047857;
        letter-spacing: -0.02em;
    }
    .stat-chip .chip-sub {
        font-size: .7rem;
        color: #6b7280;
        margin-top: .2rem;
    }

    /* Total Banner */
    .stat-total {
        background: linear-gradient(135deg, #059669 0%, #10b981 50%, #047857 100%);
        color: #ffffff;
        border-radius: 1.25rem;
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1.25rem;
        box-shadow: 0 12px 28px -6px rgba(5, 150, 105, 0.35);
        border: 1px solid #6ee7b7;
    }

    /* Category Cards Grid */
    .cat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(17.5rem, 1fr));
        gap: 1rem;
    }
    .cat-card {
        background: #ffffff;
        border: 1.5px solid #dcfce7;
        border-radius: 1.25rem;
        overflow: hidden;
        transition: all .22s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.05);
    }
    .cat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px -6px rgba(16, 185, 129, 0.16);
        border-color: #86efac;
    }
    .cat-card-header {
        padding: 1.1rem 1.25rem .9rem;
        display: flex;
        align-items: center;
        gap: .85rem;
        background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
        border-bottom: 1px solid #dcfce7;
    }
    .cat-badge {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: .85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        background: #ffffff;
        border: 1.5px solid #bbf7d0;
        box-shadow: 0 3px 8px rgba(16, 185, 129, 0.1);
    }
    .cat-card-body {
        padding: 1rem 1.25rem 1.2rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        justify-content: space-between;
    }
    .cat-count {
        font-size: 2rem;
        font-weight: 900;
        color: #064e3b;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }
    .cat-records {
        font-size: .75rem;
        color: #059669;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-top: .15rem;
    }
    .cat-reset-btn {
        margin-top: 1.15rem;
        width: 100%;
        padding: .65rem 1.1rem;
        border-radius: .8rem;
        font-size: .825rem;
        font-weight: 700;
        border: 1.5px solid #a7f3d0;
        background: #f0fdf4;
        color: #047857;
        cursor: pointer;
        transition: all .2s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .45rem;
    }
    .cat-reset-btn:hover:not(:disabled) {
        background: #059669;
        color: #ffffff;
        border-color: #059669;
        box-shadow: 0 6px 18px rgba(5, 150, 105, 0.3);
        transform: translateY(-1px);
    }
    .cat-reset-btn:disabled {
        opacity: .5;
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
        transform: none;
    }

    /* Danger / Global Reset Zone */
    .danger-zone {
        border: 2px solid #86efac;
        border-radius: 1.5rem;
        overflow: hidden;
        background: #ffffff;
        box-shadow: 0 16px 36px -10px rgba(16, 185, 129, 0.15);
        animation: subtleGlow 4s ease-in-out infinite;
    }
    .danger-header {
        background: linear-gradient(135deg, #047857 0%, #059669 60%, #10b981 100%);
        padding: 1.25rem 1.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        color: #ffffff;
    }
    .danger-body {
        padding: 2rem;
        background: #ffffff;
    }
    .danger-warning-box {
        background: #f0fdf4;
        border: 1.5px solid #bbf7d0;
        border-radius: 1rem;
        padding: 1.15rem 1.35rem;
        margin-bottom: 1.5rem;
    }
    .danger-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: .55rem;
    }
    .danger-list li {
        display: flex;
        align-items: flex-start;
        gap: .6rem;
        font-size: .835rem;
        font-weight: 600;
        color: #064e3b;
    }
    .danger-list li svg {
        flex-shrink: 0;
        margin-top: .15rem;
        color: #059669;
    }
    .confirm-code {
        background: #dcfce7;
        border: 1px solid #86efac;
        padding: .2rem .55rem;
        border-radius: .4rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: .85rem;
        font-weight: 800;
        color: #065f46;
        letter-spacing: .05em;
    }
    .confirm-input {
        width: 100%;
        border: 2px solid #a7f3d0;
        border-radius: .85rem;
        padding: .85rem 1.15rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: .95rem;
        font-weight: 600;
        background: #fdfefe;
        color: #064e3b;
        outline: none;
        transition: all .2s;
    }
    .confirm-input:focus {
        border-color: #059669;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
    }
    .reset-all-btn {
        width: 100%;
        padding: 1.05rem 1.75rem;
        border-radius: .95rem;
        font-weight: 800;
        font-size: .95rem;
        border: none;
        cursor: pointer;
        transition: all .22s cubic-bezier(0.16, 1, 0.3, 1);
        margin-top: 1.15rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        letter-spacing: .01em;
    }
    .reset-all-btn.locked {
        background: #f1f5f9;
        color: #94a3b8;
        border: 1.5px solid #e2e8f0;
        cursor: not-allowed;
    }
    .reset-all-btn.unlocked {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: #ffffff;
        border: 1px solid #34d399;
        box-shadow: 0 10px 28px -4px rgba(16, 185, 129, 0.45);
        animation: lightgreenPulse 2s ease-in-out infinite;
    }
    .reset-all-btn.unlocked:hover {
        background: linear-gradient(135deg, #047857 0%, #059669 100%);
        transform: translateY(-1px);
        box-shadow: 0 14px 34px -4px rgba(16, 185, 129, 0.55);
    }
    .audit-note {
        margin-top: .85rem;
        text-align: center;
        font-size: .75rem;
        color: #059669;
        font-weight: 500;
    }

    /* Modal Styling */
    .modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(6, 78, 59, 0.45);
        backdrop-filter: blur(6px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1.25rem;
        opacity: 0;
        pointer-events: none;
        transition: opacity .22s ease;
    }
    .modal-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }
    .modal-box {
        background: #ffffff;
        border: 1.5px solid #86efac;
        border-radius: 1.5rem;
        max-width: 27rem;
        width: 100%;
        overflow: hidden;
        transform: scale(.95);
        transition: transform .25s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 25px 60px -10px rgba(6, 78, 59, 0.3);
    }
    .modal-overlay.open .modal-box {
        transform: scale(1);
    }
    .modal-head {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        padding: 1.25rem 1.6rem;
        display: flex;
        align-items: center;
        gap: .85rem;
        color: #ffffff;
    }
    .modal-body {
        padding: 1.75rem 1.6rem;
    }
    .modal-input {
        width: 100%;
        border: 2px solid #a7f3d0;
        border-radius: .75rem;
        padding: .75rem 1rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: .925rem;
        outline: none;
        transition: all .2s;
        background: #fdfefe;
        color: #064e3b;
    }
    .modal-input:focus {
        border-color: #059669;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.18);
    }
    .modal-confirm-btn {
        width: 100%;
        margin-top: 1rem;
        padding: .85rem 1.15rem;
        border-radius: .85rem;
        font-weight: 800;
        font-size: .9rem;
        border: none;
        cursor: pointer;
        transition: all .2s;
    }
    .modal-confirm-btn.locked {
        background: #f1f5f9;
        color: #94a3b8;
        border: 1px solid #e2e8f0;
        cursor: not-allowed;
    }
    .modal-confirm-btn.unlocked {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.35);
    }
    .modal-confirm-btn.unlocked:hover {
        background: linear-gradient(135deg, #047857 0%, #059669 100%);
        transform: translateY(-1px);
    }
    .modal-cancel-btn {
        width: 100%;
        margin-top: .6rem;
        padding: .65rem 1rem;
        background: transparent;
        border: none;
        font-size: .825rem;
        color: #047857;
        font-weight: 600;
        cursor: pointer;
        border-radius: .7rem;
        transition: background .18s;
    }
    .modal-cancel-btn:hover {
        background: #f0fdf4;
        color: #064e3b;
    }
</style>
@endpush

@section('content')
<div class="reset-page space-y-7">

    {{-- Hero Section --}}
    <div class="hero-banner anim-1">
        <div class="hero-left">
            <div class="hero-icon lightgreen-glow">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="hero-tag">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        Super Admin Protocol
                    </span>
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-100/80 px-2.5 py-0.5 rounded-full">
                        Secure Environment
                    </span>
                </div>
                <h1 class="hero-title">Score Data Reset Center</h1>
                <p class="hero-sub">
                    Purge individual category score records or perform a complete multi-category wipe.<br>
                    Actions are strictly <strong class="text-emerald-900 underline decoration-emerald-400 font-extrabold">permanent &amp; logged</strong> to the Immutable Security Ledger.
                </p>
            </div>
        </div>
        <div class="hidden md:flex flex-col items-end gap-2 shrink-0">
            <a href="{{ route('super-admin.categories.management') }}"
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-white/90 border border-emerald-300 hover:bg-emerald-50 hover:border-emerald-400 transition-all shadow-xs">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Category Management
            </a>
            <span class="text-[11px] text-emerald-700 font-medium">Logged in as: {{ auth()->user()->name }}</span>
        </div>
    </div>

    {{-- Flash Notifications --}}
    @if(session('success'))
    <div class="anim-1 flex items-start gap-3 bg-emerald-50 border-2 border-emerald-400 rounded-2xl px-5 py-4 shadow-sm">
        <div class="p-1 rounded-lg bg-emerald-500 text-white shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-emerald-900">Success Notification</p>
            <p class="text-xs font-semibold text-emerald-800 mt-0.5">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="anim-1 flex items-start gap-3 bg-amber-50 border-2 border-amber-400 rounded-2xl px-5 py-4 shadow-sm">
        <div class="p-1 rounded-lg bg-amber-500 text-white shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-amber-900">Action Alert</p>
            <p class="text-xs font-semibold text-amber-800 mt-0.5">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="anim-1 flex items-start gap-3 bg-rose-50 border-2 border-rose-300 rounded-2xl px-5 py-4 shadow-sm">
        <div class="p-1 rounded-lg bg-rose-500 text-white shrink-0 mt-0.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-bold text-rose-900">Validation Notice</p>
            <div class="mt-0.5 space-y-0.5">
                @foreach($errors->all() as $error)
                    <p class="text-xs font-semibold text-rose-800">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Category Metadata Mapping --}}
    @php
    $categoryMeta = [
        'production'         => ['label' => 'Production',         'icon' => '🎭', 'color' => '#059669', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
        'fitness'            => ['label' => 'Fitness',            'icon' => '💪', 'color' => '#047857', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'],
        'traditional_attire' => ['label' => 'Traditional Attire', 'icon' => '👘', 'color' => '#0f766e', 'bg' => '#f0fdfa', 'border' => '#99f6e4'],
        'indigenous_attire'  => ['label' => 'Indigenous Attire',  'icon' => '🪬', 'color' => '#15803d', 'bg' => '#f0fdf4', 'border' => '#bbf7d0'],
        'qa'                 => ['label' => 'Final Q & A',        'icon' => '🎤', 'color' => '#065f46', 'bg' => '#ecfdf5', 'border' => '#86efac'],
        'custom'             => ['label' => 'Custom Categories',  'icon' => '✨', 'color' => '#166534', 'bg' => '#f0fdf4', 'border' => '#86efac'],
    ];
    @endphp

    {{-- Score Overview Strip --}}
    <div class="anim-2 space-y-3.5">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-extrabold text-emerald-800 uppercase tracking-widest">Active Score Metrics</h2>
            </div>
            <span class="text-xs font-semibold text-emerald-700">Live Database Counters</span>
        </div>

        <div class="stat-strip">
            @foreach($counts as $key => $count)
            @php $m = $categoryMeta[$key]; @endphp
            <div class="stat-chip">
                <div>
                    <div class="chip-head">
                        <span class="chip-label">{{ $m['label'] }}</span>
                        <div class="chip-icon-box">{{ $m['icon'] }}</div>
                    </div>
                    <div class="chip-count">{{ number_format($count) }}</div>
                </div>
                <div class="chip-sub">{{ Str::plural('score entry', $count) }}</div>
            </div>
            @endforeach
        </div>

        {{-- Total Strip Banner --}}
        <div class="stat-total">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-white/15 border border-white/30 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-emerald-100 uppercase tracking-wider">System-Wide Aggregate</p>
                    <p class="text-sm text-emerald-50 font-medium">Total Recorded Scores Across All Categories</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-3xl font-black tracking-tight text-white">{{ number_format($totalScores) }}</span>
                <span class="block text-[11px] font-bold text-emerald-200 uppercase tracking-widest mt-0.5">Total Records</span>
            </div>
        </div>
    </div>

    {{-- Individual Category Reset Section --}}
    <div class="anim-3 space-y-3.5">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <h2 class="text-xs font-extrabold text-emerald-800 uppercase tracking-widest">Selective Category Reset</h2>
            </div>
            <p class="text-xs text-emerald-700 font-medium">Resets only the chosen category — all other categories remain untouched</p>
        </div>

        <div class="cat-grid">
            @foreach($counts as $key => $count)
            @php $m = $categoryMeta[$key]; @endphp
            <div class="cat-card">
                <div class="cat-card-header">
                    <div class="cat-badge">
                        {{ $m['icon'] }}
                    </div>
                    <div>
                        <h3 class="font-extrabold text-sm text-emerald-950">{{ $m['label'] }}</h3>
                        <p class="text-[11px] text-emerald-600 font-semibold">Individual Score Table</p>
                    </div>
                </div>
                <div class="cat-card-body">
                    <div>
                        <div class="cat-count">{{ number_format($count) }}</div>
                        <div class="cat-records">{{ Str::plural('score record', $count) }} active</div>
                    </div>
                    <button
                        type="button"
                        class="cat-reset-btn"
                        onclick="openSingleModal('{{ $key }}', '{{ $m['label'] }}', {{ $count }})"
                        @if($count === 0) disabled title="No records available to reset" @endif
                    >
                        <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        {{ $count === 0 ? 'No Data Recorded' : 'Reset '.$m['label'].' Scores' }}
                    </button>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Danger Zone — Reset All Categories Simultaneously --}}
    <div class="anim-4 danger-zone">
        <div class="danger-header">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 border border-white/30 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-white font-black text-base leading-tight">Master Reset — Wipe All Score Categories</h2>
                    <p class="text-emerald-100 text-xs mt-0.5">Perform a complete system reset across all standard and custom score tables</p>
                </div>
            </div>
            <span class="hidden sm:inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-950/40 text-emerald-100 border border-emerald-300/30 uppercase tracking-wider">
                Full Database Sweep
            </span>
        </div>

        <div class="danger-body">
            <div class="danger-warning-box">
                <p class="text-xs font-extrabold text-emerald-900 uppercase tracking-wider mb-2">Scope of Master Purge</p>
                <ul class="danger-list">
                    <li>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Scores across <strong>Production, Fitness, Traditional Attire, Indigenous Attire, and Final Q&amp;A</strong> will be erased.</span>
                    </li>
                    <li>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>All custom stage categories and auxiliary score records will be permanently removed.</span>
                    </li>
                    <li>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Candidates, judges, categories, and criteria definitions <strong>will NOT be deleted</strong>.</span>
                    </li>
                    <li>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>This operation is irreversible — once confirmed, truncated tables cannot be restored.</span>
                    </li>
                </ul>
            </div>

            <form method="POST" action="{{ route('super-admin.categories.reset.confirm') }}"
                  id="reset-all-form" onsubmit="return handleResetAllSubmit(event)">
                @csrf
                <input type="hidden" name="scope" value="all">

                <div class="space-y-2">
                    <label class="block text-xs font-bold text-emerald-950 uppercase tracking-wider" for="reset-all-input">
                        Security Verification — Type <code class="confirm-code">RESET ALL DATA</code> to unlock
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="reset-all-input"
                               name="confirmation"
                               placeholder="Type RESET ALL DATA exactly as shown..."
                               autocomplete="off"
                               class="confirm-input"
                               oninput="checkResetAll()">
                    </div>
                </div>

                @error('confirmation')
                    <p class="text-xs text-rose-600 mt-2 font-bold">{{ $message }}</p>
                @enderror

                <button type="submit" id="reset-all-btn" disabled class="reset-all-btn locked">
                    <svg class="w-4 h-4 text-current" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span id="reset-all-label">Locked — Type Confirmation to Proceed</span>
                </button>

                <p class="audit-note">
                    🛡️ Audited: A permanent cryptographic entry is added to the Security Ledger with Admin ID, IP, and timestamp.
                </p>
            </form>
        </div>
    </div>

    {{-- Bottom Navigation --}}
    <div class="text-center pt-2">
        <a href="{{ route('super-admin.categories.management') }}"
           class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-900 bg-white border border-emerald-200 hover:border-emerald-400 px-4 py-2.5 rounded-xl shadow-xs transition-all">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Return to Category Management
        </a>
    </div>

</div>

{{-- Single Category Confirmation Modal (Lightgreen Themed) --}}
<div class="modal-overlay" id="single-modal" role="dialog" aria-modal="true">
    <div class="modal-box">
        <div class="modal-head">
            <div class="w-9 h-9 rounded-lg bg-white/20 border border-white/30 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <div>
                <p class="text-white font-extrabold text-sm leading-tight" id="modal-title">Reset Category</p>
                <p class="text-emerald-100 text-xs mt-0.5" id="modal-subtitle">Permanently delete score records</p>
            </div>
        </div>
        <div class="modal-body">
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl mb-3.5 text-xs text-emerald-900 leading-relaxed">
                You are about to purge all scores for this specific category. To confirm, type <code class="confirm-code" id="modal-required-text"></code> below.
            </div>

            <label class="block text-xs font-bold text-emerald-950 uppercase tracking-wider mb-1.5" for="modal-input">
                Confirmation Input
            </label>
            <input type="text"
                   id="modal-input"
                   class="modal-input"
                   placeholder="Type category name here..."
                   autocomplete="off"
                   oninput="checkModal()">

            <button type="button"
                    id="modal-confirm-btn"
                    class="modal-confirm-btn locked"
                    disabled
                    onclick="submitSingleReset()">
                Confirm &amp; Reset Scores
            </button>

            <button type="button" class="modal-cancel-btn" onclick="closeModal()">
                Cancel — Keep Category Data
            </button>

            <form id="single-reset-form" method="POST" action="" style="display:none">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const RESET_ALL_TEXT = 'RESET ALL DATA';

function checkResetAll() {
    const input = document.getElementById('reset-all-input');
    const btn   = document.getElementById('reset-all-btn');
    const label = document.getElementById('reset-all-label');
    const match = input.value.trim() === RESET_ALL_TEXT;

    btn.disabled  = !match;
    btn.className = 'reset-all-btn ' + (match ? 'unlocked' : 'locked');
    label.textContent = match ? '⚡ Purge & Reset ALL Category Scores Now' : 'Locked — Type Confirmation to Proceed';
}

function handleResetAllSubmit(e) {
    if (document.getElementById('reset-all-input').value.trim() !== RESET_ALL_TEXT) {
        e.preventDefault();
        return false;
    }
    return confirm('⚠️ FINAL SYSTEM WARNING: This will permanently wipe ALL score records across every category. Are you absolutely certain you want to execute this?');
}

let _catKey = '', _catLabel = '', _catCount = 0;

function openSingleModal(key, label, count) {
    _catKey = key;
    _catLabel = label;
    _catCount = count;
    document.getElementById('modal-title').textContent    = 'Reset ' + label + ' Scores';
    document.getElementById('modal-subtitle').textContent = count + ' recorded score(s) will be permanently deleted.';
    document.getElementById('modal-required-text').textContent = label.toUpperCase();
    document.getElementById('modal-input').value = '';
    const btn = document.getElementById('modal-confirm-btn');
    btn.disabled = true;
    btn.className = 'modal-confirm-btn locked';
    document.getElementById('single-modal').classList.add('open');
    setTimeout(() => document.getElementById('modal-input').focus(), 200);
}

function closeModal() {
    document.getElementById('single-modal').classList.remove('open');
}

function checkModal() {
    const val   = document.getElementById('modal-input').value.trim().toUpperCase();
    const match = val === _catLabel.toUpperCase();
    const btn   = document.getElementById('modal-confirm-btn');
    btn.disabled  = !match;
    btn.className = 'modal-confirm-btn ' + (match ? 'unlocked' : 'locked');
}

function submitSingleReset() {
    const form = document.getElementById('single-reset-form');
    form.action = '/super-admin/categories/reset/' + _catKey;
    if (confirm('Permanently purge all scores for ' + _catLabel + ' (' + _catCount + ' records)? This action cannot be reversed.')) {
        form.submit();
    }
}

document.getElementById('single-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeModal();
});
</script>
@endpush
