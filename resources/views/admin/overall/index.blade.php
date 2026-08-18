@extends('layouts.admin')

@section('title', 'Overall Tabulation & Judge Votes')

@push('styles')
<style>
    @media print {
        /* Force landscape orientation and tight margins for bond paper */
        @page {
            size: landscape;
            margin: 0.4in 0.3in;
        }

        /* Hide all UI chrome */
        .sidebar, .topbar, .page-header, .no-print, nav, header, button,
        .lg\:hidden, [x-data], .alert, .legend-bar {
            display: none !important;
        }

        /* Reset body */
        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 9pt !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Reset main content area — remove sidebar offset */
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Print header */
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

        /* Print signatures */
        .print-signatures {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)) !important;
            gap: 14px 18px;
            margin-top: 24px;
            page-break-inside: avoid;
        }

        /* Panel resets */
        .panel {
            box-shadow: none !important;
            border: none !important;
            border-radius: 0 !important;
            overflow: visible !important;
        }

        /* Remove overflow-x-auto clipping */
        .overflow-x-auto {
            overflow: visible !important;
        }

        /* Gender section headers — compact for print */
        .mb-8 {
            margin-bottom: 10px !important;
        }
        .mb-3 {
            margin-bottom: 4px !important;
        }

        /* ===== TABLE PRINT STYLES ===== */
        .data-table {
            border-collapse: collapse !important;
            width: 100% !important;
            table-layout: fixed !important;
        }

        /* Remove all min-width constraints */
        .data-table th,
        .data-table td {
            border: 1px solid #000000 !important;
            padding: 3px 4px !important;
            font-size: 8pt !important;
            color: #000000 !important;
            min-width: 0 !important;
            max-width: none !important;
            position: static !important;
            left: auto !important;
            z-index: auto !important;
            background: #ffffff !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            white-space: normal !important;
        }

        .data-table th {
            background-color: #e5e7eb !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            font-size: 7pt !important;
            padding: 4px 3px !important;
            text-align: center !important;
        }

        /* Column widths — fixed proportions to fit on paper */
        .data-table th:nth-child(1),
        .data-table td:nth-child(1) { width: 7% !important; }   /* Cand # */
        .data-table th:nth-child(2),
        .data-table td:nth-child(2) { width: 20% !important; }  /* Name */
        .data-table th:nth-child(3),
        .data-table td:nth-child(3) { width: 13% !important; }  /* Production */
        .data-table th:nth-child(4),
        .data-table td:nth-child(4) { width: 13% !important; }  /* Fitness */
        .data-table th:nth-child(5),
        .data-table td:nth-child(5) { width: 14% !important; }  /* Traditional */
        .data-table th:nth-child(6),
        .data-table td:nth-child(6) { width: 14% !important; }  /* Indigenous */
        .data-table th:nth-child(7),
        .data-table td:nth-child(7) { width: 12% !important; }  /* Grand Total */
        .data-table th:nth-child(8),
        .data-table td:nth-child(8) { width: 7% !important; }   /* Rank */

        /* Grand Total column — keep subtle highlight */
        .data-table td:nth-child(7) {
            background-color: #f3f4f6 !important;
            font-weight: 800 !important;
        }

        /* Rank column */
        .data-table td:nth-child(8) {
            background-color: #fefce8 !important;
            text-align: center !important;
        }

        /* Hide avatars/photos in print — show only text */
        .data-table td img,
        .data-table td .w-9,
        .data-table td .w-10 {
            display: none !important;
        }

        /* Candidate number — plain text, no styled badge */
        .data-table td:nth-child(1) span {
            display: inline !important;
            width: auto !important;
            height: auto !important;
            background: none !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            color: #000000 !important;
            font-size: 9pt !important;
            font-weight: 700 !important;
            padding: 0 !important;
        }

        /* Name cell — remove flex gap from hidden avatar */
        .data-table td:nth-child(2) .flex {
            gap: 0 !important;
        }
        .data-table td:nth-child(2) .truncate {
            max-width: none !important;
            white-space: normal !important;
            font-size: 8pt !important;
        }

        /* Score cells text */
        .data-table td .font-semibold,
        .data-table td .font-extrabold {
            font-size: 9pt !important;
            color: #000000 !important;
        }

        /* Rank badges — plain text in print */
        .data-table td:nth-child(8) span {
            display: inline !important;
            width: auto !important;
            height: auto !important;
            background: none !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            color: #000000 !important;
            font-size: 9pt !important;
            font-weight: 700 !important;
            padding: 0 !important;
        }

        /* Page break — each gender group starts on a new page if needed */
        .mb-8.animate-fade-in-up {
            page-break-inside: avoid;
        }

        /* Gender header badges — simplify for print */
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
</style>
@endpush

