@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div x-data="dashboardData()" x-init="startPolling()">
    {{-- Page header --}}
    <div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="page-title flex items-center gap-3">
                <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Admin Dashboard
            </h1>
            <p class="page-subtitle">Real-time tabulation overview, judging progress, and category rankings</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="fetchLiveData()" class="btn btn-outline btn-sm flex items-center gap-1.5 text-xs font-semibold text-[var(--text-muted)] hover:text-emerald-700">
                <svg class="w-3.5 h-3.5" :class="{ 'animate-spin': isRefreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
            <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] bg-[var(--bg-card)] border border-[var(--border-default)] px-3 py-1.5 rounded-xl shadow-sm">
                <div class="live-dot"></div>
                <span class="font-medium">Live Feed</span>
            </div>
        </div>
    </div>

    {{-- Top Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
        {{-- Total Candidates Card --}}
        <div class="stat-card animate-fade-in-up">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Total Candidates</p>
                    <p class="stat-value mt-2 text-[var(--text-primary)]" x-text="stats.total_candidates">{{ $totalCandidates }}</p>
                    <div class="flex items-center gap-3 mt-2 text-xs">
                        <span class="inline-flex items-center gap-1 text-blue-600 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            <span x-text="stats.total_male_candidates">{{ $totalMaleCandidates }}</span> Male
                        </span>
                        <span class="inline-flex items-center gap-1 text-pink-600 font-semibold">
                            <span class="w-2 h-2 rounded-full bg-pink-500"></span>
                            <span x-text="stats.total_female_candidates">{{ $totalFemaleCandidates }}</span> Female
                        </span>
                    </div>
                </div>
                <div class="stat-icon bg-blue-50 text-blue-600 border border-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Judges Card --}}
        <div class="stat-card animate-fade-in-up delay-100">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Active Judges</p>
                    <p class="stat-value mt-2 text-[var(--text-primary)]" x-text="stats.total_judges">{{ $totalJudges }}</p>
                    <p class="text-xs text-[var(--text-muted)] mt-2 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Assigned to all categories
                    </p>
                </div>
                <div class="stat-icon bg-purple-50 text-purple-600 border border-purple-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Scores Submitted Card --}}
        <div class="stat-card animate-fade-in-up delay-200">
            <div class="flex items-start justify-between">
                <div>
                    <p class="stat-label">Scores Submitted</p>
                    <p class="stat-value mt-2 text-[var(--green-700)]" x-text="stats.total_scores">{{ $totalScores }}</p>
                    <p class="text-xs text-[var(--text-muted)] mt-2">
                        <span class="font-bold text-[var(--text-primary)]" x-text="stats.submission_progress + '%'">{{ $submissionProgress }}%</span> of expected entries
                    </p>
                </div>
                <div class="stat-icon bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Division Leaders Card --}}
        <div class="stat-card animate-fade-in-up delay-300">
            <div class="flex items-start justify-between">
                <div class="w-full">
                    <p class="stat-label">Current Leaders</p>
                    <div class="mt-2 space-y-1.5">
                        {{-- Male Leader --}}
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-blue-700 font-bold flex items-center gap-1">
                                ♂ <span x-text="stats.leading_male ? stats.leading_male.candidate_name : 'No scores'">{{ $leadingMale['candidate_name'] ?? 'No scores' }}</span>
                            </span>
                            <span class="font-mono font-bold text-blue-800" x-text="stats.leading_male ? Number(stats.leading_male.total_score).toFixed(2) : '—'">
                                {{ isset($leadingMale['total_score']) ? number_format($leadingMale['total_score'], 2) : '—' }}
                            </span>
                        </div>
                        {{-- Female Leader --}}
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-pink-700 font-bold flex items-center gap-1">
                                ♀ <span x-text="stats.leading_female ? stats.leading_female.candidate_name : 'No scores'">{{ $leadingFemale['candidate_name'] ?? 'No scores' }}</span>
                            </span>
                            <span class="font-mono font-bold text-pink-800" x-text="stats.leading_female ? Number(stats.leading_female.total_score).toFixed(2) : '—'">
                                {{ isset($leadingFemale['total_score']) ? number_format($leadingFemale['total_score'], 2) : '—' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="stat-icon bg-amber-50 text-amber-600 border border-amber-200 ml-3">
                    👑
                </div>
            </div>
        </div>
    </div>

    {{-- Category Progress & Completion Overview --}}
    <div class="panel mb-8 animate-fade-in-up delay-200 border border-[var(--border-default)] shadow-sm rounded-xl p-5 bg-[var(--bg-card)]">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-bold text-[var(--text-primary)] flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Category Submission Completion
            </h2>
            <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full" x-text="'Overall: ' + stats.submission_progress + '%'">
                Overall: {{ $submissionProgress }}%
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <template x-for="(cat, key) in categoryProgress" :key="key">
                <div class="p-3.5 rounded-xl border border-gray-100 bg-gray-50/50">
                    <div class="flex items-center justify-between text-xs font-semibold text-gray-700 mb-1.5">
                        <span x-text="cat.name"></span>
                        <span class="font-bold font-mono" x-text="cat.percentage + '%'"></span>
                    </div>
                    <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full transition-all duration-500 rounded-full"
                             :class="cat.color"
                             :style="'width: ' + cat.percentage + '%'"></div>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-[var(--text-muted)] mt-1.5 font-mono">
                        <span><span x-text="cat.count"></span>/<span x-text="cat.expected"></span> votes</span>
                        <span x-text="cat.weight + '% wt.'"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- Main Content Grid: Rankings & Category Leaders --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
        {{-- Overall Tabulation Rankings with Division Tabs --}}
        <div class="xl:col-span-2 panel border border-[var(--border-default)] shadow-sm rounded-xl overflow-hidden">
            <div class="panel-header flex flex-wrap items-center justify-between gap-3 p-4 border-b border-[var(--border-default)] bg-[var(--bg-card)]">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <h2 class="font-bold text-sm text-[var(--text-primary)]">Leaderboard Rankings</h2>
                </div>

                {{-- Division Filter Tabs --}}
                <div class="flex items-center gap-1 bg-gray-100 p-1 rounded-xl text-xs font-semibold">
                    <button type="button"
                            @click="rankTab = 'all'"
                            :class="rankTab === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600 hover:text-gray-900'"
                            class="px-3 py-1 rounded-lg transition-all">
                        All (<span x-text="rankings.length"></span>)
                    </button>
                    <button type="button"
                            @click="rankTab = 'Male'"
                            :class="rankTab === 'Male' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:text-blue-600'"
                            class="px-3 py-1 rounded-lg transition-all">
                        ♂ Male
                    </button>
                    <button type="button"
                            @click="rankTab = 'Female'"
                            :class="rankTab === 'Female' ? 'bg-pink-600 text-white shadow-sm' : 'text-gray-600 hover:text-pink-600'"
                            class="px-3 py-1 rounded-lg transition-all">
                        ♀ Female
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table min-w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-2.5 px-3 text-center min-w-[50px] font-bold text-gray-700">Rank</th>
                            <th class="py-2.5 px-3 text-center min-w-[60px] font-bold text-gray-700">Cand. #</th>
                            <th class="py-2.5 px-3 text-left min-w-[160px] font-bold text-gray-700">Candidate</th>
                            <th class="py-2.5 px-3 text-center min-w-[70px] font-bold text-blue-800 bg-blue-50/40">Prod.</th>
                            <th class="py-2.5 px-3 text-center min-w-[70px] font-bold text-emerald-800 bg-emerald-50/40">Fit.</th>
                            <th class="py-2.5 px-3 text-center min-w-[70px] font-bold text-purple-800 bg-purple-50/40">Indig.</th>
                            <th class="py-2.5 px-3 text-center min-w-[70px] font-bold text-amber-800 bg-amber-50/40">Trad.</th>
                            <th class="py-2.5 px-3 text-center min-w-[90px] font-bold text-emerald-900 bg-emerald-100/50">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="r in filteredRankings" :key="r.candidate_id">
                            <tr class="hover:bg-gray-50/80 transition-colors">
                                <td class="py-2 px-3 text-center">
                                    <template x-if="r.rank === 1">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-400 text-white font-black shadow-sm text-xs">🥇</span>
                                    </template>
                                    <template x-if="r.rank === 2">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-300 text-gray-800 font-black shadow-sm text-xs">🥈</span>
                                    </template>
                                    <template x-if="r.rank === 3">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-600 text-white font-black shadow-sm text-xs">🥉</span>
                                    </template>
                                    <template x-if="r.rank !== 1 && r.rank !== 2 && r.rank !== 3">
                                        <span class="text-gray-500 font-bold font-mono" x-text="r.rank"></span>
                                    </template>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-xs font-bold shadow-sm"
                                          :style="r.gender === 'Male' ? 'background-color: #2563eb;' : 'background-color: #db2777;'"
                                          x-text="r.candidate_number"></span>
                                </td>
                                <td class="py-2 px-3">
                                    <div class="flex items-center gap-2">
                                        <template x-if="r.photo_url">
                                            <img :src="'/storage/' + r.photo_url" class="w-6 h-6 rounded-full object-cover border border-gray-200">
                                        </template>
                                        <div>
                                            <span class="font-semibold text-gray-900 block truncate max-w-[130px]" x-text="r.candidate_name"></span>
                                            <span class="text-[10px] text-gray-400 block" x-text="r.origin || ''"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 px-3 text-center font-mono font-semibold text-blue-900" x-text="r.production_avg > 0 ? Number(r.production_avg).toFixed(1) : '—'"></td>
                                <td class="py-2 px-3 text-center font-mono font-semibold text-emerald-900" x-text="r.fitness_avg > 0 ? Number(r.fitness_avg).toFixed(1) : '—'"></td>
                                <td class="py-2 px-3 text-center font-mono font-semibold text-purple-900" x-text="r.indigenous_avg > 0 ? Number(r.indigenous_avg).toFixed(1) : '—'"></td>
                                <td class="py-2 px-3 text-center font-mono font-semibold text-amber-900" x-text="r.traditional_avg > 0 ? Number(r.traditional_avg).toFixed(1) : '—'"></td>
                                <td class="py-2 px-3 text-center font-mono font-black text-emerald-900 bg-emerald-50/30 text-sm" x-text="r.total_score > 0 ? Number(r.total_score).toFixed(2) : '—'"></td>
                            </tr>
                        </template>
                        <template x-if="filteredRankings.length === 0">
                            <tr>
                                <td colspan="8" class="text-center text-gray-400 py-8">No candidates found for this division</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Category Leaders Cards --}}
        <div class="panel border border-[var(--border-default)] shadow-sm rounded-xl p-4 bg-[var(--bg-card)]">
            <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <h2 class="font-bold text-sm text-[var(--text-primary)]">Category Top Scorers</h2>
            </div>

            <div class="space-y-3">
                <template x-for="leader in categoryLeaders" :key="leader.category_key">
                    <div class="p-3 rounded-xl border transition-all hover:shadow-sm" :class="leader.bg">
                        <div class="flex items-center justify-between text-[11px] font-bold uppercase tracking-wider mb-1" :class="leader.color">
                            <span x-text="leader.category_name"></span>
                            <span class="font-mono text-xs" x-text="'Avg: ' + Number(leader.score).toFixed(1)"></span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-white text-[11px] font-bold shadow-xs"
                                  :style="leader.candidate_gender === 'Male' ? 'background-color: #2563eb;' : 'background-color: #db2777;'"
                                  x-text="'#' + leader.candidate_number"></span>
                            <span class="font-semibold text-gray-900 text-xs truncate" x-text="leader.candidate_name"></span>
                        </div>
                    </div>
                </template>
                <template x-if="categoryLeaders.length === 0">
                    <p class="text-xs text-gray-400 text-center py-6">Scores are still being submitted.</p>
                </template>
            </div>
        </div>
    </div>

    {{-- Bottom Grid: Recent Score Feed & Judge Submission Progress --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Recent Scores Stream --}}
        <div class="panel border border-[var(--border-default)] shadow-sm rounded-xl overflow-hidden">
            <div class="panel-header p-4 border-b border-[var(--border-default)] bg-[var(--bg-card)] flex items-center justify-between">
                <h2 class="font-bold text-sm text-[var(--text-primary)] flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Recent Judge Submissions
                </h2>
                <span class="text-xs text-[var(--text-muted)]">Live Stream</span>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table min-w-full text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="py-2.5 px-3 text-left font-bold text-gray-600">Judge</th>
                            <th class="py-2.5 px-3 text-left font-bold text-gray-600">Candidate</th>
                            <th class="py-2.5 px-3 text-center font-bold text-gray-600">Category</th>
                            <th class="py-2.5 px-3 text-center font-bold text-gray-600">Score</th>
                            <th class="py-2.5 px-3 text-right font-bold text-gray-600">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="(s, idx) in recentScores" :key="idx">
                            <tr class="hover:bg-gray-50">
                                <td class="py-2 px-3 font-semibold text-gray-800" x-text="s.judge_name"></td>
                                <td class="py-2 px-3">
                                    <span class="font-bold mr-1" :class="s.candidate_gender === 'Male' ? 'text-blue-600' : 'text-pink-600'" x-text="'#' + s.candidate_number"></span>
                                    <span class="text-gray-700" x-text="s.candidate_name"></span>
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <span class="badge badge-green text-[10px] font-bold" x-text="s.category_name"></span>
                                </td>
                                <td class="py-2 px-3 text-center font-mono font-bold text-emerald-800" x-text="s.score + '/' + s.max_score"></td>
                                <td class="py-2 px-3 text-right text-gray-400 text-[11px]" x-text="s.submitted_at"></td>
                            </tr>
                        </template>
                        <template x-if="recentScores.length === 0">
                            <tr>
                                <td colspan="5" class="text-center text-gray-400 py-6">No scores recorded yet</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Judge Progress Progress Bars --}}
        <div class="panel border border-[var(--border-default)] shadow-sm rounded-xl p-4 bg-[var(--bg-card)]">
            <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                <h2 class="font-bold text-sm text-[var(--text-primary)] flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Judge Submission Completion
                </h2>
                <span class="text-xs text-[var(--text-muted)]" x-text="judgeProgress.length + ' Active Judges'"></span>
            </div>

            <div class="space-y-4">
                <template x-for="j in judgeProgress" :key="j.judge_id">
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold text-gray-700 mb-1">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full" :class="j.percentage >= 100 ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                                <span x-text="j.judge_name"></span>
                            </span>
                            <span class="font-mono text-gray-600">
                                <span x-text="j.submitted"></span>/<span x-text="j.expected"></span>
                                (<span class="font-bold text-emerald-700" x-text="j.percentage + '%'"></span>)
                            </span>
                        </div>
                        <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" :style="'width: ' + j.percentage + '%'"></div>
                        </div>
                    </div>
                </template>
                <template x-if="judgeProgress.length === 0">
                    <p class="text-xs text-gray-400 text-center py-6">No active judges configured.</p>
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
        rankTab: 'all',
        isRefreshing: false,
        stats: {
            total_candidates: {{ $totalCandidates }},
            total_male_candidates: {{ $totalMaleCandidates }},
            total_female_candidates: {{ $totalFemaleCandidates }},
            total_judges: {{ $totalJudges }},
            total_scores: {{ $totalScores }},
            submission_progress: {{ $submissionProgress }},
            leading_male: @json($leadingMale),
            leading_female: @json($leadingFemale),
            leading_candidate: @json($leadingCandidate),
        },
        categoryProgress: @json($categoryProgress),
        rankings: @json($overallRankings),
        categoryLeaders: @json($categoryLeaders),
        judgeProgress: @json($judgeProgress),
        recentScores: @json($recentScores),
        pollInterval: null,

        get filteredRankings() {
            if (this.rankTab === 'all') return this.rankings;
            return this.rankings.filter(r => r.gender === this.rankTab);
        },

        startPolling() {
            this.pollInterval = setInterval(() => this.fetchLiveData(), 5000);
        },

        async fetchLiveData() {
            this.isRefreshing = true;
            try {
                const response = await fetch('{{ route("admin.dashboard.live-stats") }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });

                if (!response.ok) return;
                const data = await response.json();

                this.stats = {
                    total_candidates: data.totalCandidates,
                    total_male_candidates: data.totalMaleCandidates,
                    total_female_candidates: data.totalFemaleCandidates,
                    total_judges: data.totalJudges,
                    total_scores: data.totalScores,
                    submission_progress: data.submissionProgress,
                    leading_male: data.leadingMale,
                    leading_female: data.leadingFemale,
                    leading_candidate: data.leadingCandidate,
                };
                this.categoryProgress = data.categoryProgress;
                this.rankings = data.overallRankings;
                this.categoryLeaders = data.categoryLeaders;
                this.judgeProgress = data.judgeProgress;
                this.recentScores = data.recentScores;
            } catch (error) {
                console.error('Failed to fetch dashboard updates:', error);
            } finally {
                setTimeout(() => { this.isRefreshing = false; }, 400);
            }
        },

        destroy() {
            if (this.pollInterval) clearInterval(this.pollInterval);
        }
    };
}
</script>
@endpush
