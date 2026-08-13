@extends('layouts.admin')

@section('title', 'Fitness Scoring')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Fitness — Scoring Table
        </h1>
        <p class="page-subtitle">Scores given by each judge per candidate (1–10). Totals and rankings update live.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="text-xs text-[var(--text-muted)] bg-[var(--bg-card)] border border-[var(--border-default)] px-3 py-1.5 rounded-lg">
            {{ $judges->count() }} {{ Str::plural('Judge', $judges->count()) }} &nbsp;·&nbsp; {{ $candidates->count() }} {{ Str::plural('Candidate', $candidates->count()) }}
        </span>
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

{{-- Auto-save toast --}}
<div id="save-toast" class="fixed top-5 right-5 z-50 hidden">
    <div class="flex items-center gap-2 bg-green-600 text-white text-sm font-medium px-4 py-2.5 rounded-xl shadow-lg">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Score saved!
    </div>
</div>

@php
    $groups = [
        'Male'   => ['label' => '♂ Male Candidates',   'key' => 'Male',   'candidates' => $candidates->filter(fn($c) => $c->gender === 'Male')],
        'Female' => ['label' => '♀ Female Candidates', 'key' => 'Female', 'candidates' => $candidates->filter(fn($c) => $c->gender === 'Female')],
        'Other'  => ['label' => '⚧ Other Candidates',  'key' => 'Other',  'candidates' => $candidates->filter(fn($c) => $c->gender === 'Other')],
        'Unset'  => ['label' => '— Unspecified',        'key' => 'Unset',  'candidates' => $candidates->filter(fn($c) => !in_array($c->gender, ['Male','Female','Other']))],
    ];

    // Per-group rank computation (only candidates with total > 0 get ranked)
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
        $sortedGroup = $gCandidates->sortBy(fn($c) => $c->candidate_number);
        $groupId = strtolower($gKey) . '-table';
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
            @elseif($gKey === 'Female')
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #db2777;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-pink-200 opacity-60"></div>
            @elseif($gKey === 'Other')
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #9333ea;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-purple-200 opacity-60"></div>
            @else
                <div class="flex items-center gap-2 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-md" style="background-color: #4b5563;">
                    {{ $group['label'] }}
                    <span class="bg-white/25 text-white text-xs font-bold px-2.5 py-0.5 rounded-full">{{ $gCandidates->count() }}</span>
                </div>
                <div class="h-px flex-1 bg-gray-200 opacity-60"></div>
            @endif
        </div>

        {{-- Scoring Table --}}
        <div class="panel overflow-x-auto border border-[var(--border-default)] shadow-sm rounded-xl">
            <table class="data-table min-w-full" id="{{ $groupId }}" data-group="{{ $gKey }}">
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
                        @elseif($gKey === 'Other')
                            <th class="text-center min-w-[100px] text-xs font-bold text-purple-700 uppercase tracking-wider py-3 px-4" style="background-color: #faf5ff;">Total</th>
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
                        <tr data-candidate-id="{{ $candidate->id }}" data-group="{{ $gKey }}" class="hover:bg-gray-50/80 transition-colors">
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
                                @elseif($gKey === 'Other')
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full text-white text-base font-black shadow-md" style="background-color: #9333ea; border: 2px solid #c084fc;">
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

                            {{-- Score inputs per judge --}}
                            @foreach($judges as $judge)
                                @php
                                    $key = $candidate->id . '_' . $judge->id;
                                    $existingScore = isset($scores[$key]) ? $scores[$key]->score : '';
                                @endphp
                                <td class="text-center p-2">
                                    <input
                                        type="number"
                                        min="1" max="10" step="0.01"
                                        value="{{ $existingScore }}"
                                        placeholder="—"
                                        data-candidate="{{ $candidate->id }}"
                                        data-judge="{{ $judge->id }}"
                                        data-group="{{ $gKey }}"
                                        class="score-input w-20 text-center text-sm font-bold border border-gray-300 rounded-lg px-2 py-1.5 bg-white focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all placeholder:text-gray-300"
                                        title="Score for {{ $candidate->display_name }} by {{ $judge->name }}"
                                    >
                                </td>
                            @endforeach

                            {{-- Total --}}
                            @if($gKey === 'Male')
                                <td class="text-center py-3 px-4" style="background-color: #eff6ff;">
                                    <span class="total-cell font-extrabold text-lg text-blue-700" data-candidate="{{ $candidate->id }}">
                                        {{ $total > 0 ? number_format($total, 2) : '—' }}
                                    </span>
                                </td>
                            @elseif($gKey === 'Female')
                                <td class="text-center py-3 px-4" style="background-color: #fdf2f8;">
                                    <span class="total-cell font-extrabold text-lg text-pink-700" data-candidate="{{ $candidate->id }}">
                                        {{ $total > 0 ? number_format($total, 2) : '—' }}
                                    </span>
                                </td>
                            @elseif($gKey === 'Other')
                                <td class="text-center py-3 px-4" style="background-color: #faf5ff;">
                                    <span class="total-cell font-extrabold text-lg text-purple-700" data-candidate="{{ $candidate->id }}">
                                        {{ $total > 0 ? number_format($total, 2) : '—' }}
                                    </span>
                                </td>
                            @else
                                <td class="text-center py-3 px-4" style="background-color: #f3f4f6;">
                                    <span class="total-cell font-extrabold text-lg text-gray-700" data-candidate="{{ $candidate->id }}">
                                        {{ $total > 0 ? number_format($total, 2) : '—' }}
                                    </span>
                                </td>
                            @endif

                            {{-- Rank --}}
                            <td class="text-center py-3 px-4" style="background-color: #fffbeb;">
                                <span class="rank-cell" data-candidate="{{ $candidate->id }}">
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
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

