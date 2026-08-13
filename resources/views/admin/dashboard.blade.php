@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div x-data="dashboardData()" x-init="startPolling()">
    {{-- Page header --}}
    <div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="page-title flex items-center gap-3">
                <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </h1>
            <p class="page-subtitle">Real-time overview of your judging system</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2 text-xs text-[var(--text-muted)]">
                <div class="live-dot"></div>
                <span>Live Updates</span>
            </div>
            @if($activePageant)
                <span class="badge badge-green">{{ $activePageant->name }}</span>
            @endif
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        {{-- Total Candidates --}}
        <div class="stat-card animate-fade-in-up delay-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Total Candidates</p>
                    <p class="stat-value mt-2 text-[var(--text-primary)]" x-text="stats.total_candidates">{{ $totalCandidates }}</p>
                </div>
                <div class="stat-icon bg-blue-500/15 text-blue-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Judges --}}
        <div class="stat-card animate-fade-in-up delay-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Total Judges</p>
                    <p class="stat-value mt-2 text-[var(--text-primary)]" x-text="stats.total_judges">{{ $totalJudges }}</p>
                </div>
                <div class="stat-icon bg-purple-500/15 text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Scores Submitted --}}
        <div class="stat-card animate-fade-in-up delay-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Scores Submitted</p>
                    <p class="stat-value mt-2 text-[var(--green-600)]" x-text="stats.total_scores">{{ $totalScores }}</p>
                </div>
                <div class="stat-icon bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Leading Candidate --}}
        <div class="stat-card animate-fade-in-up delay-400 animate-pulse-glow">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Leading Candidate</p>
                    @if($leadingCandidate)
                        <p class="stat-value mt-2 text-lg text-[var(--green-500)]" x-text="stats.leading_candidate ? stats.leading_candidate.name : 'N/A'">
                            {{ $leadingCandidate['name'] }}
                        </p>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            #<span x-text="stats.leading_candidate ? stats.leading_candidate.candidate_number : ''">{{ $leadingCandidate['candidate_number'] }}</span>
                            · Score: <span class="text-[var(--green-600)]" x-text="stats.leading_candidate ? stats.leading_candidate.total_score : ''">{{ number_format($leadingCandidate['total_score'], 2) }}</span>
                        </p>
                    @else
                        <p class="stat-value mt-2 text-lg text-[var(--text-muted)]" x-text="stats.leading_candidate ? stats.leading_candidate.name : 'No scores yet'">No scores yet</p>
                    @endif
                </div>
                <div class="stat-icon bg-gradient-to-br from-green-100 to-emerald-100 text-green-600">
                    👑
                </div>
            </div>
        </div>
    </div>

    {{-- Submission Progress --}}
    <div class="panel mb-8 animate-fade-in-up delay-500">
        <div class="panel-header">
            <h2 class="panel-title flex items-center gap-2">
                <svg class="w-5 h-5 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Submission Progress
            </h2>
            <span class="text-sm font-semibold text-[var(--green-600)]" x-text="stats.submission_progress + '%'">{{ $submissionProgress }}%</span>
        </div>
        <div class="panel-body">
            <div class="progress-bar">
                <div class="progress-fill" :style="'width: ' + stats.submission_progress + '%'" style="width: {{ $submissionProgress }}%"></div>
            </div>
            <p class="text-xs text-[var(--text-muted)] mt-2">Overall scoring completion across all judges and candidates</p>
        </div>
    </div>

    {{-- Main content grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        {{-- Overall Rankings --}}
        <div class="xl:col-span-2 panel animate-fade-in-up delay-200">
            <div class="panel-header">
                <h2 class="panel-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3l3.5 7L12 6l3.5 4L19 3"/>
                    </svg>
                    Overall Rankings
                </h2>
                <div class="flex items-center gap-2">
                    <div class="live-dot"></div>
                    <span class="text-xs text-[var(--text-muted)]">Live</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 60px">Rank</th>
                            <th style="width: 60px">#</th>
                            <th>Candidate</th>
                            <th style="width: 120px" class="text-right">Total Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(ranking, index) in rankings" :key="ranking.candidate_id">
                            <tr class="transition-all duration-300">
                                <td>
                                    <div :class="'rank-badge ' + (ranking.rank <= 3 ? 'rank-' + ranking.rank : 'rank-default')" x-text="ranking.rank"></div>
                                </td>
                                <td class="text-[var(--text-muted)]" x-text="ranking.candidate_number"></td>
                                <td class="font-medium" x-text="ranking.candidate_name"></td>
                                <td class="text-right">
                                    <span class="font-semibold text-[var(--green-600)]" x-text="parseFloat(ranking.total_score).toFixed(2)"></span>
                                </td>
                            </tr>
                        </template>
                        <template x-if="rankings.length === 0">
                            <tr>
                                <td colspan="4" class="text-center text-[var(--text-muted)] py-8">No rankings available yet</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Category Leaders --}}
        <div class="panel animate-fade-in-up delay-300">
            <div class="panel-header">
                <h2 class="panel-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    Category Leaders
                </h2>
            </div>
            <div class="panel-body space-y-4">
                <template x-for="leader in categoryLeaders" :key="leader.category_name">
                    <div class="leader-card">
                        <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider font-semibold mb-2" x-text="leader.category_name"></p>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-[var(--text-primary)]" x-text="leader.leader.candidate_name"></p>
                                <p class="text-xs text-[var(--text-muted)]">Candidate #<span x-text="leader.leader.candidate_number"></span></p>
                            </div>
                            <span class="text-lg font-bold text-[var(--green-600)]" x-text="parseFloat(leader.leader.category_score).toFixed(2)"></span>
                        </div>
                    </div>
                </template>
                <template x-if="categoryLeaders.length === 0">
                    <p class="text-center text-[var(--text-muted)] py-6 text-sm">No category leaders yet</p>
                </template>
            </div>
        </div>
    </div>

    {{-- Bottom grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Recent Score Submissions --}}
        <div class="panel animate-fade-in-up delay-400">
            <div class="panel-header">
                <h2 class="panel-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Recent Score Submissions
                </h2>
                <div class="flex items-center gap-2">
                    <div class="live-dot"></div>
                    <span class="text-xs text-[var(--text-muted)]">Real-time</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Judge</th>
                            <th>Candidate</th>
                            <th>Category</th>
                            <th class="text-right">Score</th>
                            <th class="text-right">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(score, index) in recentScores" :key="index">
                            <tr>
                                <td class="text-[var(--text-secondary)]" x-text="score.judge_name"></td>
                                <td class="font-medium">
                                    <span class="text-[var(--text-muted)]">#<span x-text="score.candidate_number"></span></span>
                                    <span x-text="score.candidate_name"></span>
                                </td>
                                <td><span class="badge badge-green" x-text="score.category_name"></span></td>
                                <td class="text-right font-semibold text-[var(--green-600)]">
                                    <span x-text="score.score"></span>/<span class="text-[var(--text-muted)]" x-text="score.max_score"></span>
                                </td>
                                <td class="text-right text-xs text-[var(--text-muted)]" x-text="score.submitted_at"></td>
                            </tr>
                        </template>
                        <template x-if="recentScores.length === 0">
                            <tr>
                                <td colspan="5" class="text-center text-[var(--text-muted)] py-8">No scores submitted yet</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Judge Progress --}}
        <div class="panel animate-fade-in-up delay-500">
            <div class="panel-header">
                <h2 class="panel-title flex items-center gap-2">
                    <svg class="w-5 h-5 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/>
                    </svg>
                    Judge Submission Progress
                </h2>
            </div>
            <div class="panel-body space-y-5">
                <template x-for="judge in judgeProgress" :key="judge.judge_id">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-[var(--text-primary)]" x-text="judge.judge_name"></span>
                            <span class="text-xs text-[var(--text-muted)]">
                                <span x-text="judge.submitted"></span>/<span x-text="judge.expected"></span>
                                (<span class="text-[var(--green-600)]" x-text="judge.percentage + '%'"></span>)
                            </span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" :style="'width: ' + judge.percentage + '%'"
                                 :class="judge.percentage >= 100 ? 'bg-green-500!' : ''"></div>
                        </div>
                    </div>
                </template>
                <template x-if="judgeProgress.length === 0">
                    <p class="text-center text-[var(--text-muted)] py-6 text-sm">No judges assigned yet</p>
                </template>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboardData() {
    return {
        stats: {
            total_candidates: {{ $totalCandidates }},
            total_judges: {{ $totalJudges }},
            total_scores: {{ $totalScores }},
            submission_progress: {{ $submissionProgress }},
            leading_candidate: @json($leadingCandidate),
        },
        rankings: @json($overallRankings),
        categoryLeaders: @json($categoryLeaders),
        recentScores: @json($recentScores),
        judgeProgress: @json($judgeProgress),
        pollInterval: null,

        startPolling() {
            this.pollInterval = setInterval(() => this.fetchLiveData(), 5000);
        },

        async fetchLiveData() {
            try {
                const pageantId = @json($activePageant?->id);
                const url = '{{ route("admin.dashboard.live-stats") }}' + (pageantId ? '?pageant_id=' + pageantId : '');
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) return;
                const data = await response.json();

                this.stats.total_candidates = data.total_candidates;
                this.stats.total_judges = data.total_judges;
                this.stats.total_scores = data.total_scores;
                this.stats.submission_progress = data.submission_progress;
                this.stats.leading_candidate = data.leading_candidate;
                this.rankings = data.overall_rankings;
                this.categoryLeaders = data.category_leaders;
                this.recentScores = data.recent_scores;
                this.judgeProgress = data.judge_progress;
            } catch (error) {
                console.error('Failed to fetch live data:', error);
            }
        },

        destroy() {
            if (this.pollInterval) clearInterval(this.pollInterval);
        }
    };
}
</script>
@endpush
