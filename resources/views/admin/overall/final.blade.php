@extends('layouts.admin')

@section('title', 'Final Overall Tabulation — Top 5 Finalists')

@push('styles')
<style>
    @media print {
        .sidebar, .topbar, .page-header, .no-print, nav, header, button {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 12pt;
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
            padding-bottom: 10px;
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
            font-size: 10pt !important;
            color: #000000 !important;
        }

        .data-table th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .panel {
            box-shadow: none !important;
            border: none !important;
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
    <p style="font-size: 13pt; margin: 4px 0 0; color: #111827; font-weight: bold;">Final Overall Results — Top 5 Finalists</p>
    <p style="font-size: 10pt; margin: 4px 0 0; color: #6b7280;">Preliminary ({{ (int)$prelimWeight }}%) + Final Q &amp; A ({{ (int)$qaWeight }}%) — Generated on {{ now()->format('F d, Y — h:i A') }}</p>
</div>

<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 no-print">
    <div>
        <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
            <a href="{{ route('admin.overall.index') }}" class="hover:text-[var(--green-600)] transition-colors">Results</a>
            <span>/</span>
            <span class="text-[var(--green-700)] font-semibold">Final Overall (Top 5)</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.504-1.125-1.125-1.125h-6.75c-.621 0-1.125.504-1.125 1.125V18.75m9 0h-9M12 3a6.75 6.75 0 00-6.75 6.75c0 2.235.918 4.255 2.4 5.714.507.5.85 1.164.85 1.911h7c0-.747.343-1.411.85-1.911A6.716 6.716 0 0018.75 9.75 6.75 6.75 0 0012 3z"/>
            </svg>
            Final Overall Tabulation — Top 5 Finalists
        </h1>
        <p class="page-subtitle">Final Grand Total computed from Preliminary Grand Total ({{ (int)$prelimWeight }}%) and Final Q &amp; A ({{ (int)$qaWeight }}%).</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <div id="realtime-status-badge" class="flex items-center gap-2 text-xs bg-[var(--bg-card)] border border-[var(--border-default)] px-3.5 py-1.5 rounded-xl shadow-sm transition-all">
            <span id="realtime-status-dot" class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span id="realtime-status-text" class="font-semibold text-xs text-emerald-700">⚡ Real-Time Active</span>
        </div>
        <a href="{{ route('admin.settings.final') }}" class="btn btn-outline flex items-center gap-2 shadow-sm text-xs font-semibold">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Final Settings ({{ (int)$prelimWeight }}% / {{ (int)$qaWeight }}%)
        </a>
        <a href="{{ route('admin.overall.index') }}" class="btn btn-outline flex items-center gap-2 shadow-sm text-xs font-semibold">
            <svg class="w-4 h-4 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Preliminary Results
        </a>
        <button onclick="window.print()" class="btn btn-green flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Final Sheet
        </button>
    </div>
</div>

@if($finalists->isEmpty())
    <div class="panel animate-fade-in-up">
        <div class="panel-body text-center py-16">
            <svg class="w-12 h-12 mx-auto text-[var(--text-muted)] opacity-40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-[var(--text-muted)] font-medium">No finalists found. Please complete preliminary judging first.</p>
            <a href="{{ route('admin.overall.index') }}" class="btn btn-green btn-sm mt-4">View Preliminary Tabulation</a>
        </div>
    </div>
@else

@php
    $groups = [
        'Male'   => ['label' => '♂ Male Finalists (Top 5)',   'key' => 'Male',   'candidates' => $top5Male],
        'Female' => ['label' => '♀ Female Finalists (Top 5)', 'key' => 'Female', 'candidates' => $top5Female],
    ];

    // Compute final ranks for each gender group based on final total
    $groupRanks = [];
    foreach ($groups as $gKey => $group) {
        $gCandidates = $group['candidates'];
        if ($gCandidates->isEmpty()) continue;
        $gTotals = [];
        foreach ($gCandidates as $c) {
            $gTotals[$c->id] = $finalBreakdown[$c->id]['total'] ?? 0;
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
        $sortedGroup = $gCandidates->sort(function($a, $b) use ($finalBreakdown) {
            $totA = $finalBreakdown[$a->id]['total'] ?? 0;
            $totB = $finalBreakdown[$b->id]['total'] ?? 0;
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
    <div class="mb-8 animate-fade-in-up" id="section-{{ strtolower($gKey) }}">
        <div class="flex items-center gap-3 mb-3">
            @if($gKey === 'Male')
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #2563eb;">
                    {{ $group['label'] }}
                    <span id="badge-count-male" class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-blue-200 opacity-60"></div>
            @else
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #db2777;">
                    {{ $group['label'] }}
                    <span id="badge-count-female" class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
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
                        <th class="text-center min-w-[140px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">
                            Prelim Total
                            <span class="block text-[11px] font-bold text-blue-600 lowercase tracking-normal">({{ (int)$prelimWeight }}% weight)</span>
                        </th>
                        <th class="text-center min-w-[140px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">
                            Q & A Avg
                            <span class="block text-[11px] font-bold text-emerald-600 lowercase tracking-normal">({{ (int)$qaWeight }}% weight)</span>
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
                <tbody id="tbody-{{ strtolower($gKey) }}" class="divide-y divide-gray-100">
                    @foreach($sortedGroup as $candidate)
                        @php
                            $b     = $finalBreakdown[$candidate->id];
                            $total = $b['total'];
                            $rank  = $groupRanks[$candidate->id] ?? null;
                        @endphp
                        <tr id="row-cand-{{ $candidate->id }}"
                            data-cand-id="{{ $candidate->id }}"
                            data-cand-num="{{ $candidate->candidate_number }}"
                            data-gender="{{ $candidate->gender }}"
                            class="hover:bg-gray-50/80 transition-colors">
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

                            {{-- Prelim Score (Weighted) --}}
                            <td class="text-center py-3 px-4">
                                <span id="prelim-val-{{ $candidate->id }}" class="inline-flex items-center justify-center w-14 h-7 rounded-md bg-blue-50 text-sm font-bold text-blue-900 font-mono transition-all">
                                    {{ $b['prelim_weighted'] > 0 ? number_format($b['prelim_weighted'], 2) : '—' }}
                                </span>
                            </td>

                            {{-- Q&A Avg (Weighted) --}}
                            <td class="text-center py-3 px-4">
                                <span id="qa-val-{{ $candidate->id }}" class="inline-flex items-center justify-center w-14 h-7 rounded-md bg-emerald-50 text-sm font-bold text-emerald-900 font-mono transition-all">
                                    {{ $b['qa_weighted'] > 0 ? number_format($b['qa_weighted'], 2) : '—' }}
                                </span>
                            </td>

                            {{-- Final Grand Total --}}
                            @if($gKey === 'Male')
                                <td class="text-center py-3 px-4" style="background-color: #eff6ff;">
                                    <span id="total-val-{{ $candidate->id }}" class="font-extrabold text-lg text-blue-700 transition-all">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
                                </td>
                            @else
                                <td class="text-center py-3 px-4" style="background-color: #fdf2f8;">
                                    <span id="total-val-{{ $candidate->id }}" class="font-extrabold text-lg text-pink-700 transition-all">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
                                </td>
                            @endif

                            {{-- Rank --}}
                            <td id="rank-cell-{{ $candidate->id }}" class="text-center py-3 px-4" style="background-color: #fffbeb;">
                                <div id="rank-val-{{ $candidate->id }}" class="inline-flex items-center justify-center transition-all">
                                     @if(empty($rank))
                                        <span class="text-gray-400 font-medium">—</span>
                                     @elseif($rank === 1)
                                        <span class="inline-flex items-center justify-center w-11 h-11 rounded-full text-2xl shadow-lg ring-4 ring-amber-300/70 bg-gradient-to-tr from-amber-500 via-yellow-400 to-amber-300 text-white" title="1st Place (Champion)">🥇</span>
                                     @elseif($rank === 2)
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-xl shadow-md ring-2 ring-slate-300 bg-gradient-to-tr from-slate-400 via-gray-300 to-slate-200 text-slate-800" title="2nd Place">🥈</span>
                                     @elseif($rank === 3)
                                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-xl shadow-md ring-2 ring-orange-300 bg-gradient-to-tr from-amber-700 via-orange-500 to-amber-400 text-white" title="3rd Place">🥉</span>
                                     @elseif($rank === 4)
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-black shadow-md ring-2 ring-blue-300 bg-gradient-to-tr from-blue-600 to-indigo-600 text-white" title="4th Place">4</span>
                                     @elseif($rank === 5)
                                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-black shadow-md ring-2 ring-purple-300 bg-gradient-to-tr from-purple-600 to-pink-600 text-white" title="5th Place">5</span>
                                     @else
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 border-2 border-gray-300 text-gray-700 text-xs font-extrabold shadow-sm">{{ $rank }}</span>
                                     @endif
                                </div>
                            </td>

                            {{-- Judge Votes Action Button --}}
                            <td class="text-center py-3 px-4 no-print">
                                <a href="{{ route('admin.overall.candidate-votes', ['candidate' => $candidate->id, 'from' => 'final']) }}"
                                   class="btn btn-outline btn-sm font-semibold flex items-center justify-center gap-1.5 mx-auto text-xs hover:border-emerald-500 hover:text-emerald-700 transition-colors whitespace-nowrap">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            <p style="font-size: 8pt; color: #16a34a; margin: 0; font-weight: bold;">Judge {{ $judge->judge_number ?? $judge->id }}</p>
            <p style="font-size: 8pt; color: #4b5563; margin: 0;">Judge Signature &amp; Date</p>
        </div>
    @endforeach
</div>

{{-- Legend --}}
<div class="mt-4 flex flex-wrap items-center gap-4 text-xs text-[var(--text-muted)] no-print">
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> 👑 Winner / Champion</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> 🥈 1st Runner-Up</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span> 🥉 2nd Runner-Up</span>
    <span>· Final Overall = (Prelim Total × {{ (int)$prelimWeight }}%) + (Q&amp;A Average × {{ (int)$qaWeight }}%).</span>
</div>

@endif

@push('scripts')
<script>
    // Initial Datasets
    const CANDIDATES_DATA  = {!! json_encode($candidatesJson ?? []) !!};
    const JUDGES_DATA      = {!! json_encode($judgesJson ?? []) !!};
    const PRELIM_TOTALS    = {!! json_encode($prelimTotals ?? (object)[]) !!};
    const PRELIM_WEIGHT    = {{ (float)$prelimWeight }};
    const QA_WEIGHT        = {{ (float)$qaWeight }};
    let RAW_QA             = {!! json_encode($rawQaMap ?? (object)[]) !!};

    const JUDGE_COUNT = JUDGES_DATA.length;
    let fallbackPollTimer = null;
    let isSocketConnected = false;

    document.addEventListener('DOMContentLoaded', () => {
        setupFinalEcho();
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

    function setupFinalEcho() {
        if (!window.Echo) {
            updateStatusIndicator('connecting');
            setTimeout(setupFinalEcho, 400);
            return;
        }

        try {
            window.Echo.private('admin.scores')
                .subscribed(() => {
                    isSocketConnected = true;
                    updateStatusIndicator('connected');
                    console.log('⚡ Connected to admin.scores channel on Final Overall Tabulation');
                })
                .error((err) => {
                    console.warn('Echo subscription warning, using live fallback:', err);
                    isSocketConnected = false;
                    updateStatusIndicator('connecting');
                })
                .listen('.score.submitted', (e) => {
                    if (e.category === 'qa' || e.category === 'qanda') {
                        const isResetAction = (e.action === 'reset' || e.score === null || e.score === '' || e.score === undefined);
                        handleFinalScoreChange(
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
            const res = await fetch('{{ route("admin.overall.final") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.success && data.rawQa) {
                const serverScores = data.rawQa;

                // 1. Check for updated scores
                for (const key in serverScores) {
                    const newScore = parseFloat(serverScores[key]);
                    const currentScore = RAW_QA[key];

                    if (currentScore === undefined || currentScore !== newScore) {
                        const parts = key.split('_');
                        const candId = parseInt(parts[0]);
                        const judgeId = parseInt(parts[1]);
                        const cand = CANDIDATES_DATA.find(c => c.id === candId);
                        const judge = JUDGES_DATA.find(j => j.id === judgeId);

                        handleFinalScoreChange(
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
                for (const key in RAW_QA) {
                    if (!(key in serverScores)) {
                        const parts = key.split('_');
                        const candId = parseInt(parts[0]);
                        const judgeId = parseInt(parts[1]);
                        const cand = CANDIDATES_DATA.find(c => c.id === candId);
                        const judge = JUDGES_DATA.find(j => j.id === judgeId);

                        handleFinalScoreChange(
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

    function handleFinalScoreChange(candId, judgeId, newScore, judgeName, candName, candNum, action = 'saved', triggerToast = true) {
        const key = `${candId}_${judgeId}`;
        const isReset = (action === 'reset' || newScore === null || isNaN(newScore) || newScore === '');

        if (!isReset) {
            RAW_QA[key] = parseFloat(newScore);
        } else {
            delete RAW_QA[key];
        }

        // 1. Recalculate Q&A weighted score for candidate
        let qSum = 0;
        JUDGES_DATA.forEach(j => {
            const s = RAW_QA[`${candId}_${j.id}`];
            if (s !== undefined && s !== null && !isNaN(s)) qSum += parseFloat(s);
        });
        const qAvg = JUDGE_COUNT > 0 ? (qSum / JUDGE_COUNT) : 0;
        const qWeighted = qAvg * (QA_WEIGHT / 100.0);

        const qaValEl = document.getElementById(`qa-val-${candId}`);
        if (qaValEl) {
            qaValEl.textContent = qWeighted > 0 ? qWeighted.toFixed(2) : '—';
            qaValEl.classList.remove('cell-flash', 'cell-reset-flash');
            void qaValEl.offsetWidth;
            qaValEl.classList.add(isReset ? 'cell-reset-flash' : 'cell-flash');
            setTimeout(() => qaValEl.classList.remove('cell-flash', 'cell-reset-flash'), 1800);
        }

        // 2. Recalculate Final Grand Total
        const pScore = parseFloat(PRELIM_TOTALS[candId] ?? 0);
        const pWeighted = pScore * (PRELIM_WEIGHT / 100.0);
        const finalGrandTotal = pWeighted + qWeighted;

        const totalValEl = document.getElementById(`total-val-${candId}`);
        if (totalValEl) {
            totalValEl.textContent = finalGrandTotal > 0 ? finalGrandTotal.toFixed(2) : '—';
            totalValEl.classList.remove('cell-flash', 'cell-reset-flash');
            void totalValEl.offsetWidth;
            totalValEl.classList.add(isReset ? 'cell-reset-flash' : 'cell-flash');
            setTimeout(() => totalValEl.classList.remove('cell-flash', 'cell-reset-flash'), 1800);
        }

        // 3. Recalculate Rankings
        const candidateObj = CANDIDATES_DATA.find(c => c.id === candId);
        if (candidateObj) {
            recalculateFinalGenderRanks(candidateObj.gender);
        }

        // 4. Toast notification
        if (triggerToast && window.showToast) {
            const formattedCandNum = String(candNum || '').padStart(2, '0');
            if (isReset) {
                window.showToast(
                    'Score Reset',
                    `${judgeName} reset Q&A score for Finalist #${formattedCandNum} (${candName})`,
                    'warning',
                    4000
                );
            } else {
                window.showToast(
                    'Final Q & A Score Recorded',
                    `${judgeName} scored ${parseFloat(newScore).toFixed(1)} for Finalist #${formattedCandNum} (${candName})`,
                    'success',
                    4000
                );
            }
        }
    }

    function recalculateFinalGenderRanks(gender) {
        const groupCandidates = CANDIDATES_DATA.filter(c => c.gender === gender);
        if (!groupCandidates.length) return;

        const totals = [];
        groupCandidates.forEach(c => {
            let qSum = 0;
            JUDGES_DATA.forEach(j => {
                const s = RAW_QA[`${c.id}_${j.id}`];
                if (s !== undefined && s !== null) qSum += parseFloat(s);
            });
            const qAvg = JUDGE_COUNT > 0 ? (qSum / JUDGE_COUNT) : 0;
            const qWeighted = qAvg * (QA_WEIGHT / 100.0);

            const pScore = parseFloat(PRELIM_TOTALS[c.id] ?? 0);
            const pWeighted = pScore * (PRELIM_WEIGHT / 100.0);
            const finalTotal = pWeighted + qWeighted;

            totals.push({
                id: c.id,
                candidate_number: c.candidate_number,
                total: finalTotal
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
