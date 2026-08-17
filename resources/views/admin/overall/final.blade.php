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
    <div class="flex items-center gap-3">
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

<div x-data="{ showModal: false, activeCand: null, activeName: '', activeNum: '', activePhoto: '', activeScores: [], activeTotal: '' }">

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
    <div class="mb-8 animate-fade-in-up">
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
                <tbody class="divide-y divide-gray-100">
                    @foreach($sortedGroup as $candidate)
                        @php
                            $b          = $finalBreakdown[$candidate->id];
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

                            {{-- Prelim Score (Weighted) --}}
                            <td class="text-center py-3 px-4">
                                <span class="inline-flex items-center justify-center w-14 h-7 rounded-md bg-blue-50 text-sm font-bold text-blue-900 font-mono">
                                    {{ $b['prelim_weighted'] > 0 ? number_format($b['prelim_weighted'], 2) : '—' }}
                                </span>
                            </td>

                            {{-- Q&A Avg (Weighted) --}}
                            <td class="text-center py-3 px-4">
                                <span class="inline-flex items-center justify-center w-14 h-7 rounded-md bg-emerald-50 text-sm font-bold text-emerald-900 font-mono">
                                    {{ $b['qa_weighted'] > 0 ? number_format($b['qa_weighted'], 2) : '—' }}
                                </span>
                            </td>

                            {{-- Final Grand Total --}}
                            @if($gKey === 'Male')
                                <td class="text-center py-3 px-4" style="background-color: #eff6ff;">
                                    <span class="font-extrabold text-lg text-blue-700">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
                                </td>
                            @else
                                <td class="text-center py-3 px-4" style="background-color: #fdf2f8;">
                                    <span class="font-extrabold text-lg text-pink-700">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
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
                            </td>

                            {{-- Judge Votes Action Button --}}
                            <td class="text-center py-3 px-4 no-print">
                                <button @click="showModal = true; activeName = '{{ addslashes($candidate->display_name) }}'; activeNum = '{{ $candidate->candidate_number }}'; activePhoto = '{{ $candidate->photo_url ? asset('storage/' . $candidate->photo_url) : '' }}'; activeScores = {{ $jBreakdown }}; activeTotal = '{{ $b['qa_avg'] > 0 ? number_format($b['qa_avg'], 2) : '—' }}'"
                                        class="btn btn-outline btn-sm font-semibold flex items-center justify-center gap-1.5 mx-auto text-xs">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Votes
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

{{-- Judge Breakdown Modal --}}
<div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto no-print" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-60" @click="showModal = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div x-show="showModal" x-transition.scale class="inline-block px-6 pt-5 pb-6 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-2xl sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <template x-if="activePhoto">
                        <img :src="activePhoto" class="w-12 h-12 rounded-full object-cover border-2 border-emerald-500 shadow-sm">
                    </template>
                    <template x-if="!activePhoto">
                        <div class="w-12 h-12 rounded-full bg-emerald-600 flex items-center justify-center text-white text-lg font-bold shadow-sm" x-text="activeName.charAt(0)"></div>
                    </template>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-800 rounded-md text-xs font-mono" x-text="'Candidate #' + activeNum"></span>
                            <span x-text="activeName"></span>
                        </h3>
                        <p class="text-xs text-[var(--text-muted)]">Individual Judge Scores for Q &amp; A</p>
                    </div>
                </div>

                <button type="button" @click="showModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-200 mb-4">
                <table class="data-table min-w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-2.5 px-4 text-left font-bold text-gray-600">Judge Name</th>
                            <th class="py-2.5 px-4 text-center font-bold text-emerald-800 bg-emerald-50">Q &amp; A Score</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="j in activeScores" :key="j.judge_id">
                            <tr class="hover:bg-gray-50">
                                <td class="py-2.5 px-4 font-semibold text-gray-900" x-text="j.judge_name"></td>
                                <td class="py-2.5 px-4 text-center text-emerald-800 font-bold font-mono bg-emerald-50/50" x-text="j.qa_score !== null ? Number(j.qa_score).toFixed(1) : '—'"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold border-t border-gray-200">
                            <td class="py-2.5 px-4 text-gray-900">Q &amp; A Average</td>
                            <td class="py-2.5 px-4 text-center text-emerald-900 font-mono text-sm" x-text="activeTotal"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="flex justify-end">
                <button type="button" @click="showModal = false" class="btn btn-outline">Close</button>
            </div>
        </div>
    </div>
</div>

</div>

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
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> 👑 Winner / Champion</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> 🥈 1st Runner-Up</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span> 🥉 2nd Runner-Up</span>
    <span>· Final Overall = (Prelim Total × {{ (int)$prelimWeight }}%) + (Q&amp;A Average × {{ (int)$qaWeight }}%).</span>
</div>

@endif
@endsection
