@extends('layouts.admin')

@section('title', 'Candidate #' . $candidate->candidate_number . ' ' . $candidate->display_name . ' — Judge Votes')

@push('styles')
<style>
    /* Metric Card Progress Mini Bar */
    .metric-progress-bg {
        background-color: #f1f5f9;
        height: 6px;
        border-radius: 9999px;
        overflow: hidden;
    }
    .metric-progress-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.6s ease-in-out;
    }

    @media print {
        .sidebar, .topbar, .page-header, .no-print, nav, header, button, .action-buttons {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 11pt;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000000;
            padding-bottom: 12px;
        }

        .print-signatures {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)) !important;
            gap: 20px 24px;
            margin-top: 40px;
            page-break-inside: avoid;
        }

        .data-table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        .data-table th, .data-table td {
            border: 1px solid #000000 !important;
            padding: 8px !important;
            font-size: 9.5pt !important;
            color: #000000 !important;
        }

        .data-table th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .panel {
            box-shadow: none !important;
            border: 1px solid #e5e7eb !important;
        }
    }

    .print-header, .print-signatures {
        display: none;
    }
</style>
@endpush

@section('content')

{{-- Printable Document Header --}}
<div class="print-header">
    <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 8px;">
        <img src="{{ asset('images/logo.png') }}" style="width: 55px; height: 55px; object-fit: contain;">
        <div style="text-align: center;">
            <h1 style="font-size: 17pt; font-weight: 800; margin: 0; text-transform: uppercase; letter-spacing: 0.02em;">Official Judge Tabulation &amp; Vote Sheet</h1>
            <p style="font-size: 11pt; margin: 2px 0 0; color: #374151; font-weight: 600;">Central Philippines State University — Pageant Tabulation System</p>
        </div>
    </div>
    <div style="display: flex; justify-content: space-between; border-top: 1.5px solid #111827; border-bottom: 1px dashed #9ca3af; padding: 8px 4px; font-size: 10.5pt; color: #111827; margin-top: 8px;">
        <span><strong>Candidate:</strong> #{{ $candidate->candidate_number }} — {{ $candidate->display_name }} ({{ $candidate->gender ?? 'N/A' }})</span>
        <span><strong>Preliminary Weighted:</strong> {{ number_format($summary['prelim_total'], 2) }}%</span>
        <span><strong>Final Score:</strong> {{ number_format($summary['final_total'], 2) }}%</span>
        <span><strong>Generated:</strong> {{ now()->format('M d, Y · h:i A') }}</span>
    </div>
</div>

{{-- Main Page Header & Breadcrumb --}}
<div class="page-header flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6 no-print">
    <div>
        <div class="flex items-center gap-2 text-xs font-semibold text-[var(--text-muted)] mb-2">
            @if($from === 'final')
                <a href="{{ route('admin.overall.final') }}" class="hover:text-[var(--green-600)] transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Final Overall (Top 5)
                </a>
            @else
                <a href="{{ route('admin.overall.index') }}" class="hover:text-[var(--green-600)] transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    Preliminary Overall Tabulation
                </a>
            @endif
            <span>/</span>
            <span class="text-[var(--green-700)] bg-[var(--green-50)] border border-[var(--green-200)] px-2 py-0.5 rounded-md font-mono">
                Candidate #{{ $candidate->candidate_number }}
            </span>
        </div>
        <h1 class="page-title flex items-center gap-3 text-2xl font-black text-[var(--text-primary)] tracking-tight">
            <span class="p-2 rounded-xl bg-gradient-to-br from-green-500 to-emerald-700 text-white shadow-md shadow-green-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </span>
            Judge Votes Breakdown
        </h1>
        <p class="page-subtitle mt-1 text-sm text-[var(--text-muted)]">Detailed itemized scoring submitted by each accredited judge for Candidate #{{ $candidate->candidate_number }}.</p>
    </div>

    <div class="flex items-center gap-3 action-buttons">
        <button onclick="window.print()" class="btn btn-green flex items-center gap-2 shadow-md shadow-green-600/20 font-semibold px-4 py-2.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Score Sheet
        </button>

        @if($from === 'final')
            <a href="{{ route('admin.overall.final') }}" class="btn btn-outline flex items-center gap-2 font-semibold px-4 py-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Final Overall
            </a>
        @else
            <a href="{{ route('admin.overall.index') }}" class="btn btn-outline flex items-center gap-2 font-semibold px-4 py-2.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Results
            </a>
        @endif
    </div>
