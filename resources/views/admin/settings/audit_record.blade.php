@extends('layouts.admin')

@section('title', 'Audit Management & Security Threat Engine')

@section('content')
<div x-data="{
    autoRefresh: false,
    refreshInterval: null,
    historyModalOpen: false,
    historyCandidateName: '',
    historyCandidateId: null,
    historyRecords: [],
    historyLoading: false,
    noteModalOpen: false,
    noteRecordId: null,
    noteActionUrl: '',
    toggleAutoRefresh() {
        this.autoRefresh = !this.autoRefresh;
        if (this.autoRefresh) {
            this.refreshInterval = setInterval(() => {
                window.location.reload();
            }, 10000);
        } else {
            clearInterval(this.refreshInterval);
        }
    },
    openHistoryModal(candidateId, candidateName) {
        this.historyCandidateId = candidateId;
        this.historyCandidateName = candidateName;
        this.historyModalOpen = true;
        this.historyLoading = true;
        this.historyRecords = [];

        fetch('{{ route("admin.settings.audit_record.candidate_history") }}?candidate_id=' + candidateId)
            .then(res => res.json())
            .then(data => {
                this.historyRecords = data.records || [];
                this.historyLoading = false;
            })
            .catch(err => {
                this.historyLoading = false;
            });
    },
    openNoteModal(recordId, actionUrl) {
        this.noteRecordId = recordId;
        this.noteActionUrl = actionUrl;
        this.noteModalOpen = true;
    }
}">

    {{-- Page Header --}}
    <div class="page-header flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
                <a href="{{ route('admin.settings.index') }}" class="hover:text-[var(--green-600)] transition-colors">Settings</a>
                <span>/</span>
                <span class="text-[var(--green-700)] font-semibold">Audit Management</span>
            </div>
            <h1 class="page-title flex items-center gap-3">
                <svg class="w-7 h-7 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Audit Management &amp; Security Threat Engine
            </h1>
            <p class="page-subtitle">Track judge score recordings, score resets, automated anomaly alerts, and threat indicators in real-time.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Auto-refresh toggle button --}}
            <button @click="toggleAutoRefresh()"
                    :class="autoRefresh ? 'bg-emerald-600 text-white border-emerald-600 shadow-md ring-2 ring-emerald-300' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                    class="btn text-xs font-semibold px-3.5 py-2 rounded-xl border flex items-center gap-2 transition-all">
                <span class="w-2.5 h-2.5 rounded-full" :class="autoRefresh ? 'bg-white animate-pulse' : 'bg-gray-400'"></span>
                <span x-text="autoRefresh ? 'Live Monitoring ON (10s)' : 'Enable Live Auto-Refresh'"></span>
            </button>

            {{-- Export CSV --}}
            <a href="{{ route('admin.settings.audit_record.export') }}" class="btn btn-outline flex items-center gap-2 text-xs font-semibold shadow-sm bg-white hover:bg-emerald-50">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </a>

            {{-- Clear Audit History --}}
            <form action="{{ route('admin.settings.audit_record.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all audit records? This action cannot be undone.');" class="inline">
                @csrf
                <button type="submit" class="btn btn-outline text-rose-600 hover:bg-rose-50 border-rose-200 text-xs font-semibold flex items-center gap-1.5 shadow-sm bg-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Clear History
                </button>
            </form>
        </div>
    </div>

    {{-- Navigation Tabs between Settings Sections --}}
    <div class="flex flex-nowrap overflow-x-auto items-center gap-2 mb-6 border-b border-[var(--border-default)] pb-3 hide-scrollbar">
        <a href="{{ route('admin.settings.preliminary') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Preliminary Criteria
        </a>
        <a href="{{ route('admin.settings.final') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Final Criteria
        </a>
        <a href="{{ route('admin.settings.judge-scores') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Judge Score Sheets
        </a>
        <a href="{{ route('admin.settings.audit_record') }}"
           class="px-4 py-2 rounded-xl text-sm font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-2 shadow-sm whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
            Audit Record
        </a>
        <a href="{{ route('admin.cache.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Cache Management
        </a>
        <a href="{{ route('admin.logs.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Logs Management
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 mb-6 text-sm flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Statistics Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <div class="panel p-4 border border-gray-200 shadow-sm rounded-xl bg-white">
            <p class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Audit Logs</p>
            <p class="text-2xl font-black text-gray-900 mt-1">{{ number_format($stats['total_records']) }}</p>
        </div>
        <div class="panel p-4 border border-gray-200 shadow-sm rounded-xl bg-white">
            <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Score Submissions</p>
            <p class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['score_submissions']) }}</p>
        </div>
        <div class="panel p-4 border border-gray-200 shadow-sm rounded-xl bg-white">
            <p class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Score Resets</p>
            <p class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['score_resets']) }}</p>
        </div>
        <div class="panel p-4 border border-amber-200 bg-amber-50/50 shadow-sm rounded-xl">
            <p class="text-[11px] font-bold text-amber-700 uppercase tracking-wider">Flagged Suspicious</p>
            <p class="text-2xl font-black text-amber-700 mt-1">{{ number_format($stats['flagged_suspicious']) }}</p>
        </div>
        <div class="panel p-4 border border-rose-200 bg-rose-50/50 shadow-sm rounded-xl">
            <p class="text-[11px] font-bold text-rose-700 uppercase tracking-wider">Critical Threats</p>
            <p class="text-2xl font-black text-rose-700 mt-1">{{ number_format($stats['critical_alerts']) }}</p>
        </div>
        <div class="panel p-4 border border-gray-200 shadow-sm rounded-xl bg-white">
            <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Judges Audited</p>
            <p class="text-2xl font-black text-indigo-600 mt-1">{{ number_format($stats['unique_judges']) }}</p>
        </div>
    </div>

    {{-- Filter & Search Panel --}}
    <div class="panel p-4 mb-6 border border-gray-200 shadow-sm rounded-xl bg-white">
        <form action="{{ route('admin.settings.audit_record') }}" method="GET" class="flex flex-col gap-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                {{-- Search --}}
                <div class="lg:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Search Audit Log</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search judge, candidate, IP, or reason..."
                           class="w-full px-3.5 py-2 text-xs sm:text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Category</label>
                    <select name="category" class="w-full text-xs sm:text-sm px-3.5 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="">All Categories</option>
                        <option value="production" {{ $categoryFilter === 'production' ? 'selected' : '' }}>Production</option>
                        <option value="fitness" {{ $categoryFilter === 'fitness' ? 'selected' : '' }}>Fitness</option>
                        <option value="traditional-attire" {{ $categoryFilter === 'traditional-attire' ? 'selected' : '' }}>Traditional Attire</option>
                        <option value="indigenous-attire" {{ $categoryFilter === 'indigenous-attire' ? 'selected' : '' }}>Indigenous Attire</option>
                        <option value="qa" {{ $categoryFilter === 'qa' ? 'selected' : '' }}>Final Q &amp; A</option>
                        <option value="system" {{ $categoryFilter === 'system' ? 'selected' : '' }}>System Event</option>
                    </select>
                </div>

                {{-- Threat / Risk Level --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Threat Level</label>
                    <select name="risk_level" class="w-full text-xs sm:text-sm px-3.5 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="">All Threat Levels</option>
                        <option value="normal" {{ $riskFilter === 'normal' ? 'selected' : '' }}>🟢 Normal</option>
                        <option value="warning" {{ $riskFilter === 'warning' ? 'selected' : '' }}>🟡 Warning Alert</option>
                        <option value="critical" {{ $riskFilter === 'critical' ? 'selected' : '' }}>🔴 Critical Threat</option>
                    </select>
                </div>

                {{-- Date Preset --}}
                <div>
                    <label class="block text-[11px] font-bold text-gray-500 uppercase mb-1">Date Range</label>
                    <select name="date_preset" class="w-full text-xs sm:text-sm px-3.5 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none bg-white">
                        <option value="all" {{ $datePreset === 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ $datePreset === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="7days" {{ $datePreset === '7days' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="30days" {{ $datePreset === '30days' ? 'selected' : '' }}>Last 30 Days</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-gray-100">
                {{-- Suspicious Only Toggle --}}
                <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-bold text-gray-700">
                    <input type="checkbox" name="suspicious_only" value="1" {{ $suspiciousOnly ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500 border-gray-300">
                    <span class="text-amber-800 font-extrabold flex items-center gap-1">
                        ⚠️ Show Only Suspicious &amp; Anomalous Activities
                    </span>
                </label>

                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-green text-xs font-semibold px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Filter Audit Logs
                    </button>
                    @if($search || $categoryFilter || $eventFilter || $riskFilter || $suspiciousOnly || $datePreset !== 'all')
                        <a href="{{ route('admin.settings.audit_record') }}" class="btn btn-outline text-xs font-semibold px-3 py-2 rounded-xl">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Main Audit Data Table --}}
    <div class="overflow-x-auto border border-gray-200 shadow-sm rounded-xl bg-white mb-6">
        @if($auditRecords->isEmpty())
            <div class="text-center py-14">
                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <p class="text-gray-400 font-medium text-sm">No audit records found matching your active filters.</p>
            </div>
        @else
            <table class="min-w-full border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-emerald-800 text-white border-b-2 border-emerald-900">
                        <th class="py-3.5 px-4 text-left text-xs font-bold uppercase tracking-wider w-[170px] border-r border-emerald-700/50">
                            Timestamp (Login/Logout)
                        </th>
                        <th class="py-3.5 px-4 text-left text-xs font-bold uppercase tracking-wider w-[180px] border-r border-emerald-700/50">
                            User Details
                        </th>
                        <th class="py-3.5 px-4 text-left text-xs font-bold uppercase tracking-wider w-[170px] border-r border-emerald-700/50">
                            Candidate Details
                        </th>
                        <th class="py-3.5 px-4 text-left text-xs font-bold uppercase tracking-wider w-[180px] border-r border-emerald-700/50">
                            Category &amp; Score
                        </th>
                        <th class="py-3.5 px-4 text-left text-xs font-bold uppercase tracking-wider border-r border-emerald-700/50">
                            Action Taken / Description
                        </th>
                        <th class="py-3.5 px-4 text-left text-xs font-bold uppercase tracking-wider w-[150px] border-r border-emerald-700/50">
                            Device &amp; IP Address
                        </th>
                        <th class="py-3.5 px-4 text-left text-xs font-bold uppercase tracking-wider w-[160px] border-r border-emerald-700/50">
                            Threat Indicator
                        </th>
                        <th class="py-3.5 px-4 text-center text-xs font-bold uppercase tracking-wider w-[120px]">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($auditRecords as $i => $record)
                        @php
                            $details = $record->details ? json_decode($record->details, true) : [];
                            $isEven  = $i % 2 === 0;

                            // Threat / Risk badge mapping
                            $riskBadgeClass = 'bg-emerald-100 text-emerald-800 border-emerald-300';
                            $riskIcon = '🟢';
                            $riskLabel = 'Normal';
                            if ($record->risk_level === 'critical') {
                                $riskBadgeClass = 'bg-rose-100 text-rose-800 border-rose-300 ring-2 ring-rose-300';
                                $riskIcon = '🔴';
                                $riskLabel = 'Critical Alert';
                            } elseif ($record->risk_level === 'warning' || $record->is_suspicious) {
                                $riskBadgeClass = 'bg-amber-100 text-amber-800 border-amber-300';
                                $riskIcon = '🟡';
                                $riskLabel = 'Warning Alert';
                            }

                            // Score Trend Arrow
                            $trendIcon = '➡️';
                            if ($record->old_score !== null && $record->new_score !== null) {
                                if ($record->new_score > $record->old_score) {
                                    $trendIcon = '⬆️';
                                } elseif ($record->new_score < $record->old_score) {
                                    $trendIcon = '⬇️';
                                }
                            }

                            // Score Color Badge Helper
                            $getScoreBadgeClass = function($scoreVal) {
                                if ($scoreVal === null) return 'bg-gray-100 text-gray-500 border-gray-200';
                                if ($scoreVal >= 9.0) return 'bg-emerald-100 text-emerald-800 border-emerald-300 font-black';
                                if ($scoreVal >= 6.0) return 'bg-amber-100 text-amber-800 border-amber-300 font-bold';
                                return 'bg-rose-100 text-rose-800 border-rose-300 font-black';
                            };
                        @endphp
                        <tr class="hover:bg-emerald-50/40 transition-colors {{ $record->is_suspicious ? 'bg-amber-50/40' : ($isEven ? 'bg-white' : 'bg-gray-50/50') }}">

                            {{-- Col 1: Timestamp --}}
                            <td class="py-3.5 px-4 align-top border-r border-gray-100">
                                <div class="flex flex-col gap-0.5">
                                    <span class="font-bold text-gray-900 text-xs font-mono">
                                        {{ $record->created_at->format('Y-m-d H:i:s') }}
                                    </span>
                                    <span class="text-[10px] text-gray-500 font-medium">
                                        {{ $record->created_at->format('h:i A') }} ({{ $record->created_at->diffForHumans() }})
                                    </span>
                                </div>
                            </td>

                            {{-- Col 2: User Details --}}
                            <td class="py-3.5 px-4 align-top border-r border-gray-100">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black flex-shrink-0
                                            {{ $record->user_role === 'admin' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                            {{ strtoupper(substr($record->user_name ?? 'S', 0, 1)) }}
                                        </span>
                                        <span class="font-bold text-gray-900 text-xs leading-tight">
                                            {{ $record->user_name ?? 'System' }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-gray-500 italic pl-8">
                                        Dept: <span class="capitalize font-semibold text-gray-700">{{ $record->user_role ?? 'system' }}</span>
                                    </span>
                                </div>
                            </td>

                            {{-- Col 3: Candidate Details --}}
                            <td class="py-3.5 px-4 align-top border-r border-gray-100">
                                @if($record->candidate_name)
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1.5">
                                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-100 text-blue-800 text-[10px] font-black flex-shrink-0">
                                                #{{ $record->candidate_number }}
                                            </span>
                                            <span class="font-bold text-gray-800 text-xs truncate max-w-[120px]">{{ $record->candidate_name }}</span>
                                        </div>
                                        @if($record->candidate_id)
                                            <button type="button"
                                                    @click="openHistoryModal({{ $record->candidate_id }}, '{{ addslashes($record->candidate_name) }}')"
                                                    class="text-[10px] text-blue-600 hover:text-blue-800 underline text-left font-semibold">
                                                View Candidate History
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs italic">— N/A —</span>
                                @endif
                            </td>

                            {{-- Col 4: Category & Score --}}
                            <td class="py-3.5 px-4 align-top border-r border-gray-100">
                                <div class="flex flex-col gap-1.5">
                                    <span class="font-bold text-xs text-gray-800">
                                        @if($record->category === 'production')      🎭 Production
                                        @elseif($record->category === 'fitness')     🏃 Fitness
                                        @elseif($record->category === 'traditional-attire') 👘 Traditional
                                        @elseif($record->category === 'indigenous-attire')  🌿 Indigenous
                                        @elseif($record->category === 'qa')          🎤 Q &amp; A
                                        @elseif($record->category === 'system')      ⚙️ System
                                        @elseif($record->category)                   {{ ucfirst(str_replace('-', ' ', $record->category)) }}
                                        @else                                        — General
                                        @endif
                                    </span>

                                    {{-- Score Display with Trend Arrow --}}
                                    @if($record->new_score !== null || $record->old_score !== null)
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if($record->old_score !== null)
                                                <span class="px-1.5 py-0.5 rounded text-[11px] border text-gray-400 line-through {{ $getScoreBadgeClass($record->old_score) }}">
                                                    {{ number_format($record->old_score, 1) }}
                                                </span>
                                                <span class="text-xs">{{ $trendIcon }}</span>
                                            @endif

                                            @if($record->new_score !== null)
                                                <span class="px-2 py-0.5 rounded text-xs border {{ $getScoreBadgeClass($record->new_score) }}">
                                                    {{ number_format($record->new_score, 1) }}
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded text-xs border bg-rose-50 text-rose-700 font-bold border-rose-200">
                                                    Reset (—)
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Col 5: Action Taken / Description --}}
                            <td class="py-3.5 px-4 align-top border-r border-gray-100">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs text-gray-800 font-semibold leading-snug">{{ $record->action_description }}</span>

                                    {{-- Suspicious reason alert box --}}
                                    @if($record->suspicious_reason)
                                        <div class="p-2 rounded-lg bg-amber-100/80 border border-amber-300 text-amber-900 text-[11px] font-bold flex items-start gap-1.5 mt-1">
                                            <span class="text-sm">⚠️</span>
                                            <span>{{ $record->suspicious_reason }}</span>
                                        </div>
                                    @endif

                                    {{-- Review notes if any --}}
                                    @if($record->review_notes)
                                        <div class="p-1.5 rounded bg-blue-50 border border-blue-200 text-blue-800 text-[10px] mt-1">
                                            <strong>Note by {{ $record->reviewer_name }}:</strong> {{ $record->review_notes }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Col 6: Device & IP Address --}}
                            <td class="py-3.5 px-4 align-top border-r border-gray-100">
                                <div class="flex flex-col gap-1 text-xs">
                                    <span class="font-mono text-gray-700 font-bold">IP: {{ $record->ip_address ?? '127.0.0.1' }}</span>
                                    <span class="text-[10px] text-gray-400 truncate max-w-[140px]" title="{{ $record->user_agent }}">
                                        {{ $record->user_agent ?? 'Browser/Web' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Col 7: Threat Indicator --}}
                            <td class="py-3.5 px-4 align-top border-r border-gray-100">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border {{ $riskBadgeClass }}">
                                        <span>{{ $riskIcon }}</span>
                                        <span>{{ $riskLabel }}</span>
                                    </span>
                                </div>
                            </td>

                            {{-- Col 8: Quick Actions --}}
                            <td class="py-3.5 px-4 align-top text-center">
                                <div class="flex flex-col items-center gap-1">
                                    {{-- Add Note Modal Trigger --}}
                                    <button type="button"
                                            @click="openNoteModal({{ $record->id }}, '{{ route('admin.settings.audit_record.review', $record->id) }}')"
                                            class="w-full px-2 py-1 text-[10px] font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg border border-gray-300 flex items-center justify-center gap-1">
                                        📝 Note
                                    </button>

                                    {{-- Toggle Flag Button --}}
                                    <form action="{{ route('admin.settings.audit_record.flag', $record->id) }}" method="POST" class="w-full">
                                        @csrf
                                        <button type="submit" class="w-full px-2 py-1 text-[10px] font-semibold {{ $record->is_suspicious ? 'bg-amber-100 text-amber-800 border-amber-300' : 'bg-gray-50 text-gray-600 border-gray-200' }} hover:bg-amber-200 rounded-lg border flex items-center justify-center gap-1">
                                            🚩 {{ $record->is_suspicious ? 'Unflag' : 'Flag' }}
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Pagination --}}
    @if($auditRecords->hasPages())
        <div class="mt-4">
            {{ $auditRecords->links() }}
        </div>
    @endif

    {{-- Candidate Score History Modal --}}
    <div x-show="historyModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="historyModalOpen = false" class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl border border-gray-200">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    Candidate Score History: <span x-text="historyCandidateName" class="text-blue-600"></span>
                </h3>
                <button @click="historyModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <div x-show="historyLoading" class="py-8 text-center text-gray-500 text-sm">
                Loading history trail...
            </div>

            <div x-show="!historyLoading && historyRecords.length === 0" class="py-8 text-center text-gray-400 text-sm">
                No score records found for this candidate.
            </div>

            <div x-show="!historyLoading && historyRecords.length > 0" class="max-h-[380px] overflow-y-auto space-y-2 pr-1">
                <template x-for="rec in historyRecords" :key="rec.id">
                    <div class="p-3 rounded-xl border border-gray-100 bg-gray-50 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-gray-900" x-text="rec.user_name"></span>
                            <span class="text-gray-400 italic">in <span x-text="rec.category"></span></span>
                            <p class="text-gray-600 text-[11px] mt-0.5" x-text="rec.action_description"></p>
                        </div>
                        <div class="text-right">
                            <span class="font-mono text-gray-500 text-[10px] block" x-text="rec.created_at"></span>
                            <span :class="rec.new_score ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'"
                                  class="px-2 py-0.5 rounded font-black text-xs inline-block mt-1"
                                  x-text="rec.new_score ? rec.new_score : 'Reset'">
                            </span>
                        </div>
                    </div>
                </template>
            </div>

            <div class="pt-4 border-t border-gray-100 text-right mt-4">
                <button @click="historyModalOpen = false" class="btn btn-outline text-xs font-semibold px-4 py-2 rounded-xl">Close</button>
            </div>
        </div>
    </div>

    {{-- Review Note Modal --}}
    <div x-show="noteModalOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="noteModalOpen = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                    📝 Add Admin Review Note
                </h3>
                <button @click="noteModalOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">&times;</button>
            </div>

            <form :action="noteActionUrl" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-700 mb-1">Document Justification / Investigation Note</label>
                    <textarea name="review_notes" rows="4" required placeholder="Enter administrative notes, review findings, or audit justification..."
                              class="w-full p-3 text-xs sm:text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" @click="noteModalOpen = false" class="btn btn-outline text-xs font-semibold px-4 py-2 rounded-xl">Cancel</button>
                    <button type="submit" class="btn btn-green text-xs font-semibold px-4 py-2 rounded-xl">Save Note</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
