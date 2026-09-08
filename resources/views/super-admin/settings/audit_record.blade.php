@extends('layouts.super-admin')

@section('title', 'System Audit Trail & Security Ledger - Super Admin')

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

        fetch('{{ route("super-admin.settings.audit_record.candidate_history") }}?candidate_id=' + candidateId)
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
}" class="space-y-6">

    {{-- Header Banner --}}
    <div class="panel border-l-4 border-purple-600 bg-gradient-to-r from-purple-500/5 via-transparent to-transparent">
        <div class="panel-body py-5 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
                    <a href="{{ route('super-admin.dashboard') }}" class="hover:text-purple-600 transition-colors">Super Dashboard</a>
                    <span>/</span>
                    <span class="text-purple-700 font-semibold">Security &amp; Audit Trail</span>
                </div>
                <h1 class="page-title text-2xl font-bold text-[var(--text-primary)] flex items-center gap-2.5">
                    <span class="p-1.5 rounded-xl bg-purple-100 text-purple-700 text-xl">📜</span>
                    System Audit Trail &amp; Security Ledger
                </h1>
                <p class="page-subtitle text-sm text-[var(--text-muted)] mt-1">
                    Immutable activity ledger tracking score submissions, category updates, administrative changes, and security anomaly alerts in real-time.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                {{-- Auto-refresh toggle button --}}
                <button @click="toggleAutoRefresh()"
                        :class="autoRefresh ? 'bg-purple-600 text-white border-purple-600 shadow-md ring-2 ring-purple-300' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'"
                        class="btn text-xs font-semibold px-3.5 py-2 rounded-xl border flex items-center gap-2 transition-all">
                    <span class="w-2.5 h-2.5 rounded-full" :class="autoRefresh ? 'bg-white animate-pulse' : 'bg-gray-400'"></span>
                    <span x-text="autoRefresh ? 'Live Auto-Refresh (10s)' : 'Enable Live Auto-Refresh'"></span>
                </button>

                {{-- Export CSV --}}
                <a href="{{ route('super-admin.settings.audit_record.export') }}" class="btn btn-outline flex items-center gap-2 text-xs font-semibold shadow-2xs bg-white hover:bg-purple-50 border-purple-200 text-purple-800">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export CSV
                </a>

                {{-- Clear Audit History --}}
                <form action="{{ route('super-admin.settings.audit_record.clear') }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to purge all audit records? This action cannot be reversed and will be logged.');" class="inline">
                    @csrf
                    <button type="submit" class="btn btn-outline text-rose-600 hover:bg-rose-50 border-rose-200 text-xs font-semibold flex items-center gap-1.5 shadow-2xs bg-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Clear Ledger
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Metrics KPI Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        {{-- Total Records --}}
        <div class="p-4 rounded-2xl bg-white border border-[var(--border-default)] shadow-xs">
            <div class="flex items-center justify-between text-[var(--text-muted)] text-xs font-semibold uppercase tracking-wider">
                <span>Total Logs</span>
                <span>📜</span>
            </div>
            <div class="text-2xl font-extrabold text-[var(--text-primary)] mt-2">
                {{ number_format($stats['total_records']) }}
            </div>
            <div class="text-[11px] text-[var(--text-muted)] mt-1">Recorded Events</div>
        </div>

        {{-- Score Submissions --}}
        <div class="p-4 rounded-2xl bg-white border border-[var(--border-default)] shadow-xs">
            <div class="flex items-center justify-between text-emerald-700 text-xs font-semibold uppercase tracking-wider">
                <span>Submissions</span>
                <span>⭐</span>
            </div>
            <div class="text-2xl font-extrabold text-emerald-700 mt-2">
                {{ number_format($stats['score_submissions']) }}
            </div>
            <div class="text-[11px] text-[var(--text-muted)] mt-1">Scoring Submissions</div>
        </div>

        {{-- Score Resets --}}
        <div class="p-4 rounded-2xl bg-white border border-[var(--border-default)] shadow-xs">
            <div class="flex items-center justify-between text-rose-700 text-xs font-semibold uppercase tracking-wider">
                <span>Score Resets</span>
                <span>🔄</span>
            </div>
            <div class="text-2xl font-extrabold text-rose-700 mt-2">
                {{ number_format($stats['score_resets']) }}
            </div>
            <div class="text-[11px] text-[var(--text-muted)] mt-1">Retracted Scores</div>
        </div>

        {{-- Flagged / Anomaly --}}
        <div class="p-4 rounded-2xl bg-white border border-[var(--border-default)] shadow-xs">
            <div class="flex items-center justify-between text-amber-700 text-xs font-semibold uppercase tracking-wider">
                <span>Threat Alerts</span>
                <span>🛡️</span>
            </div>
            <div class="text-2xl font-extrabold text-amber-700 mt-2">
                {{ number_format($stats['flagged_suspicious'] + $stats['critical_alerts']) }}
            </div>
            <div class="text-[11px] text-[var(--text-muted)] mt-1">Flagged / Suspicious</div>
        </div>

        {{-- Active Judges Audited --}}
        <div class="p-4 rounded-2xl bg-white border border-[var(--border-default)] shadow-xs col-span-2 sm:col-span-1">
            <div class="flex items-center justify-between text-indigo-700 text-xs font-semibold uppercase tracking-wider">
                <span>Judges Audited</span>
                <span>⚖️</span>
            </div>
            <div class="text-2xl font-extrabold text-indigo-700 mt-2">
                {{ number_format($stats['unique_judges']) }}
            </div>
            <div class="text-[11px] text-[var(--text-muted)] mt-1">Distinct Evaluators</div>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <div class="panel shadow-xs">
        <div class="panel-body py-4">
            <form action="{{ route('super-admin.settings.audit_record') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3">
                    {{-- Search Input --}}
                    <div class="lg:col-span-2">
                        <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase mb-1">Search Activity</label>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search action, user, candidate, IP..." class="form-input text-xs">
                    </div>

                    {{-- Category Filter --}}
                    <div>
                        <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase mb-1">Category</label>
                        <select name="category" class="form-input text-xs">
                            <option value="">All Categories</option>
                            <option value="production" {{ $categoryFilter === 'production' ? 'selected' : '' }}>Production</option>
                            <option value="fitness" {{ $categoryFilter === 'fitness' ? 'selected' : '' }}>Fitness</option>
                            <option value="traditional_attire" {{ in_array($categoryFilter, ['traditional_attire', 'traditional-attire']) ? 'selected' : '' }}>Traditional Attire</option>
                            <option value="indigenous_attire" {{ in_array($categoryFilter, ['indigenous_attire', 'indigenous-attire']) ? 'selected' : '' }}>Indigenous Attire</option>
                            <option value="qa" {{ in_array($categoryFilter, ['qa', 'qanda']) ? 'selected' : '' }}>Q&amp;A Final</option>
                            <option value="talent_portion" {{ $categoryFilter === 'talent_portion' ? 'selected' : '' }}>Talent Portion</option>
                            <option value="photogenic" {{ $categoryFilter === 'photogenic' ? 'selected' : '' }}>Photogenic</option>
                            <option value="system" {{ $categoryFilter === 'system' ? 'selected' : '' }}>System</option>
                            <option value="security" {{ $categoryFilter === 'security' ? 'selected' : '' }}>Security</option>
                        </select>
                    </div>

                    {{-- Event Type --}}
                    <div>
                        <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase mb-1">Event Type</label>
                        <select name="event_type" class="form-input text-xs">
                            <option value="">All Event Types</option>
                            <option value="score_submitted" {{ $eventFilter === 'score_submitted' ? 'selected' : '' }}>Score Submitted</option>
                            <option value="score_reset" {{ $eventFilter === 'score_reset' ? 'selected' : '' }}>Score Reset</option>
                            <option value="login" {{ $eventFilter === 'login' ? 'selected' : '' }}>User Login</option>
                            <option value="logout" {{ $eventFilter === 'logout' ? 'selected' : '' }}>User Logout</option>
                            <option value="criteria_updated" {{ $eventFilter === 'criteria_updated' ? 'selected' : '' }}>Criteria Updated</option>
                            <option value="audit_cleared" {{ $eventFilter === 'audit_cleared' ? 'selected' : '' }}>Audit Cleared</option>
                        </select>
                    </div>

                    {{-- Threat Level --}}
                    <div>
                        <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase mb-1">Risk Status</label>
                        <select name="risk_level" class="form-input text-xs">
                            <option value="">All Threat Levels</option>
                            <option value="normal" {{ $riskFilter === 'normal' ? 'selected' : '' }}>Normal (Verified)</option>
                            <option value="warning" {{ $riskFilter === 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="critical" {{ $riskFilter === 'critical' ? 'selected' : '' }}>Critical Alert</option>
                        </select>
                    </div>

                    {{-- Date Preset --}}
                    <div>
                        <label class="block text-[11px] font-bold text-[var(--text-muted)] uppercase mb-1">Timeframe</label>
                        <select name="date_preset" class="form-input text-xs">
                            <option value="all" {{ $datePreset === 'all' ? 'selected' : '' }}>All Time</option>
                            <option value="today" {{ $datePreset === 'today' ? 'selected' : '' }}>Today</option>
                            <option value="7days" {{ $datePreset === '7days' ? 'selected' : '' }}>Past 7 Days</option>
                            <option value="30days" {{ $datePreset === '30days' ? 'selected' : '' }}>Past 30 Days</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-[var(--border-default)]">
                    <label class="inline-flex items-center gap-2 cursor-pointer text-xs font-semibold text-[var(--text-primary)]">
                        <input type="checkbox" name="suspicious_only" value="1" {{ $suspiciousOnly ? 'checked' : '' }} class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                        <span>Show Flagged &amp; Suspicious Entries Only</span>
                    </label>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn btn-primary text-xs font-semibold px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-xs">
                            Apply Filters
                        </button>
                        @if($search || $categoryFilter || $eventFilter || $riskFilter || $suspiciousOnly || $datePreset !== 'all')
                            <a href="{{ route('super-admin.settings.audit_record') }}" class="btn btn-outline text-xs font-semibold px-3 py-2 rounded-xl text-[var(--text-muted)]">
                                Reset
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Category Badge Helper --}}
    @php
        $formatCategory = function($cat) {
            $cat = strtolower(trim($cat ?? ''));
            return match($cat) {
                'qa', 'qanda' => ['label' => 'Q&A Final', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
                'production' => ['label' => 'Production', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
                'fitness' => ['label' => 'Fitness', 'class' => 'bg-emerald-100 text-emerald-800 border-emerald-200'],
                'traditional_attire', 'traditional-attire' => ['label' => 'Traditional Attire', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
                'indigenous_attire', 'indigenous-attire' => ['label' => 'Indigenous Attire', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
                'talent_portion', 'talent-portion' => ['label' => 'Talent Portion', 'class' => 'bg-rose-100 text-rose-800 border-rose-200'],
                'photogenic' => ['label' => 'Photogenic', 'class' => 'bg-cyan-100 text-cyan-800 border-cyan-200'],
                'security', 'auth' => ['label' => 'Security & Auth', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
                default => ['label' => ucwords(str_replace(['_', '-'], ' ', $cat ?: 'General')), 'class' => 'bg-gray-100 text-gray-800 border-gray-200'],
            };
        };
    @endphp

    {{-- Main Ledger Table --}}
    <div class="panel shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr class="bg-[var(--surface-100)] text-[var(--text-muted)] text-[11px] uppercase tracking-wider border-b border-[var(--border-default)]">
                        <th class="py-3 px-4 w-20 text-left font-bold">Event ID</th>
                        <th class="py-3 px-4 text-left font-bold w-48">Actor / User</th>
                        <th class="py-3 px-4 text-left font-bold w-48">Candidate Details</th>
                        <th class="py-3 px-4 text-left font-bold">Action Taken &amp; Score Details</th>
                        <th class="py-3 px-4 text-left font-bold w-36">Category</th>
                        <th class="py-3 px-4 text-left font-bold w-32">Threat Level</th>
                        <th class="py-3 px-4 text-right font-bold w-40">Timestamp</th>
                        <th class="py-3 px-4 text-center font-bold w-24">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-default)] bg-white text-xs">
                    @forelse($auditRecords as $record)
                        @php
                            $details = is_string($record->details) ? json_decode($record->details, true) : (is_array($record->details) ? $record->details : []);
                            $scoreVal = $record->new_score ?? ($details['score'] ?? null);
                            $oldScoreVal = $record->old_score ?? ($details['old_score'] ?? null);
                            $categoryKey = $record->category ?? ($details['category'] ?? 'general');
                            $catInfo = $formatCategory($categoryKey);
                            $actionType = $details['action'] ?? $record->event_type ?? ($record->action_description ?? 'activity');
                            $candNum = $record->candidate_number ?? ($details['candidate_number'] ?? null);
                            $candName = $record->candidate_name ?? ($details['candidate_name'] ?? null);
                        @endphp
                        <tr class="hover:bg-purple-50/20 transition-colors {{ $record->is_suspicious ? 'bg-amber-50/40' : '' }}">
                            {{-- Event ID --}}
                            <td class="py-3.5 px-4 font-mono font-bold text-[var(--text-muted)] align-middle">
                                <span class="px-2 py-1 rounded-md bg-[var(--surface-100)] border border-[var(--border-default)] text-[11px]">
                                    #{{ $record->id }}
                                </span>
                            </td>

                            {{-- Actor User --}}
                            <td class="py-3.5 px-4 align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0 shadow-2xs
                                        {{ in_array($record->user_role, ['super-admin', 'super_admin']) ? 'bg-purple-100 text-purple-700 border border-purple-200' :
                                           ($record->user_role === 'admin' ? 'bg-blue-100 text-blue-700 border border-blue-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200') }}">
                                        {{ strtoupper(substr($record->user_name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-sm text-[var(--text-primary)] truncate">
                                            {{ $record->user_name ?? 'System Event' }}
                                        </div>
                                        <div class="text-[11px] text-[var(--text-muted)] capitalize flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full {{ $record->user_role === 'judge' ? 'bg-emerald-500' : ($record->user_role === 'admin' ? 'bg-blue-500' : 'bg-purple-500') }}"></span>
                                            {{ $record->user_role ?? 'Automated' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Candidate Details --}}
                            <td class="py-3.5 px-4 align-middle">
                                @if($candNum)
                                    <div class="flex items-center gap-1.5">
                                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-md bg-purple-50 text-purple-700 border border-purple-200 text-xs font-extrabold flex-shrink-0">
                                            #{{ $candNum }}
                                        </span>
                                        <div class="min-w-0">
                                            <div class="font-semibold text-xs text-[var(--text-primary)] truncate">
                                                {{ $candName ?? 'Candidate #'.$candNum }}
                                            </div>
                                            <button type="button" @click="openHistoryModal({{ $record->candidate_id ?? $candNum }}, '{{ addslashes($candName ?? ('Candidate #'.$candNum)) }}')" class="text-[10px] text-purple-600 hover:underline">
                                                View Score History &rarr;
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[var(--text-muted)] italic text-xs">N/A (System)</span>
                                @endif
                            </td>

                            {{-- Action & Score Details --}}
                            <td class="py-3.5 px-4 align-middle">
                                <div class="flex flex-col gap-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($actionType === 'reset' || $actionType === 'score_reset')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Score Reset
                                            </span>
                                        @elseif($oldScoreVal !== null && $scoreVal !== null && $oldScoreVal != $scoreVal)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                Score Updated
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                                Score Saved
                                            </span>
                                        @endif

                                        {{-- Score Display --}}
                                        @if($scoreVal !== null)
                                            <div class="inline-flex items-center gap-1.5 ml-1">
                                                @if($oldScoreVal !== null && $oldScoreVal != $scoreVal)
                                                    <span class="text-[var(--text-muted)] line-through font-mono text-xs">{{ number_format((float)$oldScoreVal, 2) }}</span>
                                                    <span class="text-[var(--text-muted)]">&rarr;</span>
                                                @endif
                                                <span class="px-2 py-0.5 rounded-md font-mono font-black text-xs bg-emerald-50 text-emerald-800 border border-emerald-300 shadow-2xs">
                                                    {{ number_format((float)$scoreVal, 2) }} / 10
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    @if(!empty($record->action_description))
                                        <div class="text-[11px] text-[var(--text-muted)] mt-0.5">
                                            {{ $record->action_description }}
                                        </div>
                                    @endif

                                    @if($record->reviewer_name && $record->review_notes)
                                        <div class="mt-1 p-2 rounded-lg bg-purple-50/70 border border-purple-200 text-[11px] text-purple-900">
                                            <span class="font-bold">Reviewed by {{ $record->reviewer_name }}:</span> {{ $record->review_notes }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="py-3.5 px-4 align-middle">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $catInfo['class'] }}">
                                    {{ $catInfo['label'] }}
                                </span>
                            </td>

                            {{-- Threat Level --}}
                            <td class="py-3.5 px-4 align-middle">
                                @if($record->is_suspicious || $record->risk_level === 'critical')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-300 animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span> Critical
                                    </span>
                                @elseif($record->risk_level === 'warning')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span> Warning
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium text-emerald-700 bg-emerald-50 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Verified
                                    </span>
                                @endif
                            </td>

                            {{-- Timestamp --}}
                            <td class="py-3.5 px-4 text-right align-middle font-mono">
                                <div class="font-bold text-xs text-[var(--text-primary)]">
                                    {{ $record->created_at->diffForHumans() }}
                                </div>
                                <div class="text-[10px] text-[var(--text-muted)] mt-0.5">
                                    {{ $record->created_at->format('M d, Y • H:i:s') }}
                                </div>
                            </td>

                            {{-- Actions --}}
                            <td class="py-3.5 px-4 text-center align-middle">
                                <div class="flex items-center justify-center gap-1">
                                    {{-- Review / Notes Button --}}
                                    <button type="button" @click="openNoteModal({{ $record->id }}, '{{ route('super-admin.settings.audit_record.review', $record->id) }}')"
                                            title="Add Reviewer Note"
                                            class="p-1.5 text-purple-600 hover:bg-purple-50 rounded-lg transition-colors border border-transparent hover:border-purple-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>

                                    {{-- Flag Toggle Form --}}
                                    <form action="{{ route('super-admin.settings.audit_record.flag', $record->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                                title="{{ $record->is_suspicious ? 'Remove Flag' : 'Flag as Suspicious' }}"
                                                class="p-1.5 rounded-lg transition-colors border border-transparent {{ $record->is_suspicious ? 'text-amber-600 hover:bg-amber-50 hover:border-amber-200' : 'text-gray-400 hover:text-amber-600 hover:bg-gray-50' }}">
                                            <svg class="w-4 h-4" fill="{{ $record->is_suspicious ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center text-[var(--text-muted)]">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-12 h-12 text-[var(--text-muted)] opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-[var(--text-primary)]">No audit records found matching your active filters.</span>
                                    <span class="text-xs">Adjust your search parameters or reset filters to display all logged activities.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auditRecords->hasPages())
            <div class="px-6 py-4 border-t border-[var(--border-default)] bg-[var(--surface-50)]">
                {{ $auditRecords->links() }}
            </div>
        @endif
    </div>

    {{-- Review Note Modal --}}
    <div x-show="noteModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         style="display: none;">
        <div @click.away="noteModalOpen = false" class="panel max-w-lg w-full shadow-2xl animate-fade-in-up">
            <div class="panel-header border-b border-[var(--border-default)] py-4 px-6 flex items-center justify-between">
                <h3 class="text-base font-bold text-[var(--text-primary)] flex items-center gap-2">
                    <span>📝</span> Add Super-Admin Audit Note
                </h3>
                <button @click="noteModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            <form :action="noteActionUrl" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-[var(--text-primary)] mb-1 uppercase tracking-wider">
                        Reviewer Observations / Justification
                    </label>
                    <textarea name="review_notes" rows="4" required placeholder="State your observations, validation reason, or security follow-up note..." class="form-input text-xs"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2 border-t border-[var(--border-default)]">
                    <button type="button" @click="noteModalOpen = false" class="btn btn-outline text-xs">Cancel</button>
                    <button type="submit" class="btn btn-primary text-xs bg-purple-600 hover:bg-purple-700 text-white shadow-xs">Save Review Note</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Candidate History Modal --}}
    <div x-show="historyModalOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
         style="display: none;">
        <div @click.away="historyModalOpen = false" class="panel max-w-2xl w-full shadow-2xl max-h-[85vh] flex flex-col animate-fade-in-up">
            <div class="panel-header border-b border-[var(--border-default)] py-4 px-6 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-[var(--text-primary)] flex items-center gap-2">
                        <span>🌟</span> Candidate Scoring History
                    </h3>
                    <p class="text-xs text-[var(--text-muted)]" x-text="'All audit entries associated with ' + historyCandidateName"></p>
                </div>
                <button @click="historyModalOpen = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto flex-1 divide-y divide-[var(--border-default)]">
                <template x-if="historyLoading">
                    <div class="py-12 text-center text-xs text-[var(--text-muted)]">
                        Loading candidate history...
                    </div>
                </template>
                <template x-if="!historyLoading && historyRecords.length === 0">
                    <div class="py-12 text-center text-xs text-[var(--text-muted)]">
                        No previous audit history found for this candidate.
                    </div>
                </template>
                <template x-for="item in historyRecords" :key="item.id">
                    <div class="py-3 text-xs flex items-center justify-between gap-4">
                        <div>
                            <div class="font-bold text-[var(--text-primary)]" x-text="item.action_description || (item.category + ' score event')"></div>
                            <div class="text-[11px] text-[var(--text-muted)] mt-0.5" x-text="'Evaluator: ' + (item.user_name || 'Judge') + ' • Category: ' + (item.category || 'General')"></div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="px-2 py-0.5 rounded font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200" x-text="item.new_score ? ('Score: ' + item.new_score) : 'Reset'"></span>
                            <div class="text-[10px] text-[var(--text-muted)] mt-1 font-mono" x-text="item.created_at"></div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="p-4 border-t border-[var(--border-default)] bg-[var(--surface-50)] text-right">
                <button type="button" @click="historyModalOpen = false" class="btn btn-outline text-xs">Close History</button>
            </div>
        </div>
    </div>
</div>
@endsection