{{-- Legend --}}
<div class="mt-2 flex flex-wrap items-center gap-4 text-xs text-[var(--text-muted)]">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400 inline-block"></span> 1st Place</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-300 inline-block"></span> 2nd Place</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span> 3rd Place</span>
    <span>· Rankings are <strong>per gender group</strong>. Scores 1–10. Auto-saves on change.</span>
</div>

@endif
@endsection

@push('scripts')
<script>
(function () {
    const SAVE_URL = "{{ route('admin.fitness.save-score') }}";
    const CSRF    = "{{ csrf_token() }}";

    function debounce(fn, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => fn.apply(this, args), delay);
        };
    }

    function showToast() {
        const toast = document.getElementById('save-toast');
        toast.classList.remove('hidden');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(() => toast.classList.add('hidden'), 2000);
    }

    function rankBadge(r) {
        if (!r || r <= 0) return `<span class="text-gray-400 font-medium">—</span>`;
        if (r === 1) return `<span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-black shadow-md" style="background-color: #f59e0b;" title="1st Place">🥇</span>`;
        if (r === 2) return `<span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-700 text-sm font-black shadow-md" style="background-color: #d1d5db;" title="2nd Place">🥈</span>`;
        if (r === 3) return `<span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-white text-sm font-black shadow-md" style="background-color: #fb923c;" title="3rd Place">🥉</span>`;
        return `<span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-gray-100 border border-gray-300 text-gray-600 text-xs font-bold">${r}</span>`;
    }

    function recalculate() {
        document.querySelectorAll('[id$="-table"]').forEach(table => {
            const rows = table.querySelectorAll('tbody tr');
            const totals = {};

            rows.forEach(row => {
                const cid = row.dataset.candidateId;
                let sum = 0;
                row.querySelectorAll('.score-input').forEach(inp => {
                    const v = parseFloat(inp.value);
                    if (!isNaN(v)) sum += v;
                });
                totals[cid] = sum;

                const totalEl = row.querySelector('.total-cell');
                if (totalEl) totalEl.textContent = sum > 0 ? sum.toFixed(2) : '—';
            });

            // Rank within this table only for candidates with sum > 0
            const scored = Object.entries(totals).filter(([cid, total]) => total > 0).sort((a, b) => b[1] - a[1]);
            const rankMap = {};
            let rank = 1;
            for (let i = 0; i < scored.length; i++) {
                const [cid, total] = scored[i];
                if (i > 0 && total === scored[i - 1][1]) {
                    rankMap[cid] = rankMap[scored[i - 1][0]];
                } else {
                    rankMap[cid] = rank;
                }
                rank++;
            }

            rows.forEach(row => {
                const cid = row.dataset.candidateId;
                const rankEl = row.querySelector('.rank-cell');
                if (rankEl) rankEl.innerHTML = rankBadge(rankMap[cid] || 0);
            });
        });
    }

    const saveScore = debounce(function(input) {
        const score = parseFloat(input.value);
        if (isNaN(score) || score < 1 || score > 10) return;

        input.classList.add('border-green-400', 'bg-green-50');

        fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                candidate_id: input.dataset.candidate,
                judge_id: input.dataset.judge,
                score: score,
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showToast();
                recalculate();
                setTimeout(() => input.classList.remove('border-green-400', 'bg-green-50'), 1500);
            }
        })
        .catch(() => {
            input.classList.remove('border-green-400', 'bg-green-50');
            input.classList.add('border-red-400', 'bg-red-50');
            setTimeout(() => input.classList.remove('border-red-400', 'bg-red-50'), 2000);
        });
    }, 600);

    document.querySelectorAll('.score-input').forEach(input => {
        input.addEventListener('input', function () {
            recalculate();
            saveScore(this);
        });

        input.addEventListener('blur', function () {
            const v = parseFloat(this.value);
            if (this.value !== '' && (isNaN(v) || v < 1 || v > 10)) {
                this.value = '';
                recalculate();
            }
        });
    });
})();
</script>
@endpush
