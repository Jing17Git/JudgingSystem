@extends('layouts.judge')

@section('title', 'Judge Dashboard')

@section('content')
{{-- Header --}}
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Welcome, {{ auth()->user()->name }}!
        </h1>
        <p class="page-subtitle">Judge Panel — Evaluate candidates and submit scores across assigned categories</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 border border-green-200">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
            Active Scoring Session
        </span>
    </div>
</div>

{{-- Top 4 Overview Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    {{-- Assigned Categories --}}
    <div class="panel p-5 animate-fade-in-up hover:border-green-300 transition-all shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Assigned Categories</span>
            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>
        <div class="text-3xl font-black text-gray-900 mb-1">{{ $totalAssignedCategories }}</div>
        <p class="text-xs text-[var(--text-muted)]">Production, Fitness, Traditional, Indigenous</p>
    </div>

    {{-- Total Candidates --}}
    <div class="panel p-5 animate-fade-in-up delay-100 hover:border-green-300 transition-all shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Total Candidates</span>
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-3xl font-black text-gray-900 mb-1">{{ $totalCandidates }}</div>
        <p class="text-xs text-[var(--text-muted)]">
            <span class="text-blue-600 font-semibold">{{ $maleCandidatesCount }} Male</span> &nbsp;·&nbsp;
            <span class="text-pink-600 font-semibold">{{ $femaleCandidatesCount }} Female</span>
        </p>
    </div>

    {{-- Submission Status --}}
    <div class="panel p-5 animate-fade-in-up delay-200 hover:border-green-300 transition-all shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Submission Status</span>
            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-3xl font-black text-gray-900 mb-1">{{ $totalSubmittedScores }} / {{ $totalRequiredScores }}</div>
        <div class="w-full bg-gray-100 rounded-full h-2 mt-2 overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2 rounded-full transition-all duration-500" style="width: {{ $overallProgressPercent }}%;"></div>
        </div>
        <p class="text-xs text-[var(--text-muted)] mt-1.5 font-semibold text-right">{{ $overallProgressPercent }}% Complete</p>
    </div>

    {{-- Current Session --}}
    <div class="panel p-5 animate-fade-in-up delay-300 hover:border-green-300 transition-all shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Current Session</span>
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
        <div class="text-xl font-bold text-gray-900 mb-1">Pageant 2026</div>
        <span class="inline-block text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-700">Official Evaluation</span>
    </div>
</div>

{{-- Current Scoring Session Category Cards --}}
<div class="mb-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg>
        Current Scoring Session — Category Progress
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($categories as $cat)
            @php
                $pct = $totalCandidates > 0 ? round(($cat['submitted'] / $totalCandidates) * 100) : 0;
                $isFinished = $cat['submitted'] >= $totalCandidates && $totalCandidates > 0;
            @endphp
            <div class="panel p-6 border border-gray-200 hover:border-green-300 transition-all rounded-2xl shadow-sm bg-white animate-fade-in-up">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center text-white shadow-md">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $cat['icon'] }}"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-gray-900">{{ $cat['name'] }}</h3>
                            <p class="text-xs text-[var(--text-muted)]">Category Evaluation (1–10)</p>
                        </div>
                    </div>
                    @if($isFinished)
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-green-100 text-green-700 border border-green-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Completed
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-amber-100 text-amber-700 border border-amber-200">
                            In Progress
                        </span>
                    @endif
                </div>

                {{-- Progress Info --}}
                <div class="mb-4">
                    <div class="flex justify-between text-xs font-semibold mb-1">
                        <span class="text-gray-600">Scored Candidates</span>
                        <span class="text-gray-900">{{ $cat['submitted'] }} / {{ $totalCandidates }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $pct }}%;"></div>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="pt-2 flex justify-end">
                    <a href="{{ route($cat['route']) }}" class="btn btn-green btn-md w-full sm:w-auto flex items-center justify-center gap-2 font-bold">
                        <span>{{ $cat['submitted'] > 0 ? 'Continue Scoring' : 'Start Scoring' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
