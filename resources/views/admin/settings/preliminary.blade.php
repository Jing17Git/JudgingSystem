@extends('layouts.admin')

@section('title', 'Preliminary Criteria Settings')

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
            <span>Settings</span>
            <span>/</span>
            <span class="text-[var(--green-700)] font-semibold">Preliminary Criteria</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Preliminary — Criteria Percentage Settings
        </h1>
        <p class="page-subtitle">Configure percentage weight for Preliminary judging categories (Production, Fitness, Indigenous Attire, Traditional Attire). Must total 100%.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.settings.final') }}" class="btn btn-outline flex items-center gap-2 shadow-sm text-xs font-semibold">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
            Go to Final Settings
        </a>
    </div>
</div>

<div class="max-w-4xl" x-data="{
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
    setEqual() {
        const count = Object.keys(this.values).length;
        const equalVal = count > 0 ? Math.floor(100 / count) : 0;
        for (let key in this.values) {
            this.values[key] = equalVal;
        }
    }
}">

    {{-- Navigation Tabs between Settings Sections --}}
    <div class="flex flex-wrap items-center gap-2 mb-6 border-b border-[var(--border-default)] pb-3">
        <a href="{{ route('admin.settings.preliminary') }}"
           class="px-4 py-2 rounded-xl text-sm font-bold bg-[var(--green-100)] text-[var(--green-800)] border border-[var(--green-300)] flex items-center gap-2 shadow-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-[var(--green-600)]"></span>
            Preliminary Criteria
        </a>
        <a href="{{ route('admin.settings.final') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Final Criteria
        </a>
        <a href="{{ route('admin.settings.judge-scores') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Judge Score Sheets
        </a>
        <a href="{{ route('admin.cache.index') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Cache Management
        </a>
    </div>

    

    <form action="{{ route('admin.settings.update') }}" method="POST" style="width: 135%;">
        @csrf
        @method('PUT')
        <input type="hidden" name="stage" value="preliminary">

        <div class="panel border border-[var(--border-default)] shadow-sm rounded-xl overflow-hidden mb-6">
            <div class="panel-header px-6 py-4 border-b border-[var(--border-default)] bg-[var(--bg-card)] flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-bold text-[var(--text-primary)]">Preliminary Category Weights</h2>
                    <p class="text-xs text-[var(--text-muted)]">Set whole number percentage weights for each preliminary category. Must total 100%.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="setEqual()" class="btn btn-outline btn-sm text-xs font-semibold">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Set Equal (25% each)
                    </button>
                </div>
            </div>

            <div class="panel-body p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($settings as $setting)
                        @php
                            $iconMap = [
                                'production' => 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
                                'fitness' => 'M3.75 13.5l10.5-10.5m0 0L18 6.75M14.25 3l3.75 3.75M3 14.25l3.75 3.75m0 0l10.5-10.5M6.75 18L3 14.25',
                                'indigenous_attire' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z',
                                'traditional_attire' => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z',
                            ];
                            $icon = $iconMap[$setting->key] ?? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z';
                        @endphp

                        <div class="p-4 rounded-xl border border-[var(--border-default)] bg-[var(--bg-default)] hover:border-[var(--green-500)] transition-all shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-[var(--green-100)] text-[var(--green-700)] flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="{{ $icon }}"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[var(--text-primary)]">{{ $setting->name }}</h3>
                                        <span class="text-[11px] text-[var(--text-muted)] font-mono">{{ $setting->key }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="relative">
                                <input type="number"
                                       step="1"
                                       min="0"
                                       max="100"
                                       name="percentages[{{ $setting->key }}]"
                                       x-model.number="values['{{ $setting->key }}']"
                                       @keydown="if(['.', 'e', 'E', '+', '-'].includes($event.key)) $event.preventDefault()"
                                       class="form-input pr-10 text-right font-mono font-bold text-base"
                                       required>
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm pointer-events-none">%</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Live Percentage Summary & Progress Bar --}}
                <div class="p-5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-card)] mt-6">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-bold text-[var(--text-primary)]">Total Percentage Allocation</span>
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
                    <div class="w-full h-3.5 bg-gray-200 rounded-full overflow-hidden flex shadow-inner">
                        <div class="bg-blue-500 transition-all duration-300" :style="'width: ' + Math.min(100, Math.max(0, values['production'] || 0)) + '%'" title="Production"></div>
                        <div class="bg-emerald-500 transition-all duration-300" :style="'width: ' + Math.min(100, Math.max(0, values['fitness'] || 0)) + '%'" title="Fitness"></div>
                        <div class="bg-purple-500 transition-all duration-300" :style="'width: ' + Math.min(100, Math.max(0, values['indigenous_attire'] || 0)) + '%'" title="Indigenous Attire"></div>
                        <div class="bg-amber-500 transition-all duration-300" :style="'width: ' + Math.min(100, Math.max(0, values['traditional_attire'] || 0)) + '%'" title="Traditional Attire"></div>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-[var(--text-muted)]">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span> Production (<span x-text="(values['production'] || 0) + '%'"></span>)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span> Fitness (<span x-text="(values['fitness'] || 0) + '%'"></span>)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-500 inline-block"></span> Indigenous (<span x-text="(values['indigenous_attire'] || 0) + '%'"></span>)</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500 inline-block"></span> Traditional (<span x-text="(values['traditional_attire'] || 0) + '%'"></span>)</span>
                    </div>
                </div>
            </div>

            <div class="panel-footer px-6 py-4 border-t border-[var(--border-default)] bg-[var(--bg-card)] flex flex-wrap justify-between items-center gap-3">
                <a href="{{ route('admin.overall.index') }}" class="btn btn-outline">View Preliminary Results</a>
                
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
                        Save Preliminary Settings
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