@section('content')

{{-- Printable Document Header --}}
<div class="print-header">
    <h1 style="font-size: 22pt; font-weight: bold; margin: 0; text-transform: uppercase;">Official Pageant Tabulation Sheet</h1>
    <p style="font-size: 12pt; margin: 4px 0 0; color: #4b5563;">Overall Category Results &amp; Final Rankings</p>
    <p style="font-size: 10pt; margin: 4px 0 0; color: #6b7280;">Generated on {{ now()->format('F d, Y — h:i A') }}</p>
</div>

<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 no-print">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Overall Tabulation &amp; Judge Votes
        </h1>
        <p class="page-subtitle">Weighted score breakdown across Production, Fitness, Traditional Attire, and Indigenous Attire based on configured criteria percentages.</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.overall.final') }}" class="btn btn-outline flex items-center gap-2 shadow-sm text-xs font-semibold text-emerald-700 border-emerald-300 hover:bg-emerald-50">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-6.75c-.621 0-1.125.504-1.125 1.125V18.75m9 0h-9M12 3a6.75 6.75 0 00-6.75 6.75c0 2.235.918 4.255 2.4 5.714.507.5.85 1.164.85 1.911h7c0-.747.343-1.411.85-1.911A6.716 6.716 0 0018.75 9.75 6.75 6.75 0 0012 3z"/>
            </svg>
            Final Overall (Q &amp; A)
        </a>
        <a href="{{ route('admin.settings.index') }}" class="btn btn-outline flex items-center gap-2 shadow-sm text-xs font-semibold">
            <svg class="w-4 h-4 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Criteria Settings
        </a>
        <button onclick="window.print()" class="btn btn-green flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Results Sheet
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
        $r = 1; $prev = null; $same = 1;
        foreach ($gTotals as $cid => $tot) {
            if ($tot <= 0) {
                $groupRanks[$cid] = null;
                continue;
            }
            if ($prev !== null && $tot === $prev) {
                $groupRanks[$cid] = $r - $same;
                $same++;
            } else {
                $groupRanks[$cid] = $r;
                $same = 1;
            }
            $prev = $tot;
            $r++;
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
    <div class="mb-8 animate-fade-in-up">
        {{-- Section Header --}}
        <div class="flex items-center gap-3 mb-3">
            @if($gKey === 'Male')
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #2563eb;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-blue-200 opacity-60"></div>
            @else
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #db2777;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-pink-200 opacity-60"></div>
            @endif
        </div>

        {{-- Scoring Table --}}
        <div class="panel overflow-x-auto border border-[var(--border-default)] shadow-sm rounded-xl">
            <table class="data-table min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="sticky left-0 z-10 bg-gray-50 min-w-[90px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">Cand. #</th>
                        <th class="sticky left-[90px] z-10 bg-gray-50 min-w-[180px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">Name</th>
                        <th class="text-center min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">
                            Production
                            <span class="block text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)($weights['production'] ?? 25) }}%)</span>
                        </th>
                        <th class="text-center min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">
                            Fitness
                            <span class="block text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)($weights['fitness'] ?? 25) }}%)</span>
                        </th>
                        <th class="text-center min-w-[140px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">
                            Traditional Attire
                            <span class="block text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)($weights['traditional_attire'] ?? 25) }}%)</span>
                        </th>
                        <th class="text-center min-w-[140px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">
                            Indigenous Attire
                            <span class="block text-[11px] font-bold text-[var(--green-700)] lowercase tracking-normal">({{ (int)($weights['indigenous_attire'] ?? 25) }}%)</span>
                        </th>
                       

                        @if($gKey === 'Male')
                            <th class="text-center min-w-[100px] text-xs font-bold text-blue-700 uppercase tracking-wider py-3 px-4" style="background-color: #eff6ff;">Grand Total</th>
                        @else
                            <th class="text-center min-w-[100px] text-xs font-bold text-pink-700 uppercase tracking-wider py-3 px-4" style="background-color: #fdf2f8;">Grand Total</th>
                        @endif

                        <th class="text-center min-w-[85px] text-xs font-bold text-amber-800 uppercase tracking-wider py-3 px-4" style="background-color: #fffbeb;">Rank</th>
                        <th class="text-center min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4 no-print">Judge Votes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sortedGroup as $candidate)
                        @php
                            $b          = $breakdown[$candidate->id];
                            $total      = $b['total'];
                            $rank       = $groupRanks[$candidate->id] ?? null;
                            $jBreakdown = json_encode($judgeBreakdown[$candidate->id] ?? []);
                        @endphp
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            {{-- Candidate Number --}}
                            <td class="sticky left-0 bg-white py-3 px-4">
                                @if($gKey === 'Male')
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white text-base font-black shadow-md" style="background-color: #2563eb; border: 2px solid #60a5fa;">
                                        {{ $candidate->candidate_number }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white text-base font-black shadow-md" style="background-color: #db2777; border: 2px solid #f472b6;">
                                        {{ $candidate->candidate_number }}
                                    </span>
                                @endif
                            </td>

                            {{-- Candidate Name --}}
                            <td class="sticky left-[90px] bg-white py-3 px-4 font-semibold text-gray-900">
                                <div class="flex items-center gap-3">
                                    @if($candidate->photo_url)
                                        <img src="{{ asset('storage/' . $candidate->photo_url) }}" class="w-9 h-9 rounded-full object-cover border-2 border-gray-200 shadow-sm flex-shrink-0">
                                    @else
                                        @if($gKey === 'Male')
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm" style="background-color: #2563eb;">
                                                {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                                            </div>
                                        @else
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm" style="background-color: #db2777;">
                                                {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    @endif
                                    <span class="truncate max-w-[140px] text-sm text-gray-900 font-semibold">{{ $candidate->display_name }}</span>
                                </div>
                            </td>

                            {{-- Production Score --}}
                            <td class="text-center py-3 px-4 font-semibold text-gray-700 text-sm">
                                {{ $b['production'] > 0 ? number_format($b['production'], 2) : '—' }}
                            </td>

                            {{-- Fitness Score --}}
                            <td class="text-center py-3 px-4 font-semibold text-gray-700 text-sm">
                                {{ $b['fitness'] > 0 ? number_format($b['fitness'], 2) : '—' }}
                            </td>

                            {{-- Traditional Attire Score --}}
                            <td class="text-center py-3 px-4 font-semibold text-gray-700 text-sm">
                                {{ $b['traditional'] > 0 ? number_format($b['traditional'], 2) : '—' }}
                            </td>

                            {{-- Indigenous Attire Score --}}
                            <td class="text-center py-3 px-4 font-semibold text-gray-700 text-sm">
                                {{ $b['indigenous'] > 0 ? number_format($b['indigenous'], 2) : '—' }}
                            </td>

                        
                            {{-- Grand Total --}}
                            @if($gKey === 'Male')
                                <td class="text-center py-3 px-4" style="background-color: #eff6ff;">
                                    <span class="font-extrabold text-lg text-blue-700">
                                        {{ $total > 0 ? number_format($total, 2) : '—' }}
                                    </span>
                                </td>
                            @else
                                <td class="text-center py-3 px-4" style="background-color: #fdf2f8;">
                                    <span class="font-extrabold text-lg text-pink-700">
                                        {{ $total > 0 ? number_format($total, 2) : '—' }}
                                    </span>
                                </td>
                            @endif

                            {{-- Rank --}}
                            <td class="text-center py-3 px-4" style="background-color: #fffbeb;">
                                @if(empty($rank))
                                    <span class="text-gray-400 font-medium">—</span>
                                @elseif($rank === 1)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-black shadow-md" style="background-color: #f59e0b;" title="1st Place">🥇</span>
                                @elseif($rank === 2)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-700 text-sm font-black shadow-md" style="background-color: #d1d5db;" title="2nd Place">🥈</span>
                                @elseif($rank === 3)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-black shadow-md" style="background-color: #fb923c;" title="3rd Place">🥉</span>
                                @else
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 border border-gray-300 text-gray-600 text-xs font-bold">{{ $rank }}</span>
                                @endif
                                  {{-- Judge Votes Action Button --}}
                            <td class="text-center py-3 px-4 no-print">
                                <a href="{{ route('admin.overall.candidate-votes', ['candidate' => $candidate->id, 'from' => 'overall']) }}"
                                   class="btn btn-outline btn-sm font-semibold flex items-center justify-center gap-1.5 mx-auto hover:border-emerald-500 hover:text-emerald-700 transition-colors">
                                    <svg class="w-4 h-4 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Votes
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
            <p style="font-size: 8pt; color: #4b5563; margin: 0;">Judge Signature &amp; Date</p>
        </div>
    @endforeach
</div>

{{-- Legend --}}
<div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-[var(--text-muted)] no-print">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> 1st Place</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> 2nd Place</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span> 3rd Place</span>
    <span>· Overall score is calculated from each category's judges average multiplied by its percentage weight (Production: {{ (int)($weights['production'] ?? 25) }}%, Fitness: {{ (int)($weights['fitness'] ?? 25) }}%, Traditional: {{ (int)($weights['traditional_attire'] ?? 25) }}%, Indigenous: {{ (int)($weights['indigenous_attire'] ?? 25) }}%).</span>
</div>

@endif
@endsection
