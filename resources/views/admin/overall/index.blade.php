@extends('layouts.admin')

@section('title', 'Overall Tabulation & Judge Votes')

@push('styles')
<style>
    @media print {
        @page {
            size: landscape;
            margin: 0.4in 0.3in;
        }

        .sidebar, .topbar, .page-header, .no-print, nav, header, button,
        .lg\:hidden, [x-data], .alert, .legend-bar {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 9pt !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
        }

        .print-header h1 {
            font-size: 14pt !important;
            margin: 0 !important;
        }

        .print-header p {
            font-size: 9pt !important;
            margin: 2px 0 0 !important;
        }

        .print-signatures {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)) !important;
            gap: 14px 18px;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        .panel {
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }

        .overflow-x-auto {
            overflow: visible !important;
        }

        .mb-8 {
            margin-bottom: 10px !important;
        }
        .mb-3 {
            margin-bottom: 4px !important;
        }

        .data-table {
            border-collapse: collapse !important;
            width: 100% !important;
            table-layout: fixed !important;
        }

        .data-table th,
        .data-table td {
            border: 1px solid #000000 !important;
            padding: 3px 4px !important;
            font-size: 8pt !important;
            color: #000000 !important;
        }

        .data-table th {
            background-color: #f3f4f6 !important;
        }

        .data-table td.sticky,
        .data-table th.sticky {
            position: static !important;
            left: auto !important;
            background: #ffffff !important;
        }

        .data-table th:nth-child(1),
        .data-table td:nth-child(1) {
            width: 48px !important;
            text-align: center !important;
        }

        .data-table th:nth-child(2),
        .data-table td:nth-child(2) {
            width: 180px !important;
            text-align: left !important;
        }

        .data-table th:nth-child(3),
        .data-table td:nth-child(3),
        .data-table th:nth-child(4),
        .data-table td:nth-child(4),
        .data-table th:nth-child(5),
        .data-table td:nth-child(5),
        .data-table th:nth-child(6),
        .data-table td:nth-child(6) {
            width: 110px !important;
            text-align: center !important;
        }

        .data-table th:nth-child(7),
        .data-table td:nth-child(7) {
            width: 90px !important;
            text-align: center !important;
            font-weight: bold !important;
            background-color: #f9fafb !important;
        }

        .data-table th:nth-child(8),
        .data-table td:nth-child(8) {
            width: 55px !important;
            text-align: center !important;
            font-weight: bold !important;
        }

        .data-table th:nth-child(9),
        .data-table td:nth-child(9) {
            display: none !important;
        }

        .data-table img {
            display: none !important;
        }

        .data-table span.rounded-full {
            background: transparent !important;
            border: none !important;
            color: #000000 !important;
            font-size: 9pt !important;
            font-weight: 700 !important;
            padding: 0 !important;
        }

        .mb-8.animate-fade-in-up {
            page-break-inside: avoid;
        }

        .rounded-xl.shadow-md {
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 2px 6px !important;
            font-size: 9pt !important;
        }

        .h-px {
            display: none !important;
        }
    }

    .print-header, .print-signatures {
        display: none;
    }

    @keyframes cellScoreFlash {
        0% { background-color: #fde047; transform: scale(1.18); box-shadow: 0 0 14px rgba(234, 179, 8, 0.5); }
        45% { background-color: #86efac; transform: scale(1.06); }
        100% { background-color: transparent; transform: scale(1); box-shadow: none; }
    }
    .cell-flash {
        animation: cellScoreFlash 1.6s ease-out;
    }

    @keyframes cellResetFlash {
        0% { background-color: #fecdd3; transform: scale(1.15); box-shadow: 0 0 14px rgba(244, 63, 94, 0.4); }
        45% { background-color: #fef08a; transform: scale(1.05); }
        100% { background-color: transparent; transform: scale(1); box-shadow: none; }
    }
    .cell-reset-flash {
        animation: cellResetFlash 1.6s ease-out;
    }

    @keyframes rankBadgePop {
        0% { transform: scale(0.4); opacity: 0.3; }
        55% { transform: scale(1.35); }
        100% { transform: scale(1); opacity: 1; }
    }
    .rank-pop {
        animation: rankBadgePop 0.85s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
</style>
@endpush

@section('content')

{{-- Printable Document Header --}}
<div class="print-header">
    <h1 style="font-size: 22pt; font-weight: bold; margin: 0; text-transform: uppercase;">Official Pageant Tabulation Sheet</h1>
    <p style="font-size: 12pt; margin: 4px 0 0; color: #4b5563;">Overall Category Results &amp; Final Rankings</p>
    <p style="font-size: 10pt; margin: 4px 0 0; color: #6b7280;">Generated on {{ now()->format('F d, Y — h:i A') }}</p>
</div>

<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-5 sm:mb-6 no-print">
    <div>
        <h1 class="page-title flex items-center gap-2.5 sm:gap-3 text-lg sm:text-xl md:text-2xl font-bold">
            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-[var(--green-600)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Overall Tabulation &amp; Judge Votes
        </h1>
        <p class="page-subtitle text-xs sm:text-sm mt-0.5 sm:mt-1">Weighted score breakdown across all pre-judging categories with real-time updates.</p>
    </div>
    <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto justify-between sm:justify-end">
        <div id="realtime-status-badge" class="flex items-center gap-1.5 sm:gap-2 text-xs bg-[var(--bg-card)] border border-[var(--border-default)] px-3 py-1.5 rounded-xl shadow-sm transition-all">
            <span id="realtime-status-dot" class="inline-block w-2 sm:w-2.5 h-2 sm:h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span id="realtime-status-text" class="font-semibold text-[11px] sm:text-xs text-emerald-700">⚡ Real-Time Active</span>
        </div>
        <button onclick="window.print()" class="btn btn-green flex items-center gap-1.5 shadow-sm text-xs font-semibold whitespace-nowrap px-3 py-1.5 sm:px-4 sm:py-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>Print Sheet</span>
        </button>
    </div>
</div>

@if($candidates->isEmpty())
    <div class="panel animate-fade-in-up">
        <div class="panel-body text-center py-16">
            <svg class="w-12 h-12 mx-auto text-[var(--text-muted)] opacity-40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-[var(--text-muted)] font-medium">No candidates found. Please add candidates first.</p>
            <a href="{{ route('admin.candidates.create') }}" class="btn btn-green btn-sm mt-4">Add Candidates</a>
        </div>
    </div>
@else

@php
    $groups = [
        'Male'   => ['label' => '♂ Male Candidates',   'key' => 'Male',   'candidates' => $candidates->filter(fn($c) => $c->gender === 'Male')],
        'Female' => ['label' => '♀ Female Candidates', 'key' => 'Female', 'candidates' => $candidates->filter(fn($c) => $c->gender === 'Female')],
    ];

    // Compute ranks per gender division for candidates with total > 0
    $groupRanks = [];
    foreach ($groups as $gKey => $group) {
        $gCandidates = $group['candidates'];
        if ($gCandidates->isEmpty()) continue;
        $gTotals = [];
        foreach ($gCandidates as $c) {
            $gTotals[$c->id] = $breakdown[$c->id]['total'] ?? 0;
        }
        arsort($gTotals);
        $r = 0; $prev = null;
        foreach ($gTotals as $cid => $tot) {
            if ($tot <= 0) {
                $groupRanks[$cid] = null;
                continue;
            }
            if ($prev === null || abs((float)$tot - (float)$prev) > 0.0001) {
                $r++;
            }
            $groupRanks[$cid] = $r;
            $prev = $tot;
        }
    }
@endphp

@foreach($groups as $gKey => $group)
    @php $gCandidates = $group['candidates']; @endphp
    @if($gCandidates->isEmpty()) @continue @endif

    @php
        $sortedGroup = $gCandidates->sort(function($a, $b) use ($breakdown) {
            $totA = $breakdown[$a->id]['total'] ?? 0;
            $totB = $breakdown[$b->id]['total'] ?? 0;
            if ($totA > 0 && $totB > 0) {
                if ($totA == $totB) {
                    return $a->candidate_number <=> $b->candidate_number;
                }
                return $totB <=> $totA;
            }
            if ($totA > 0) return -1;
            if ($totB > 0) return 1;
            return $a->candidate_number <=> $b->candidate_number;
        });
    @endphp

    {{-- Group Section --}}
    <div class="mb-6 sm:mb-8 animate-fade-in-up" id="section-{{ strtolower($gKey) }}">
        {{-- Section Header --}}
        <div class="flex items-center gap-2.5 sm:gap-3 mb-2.5 sm:mb-3">
            @if($gKey === 'Male')
                <div class="flex items-center gap-2 text-white text-xs sm:text-sm font-bold px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl shadow-md" style="background-color: #2563eb;">
                    {{ $group['label'] }}
                    <span id="badge-count-male" class="bg-white/25 text-white text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-blue-200 opacity-60"></div>
            @else
                <div class="flex items-center gap-2 text-white text-xs sm:text-sm font-bold px-3 py-1.5 sm:px-4 sm:py-2 rounded-xl shadow-md" style="background-color: #db2777;">
                    {{ $group['label'] }}
                    <span id="badge-count-female" class="bg-white/25 text-white text-[10px] sm:text-xs font-bold px-2 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-pink-200 opacity-60"></div>
            @endif
        </div>

        {{-- Scoring Table --}}
        <div class="panel overflow-x-auto border border-[var(--border-default)] shadow-sm rounded-xl -webkit-overflow-scrolling-touch bg-white">
            <table class="data-table min-w-full border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 whitespace-nowrap">
                        <th class="sticky left-0 z-20 bg-gray-50 w-[56px] sm:w-[72px] min-w-[56px] sm:min-w-[72px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-2 sm:px-3 text-center border-r border-gray-200/50">Cand. #</th>
                        <th class="sticky left-[56px] sm:left-[72px] z-20 bg-gray-50 w-[125px] sm:w-[170px] min-w-[125px] sm:min-w-[170px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-2.5 sm:px-4 text-left border-r border-gray-200/80 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.06)]">Name</th>
                        <th class="text-center min-w-[95px] sm:min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-2 sm:px-4">
                            Production
                            <span class="block text-[10px] sm:text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)($weights['production'] ?? 25) }}%)</span>
                        </th>
                        <th class="text-center min-w-[95px] sm:min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-2 sm:px-4">
                            Fitness
                            <span class="block text-[10px] sm:text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)($weights['fitness'] ?? 25) }}%)</span>
                        </th>
                        <th class="text-center min-w-[105px] sm:min-w-[135px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-2 sm:px-4">
                            Traditional Attire
                            <span class="block text-[10px] sm:text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)($weights['traditional_attire'] ?? 25) }}%)</span>
                        </th>
                        <th class="text-center min-w-[105px] sm:min-w-[135px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-2 sm:px-4">
                            Indigenous Attire
                            <span class="block text-[10px] sm:text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)($weights['indigenous_attire'] ?? 25) }}%)</span>
                        </th>

                        {{-- Dynamic custom category columns --}}
                        @if(isset($customCategories))
                            @foreach($customCategories as $customCat)
                                <th class="text-center min-w-[100px] sm:min-w-[130px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-2 sm:px-4">
                                    {{ $customCat->name }}
                                    <span class="block text-[10px] sm:text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)$customCat->percentage }}%)</span>
                                </th>
                            @endforeach
                        @endif

                        @if($gKey === 'Male')
                            <th class="text-center min-w-[90px] sm:min-w-[110px] text-xs font-bold text-blue-700 uppercase tracking-wider py-3 px-2 sm:px-4" style="background-color: #eff6ff;">Grand Total</th>
                        @else
                            <th class="text-center min-w-[90px] sm:min-w-[110px] text-xs font-bold text-pink-700 uppercase tracking-wider py-3 px-2 sm:px-4" style="background-color: #fdf2f8;">Grand Total</th>
                        @endif

                        <th class="text-center min-w-[75px] sm:min-w-[90px] text-xs font-bold text-amber-800 uppercase tracking-wider py-3 px-2 sm:px-4" style="background-color: #fffbeb;">Rank</th>
                        <th class="text-center min-w-[95px] sm:min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-2 sm:px-4 no-print">Judge Votes</th>
                    </tr>
                </thead>
                <tbody id="tbody-{{ strtolower($gKey) }}" class="divide-y divide-gray-100">
                    @foreach($sortedGroup as $candidate)
                        @php
                            $b     = $breakdown[$candidate->id];
                            $total = $b['total'];
                            $rank  = $groupRanks[$candidate->id] ?? null;
                        @endphp
                        <tr id="row-cand-{{ $candidate->id }}"
                            data-cand-id="{{ $candidate->id }}"
                            data-cand-num="{{ $candidate->candidate_number }}"
                            data-gender="{{ $candidate->gender }}"
                            class="group hover:bg-gray-50/80 transition-colors">
                            {{-- Candidate Number --}}
                            <td class="sticky left-0 bg-white group-hover:bg-gray-50 py-2.5 sm:py-3 px-2 sm:px-3 z-10 text-center border-r border-gray-100">
                                @if($gKey === 'Male')
                                    <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-white text-xs sm:text-base font-black shadow-md" style="background-color: #2563eb; border: 2px solid #60a5fa;">
                                        {{ $candidate->candidate_number }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-white text-xs sm:text-base font-black shadow-md" style="background-color: #db2777; border: 2px solid #f472b6;">
                                        {{ $candidate->candidate_number }}
                                    </span>
                                @endif
                            </td>

                            {{-- Candidate Name --}}
                            <td class="sticky left-[56px] sm:left-[72px] bg-white group-hover:bg-gray-50 py-2.5 sm:py-3 px-2.5 sm:px-4 font-semibold text-gray-900 z-10 border-r border-gray-200/80 shadow-[3px_0_6px_-2px_rgba(0,0,0,0.06)]">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    @if($candidate->photo_url)
                                        <img src="{{ asset('storage/' . $candidate->photo_url) }}" class="w-7 h-7 sm:w-9 sm:h-9 rounded-full object-cover border-2 border-gray-200 shadow-sm flex-shrink-0">
                                    @else
                                        @if($gKey === 'Male')
                                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-white text-[10px] sm:text-xs font-bold flex-shrink-0 shadow-sm" style="background-color: #2563eb;">
                                                {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                                            </div>
                                        @else
                                            <div class="w-7 h-7 sm:w-9 sm:h-9 rounded-full flex items-center justify-center text-white text-[10px] sm:text-xs font-bold flex-shrink-0 shadow-sm" style="background-color: #db2777;">
                                                {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    @endif
                                    <span class="truncate max-w-[85px] sm:max-w-[130px] md:max-w-[170px] text-xs sm:text-sm text-gray-900 font-semibold leading-tight">{{ $candidate->display_name }}</span>
                                </div>
                            </td>

                            {{-- Production Score --}}
                            <td class="text-center py-2.5 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700 text-xs sm:text-sm">
                                <span id="prod-val-{{ $candidate->id }}" class="transition-all">{{ $b['production'] > 0 ? number_format($b['production'], 2) : '—' }}</span>
                            </td>

                            {{-- Fitness Score --}}
                            <td class="text-center py-2.5 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700 text-xs sm:text-sm">
                                <span id="fit-val-{{ $candidate->id }}" class="transition-all">{{ $b['fitness'] > 0 ? number_format($b['fitness'], 2) : '—' }}</span>
                            </td>

                            {{-- Traditional Attire Score --}}
                            <td class="text-center py-2.5 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700 text-xs sm:text-sm">
                                <span id="trad-val-{{ $candidate->id }}" class="transition-all">{{ $b['traditional'] > 0 ? number_format($b['traditional'], 2) : '—' }}</span>
                            </td>

                            {{-- Indigenous Attire Score --}}
                            <td class="text-center py-2.5 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700 text-xs sm:text-sm">
                                <span id="indig-val-{{ $candidate->id }}" class="transition-all">{{ $b['indigenous'] > 0 ? number_format($b['indigenous'], 2) : '—' }}</span>
                            </td>

                            {{-- Dynamic custom category scores --}}
                            @if(isset($customCategories))
                                @foreach($customCategories as $customCat)
                                    @php $customVal = $b['custom'][$customCat->key]['weighted'] ?? 0; @endphp
                                    <td class="text-center py-2.5 sm:py-3 px-2 sm:px-4 font-semibold text-gray-700 text-xs sm:text-sm">
                                        <span id="custom-{{ $customCat->key }}-val-{{ $candidate->id }}" class="transition-all">{{ $customVal > 0 ? number_format($customVal, 2) : '—' }}</span>
                                    </td>
                                @endforeach
                            @endif

                            {{-- Grand Total --}}
                            @if($gKey === 'Male')
                                <td class="text-center py-2.5 sm:py-3 px-2 sm:px-4" style="background-color: #eff6ff;">
                                    <span id="total-val-{{ $candidate->id }}" class="font-extrabold text-base sm:text-lg text-blue-700 transition-all">
                                        {{ $total > 0 ? number_format($total, 2) : '—' }}
                                    </span>
                                </td>
                            @else
                                <td class="text-center py-2.5 sm:py-3 px-2 sm:px-4" style="background-color: #fdf2f8;">
                                    <span id="total-val-{{ $candidate->id }}" class="font-extrabold text-base sm:text-lg text-pink-700 transition-all">
                                        {{ $total > 0 ? number_format($total, 2) : '—' }}
                                    </span>
                                </td>
                            @endif

                            {{-- Rank --}}
                            <td id="rank-cell-{{ $candidate->id }}" class="text-center py-2.5 sm:py-3 px-2 sm:px-4" style="background-color: #fffbeb;">
                                <div id="rank-val-{{ $candidate->id }}" class="inline-flex items-center justify-center transition-all">
                                    @if(empty($rank))
                                        <span class="text-gray-400 font-medium">—</span>
                                    @elseif($rank === 1)
                                        <span class="inline-flex items-center justify-center w-9 h-9 sm:w-11 sm:h-11 rounded-full text-xl sm:text-2xl shadow-lg ring-4 ring-amber-300/70 bg-gradient-to-tr from-amber-500 via-yellow-400 to-amber-300 text-white" title="1st Place (Champion)">🥇</span>
                                    @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-lg sm:text-xl shadow-md ring-2 ring-slate-300 bg-gradient-to-tr from-slate-400 via-gray-300 to-slate-200 text-slate-800" title="2nd Place">🥈</span>
                                    @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full text-lg sm:text-xl shadow-md ring-2 ring-orange-300 bg-gradient-to-tr from-amber-700 via-orange-500 to-amber-400 text-white" title="3rd Place">🥉</span>
                                    @elseif($rank === 4)
                                        <span class="inline-flex items-center justify-center w-7 h-7 sm:w-9 sm:h-9 rounded-full text-xs sm:text-sm font-black shadow-md ring-2 ring-blue-300 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white" title="4th Place">4</span>
                                    @elseif($rank === 5)
                                        <span class="inline-flex items-center justify-center w-7 h-7 sm:w-9 sm:h-9 rounded-full text-xs sm:text-sm font-black shadow-md ring-2 ring-purple-300 bg-gradient-to-tr from-purple-600 to-pink-600 text-white" title="5th Place">5</span>
                                    @else
                                        <span class="inline-flex items-center justify-center w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-gray-100 border-2 border-gray-300 text-gray-700 text-xs font-extrabold shadow-sm">{{ $rank }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Judge Votes Action Button --}}
                            <td class="text-center py-2.5 sm:py-3 px-2 sm:px-4 no-print">
                                <a href="{{ route('admin.overall.candidate-votes', ['candidate' => $candidate->id, 'from' => 'overall']) }}"
                                   class="btn btn-outline btn-sm text-xs font-semibold inline-flex items-center justify-center gap-1 sm:gap-1.5 px-2.5 py-1.5 sm:px-3 sm:py-1.5 hover:border-emerald-500 hover:text-emerald-700 transition-colors whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[var(--green-600)] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span class="hidden sm:inline">View Votes</span>
                                    <span class="sm:hidden">Votes</span>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

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

{{-- Legend --}}
<div class="mt-4 p-3 sm:p-4 bg-gray-50 rounded-xl border border-gray-200/70 text-xs text-[var(--text-muted)] no-print flex flex-col md:flex-row md:items-center justify-between gap-2.5 sm:gap-3">
    <div class="flex flex-wrap items-center gap-3 font-medium">
        <span class="flex items-center gap-1.5 text-gray-800 font-semibold"><span class="text-sm sm:text-base">🥇</span> 1st Place</span>
        <span class="flex items-center gap-1.5 text-gray-800 font-semibold"><span class="text-sm sm:text-base">🥈</span> 2nd Place</span>
        <span class="flex items-center gap-1.5 text-gray-800 font-semibold"><span class="text-sm sm:text-base">🥉</span> 3rd Place</span>
    </div>
    <div class="text-[11px] text-gray-500 leading-relaxed">
        Overall score is calculated from each category's judges average multiplied by its percentage weight (Production: {{ (int)($weights['production'] ?? 25) }}%, Fitness: {{ (int)($weights['fitness'] ?? 25) }}%, Traditional: {{ (int)($weights['traditional_attire'] ?? 25) }}%, Indigenous: {{ (int)($weights['indigenous_attire'] ?? 25) }}%).
    </div>
</div>

@endif

@push('scripts')
<script>
    // Initial Datasets
    const CANDIDATES_DATA = {!! json_encode($candidatesJson ?? []) !!};
    const JUDGES_DATA     = {!! json_encode($judgesJson ?? []) !!};
    const WEIGHTS         = {
        production:         {{ (float)($weights['production'] ?? 25.0) }},
        fitness:            {{ (float)($weights['fitness'] ?? 25.0) }},
        traditional_attire: {{ (float)($weights['traditional_attire'] ?? 25.0) }},
        indigenous_attire:  {{ (float)($weights['indigenous_attire'] ?? 25.0) }}
    };

    // 'category_candidateId_judgeId' -> numeric score
    let RAW_SCORES = {!! json_encode($rawScoresMap ?? (object)[]) !!};
    const JUDGE_COUNT = JUDGES_DATA.length;
    let fallbackPollTimer = null;
    let isSocketConnected = false;

    document.addEventListener('DOMContentLoaded', () => {
        setupOverallEcho();
    });

    function updateStatusIndicator(status) {
        const badge = document.getElementById('realtime-status-badge');
        const dot = document.getElementById('realtime-status-dot');
        const text = document.getElementById('realtime-status-text');

        if (!badge || !dot || !text) return;

        if (status === 'connected') {
            dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse';
            text.className = 'font-semibold text-xs text-emerald-700';
            text.textContent = '⚡ Real-Time Connected';
            stopFallbackPolling();
        } else if (status === 'connecting') {
            dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping';
            text.className = 'font-semibold text-xs text-amber-700';
            text.textContent = '🟡 Connecting... (Live Sync Active)';
            startFallbackPolling();
        } else {
            dot.className = 'inline-block w-2.5 h-2.5 rounded-full bg-slate-400';
            text.className = 'font-semibold text-xs text-slate-600';
            text.textContent = '⚪ Live Polling Active';
            startFallbackPolling();
        }
    }

    function setupOverallEcho() {
        if (!window.Echo) {
            updateStatusIndicator('connecting');
            setTimeout(setupOverallEcho, 400);
            return;
        }

        try {
            window.Echo.private('admin.scores')
                .subscribed(() => {
                    isSocketConnected = true;
                    updateStatusIndicator('connected');
                    console.log('⚡ Connected to admin.scores channel on Overall Tabulation table');
                })
                .error((err) => {
                    console.warn('Echo subscription warning, using live fallback:', err);
                    isSocketConnected = false;
                    updateStatusIndicator('connecting');
                })
                .listen('.score.submitted', (e) => {
                    const validCategories = ['production', 'fitness', 'traditional-attire', 'traditional_attire', 'indigenous-attire', 'indigenous_attire'];
                    const normalizedCat = (e.category || '').replace('_', '-');

                    if (validCategories.includes(e.category) || validCategories.includes(normalizedCat)) {
                        const isResetAction = (e.action === 'reset' || e.score === null || e.score === '' || e.score === undefined);
                        handleOverallScoreChange(
                            normalizedCat,
                            parseInt(e.candidate_id),
                            parseInt(e.judge_id),
                            isResetAction ? null : parseFloat(e.score),
                            e.judge_name,
                            e.candidate_name,
                            e.candidate_number,
                            isResetAction ? 'reset' : 'saved'
                        );
                    }
                });

            // Bind pusher connection state events
            if (window.Echo.connector && window.Echo.connector.pusher) {
                const pusher = window.Echo.connector.pusher;
                pusher.connection.bind('connected', () => {
                    isSocketConnected = true;
                    updateStatusIndicator('connected');
                });
                pusher.connection.bind('disconnected', () => {
                    isSocketConnected = false;
                    updateStatusIndicator('disconnected');
                });
                pusher.connection.bind('unavailable', () => {
                    isSocketConnected = false;
                    updateStatusIndicator('disconnected');
                });
                pusher.connection.bind('failed', () => {
                    isSocketConnected = false;
                    updateStatusIndicator('disconnected');
                });
            }
        } catch (err) {
            console.warn('Echo setup error, fallback activated:', err);
            updateStatusIndicator('disconnected');
        }
    }

    function startFallbackPolling() {
        if (fallbackPollTimer || isSocketConnected) return;
        fallbackPollTimer = setInterval(pollScoresFallback, 3500);
    }

    function stopFallbackPolling() {
        if (fallbackPollTimer) {
            clearInterval(fallbackPollTimer);
            fallbackPollTimer = null;
        }
    }

    async function pollScoresFallback() {
        try {
            const res = await fetch('{{ route("admin.overall.index") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.success && data.rawScores) {
                const serverScores = data.rawScores;

                // 1. Check for updated scores
                for (const key in serverScores) {
                    const newScore = parseFloat(serverScores[key]);
                    const currentScore = RAW_SCORES[key];

                    if (currentScore === undefined || currentScore !== newScore) {
                        const parts = key.split('_');
                        const cat = parts[0];
                        const candId = parseInt(parts[1]);
                        const judgeId = parseInt(parts[2]);
                        const cand = CANDIDATES_DATA.find(c => c.id === candId);
                        const judge = JUDGES_DATA.find(j => j.id === judgeId);

                        handleOverallScoreChange(
                            cat,
                            candId,
                            judgeId,
                            newScore,
                            judge ? judge.name : 'Judge',
                            cand ? cand.display_name : 'Candidate',
                            cand ? cand.candidate_number : candId,
                            'saved',
                            false
                        );
                    }
                }

                // 2. Check for RESET / DELETED scores
                for (const key in RAW_SCORES) {
                    if (!(key in serverScores)) {
                        const parts = key.split('_');
                        const cat = parts[0];
                        const candId = parseInt(parts[1]);
                        const judgeId = parseInt(parts[2]);
                        const cand = CANDIDATES_DATA.find(c => c.id === candId);
                        const judge = JUDGES_DATA.find(j => j.id === judgeId);

                        handleOverallScoreChange(
                            cat,
                            candId,
                            judgeId,
                            null,
                            judge ? judge.name : 'Judge',
                            cand ? cand.display_name : 'Candidate',
                            cand ? cand.candidate_number : candId,
                            'reset',
                            false
                        );
                    }
                }
            }
        } catch (e) {
            // silent retry
        }
    }

    function handleOverallScoreChange(category, candId, judgeId, newScore, judgeName, candName, candNum, action = 'saved', triggerToast = true) {
        const catKey = category.replace('_', '-');
        const key = `${catKey}_${candId}_${judgeId}`;
        const isReset = (action === 'reset' || newScore === null || isNaN(newScore) || newScore === '');

        if (!isReset) {
            RAW_SCORES[key] = parseFloat(newScore);
        } else {
            delete RAW_SCORES[key];
        }

        // 1. Calculate new weighted score for this specific category
        let sum = 0;
        JUDGES_DATA.forEach(j => {
            const s = RAW_SCORES[`${catKey}_${candId}_${j.id}`];
            if (s !== undefined && s !== null && !isNaN(s)) sum += parseFloat(s);
        });
        const catAvg = JUDGE_COUNT > 0 ? (sum / JUDGE_COUNT) : 0;

        let weightKey = 'production';
        let cellPrefix = 'prod';
        if (catKey === 'fitness') { weightKey = 'fitness'; cellPrefix = 'fit'; }
        else if (catKey === 'traditional-attire') { weightKey = 'traditional_attire'; cellPrefix = 'trad'; }
        else if (catKey === 'indigenous-attire') { weightKey = 'indigenous_attire'; cellPrefix = 'indig'; }

        const catWeighted = catAvg * (WEIGHTS[weightKey] / 100.0);

        // Update category cell
        const catValEl = document.getElementById(`${cellPrefix}-val-${candId}`);
        if (catValEl) {
            catValEl.textContent = catWeighted > 0 ? catWeighted.toFixed(2) : '—';
            catValEl.classList.remove('cell-flash', 'cell-reset-flash');
            void catValEl.offsetWidth;
            catValEl.classList.add(isReset ? 'cell-reset-flash' : 'cell-flash');
            setTimeout(() => catValEl.classList.remove('cell-flash', 'cell-reset-flash'), 1800);
        }

        // 2. Recalculate Grand Total across all 4 categories for this candidate
        const candTotal = computeCandidateGrandTotal(candId);
        const totalValEl = document.getElementById(`total-val-${candId}`);
        if (totalValEl) {
            totalValEl.textContent = candTotal > 0 ? candTotal.toFixed(2) : '—';
            totalValEl.classList.remove('cell-flash', 'cell-reset-flash');
            void totalValEl.offsetWidth;
            totalValEl.classList.add(isReset ? 'cell-reset-flash' : 'cell-flash');
            setTimeout(() => totalValEl.classList.remove('cell-flash', 'cell-reset-flash'), 1800);
        }

        // 3. Recalculate Rankings for gender division
        const candidateObj = CANDIDATES_DATA.find(c => c.id === candId);
        if (candidateObj) {
            recalculateOverallGenderRanks(candidateObj.gender);
        }

        // 4. Toast Notification
        if (triggerToast && window.showToast) {
            const formattedCandNum = String(candNum || '').padStart(2, '0');
            const prettyCat = catKey.split('-').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ');
            if (isReset) {
                window.showToast(
                    'Score Reset',
                    `${judgeName} reset ${prettyCat} score for Candidate #${formattedCandNum} (${candName})`,
                    'warning',
                    4000
                );
            } else {
                window.showToast(
                    `${prettyCat} Score Recorded`,
                    `${judgeName} scored ${parseFloat(newScore).toFixed(1)} for Candidate #${formattedCandNum} (${candName})`,
                    'success',
                    4000
                );
            }
        }
    }

    function computeCandidateGrandTotal(candId) {
        const categories = [
            { key: 'production', weight: WEIGHTS.production },
            { key: 'fitness', weight: WEIGHTS.fitness },
            { key: 'traditional-attire', weight: WEIGHTS.traditional_attire },
            { key: 'indigenous-attire', weight: WEIGHTS.indigenous_attire },
        ];

        let grandTotal = 0;
        categories.forEach(cat => {
            let sum = 0;
            JUDGES_DATA.forEach(j => {
                const s = RAW_SCORES[`${cat.key}_${candId}_${j.id}`];
                if (s !== undefined && s !== null && !isNaN(s)) sum += parseFloat(s);
            });
            const avg = JUDGE_COUNT > 0 ? (sum / JUDGE_COUNT) : 0;
            grandTotal += avg * (cat.weight / 100.0);
        });

        return grandTotal;
    }

    function recalculateOverallGenderRanks(gender) {
        const groupCandidates = CANDIDATES_DATA.filter(c => c.gender === gender);
        if (!groupCandidates.length) return;

        const totals = [];
        groupCandidates.forEach(c => {
            const tot = computeCandidateGrandTotal(c.id);
            totals.push({
                id: c.id,
                candidate_number: c.candidate_number,
                total: tot
            });
        });

        totals.sort((a, b) => {
            if (a.total > 0 && b.total > 0) {
                if (Math.abs(a.total - b.total) < 0.0001) return a.candidate_number - b.candidate_number;
                return b.total - a.total;
            }
            if (a.total > 0) return -1;
            if (b.total > 0) return 1;
            return a.candidate_number - b.candidate_number;
        });

        let r = 0;
        let prev = null;
        const ranks = {};

        totals.forEach(item => {
            if (item.total <= 0) {
                ranks[item.id] = null;
            } else {
                if (prev === null || Math.abs(item.total - prev) >= 0.0001) {
                    r++;
                }
                ranks[item.id] = r;
                prev = item.total;
            }
        });

        totals.forEach(item => {
            const rankValEl = document.getElementById(`rank-val-${item.id}`);
            if (rankValEl) {
                const rank = ranks[item.id];
                let rankHtml = '';
                if (!rank) {
                    rankHtml = `<span class="text-gray-400 font-medium">—</span>`;
                } else if (rank === 1) {
                    rankHtml = `<span class="inline-flex items-center justify-center w-11 h-11 rounded-full text-2xl shadow-lg ring-4 ring-amber-300/70 bg-gradient-to-tr from-amber-500 via-yellow-400 to-amber-300 text-white rank-pop" title="1st Place (Champion)">🥇</span>`;
                } else if (rank === 2) {
                    rankHtml = `<span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-xl shadow-md ring-2 ring-slate-300 bg-gradient-to-tr from-slate-400 via-gray-300 to-slate-200 text-slate-800 rank-pop" title="2nd Place">🥈</span>`;
                } else if (rank === 3) {
                    rankHtml = `<span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-xl shadow-md ring-2 ring-orange-300 bg-gradient-to-tr from-amber-700 via-orange-500 to-amber-400 text-white rank-pop" title="3rd Place">🥉</span>`;
                } else if (rank === 4) {
                    rankHtml = `<span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-black shadow-md ring-2 ring-blue-300 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white rank-pop" title="4th Place">4</span>`;
                } else if (rank === 5) {
                    rankHtml = `<span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-black shadow-md ring-2 ring-purple-300 bg-gradient-to-tr from-purple-600 to-pink-600 text-white rank-pop" title="5th Place">5</span>`;
                } else {
                    rankHtml = `<span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 border-2 border-gray-300 text-gray-700 text-xs font-extrabold shadow-sm rank-pop">${rank}</span>`;
                }

                if (rankValEl.innerHTML.trim() !== rankHtml.trim()) {
                    rankValEl.innerHTML = rankHtml;
                }
            }
        });

        const tbody = document.getElementById(`tbody-${gender.toLowerCase()}`);
        if (tbody) {
            totals.forEach(item => {
                const row = document.getElementById(`row-cand-${item.id}`);
                if (row) {
                    tbody.appendChild(row);
                }
            });
        }
    }
</script>
@endpush
@endsection
