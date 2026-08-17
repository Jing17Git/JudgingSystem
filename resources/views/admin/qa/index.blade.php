@extends('layouts.admin')

@section('title', 'Q & A — Final Judging')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z"/>
            </svg>
            Q & A — Final Judging Table
        </h1>
        <p class="page-subtitle">Final Round evaluation for the Top 3 Male & Top 3 Female Finalists.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs font-semibold text-[var(--text-muted)] bg-[var(--bg-card)] border border-[var(--border-default)] px-3.5 py-2 rounded-xl shadow-sm">
            Top 3 Finalists &nbsp;·&nbsp; {{ $judges->count() }} {{ Str::plural('Judge', $judges->count()) }}
        </span>
    </div>
</div>

{{-- Top 3 Finalists Scoring Section --}}
@if($top3Male->isEmpty() && $top3Female->isEmpty())
    <div class="panel animate-fade-in-up mb-8">
        <div class="panel-body text-center py-16">
            <svg class="w-12 h-12 mx-auto text-[var(--text-muted)] opacity-40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-[var(--text-muted)] font-medium">No candidates available to compute Top 3 Finalists. Please add candidates and scores first.</p>
            <a href="{{ route('admin.candidates.create') }}" class="btn btn-green btn-sm mt-4">Add Candidates</a>
        </div>
    </div>
@else

@php
    $finalistGroups = [
        'Male'   => ['label' => '🏆 Top 3 Male Finalists',   'key' => 'Male',   'candidates' => $top3Male],
        'Female' => ['label' => '👑 Top 3 Female Finalists', 'key' => 'Female', 'candidates' => $top3Female],
    ];
@endphp

@foreach($finalistGroups as $gKey => $group)
    @php
        $gCandidates = $group['candidates'];
        if ($gCandidates->isEmpty()) continue;

        // Calculate Q&A totals & ranks for this group
        $gTotals = [];
        foreach ($gCandidates as $c) {
            $gTotals[$c->id] = $candidateTotals[$c->id] ?? 0;
        }
        arsort($gTotals);
        $groupRanks = [];
        $r = 1; $prev = null; $same = 1;
        foreach ($gTotals as $cid => $tot) {
            if ($tot <= 0) { $groupRanks[$cid] = null; continue; }
            if ($prev !== null && $tot === $prev) { $groupRanks[$cid] = $r - $same; $same++; }
            else { $groupRanks[$cid] = $r; $same = 1; }
            $prev = $tot; $r++;
        }

        // Sort candidates by total Q&A score descending (highest score / rank 1 on top)
        $sortedGroup = $gCandidates->sort(function($a, $b) use ($candidateTotals) {
            $totA = $candidateTotals[$a->id] ?? 0;
            $totB = $candidateTotals[$b->id] ?? 0;
            if ($totA > 0 && $totB > 0) {
                if ($totA == $totB) return $a->candidate_number <=> $b->candidate_number;
                return $totB <=> $totA;
            }
            if ($totA > 0) return -1;
            if ($totB > 0) return 1;
            return $a->candidate_number <=> $b->candidate_number;
        });
    @endphp

    <div class="mb-8 animate-fade-in-up">
        {{-- Section Header --}}
        <div class="flex items-center gap-3 mb-3">
            @if($gKey === 'Male')
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #2563eb;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }} Finalists</span>
                </div>
                <div class="h-px flex-1 bg-blue-200 opacity-60"></div>
            @else
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #db2777;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }} Finalists</span>
                </div>
                <div class="h-px flex-1 bg-pink-200 opacity-60"></div>
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
                            <th class="text-center min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">{{ $judge->name }}</th>
                        @endforeach

                        @if($gKey === 'Male')
                            <th class="text-center min-w-[100px] text-xs font-bold text-blue-700 uppercase tracking-wider py-3 px-4" style="background-color: #eff6ff;">Q&A Total</th>
                        @else
                            <th class="text-center min-w-[100px] text-xs font-bold text-pink-700 uppercase tracking-wider py-3 px-4" style="background-color: #fdf2f8;">Q&A Total</th>
                        @endif

                        <th class="text-center min-w-[85px] text-xs font-bold text-amber-800 uppercase tracking-wider py-3 px-4" style="background-color: #fffbeb;">Rank</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($sortedGroup as $candidate)
                        @php
                            $total = $candidateTotals[$candidate->id] ?? 0;
                            $rank  = $groupRanks[$candidate->id] ?? null;
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
                            <td class="sticky left-[110px] bg-white py-3 px-4 font-semibold text-gray-900">
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

                            {{-- Read-only scores per judge --}}
                            @foreach($judges as $judge)
                                @php
                                    $key = $candidate->id . '_' . $judge->id;
                                    $existingScore = isset($scores[$key]) ? $scores[$key]->score : null;
                                @endphp
                                <td class="text-center py-3 px-4">
                                    @if($existingScore !== null)
                                        <span class="inline-flex items-center justify-center w-10 h-7 rounded-md bg-gray-100 text-sm font-bold text-gray-800">
                                            {{ number_format($existingScore, 1) }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 font-medium">—</span>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Total --}}
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach
@endif

@endsection
