@extends('layouts.admin')

@section('title', 'Final Criteria Settings')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
            <span>Settings</span>
            <span>/</span>
            <span class="text-[var(--green-700)] font-semibold">Final Criteria</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Final — Criteria Percentage Settings
        </h1>
        <p class="page-subtitle">Configure percentage contribution for the Final Overall computation (Preliminary Grand Total vs Q &amp; A). Must total 100%.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.settings.preliminary') }}" class="btn btn-outline flex items-center gap-2 shadow-sm text-xs font-semibold">
            <svg class="w-4 h-4 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            Back to Preliminary Settings
        </a>
    </div>
</div>

<div class="w-full max-w-5xl" x-data="{
    values: {
        @foreach($settings as $setting)
            '{{ $setting->key }}': {{ (int)$setting->percentage }},
        @endforeach
    },
    get total() {
        let sum = 0;
        for (let key in this.values) {
            sum += parseInt(this.values[key]) || 0;
        }
        return sum;
    },
    setDefault() {
        this.values['preliminary_score'] = 30;
        this.values['qa_score'] = 70;
    },
    setEqual() {
        this.values['preliminary_score'] = 50;
        this.values['qa_score'] = 50;
    }
}">

    {{-- Navigation Tabs between Settings Sections --}}
    <div class="flex flex-nowrap overflow-x-auto items-center gap-2 mb-6 border-b border-[var(--border-default)] pb-3 hide-scrollbar">
        <a href="{{ route('admin.settings.categories') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Manage Categories
        </a>
        <a href="{{ route('admin.settings.preliminary') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Preliminary Criteria
        </a>
        <a href="{{ route('admin.settings.final') }}"
           class="px-4 py-2 rounded-xl text-sm font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 flex items-center gap-2 shadow-sm whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
            Final Criteria (Prelim % vs Q&amp;A %)
        </a>
        <a href="{{ route('admin.settings.judge-scores') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Judge Score Sheets
        </a>
        <a href="{{ route('admin.settings.audit_record') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2 whitespace-nowrap">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
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

    {{-- Information Alert --}}
    <div class="mb-6 p-4 rounded-xl border border-emerald-100 bg-emerald-50/70 text-emerald-950 text-sm flex items-start gap-3 shadow-sm w-full">
        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>Configure how the Final Overall Score is calculated by assigning percentage weights to Preliminary Grand Total vs Final Q&amp;A score.</span>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" class="w-full">
        @csrf
        @method('PUT')
        <input type="hidden" name="stage" value="final">

        <div class="panel border border-[var(--border-default)] shadow-sm rounded-xl overflow-hidden mb-6">
            <div class="panel-header px-6 py-4 border-b border-[var(--border-default)] bg-[var(--bg-card)] flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-[var(--text-primary)]">Final Stage Weights (Whole Numbers Only)</h2>
                    <p class="text-xs text-[var(--text-muted)]">Set the percentage weight of Preliminary Grand Total vs Question &amp; Answer segment.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="setDefault()" class="btn btn-outline btn-sm text-xs font-semibold">
                        Default (30% / 70%)
                    </button>
                    <button type="button" @click="setEqual()" class="btn btn-outline btn-sm text-xs font-semibold">
                        Equal (50% / 50%)
                    </button>
                </div>
            </div>

            <div class="panel-body p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($settings as $setting)
                        @php
                            $isPrelim = $setting->key === 'preliminary_score';
                            $icon = $isPrelim
                                ? 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'
                                : 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z';
                            $accentColor = $isPrelim ? 'text-blue-700 bg-blue-100' : 'text-emerald-700 bg-emerald-100';
                            $desc = $isPrelim
                                ? 'Carried-over weighted result from Production, Fitness, Traditional, and Indigenous attire.'
                                : 'Finalist scores given by judges during the Final Q&A portion.';
                        @endphp

                        <div class="p-5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-default)] hover:border-emerald-500 transition-all shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg {{ $accentColor }} flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[var(--text-primary)]">{{ $setting->name }}</h3>
                                        <p class="text-[11px] text-[var(--text-muted)]">{{ $desc }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="relative mt-2">
                                <input type="number"
                                       step="1"
                                       min="0"
                                       max="100"
                                       name="percentages[{{ $setting->key }}]"
                                       x-model.number="values['{{ $setting->key }}']"
                                       @keydown="if(['.', 'e', 'E', '+', '-'].includes($event.key)) $event.preventDefault()"
                                       class="form-input pr-10 text-right font-mono font-bold text-lg"
                                       required>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-base pointer-events-none">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Live Percentage Summary & Progress Bar --}}
                <div class="p-5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-card)] mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-bold text-[var(--text-primary)]">Final Split Allocation</span>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black font-mono"
                                  :class="total === 100 ? 'text-emerald-600' : (total > 100 ? 'text-rose-600' : 'text-amber-600')"
                                  x-text="total + '%'"></span>

                            <template x-if="total === 100">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Valid (100%)
                                </span>
                            </template>
                            <template x-if="total !== 100">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold"
                                      :class="total > 100 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800'"
                                      x-text="total > 100 ? 'Exceeds 100% (' + total + '%)' : 'Must equal 100% (currently ' + total + '%)'">
                                </span>
                            </template>
                        </div>
                    </div>

                    {{-- Visual stacked bar --}}
                    <div class="w-full h-4 bg-gray-200 rounded-full overflow-hidden flex shadow-inner">
                        <div class="bg-blue-500 transition-all duration-300" :style="'width: ' + Math.min(100, Math.max(0, values['preliminary_score'] || 0)) + '%'" title="Preliminary"></div>
                        <div class="bg-emerald-500 transition-all duration-300" :style="'width: ' + Math.min(100, Math.max(0, values['qa_score'] || 0)) + '%'" title="Q & A"></div>
                    </div>

                    <div class="flex flex-wrap items-center gap-6 mt-3 text-xs text-[var(--text-muted)]">
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span> Preliminary Grand Total (<span class="font-bold text-blue-700" x-text="(values['preliminary_score'] || 0) + '%'"></span>)</span>
                        <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Q &amp; A Segment (<span class="font-bold text-emerald-700" x-text="(values['qa_score'] || 0) + '%'"></span>)</span>
                    </div>
                </div>
            </div>

            <div class="panel-footer px-6 py-4 border-t border-[var(--border-default)] bg-[var(--bg-card)] flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('admin.overall.final') }}" class="btn btn-outline">View Final Overall Results</a>
                
                <div class="flex items-center gap-3">
                    <template x-if="total !== 100">
                        <span class="text-xs font-semibold text-rose-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Total must equal 100% to save
                        </span>
                    </template>

                    <button type="submit"
                            :disabled="total !== 100"
                            :class="total !== 100 ? 'opacity-50 cursor-not-allowed' : ''"
                            class="btn btn-green flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Final Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