</div>

{{-- Hero Candidate Profile Card --}}
<div class="panel p-6 mb-8 animate-fade-in-up border border-[var(--border-default)] shadow-sm bg-white relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-green-500 via-emerald-500 to-teal-600"></div>

    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pt-1">
        {{-- Candidate Info --}}
        <div class="flex items-center gap-5">
            <div class="relative flex-shrink-0">
                @if($candidate->photo_url)
                    <img src="{{ asset('storage/' . $candidate->photo_url) }}" alt="{{ $candidate->display_name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border-2 border-emerald-500 shadow-md">
                @else
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center text-white text-3xl font-black shadow-md">
                        {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                    </div>
                @endif
                <span class="absolute -bottom-2 -right-2 bg-emerald-700 text-white text-[11px] font-black px-2.5 py-0.5 rounded-full shadow border-2 border-white">
                    #{{ $candidate->candidate_number }}
                </span>
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                        Official Contestant #{{ $candidate->candidate_number }}
                    </span>
                    @if($candidate->gender === 'Male')
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-blue-700 bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-200">
                            ♂ Male Division
                        </span>
                    @elseif($candidate->gender === 'Female')
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-pink-700 bg-pink-50 px-2.5 py-0.5 rounded-full border border-pink-200">
                            ♀ Female Division
                        </span>
                    @endif
                </div>

                <h2 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight leading-tight">
                    {{ $candidate->display_name }}
                </h2>
                <p class="text-xs text-[var(--text-muted)] mt-1 flex items-center gap-2 font-medium">
                    <span>Central Philippines State University</span>
                    <span>•</span>
                    <span>Candidate ID: <strong class="font-mono text-gray-700">{{ $candidate->id }}</strong></span>
                </p>
            </div>
        </div>

        {{-- Overall Totals Scorecard --}}
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 sm:gap-4 bg-gradient-to-br from-emerald-50/70 via-white to-green-50/50 p-4 rounded-2xl border border-emerald-100 shadow-sm">
            {{-- Prelim Grand Total --}}
            <div class="text-center sm:text-right px-4 py-1">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-500 block mb-0.5">Preliminary Total</span>
                <div class="text-2xl sm:text-3xl font-black text-[var(--green-700)] font-mono leading-tight">
                    {{ number_format($summary['prelim_total'], 2) }}
                </div>
                <span class="text-[11px] font-semibold text-gray-500 block">Weighted (100%)</span>
            </div>

            <div class="hidden sm:block w-px h-12 bg-emerald-200"></div>

            {{-- Final Overall Score --}}
            <div class="text-center sm:text-right px-4 py-1 bg-white rounded-xl border border-emerald-200 shadow-sm">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 block mb-0.5">Final Overall Score</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-800 font-mono leading-tight">
                    {{ number_format($summary['final_total'], 2) }}
                </div>
                <span class="text-[11px] font-bold text-emerald-600 block">Prelim + Q&amp;A</span>
            </div>
        </div>
    </div>
</div>

{{-- 5 Category Performance Cards Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    {{-- Production --}}
    @php
        $pWeight = (int)($summary['weights']['production'] ?? 25);
        $pPercent = min(100, max(0, $summary['prod_avg']));
    @endphp
    <div class="panel p-5 animate-fade-in-up border border-[var(--border-default)] hover:border-emerald-300 transition-all shadow-sm bg-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Production</span>
            <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-md bg-green-50 text-green-700 border border-green-200">
                {{ $pWeight }}%
            </span>
        </div>
        <div class="text-2xl font-black text-gray-900 font-mono mb-1">
            {{ number_format($summary['prod_avg'], 2) }}
        </div>
        <div class="metric-progress-bg mb-2">
            <div class="metric-progress-fill bg-green-500" style="width: {{ $pPercent }}%;"></div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Weighted:</span>
            <span class="font-bold text-emerald-700 font-mono">{{ number_format($summary['prod_weighted'], 2) }}</span>
        </div>
    </div>

    {{-- Fitness --}}
    @php
        $fWeight = (int)($summary['weights']['fitness'] ?? 25);
        $fPercent = min(100, max(0, $summary['fit_avg']));
    @endphp
    <div class="panel p-5 animate-fade-in-up delay-100 border border-[var(--border-default)] hover:border-emerald-300 transition-all shadow-sm bg-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Fitness</span>
            <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-md bg-green-50 text-green-700 border border-green-200">
                {{ $fWeight }}%
            </span>
        </div>
        <div class="text-2xl font-black text-gray-900 font-mono mb-1">
            {{ number_format($summary['fit_avg'], 2) }}
        </div>
        <div class="metric-progress-bg mb-2">
            <div class="metric-progress-fill bg-teal-500" style="width: {{ $fPercent }}%;"></div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Weighted:</span>
            <span class="font-bold text-emerald-700 font-mono">{{ number_format($summary['fit_weighted'], 2) }}</span>
        </div>
    </div>

    {{-- Traditional Attire --}}
    @php
        $tWeight = (int)($summary['weights']['traditional_attire'] ?? 25);
        $tPercent = min(100, max(0, $summary['trad_avg']));
    @endphp
    <div class="panel p-5 animate-fade-in-up delay-200 border border-[var(--border-default)] hover:border-emerald-300 transition-all shadow-sm bg-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Traditional Attire</span>
            <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-md bg-green-50 text-green-700 border border-green-200">
                {{ $tWeight }}%
            </span>
        </div>
        <div class="text-2xl font-black text-gray-900 font-mono mb-1">
            {{ number_format($summary['trad_avg'], 2) }}
        </div>
        <div class="metric-progress-bg mb-2">
            <div class="metric-progress-fill bg-emerald-500" style="width: {{ $tPercent }}%;"></div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Weighted:</span>
            <span class="font-bold text-emerald-700 font-mono">{{ number_format($summary['trad_weighted'], 2) }}</span>
        </div>
    </div>

    {{-- Indigenous Attire --}}
    @php
        $iWeight = (int)($summary['weights']['indigenous_attire'] ?? 25);
        $iPercent = min(100, max(0, $summary['indig_avg']));
    @endphp
    <div class="panel p-5 animate-fade-in-up delay-300 border border-[var(--border-default)] hover:border-emerald-300 transition-all shadow-sm bg-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500">Indigenous Attire</span>
            <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-md bg-green-50 text-green-700 border border-green-200">
                {{ $iWeight }}%
            </span>
        </div>
        <div class="text-2xl font-black text-gray-900 font-mono mb-1">
            {{ number_format($summary['indig_avg'], 2) }}
        </div>
        <div class="metric-progress-bg mb-2">
            <div class="metric-progress-fill bg-green-600" style="width: {{ $iPercent }}%;"></div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500 font-medium">
            <span>Weighted:</span>
            <span class="font-bold text-emerald-700 font-mono">{{ number_format($summary['indig_weighted'], 2) }}</span>
        </div>
    </div>

    {{-- Q & A (Final) --}}
    @php
        $qaWeight = (int)($summary['final_weights']['qa'] ?? 50);
        $qaPercent = min(100, max(0, $summary['qa_avg']));
    @endphp
    <div class="panel p-5 animate-fade-in-up delay-400 border border-emerald-300 transition-all shadow-sm bg-gradient-to-br from-emerald-50/60 to-white">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-900">Q &amp; A (Final)</span>
            <span class="text-[11px] font-extrabold px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 border border-emerald-200">
                {{ $qaWeight }}% Final
            </span>
        </div>
        <div class="text-2xl font-black text-emerald-900 font-mono mb-1">
            {{ number_format($summary['qa_avg'], 2) }}
        </div>
        <div class="metric-progress-bg mb-2">
            <div class="metric-progress-fill bg-emerald-600" style="width: {{ $qaPercent }}%;"></div>
        </div>
        <div class="flex items-center justify-between text-xs text-emerald-800 font-medium">
            <span>Mean Score:</span>
            <span class="font-bold font-mono">{{ number_format($summary['qa_avg'], 2) }}</span>
        </div>
    </div>
</div>

{{-- Main Itemized Judge Votes Table --}}
<div class="panel overflow-hidden mb-8 shadow-sm border border-[var(--border-default)] bg-white rounded-2xl">
    <div class="px-6 py-4 border-b border-[var(--border-default)] bg-gray-50/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </span>
            <div>
                <h3 class="text-base font-bold text-gray-900">Itemized Judge Scoring Matrix</h3>
                <p class="text-xs text-gray-500">Official individual scores submitted per judge</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-white border border-gray-200 text-gray-700 shadow-sm">
                {{ count($judges) }} Accredited Judges
            </span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table min-w-full divide-y divide-[var(--border-default)] text-sm">
            <thead>
                <tr class="bg-gray-50 text-[var(--text-secondary)] text-xs font-bold uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-left w-12">#</th>
                    <th class="py-3.5 px-4 text-left min-w-[200px]">Judge Information</th>
                    <th class="py-3.5 px-4 text-center min-w-[110px]">
                        Production
                        <span class="block text-[10px] text-emerald-700 font-bold lowercase">({{ $pWeight }}%)</span>
                    </th>
                    <th class="py-3.5 px-4 text-center min-w-[110px]">
                        Fitness
                        <span class="block text-[10px] text-emerald-700 font-bold lowercase">({{ $fWeight }}%)</span>
                    </th>
                    <th class="py-3.5 px-4 text-center min-w-[130px]">
                        Traditional Attire
                        <span class="block text-[10px] text-emerald-700 font-bold lowercase">({{ $tWeight }}%)</span>
                    </th>
                    <th class="py-3.5 px-4 text-center min-w-[130px]">
                        Indigenous Attire
                        <span class="block text-[10px] text-emerald-700 font-bold lowercase">({{ $iWeight }}%)</span>
                    </th>
                    <th class="py-3.5 px-4 text-center min-w-[110px] bg-gray-100 text-gray-900 font-bold">
                        Prelim Sum
                    </th>
                    <th class="py-3.5 px-4 text-center min-w-[110px] text-emerald-900 bg-emerald-50/80 font-bold">
                        Q &amp; A
                    </th>
                    <th class="py-3.5 px-4 text-center min-w-[120px] bg-green-100 text-green-900 font-black">
                        Total Score
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--border-default)] bg-white">
                @forelse($judgeScores as $index => $row)
                    <tr class="hover:bg-green-50/30 transition-colors">
                        <td class="py-3.5 px-4 text-xs font-mono text-gray-400 font-bold">
                            {{ $index + 1 }}
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                @if($row['judge']->photo_url)
                                    <img src="{{ asset('storage/' . $row['judge']->photo_url) }}" alt="{{ $row['judge']->name }}" class="w-9 h-9 rounded-xl object-cover border border-gray-200 shadow-sm flex-shrink-0">
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-white text-xs font-bold shadow-sm flex-shrink-0">
                                        {{ strtoupper(substr($row['judge']->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <span class="font-bold text-gray-900 block leading-tight">{{ $row['judge']->name }}</span>
                                    <span class="text-[11px] font-semibold text-emerald-700">Judge {{ $row['judge']->judge_number ?? $row['judge']->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-semibold text-gray-800">
                            {{ $row['production'] !== null ? number_format($row['production'], 2) : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-semibold text-gray-800">
                            {{ $row['fitness'] !== null ? number_format($row['fitness'], 2) : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-semibold text-gray-800">
                            {{ $row['traditional'] !== null ? number_format($row['traditional'], 2) : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-semibold text-gray-800">
                            {{ $row['indigenous'] !== null ? number_format($row['indigenous'], 2) : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-gray-900 bg-gray-50/80">
                            {{ number_format($row['total_prelim'], 2) }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-800 bg-emerald-50/50">
                            {{ $row['qa'] !== null ? number_format($row['qa'], 2) : '—' }}
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-black text-green-900 bg-green-50/70">
                            {{ number_format($row['total'], 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-sm text-[var(--text-muted)]">
                            No judges found or no scores submitted yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            {{-- Summary Footer --}}
            <tfoot class="border-t-2 border-gray-300 bg-gray-50 font-bold">
                {{-- Row 1: Mean Averages --}}
                <tr class="border-b border-gray-200">
                    <td colspan="2" class="py-3.5 px-4 text-left uppercase text-xs tracking-wider text-gray-800 font-extrabold">
                        Category Average (Mean)
                    </td>
                    <td class="py-3.5 px-4 text-center font-mono font-black text-gray-900">
                        {{ number_format($summary['prod_avg'], 2) }}
                    </td>
                    <td class="py-3.5 px-4 text-center font-mono font-black text-gray-900">
                        {{ number_format($summary['fit_avg'], 2) }}
                    </td>
                    <td class="py-3.5 px-4 text-center font-mono font-black text-gray-900">
                        {{ number_format($summary['trad_avg'], 2) }}
                    </td>
                    <td class="py-3.5 px-4 text-center font-mono font-black text-gray-900">
                        {{ number_format($summary['indig_avg'], 2) }}
                    </td>
                    <td class="py-3.5 px-4 text-center font-mono font-black text-gray-900 bg-gray-100">
                        —
                    </td>
                    <td class="py-3.5 px-4 text-center font-mono font-black text-emerald-800 bg-emerald-100/60">
                        {{ number_format($summary['qa_avg'], 2) }}
                    </td>
                    <td class="py-3.5 px-4 text-center font-mono font-black text-green-900 bg-green-100/80">
                        —
                    </td>
                </tr>

                {{-- Row 2: Weighted Contribution --}}
                <tr class="bg-gray-100/80 text-xs">
                    <td colspan="2" class="py-3 px-4 text-left uppercase tracking-wider text-gray-700 font-extrabold">
                        Weighted Contribution
                    </td>
                    <td class="py-3 px-4 text-center font-mono font-bold text-emerald-700">
                        {{ number_format($summary['prod_weighted'], 2) }} <span class="text-[10px] text-gray-500">({{ $pWeight }}%)</span>
                    </td>
                    <td class="py-3 px-4 text-center font-mono font-bold text-emerald-700">
                        {{ number_format($summary['fit_weighted'], 2) }} <span class="text-[10px] text-gray-500">({{ $fWeight }}%)</span>
                    </td>
                    <td class="py-3 px-4 text-center font-mono font-bold text-emerald-700">
                        {{ number_format($summary['trad_weighted'], 2) }} <span class="text-[10px] text-gray-500">({{ $tWeight }}%)</span>
                    </td>
                    <td class="py-3 px-4 text-center font-mono font-bold text-emerald-700">
                        {{ number_format($summary['indig_weighted'], 2) }} <span class="text-[10px] text-gray-500">({{ $iWeight }}%)</span>
                    </td>
                    <td class="py-3 px-4 text-center font-mono font-black text-emerald-900 bg-gray-200/90 text-sm">
                        {{ number_format($summary['prelim_total'], 2) }}
                    </td>
                    <td class="py-3 px-4 text-center font-mono font-bold text-emerald-800 bg-emerald-100/50">
                        {{ $qaWeight }}% Final
                    </td>
                    <td class="py-3 px-4 text-center font-mono font-black text-green-950 bg-green-200 text-sm">
                        {{ number_format($summary['final_total'], 2) }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

{{-- Printable Signatures Block --}}
<div class="print-signatures">
    @foreach($judges as $judge)
        <div style="text-align: center;">
            <div style="border-bottom: 1px solid #000; height: 35px; margin-bottom: 6px;"></div>
            <p style="font-size: 10pt; font-weight: bold; margin: 0; text-transform: uppercase;">{{ $judge->name }}</p>
            <p style="font-size: 8pt; color: #16a34a; margin: 0; font-weight: bold;">Judge {{ $judge->judge_number ?? $judge->id }}</p>
            <p style="font-size: 8pt; color: #4b5563; margin: 0;">Judge Signature &amp; Date</p>
        </div>
    @endforeach
</div>

{{-- Informational Footer Callout --}}
<div class="panel p-4 rounded-xl border border-[var(--border-default)] bg-[var(--bg-surface)] flex items-start gap-3 text-xs text-gray-600 no-print">
    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="leading-relaxed">
        <strong>Computation Formula:</strong> Preliminary Total is calculated by summing each category's mean average across judges multiplied by its configured percentage weight (Production: {{ $pWeight }}%, Fitness: {{ $fWeight }}%, Traditional: {{ $tWeight }}%, Indigenous: {{ $iWeight }}%). Final Overall incorporates Preliminary Grand Total ({{ (int)($summary['final_weights']['preliminary_total'] ?? 50) }}%) + Q&amp;A Average ({{ $qaWeight }}%).
    </div>
</div>

@endsection
