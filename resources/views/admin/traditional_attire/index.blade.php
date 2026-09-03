@extends('layouts.admin')

@section('title', 'Traditional Attire Scoring')

@push('styles')
<style>
    @media print {
        .sidebar, .topbar, .page-header, .no-print, nav, header, .legend-bar {
            display: none !important;
        }
        body {
            background: #fff !important;
            color: #000 !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 11pt;
        }
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .gender-section {
            display: none !important;
        }
        .gender-section.print-visible {
            display: block !important;
        }
        .print-header {
            display: block !important;
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .panel {
            box-shadow: none !important;
            border: none !important;
        }
        .data-table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .data-table th, .data-table td {
            border: 1px solid #000 !important;
            padding: 6px 8px !important;
            font-size: 10pt !important;
            color: #000 !important;
        }
        .data-table th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .print-signatures {
            display: grid !important;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)) !important;
            gap: 20px 24px;
            margin-top: 40px;
            page-break-inside: avoid;
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
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Traditional Attire — Scoring Table
        </h1>
        <p class="page-subtitle">Scores given by each judge per candidate (1–10). Read-only view with real-time updates.</p>
    </div>
    <div class="flex items-center gap-3">
        <div id="realtime-status-badge" class="flex items-center gap-2 text-xs bg-[var(--bg-card)] border border-[var(--border-default)] px-3.5 py-1.5 rounded-xl shadow-sm transition-all">
            <span id="realtime-status-dot" class="inline-block w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            <span id="realtime-status-text" class="font-semibold text-xs text-emerald-700">⚡ Real-Time Active</span>
        </div>
        <span class="text-xs text-[var(--text-muted)] bg-[var(--bg-card)] border border-[var(--border-default)] px-3 py-1.5 rounded-lg">
            {{ $judges->count() }} {{ Str::plural('Judge', $judges->count()) }} &nbsp;·&nbsp; {{ $candidates->count() }} {{ Str::plural('Candidate', $candidates->count()) }}
        </span>
    </div>
</div>

{{-- Print Header (visible only when printing) --}}
<div class="print-header">
    <h1 style="font-size: 20pt; font-weight: bold; margin: 0; text-transform: uppercase;">Official Pageant Tabulation Sheet</h1>
    <p style="font-size: 13pt; margin: 4px 0 0; font-weight: bold;">Traditional Attire — Scoring Table</p>
    <p style="font-size: 10pt; margin: 4px 0 0; color: #6b7280;">Generated on {{ now()->format('F d, Y — h:i A') }}</p>
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
@elseif($judges->isEmpty())
    <div class="panel animate-fade-in-up">
        <div class="panel-body text-center py-16">
            <svg class="w-12 h-12 mx-auto text-[var(--text-muted)] opacity-40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <p class="text-[var(--text-muted)] font-medium">No active judges found. Please add and activate judges first.</p>
            <a href="{{ route('admin.judges.create') }}" class="btn btn-green btn-sm mt-4">Add Judges</a>
        </div>
    </div>
@else

<div id="traditional-attire-table-container">
@php
    $groups = [
        'Male'   => ['label' => '♂ Male Candidates',   'key' => 'Male',   'candidates' => $candidates->filter(fn($c) => $c->gender === 'Male')],
        'Female' => ['label' => '♀ Female Candidates', 'key' => 'Female', 'candidates' => $candidates->filter(fn($c) => $c->gender === 'Female')],
        'Unset'  => ['label' => '— Unspecified',        'key' => 'Unset',  'candidates' => $candidates->filter(fn($c) => !in_array($c->gender, ['Male','Female']))],
    ];

    $groupRanks = [];
    foreach ($groups as $gKey => $group) {
        $gCandidates = $group['candidates'];
        if ($gCandidates->isEmpty()) continue;
        $gTotals = [];
        foreach ($gCandidates as $c) {
            $gTotals[$c->id] = $candidateTotals[$c->id] ?? 0;
        }
        arsort($gTotals);
        $r = 1; $prev = null; $same = 1;
        foreach ($gTotals as $cid => $tot) {
            if ($tot <= 0) { $groupRanks[$cid] = null; continue; }
            if ($prev !== null && $tot === $prev) { $groupRanks[$cid] = $r - $same; $same++; }
            else { $groupRanks[$cid] = $r; $same = 1; }
            $prev = $tot; $r++;
        }
    }
@endphp

@foreach($groups as $gKey => $group)
    @php $gCandidates = $group['candidates']; @endphp
    @if($gCandidates->isEmpty()) @continue @endif

    @php
        $sortedGroup = $gCandidates->sort(function($a, $b) use ($candidateTotals) {
            $totA = $candidateTotals[$a->id] ?? 0;
            $totB = $candidateTotals[$b->id] ?? 0;
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

    <div class="mb-8 animate-fade-in-up gender-section" id="section-{{ strtolower($gKey) }}">
        {{-- Section Header --}}
        <div class="flex items-center gap-3 mb-3">
            @if($gKey === 'Male')
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #2563eb;">
                    {{ $group['label'] }}
                    <span id="badge-count-male" class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-blue-200 opacity-60"></div>
            @elseif($gKey === 'Female')
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #db2777;">
                    {{ $group['label'] }}
                    <span id="badge-count-female" class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-pink-200 opacity-60"></div>
            @else
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #4b5563;">
                    {{ $group['label'] }}
                    <span id="badge-count-unset" class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-gray-200 opacity-60"></div>
            @endif

            {{-- Print Button --}}
            @if($gKey !== 'Unset')
                <button onclick="printGenderSection('{{ strtolower($gKey) }}', 'Traditional Attire', '{{ $group['label'] }}')"
                        class="no-print btn btn-outline btn-sm flex items-center gap-1.5 text-xs font-semibold shadow-sm hover:shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print {{ $gKey }}
                </button>
            @endif
        </div>

        {{-- Scoring Table --}}
        <div class="panel overflow-x-auto border border-[var(--border-default)] shadow-sm rounded-xl">
            <table class="data-table min-w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="sticky left-0 z-10 bg-gray-50 min-w-[110px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">Cand. #</th>
                        <th class="sticky left-[110px] z-10 bg-gray-50 min-w-[180px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">Name</th>
                        @foreach($judges as $judge)
                            <th class="text-center min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">
                                <div>{{ $judge->name }}</div>
                                <span class="block text-[11px] font-semibold text-emerald-700 normal-case tracking-normal">Judge {{ $judge->judge_number ?? $judge->id }}</span>
                            </th>
                        @endforeach

                        @if($gKey === 'Male')
                            <th class="text-center min-w-[100px] text-xs font-bold text-blue-700 uppercase tracking-wider py-3 px-4" style="background-color: #eff6ff;">Total</th>
                        @elseif($gKey === 'Female')
                            <th class="text-center min-w-[100px] text-xs font-bold text-pink-700 uppercase tracking-wider py-3 px-4" style="background-color: #fdf2f8;">Total</th>
                        @else
                            <th class="text-center min-w-[100px] text-xs font-bold text-gray-700 uppercase tracking-wider py-3 px-4" style="background-color: #f3f4f6;">Total</th>
                        @endif

                        <th class="text-center min-w-[85px] text-xs font-bold text-amber-800 uppercase tracking-wider py-3 px-4" style="background-color: #fffbeb;">Rank</th>
                    </tr>
                </thead>
                <tbody id="tbody-{{ strtolower($gKey) }}" class="divide-y divide-gray-100">
                    @foreach($sortedGroup as $candidate)
                        @php
                            $total = $candidateTotals[$candidate->id] ?? 0;
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
                                @elseif($gKey === 'Female')
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white text-base font-black shadow-md" style="background-color: #db2777; border: 2px solid #f472b6;">
                                        {{ $candidate->candidate_number }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white text-base font-black shadow-md" style="background-color: #4b5563; border: 2px solid #9ca3af;">
                                        {{ $candidate->candidate_number }}
                                    </span>
                                @endif
                            </td>

                            {{-- Candidate Name --}}
                            <td class="sticky left-[110px] bg-white py-3 px-4 font-semibold text-gray-900">
                                <div class="flex items-center gap-3">
                                    @if($candidate->photo_url)
                                        <img src="{{ asset('storage/' . $candidate->photo_url) }}" class="w-9 h-9 rounded-full object-cover border-2 border-gray-200 shadow-sm flex-shrink-0">
                                    @else
                                        @if($gKey === 'Male')
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm" style="background-color: #2563eb;">
                                                {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                                            </div>
                                        @elseif($gKey === 'Female')
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm" style="background-color: #db2777;">
                                                {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                                            </div>
                                        @else
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm" style="background-color: #4b5563;">
                                                {{ strtoupper(substr($candidate->display_name, 0, 1)) }}
                                            </div>
                                        @endif
                                    @endif
                                    <span class="truncate max-w-[140px] text-sm text-gray-900 font-semibold">{{ $candidate->display_name }}</span>
                                </div>
                            </td>

                            {{-- Read-only scores per judge --}}
                            @foreach($judges as $judge)
                                @php
                                    $key = $candidate->id . '_' . $judge->id;
                                    $existingScore = isset($scores[$key]) ? $scores[$key]->score : null;
                                @endphp
                                <td id="score-cell-{{ $candidate->id }}-{{ $judge->id }}"
                                    data-cand-id="{{ $candidate->id }}"
                                    data-judge-id="{{ $judge->id }}"
                                    class="text-center py-3 px-4 transition-all">
                                    @if($existingScore !== null)
                                        <span id="score-val-{{ $candidate->id }}-{{ $judge->id }}" class="inline-flex items-center justify-center w-10 h-7 rounded-md bg-gray-100 text-sm font-bold text-gray-800 transition-all">
                                            {{ number_format($existingScore, 1) }}
                                        </span>
                                    @else
                                        <span id="score-val-{{ $candidate->id }}-{{ $judge->id }}" class="text-gray-300 font-medium">—</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Total --}}
                            @if($gKey === 'Male')
                                <td id="total-cell-{{ $candidate->id }}" class="text-center py-3 px-4" style="background-color: #eff6ff;">
                                    <span id="total-val-{{ $candidate->id }}" class="font-extrabold text-lg text-blue-700 transition-all">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
                                </td>
                            @elseif($gKey === 'Female')
                                <td id="total-cell-{{ $candidate->id }}" class="text-center py-3 px-4" style="background-color: #fdf2f8;">
                                    <span id="total-val-{{ $candidate->id }}" class="font-extrabold text-lg text-pink-700 transition-all">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
                                </td>
                            @else
                                <td id="total-cell-{{ $candidate->id }}" class="text-center py-3 px-4" style="background-color: #f3f4f6;">
                                    <span id="total-val-{{ $candidate->id }}" class="font-extrabold text-lg text-gray-700 transition-all">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

{{-- Print Signatures (visible only when printing) --}}
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
<div class="mt-2 flex flex-wrap items-center gap-4 text-xs text-[var(--text-muted)] legend-bar">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> 1st Place</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> 2nd Place</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span> 3rd Place</span>
    <span>· Rankings are <strong>per gender group</strong>. Scores are submitted by judges.</span>
</div>
</div>

@endif

@push('scripts')
<script>
    function printGenderSection(gender, category, label) {
        document.querySelectorAll('.gender-section').forEach(el => {
            el.classList.remove('print-visible');
        });
        const target = document.getElementById('section-' + gender);
        if (target) {
            target.classList.add('print-visible');
        }

        const printHeader = document.querySelector('.print-header');
        if (printHeader) {
            printHeader.querySelector('p:nth-child(2)').textContent = category + ' — ' + label;
        }

        window.print();

        const cleanup = () => {
            document.querySelectorAll('.gender-section').forEach(el => {
                el.classList.remove('print-visible');
            });
            window.removeEventListener('afterprint', cleanup);
        };

        window.addEventListener('afterprint', cleanup);
        setTimeout(cleanup, 1000);
    }

    // Client-side Initial Data
    const CANDIDATES_DATA = {!! json_encode($candidatesJson ?? []) !!};
    const JUDGES_DATA     = {!! json_encode($judgesJson ?? []) !!};
    let SCORES_STORE      = {!! json_encode($scoresJson ?? (object)[]) !!};

    const JUDGE_COUNT = JUDGES_DATA.length;
    let fallbackPollTimer = null;
    let isSocketConnected = false;

    document.addEventListener('DOMContentLoaded', () => {
        setupTraditionalEcho();
        applyUrlParameters();

        if (window.UrlNav) {
            window.UrlNav.onPopState(() => applyUrlParameters());
        }

        // Attach click listeners to candidate rows for interactive URL state navigation
        document.querySelectorAll('[data-cand-id]').forEach(row => {
            row.addEventListener('click', function(e) {
                if (e.target.closest('button, a, input, select')) return;
                const cid = this.dataset.candId;
                const currentCid = window.UrlNav ? window.UrlNav.getParam('candidate_id') : null;
                const newCid = (currentCid === cid) ? null : cid;
                if (window.UrlNav) {
                    window.UrlNav.setParam('candidate_id', newCid);
                }
                applyUrlParameters();
            });
        });
    });

    function applyUrlParameters() {
        if (!window.UrlNav) return;
        const candId = window.UrlNav.getParam('candidate_id');
        const judgeId = window.UrlNav.getParam('judge_id');
        const gender = window.UrlNav.getParam('gender');

        // Clear previous highlights
        document.querySelectorAll('.row-url-focus').forEach(el => el.classList.remove('row-url-focus', 'bg-amber-100/70', 'ring-2', 'ring-amber-400'));

        if (candId) {
            const row = document.getElementById(`row-cand-${candId}`);
            if (row) {
                row.classList.add('row-url-focus', 'bg-amber-100/70', 'ring-2', 'ring-amber-400');
            }
        }

        if (gender && !candId) {
            const sec = document.getElementById(`section-${gender.toLowerCase()}`);
            if (sec) {
                sec.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

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

    function setupTraditionalEcho() {
        if (!window.Echo) {
            updateStatusIndicator('connecting');
            setTimeout(setupTraditionalEcho, 400);
            return;
        }

        try {
            window.Echo.private('admin.scores')
                .subscribed(() => {
                    isSocketConnected = true;
                    updateStatusIndicator('connected');
                    console.log('⚡ Connected to admin.scores channel on Traditional Attire table');
                })
                .error((err) => {
                    console.warn('Echo subscription warning, using live fallback:', err);
                    isSocketConnected = false;
                    updateStatusIndicator('connecting');
                })
                .listen('.score.submitted', (e) => {
                    if (e.category === 'traditional-attire' || e.category === 'traditional_attire') {
                        const isResetAction = (e.action === 'reset' || e.score === null || e.score === '' || e.score === undefined);
                        handleLiveScoreChange(
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
            const res = await fetch('{{ route("admin.traditional-attire.index") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            if (!res.ok) return;
            const data = await res.json();

            if (data.success && data.scores) {
                const serverScores = data.scores;

                // 1. Check for new or updated scores
                for (const key in serverScores) {
                    const parts = key.split('_');
                    if (parts.length === 2) {
                        const candId = parseInt(parts[0]);
                        const judgeId = parseInt(parts[1]);
                        const newScore = parseFloat(serverScores[key]);
                        const currentScore = SCORES_STORE[key];

                        if (currentScore === undefined || currentScore !== newScore) {
                            const cand = CANDIDATES_DATA.find(c => c.id === candId);
                            const judge = JUDGES_DATA.find(j => j.id === judgeId);
                            handleLiveScoreChange(
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
                }

                // 2. Check for RESET / DELETED scores from server
                for (const key in SCORES_STORE) {
                    if (!(key in serverScores)) {
                        const parts = key.split('_');
                        const candId = parseInt(parts[0]);
                        const judgeId = parseInt(parts[1]);
                        const cand = CANDIDATES_DATA.find(c => c.id === candId);
                        const judge = JUDGES_DATA.find(j => j.id === judgeId);
                        handleLiveScoreChange(
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
            // silent network retry
        }
    }

    function handleLiveScoreChange(candId, judgeId, newScore, judgeName, candName, candNum, action = 'saved', triggerToast = true) {
        const key = `${candId}_${judgeId}`;
        const isReset = (action === 'reset' || newScore === null || isNaN(newScore) || newScore === '');

        if (!isReset) {
            SCORES_STORE[key] = parseFloat(newScore);
        } else {
            delete SCORES_STORE[key];
        }

        const scoreValEl = document.getElementById(`score-val-${candId}-${judgeId}`);
        if (scoreValEl) {
            if (!isReset) {
                scoreValEl.className = 'inline-flex items-center justify-center w-10 h-7 rounded-md bg-emerald-100 text-sm font-bold text-emerald-900 cell-flash';
                scoreValEl.textContent = parseFloat(newScore).toFixed(1);
                setTimeout(() => {
                    scoreValEl.className = 'inline-flex items-center justify-center w-10 h-7 rounded-md bg-gray-100 text-sm font-bold text-gray-800 transition-all';
                }, 1800);
            } else {
                scoreValEl.className = 'inline-flex items-center justify-center w-10 h-7 rounded-md bg-rose-100 text-sm font-bold text-rose-700 cell-reset-flash';
                scoreValEl.textContent = '—';
                setTimeout(() => {
                    scoreValEl.className = 'text-gray-300 font-medium transition-all';
                }, 1800);
            }
        }

        let candSum = 0;
        JUDGES_DATA.forEach(j => {
            const s = SCORES_STORE[`${candId}_${j.id}`];
            if (s !== undefined && s !== null && !isNaN(s)) {
                candSum += parseFloat(s);
            }
        });
        const candAvgTotal = JUDGE_COUNT > 0 ? (candSum / JUDGE_COUNT) : 0;

        const totalValEl = document.getElementById(`total-val-${candId}`);
        if (totalValEl) {
            totalValEl.textContent = candAvgTotal > 0 ? candAvgTotal.toFixed(2) : '—';
            totalValEl.classList.remove('cell-flash', 'cell-reset-flash');
            void totalValEl.offsetWidth;
            totalValEl.classList.add(isReset ? 'cell-reset-flash' : 'cell-flash');
            setTimeout(() => totalValEl.classList.remove('cell-flash', 'cell-reset-flash'), 1800);
        }

        const candidateObj = CANDIDATES_DATA.find(c => c.id === candId);
        if (candidateObj) {
            recalculateGenderRanks(candidateObj.gender);
        }

        if (triggerToast && window.showToast) {
            const formattedCandNum = String(candNum || '').padStart(2, '0');
            if (isReset) {
                window.showToast(
                    'Score Reset',
                    `${judgeName} reset score for Candidate #${formattedCandNum} (${candName})`,
                    'warning',
                    4000
                );
            } else {
                window.showToast(
                    'Traditional Attire Score Recorded',
                    `${judgeName} scored ${parseFloat(newScore).toFixed(1)} for Candidate #${formattedCandNum} (${candName})`,
                    'success',
                    4000
                );
            }
        }
    }

    function recalculateGenderRanks(gender) {
        const groupCandidates = CANDIDATES_DATA.filter(c => c.gender === gender);
        if (!groupCandidates.length) return;

        const totals = [];
        groupCandidates.forEach(c => {
            let sum = 0;
            JUDGES_DATA.forEach(j => {
                const s = SCORES_STORE[`${c.id}_${j.id}`];
                if (s !== undefined && s !== null) sum += parseFloat(s);
            });
            const avg = JUDGE_COUNT > 0 ? (sum / JUDGE_COUNT) : 0;
            totals.push({
                id: c.id,
                candidate_number: c.candidate_number,
                total: avg
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

        let r = 1;
        let prev = null;
        let same = 1;
        const ranks = {};

        totals.forEach(item => {
            if (item.total <= 0) {
                ranks[item.id] = null;
            } else if (prev !== null && Math.abs(item.total - prev) < 0.0001) {
                ranks[item.id] = r - same;
                same++;
            } else {
                ranks[item.id] = r;
                same = 1;
            }
            prev = item.total;
            r++;
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
