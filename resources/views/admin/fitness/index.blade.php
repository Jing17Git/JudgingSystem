@extends('layouts.admin')

@section('title', 'Fitness Scoring')

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
</style>
@endpush

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Fitness — Scoring Table
        </h1>
        <p class="page-subtitle">Scores given by each judge per candidate (1–10). Read-only view.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs text-[var(--text-muted)] bg-[var(--bg-card)] border border-[var(--border-default)] px-3 py-1.5 rounded-lg">
            {{ $judges->count() }} {{ Str::plural('Judge', $judges->count()) }} &nbsp;·&nbsp; {{ $candidates->count() }} {{ Str::plural('Candidate', $candidates->count()) }}
        </span>
    </div>
</div>

{{-- Print Header (visible only when printing) --}}
<div class="print-header">
    <h1 style="font-size: 20pt; font-weight: bold; margin: 0; text-transform: uppercase;">Official Pageant Tabulation Sheet</h1>
    <p style="font-size: 13pt; margin: 4px 0 0; font-weight: bold;">Fitness — Scoring Table</p>
    <p style="font-size: 10pt; margin: 4px 0 0; color: #6b7280;">Generated on {{ now()->format('F d, Y — h:i A') }}</p>
</div>

@if($candidates->isEmpty())
    <div class="panel animate-fade-in-up">
        <div class="panel-body text-center py-16">
            <svg class="w-12 h-12 mx-auto text-[var(--text-muted)] opacity-40 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 00-5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
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
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-blue-200 opacity-60"></div>
            @elseif($gKey === 'Female')
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #db2777;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-pink-200 opacity-60"></div>
            @else
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #4b5563;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-gray-200 opacity-60"></div>
            @endif

            {{-- Print Button --}}
            @if($gKey !== 'Unset')
                <button onclick="printGenderSection('{{ strtolower($gKey) }}', 'Fitness', '{{ $group['label'] }}')"
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
                            <th class="text-center min-w-[120px] text-xs font-bold text-gray-600 uppercase tracking-wider py-3 px-4">{{ $judge->name }}</th>
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
                            @elseif($gKey === 'Female')
                                <td class="text-center py-3 px-4" style="background-color: #fdf2f8;">
                                    <span class="font-extrabold text-lg text-pink-700">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
                                </td>
                            @else
                                <td class="text-center py-3 px-4" style="background-color: #f3f4f6;">
                                    <span class="font-extrabold text-lg text-gray-700">{{ $total > 0 ? number_format($total, 2) : '—' }}</span>
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

{{-- Print Signatures (visible only when printing) --}}
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
<div class="mt-2 flex flex-wrap items-center gap-4 text-xs text-[var(--text-muted)] legend-bar">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> 1st Place</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> 2nd Place</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span> 3rd Place</span>
    <span>· Rankings are <strong>per gender group</strong>. Scores are submitted by judges.</span>
</div>

@endif

@push('scripts')
<script>
    function printGenderSection(gender, category, label) {
        // Mark only the target section as print-visible
        document.querySelectorAll('.gender-section').forEach(el => {
            el.classList.remove('print-visible');
        });
        const target = document.getElementById('section-' + gender);
        if (target) {
            target.classList.add('print-visible');
        }

        // Update print header
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
</script>
@endpush
@endsection
