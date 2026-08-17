@extends('layouts.admin')

@section('title', 'Judge Score Sheets & Signatures')

@push('styles')
<style>
    @media print {
        .sidebar, .topbar, .page-header, .no-print, nav, header, button, .tabs-nav {
            display: none !important;
        }

        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
            font-size: 11pt;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }

        .judge-score-sheet {
            display: none !important;
            page-break-after: always;
            break-after: page;
        }

        .judge-score-sheet.print-visible {
            display: block !important;
        }

        .judge-score-sheet:last-child {
            page-break-after: auto;
            break-after: auto;
        }

        .print-doc-header {
            display: block !important;
            text-align: center;
            margin-bottom: 16px;
            border-bottom: 2px solid #000000;
            padding-bottom: 8px;
        }

        .panel {
            box-shadow: none !important;
            border: none !important;
            margin-bottom: 20px !important;
        }

        .data-table {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        .data-table th, .data-table td {
            border: 1px solid #000000 !important;
            padding: 6px 8px !important;
            font-size: 9.5pt !important;
            color: #000000 !important;
        }

        .data-table th {
            background-color: #f3f4f6 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-judge-signature {
            display: block !important;
            margin-top: 35px !important;
            page-break-inside: avoid;
        }
    }

    .print-doc-header {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="page-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 no-print">
    <div>
        <div class="flex items-center gap-2 text-xs text-[var(--text-muted)] mb-1">
            <span>Settings</span>
            <span>/</span>
            <span class="text-[var(--green-700)] font-semibold">Judge Score Sheets</span>
        </div>
        <h1 class="page-title flex items-center gap-3">
            <svg class="w-7 h-7 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Judge Score Sheets &amp; Signatures
        </h1>
        <p class="page-subtitle">View and print scores given by each judge for all categories and candidates with their official signature line.</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="printAllJudgeSheets()" class="btn btn-green flex items-center gap-2 shadow-sm text-xs font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print All Judges ({{ $judges->count() }})
        </button>
    </div>
</div>

<div x-data="{
    selectedJudge: 'all',
    selectedGender: 'all',
    matches(jId, gender) {
        const jOk = this.selectedJudge === 'all' || this.selectedJudge == jId;
        const gOk = this.selectedGender === 'all' || this.selectedGender === gender;
        return jOk && gOk;
    }
}">

    {{-- Navigation Tabs between Settings Sections --}}
    <div class="flex flex-wrap items-center gap-2 mb-6 border-b border-[var(--border-default)] pb-3 tabs-nav no-print">
        <a href="{{ route('admin.settings.preliminary') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Preliminary Criteria
        </a>
        <a href="{{ route('admin.settings.final') }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold text-[var(--text-secondary)] hover:bg-gray-100 transition-colors flex items-center gap-2">
            <span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span>
            Final Criteria
        </a>
        <a href="{{ route('admin.settings.judge-scores') }}"
           class="px-4 py-2 rounded-xl text-sm font-bold bg-[var(--green-100)] text-[var(--green-800)] border border-[var(--green-300)] flex items-center gap-2 shadow-sm">
            <span class="w-2.5 h-2.5 rounded-full bg-[var(--green-600)]"></span>
            Judge Score Sheets
        </a>
    </div>

    {{-- Filters and Quick Controls --}}
    <div class="panel border border-[var(--border-default)] shadow-sm rounded-xl p-4 mb-6 bg-[var(--bg-card)] no-print">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                {{-- Judge Filter --}}
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-[var(--text-muted)] uppercase">Judge:</label>
                    <select x-model="selectedJudge" class="form-input text-xs py-1.5 px-3 rounded-lg font-semibold">
                        <option value="all">All Judges ({{ $judges->count() }})</option>
                        @foreach($judges as $j)
                            <option value="{{ $j->id }}">{{ $j->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Gender Filter --}}
                <div class="flex items-center gap-2">
                    <label class="text-xs font-bold text-[var(--text-muted)] uppercase">Division:</label>
                    <select x-model="selectedGender" class="form-input text-xs py-1.5 px-3 rounded-lg font-semibold">
                        <option value="all">All Candidates</option>
                        <option value="Male">Male Candidates Only</option>
                        <option value="Female">Female Candidates Only</option>
                    </select>
                </div>
            </div>

            <div class="text-xs text-[var(--text-muted)]">
                Showing scores across: <strong>Production, Fitness, Indigenous, Traditional, Q &amp; A</strong>
            </div>
        </div>
    </div>

    @if($judges->isEmpty())
        <div class="panel text-center py-16 animate-fade-in-up">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <p class="text-gray-500 font-medium">No active judges found.</p>
        </div>
    @elseif($candidates->isEmpty())
        <div class="panel text-center py-16 animate-fade-in-up">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <p class="text-gray-500 font-medium">No candidates found.</p>
        </div>
    @else

        {{-- Loop for Each Judge --}}
        @foreach($judges as $judge)
            <div class="judge-score-sheet mb-10"
                 id="judge-sheet-{{ $judge->id }}"
                 x-show="selectedJudge === 'all' || selectedJudge == '{{ $judge->id }}'">

                {{-- Printable Document Header for this Judge --}}
                <div class="print-doc-header">
                    <h1 style="font-size: 18pt; font-weight: bold; margin: 0; text-transform: uppercase;">Official Judge Score Sheet</h1>
                    <p style="font-size: 13pt; margin: 4px 0 0; font-weight: bold; color: #166534;">Judge: {{ strtoupper($judge->name) }}</p>
                    <p style="font-size: 9pt; margin: 3px 0 0; color: #6b7280;">Summary of all scores submitted — Printed on {{ now()->format('F d, Y — h:i A') }}</p>
                </div>

                {{-- Screen Card Header --}}
                <div class="panel border border-[var(--border-default)] shadow-sm rounded-xl overflow-hidden mb-6">
                    <div class="panel-header px-6 py-4 border-b border-[var(--border-default)] bg-[var(--bg-card)] flex flex-wrap items-center justify-between gap-3 no-print">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-700 flex items-center justify-center text-white font-black text-base shadow-sm">
                                {{ strtoupper(substr($judge->name, 0, 1)) }}
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-[var(--text-primary)] flex items-center gap-2">
                                    {{ $judge->name }}
                                    <span class="badge badge-green text-[10px]">Official Judge</span>
                                </h2>
                                <p class="text-xs text-[var(--text-muted)]">Judge ID: #{{ $judge->id }} &nbsp;·&nbsp; {{ $judge->email }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button type="button"
                                    onclick="printSingleJudgeSheet('{{ $judge->id }}', '{{ addslashes($judge->name) }}')"
                                    class="btn btn-outline btn-sm flex items-center gap-2 shadow-sm text-xs font-semibold hover:border-emerald-500 hover:text-emerald-700">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                </svg>
                                Print {{ $judge->name }}'s Score Sheet
                            </button>
                        </div>
                    </div>

                    <div class="panel-body p-4 sm:p-6 space-y-6">
                        @php
                            $genderDivisions = [
                                'Male' => ['label' => '♂ Male Candidates', 'candidates' => $maleCandidates, 'color' => '#2563eb', 'bg' => '#eff6ff'],
                                'Female' => ['label' => '♀ Female Candidates', 'candidates' => $femaleCandidates, 'color' => '#db2777', 'bg' => '#fdf2f8'],
                            ];
                        @endphp

                        @foreach($genderDivisions as $gKey => $division)
                            @php $divCands = $division['candidates']; @endphp
                            @if($divCands->isEmpty()) @continue @endif

                            <div x-show="selectedGender === 'all' || selectedGender === '{{ $gKey }}'" class="space-y-2">
                                <div class="flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-lg w-fit text-white shadow-sm" style="background-color: {{ $division['color'] }};">
                                    {{ $division['label'] }} ({{ $divCands->count() }})
                                </div>

                                <div class="overflow-x-auto rounded-xl border border-gray-200">
                                    <table class="data-table min-w-full text-xs">
                                        <thead>
                                            <tr class="bg-gray-50 border-b border-gray-200">
                                                <th class="py-2.5 px-3 text-center min-w-[70px] font-bold text-gray-700">Cand. #</th>
                                                <th class="py-2.5 px-3 text-left min-w-[150px] font-bold text-gray-700">Candidate Name</th>
                                                <th class="py-2.5 px-3 text-center min-w-[90px] font-bold text-blue-800 bg-blue-50/50">Production</th>
                                                <th class="py-2.5 px-3 text-center min-w-[90px] font-bold text-emerald-800 bg-emerald-50/50">Fitness</th>
                                                <th class="py-2.5 px-3 text-center min-w-[100px] font-bold text-purple-800 bg-purple-50/50">Indigenous</th>
                                                <th class="py-2.5 px-3 text-center min-w-[100px] font-bold text-amber-800 bg-amber-50/50">Traditional</th>
                                                <th class="py-2.5 px-3 text-center min-w-[80px] font-bold text-rose-800 bg-rose-50/50">Q &amp; A</th>
                                                <th class="py-2.5 px-3 text-center min-w-[90px] font-black text-gray-900 bg-gray-100">Total Sum</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($divCands as $cand)
                                                @php
                                                    $k = $judge->id . '_' . $cand->id;
                                                    $pScore = isset($prodScores[$k])  ? (float)$prodScores[$k]->score  : null;
                                                    $fScore = isset($fitScores[$k])   ? (float)$fitScores[$k]->score   : null;
                                                    $iScore = isset($indigScores[$k]) ? (float)$indigScores[$k]->score : null;
                                                    $tScore = isset($tradScores[$k])  ? (float)$tradScores[$k]->score  : null;
                                                    $qScore = isset($qaScores[$k])    ? (float)$qaScores[$k]->score    : null;

                                                    $givenScores = array_filter([$pScore, $fScore, $iScore, $tScore, $qScore], fn($v) => $v !== null);
                                                    $sum = !empty($givenScores) ? array_sum($givenScores) : null;
                                                @endphp
                                                <tr class="hover:bg-gray-50/80 transition-colors">
                                                    <td class="py-2.5 px-3 text-center font-bold font-mono">
                                                        <span class="inline-flex items-center justify-center w-7 h-7 rounded-full text-white text-xs font-black shadow-sm" style="background-color: {{ $division['color'] }};">
                                                            {{ $cand->candidate_number }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2.5 px-3 font-semibold text-gray-900">
                                                        {{ $cand->display_name }}
                                                        @if($cand->origin)
                                                            <span class="block text-[10px] text-gray-400 font-normal">{{ $cand->origin }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-blue-900 bg-blue-50/20">
                                                        {{ $pScore !== null ? number_format($pScore, 1) : '—' }}
                                                    </td>
                                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-emerald-900 bg-emerald-50/20">
                                                        {{ $fScore !== null ? number_format($fScore, 1) : '—' }}
                                                    </td>
                                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-purple-900 bg-purple-50/20">
                                                        {{ $iScore !== null ? number_format($iScore, 1) : '—' }}
                                                    </td>
                                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-amber-900 bg-amber-50/20">
                                                        {{ $tScore !== null ? number_format($tScore, 1) : '—' }}
                                                    </td>
                                                    <td class="py-2.5 px-3 text-center font-mono font-bold text-rose-900 bg-rose-50/20">
                                                        {{ $qScore !== null ? number_format($qScore, 1) : '—' }}
                                                    </td>
                                                    <td class="py-2.5 px-3 text-center font-mono font-black text-gray-900 bg-gray-50">
                                                        {{ $sum !== null ? number_format($sum, 1) : '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach

                        {{-- Official Judge Signature Block at Bottom of Sheet --}}
                        <div class="print-judge-signature pt-6 mt-4">
                            <div class="w-80 ml-auto mr-4 text-center">
                                <div style="border-bottom: 2px solid #000; height: 45px; margin-bottom: 8px;"></div>
                                <p style="font-size: 11pt; font-weight: bold; margin: 0; text-transform: uppercase; color: #000;">
                                    {{ $judge->name }}
                                </p>
                                <p style="font-size: 9pt; color: #4b5563; margin: 0;">
                                    Official Judge Signature &amp; Date
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach

    @endif

</div>

@push('scripts')
<script>
    function printSingleJudgeSheet(judgeId, judgeName) {
        document.querySelectorAll('.judge-score-sheet').forEach(el => {
            el.classList.remove('print-visible');
        });
        const target = document.getElementById('judge-sheet-' + judgeId);
        if (target) {
            target.classList.add('print-visible');
        }

        window.print();

        const cleanup = () => {
            document.querySelectorAll('.judge-score-sheet').forEach(el => {
                el.classList.remove('print-visible');
            });
            window.removeEventListener('afterprint', cleanup);
        };

        window.addEventListener('afterprint', cleanup);
        setTimeout(cleanup, 1000);
    }

    function printAllJudgeSheets() {
        document.querySelectorAll('.judge-score-sheet').forEach(el => {
            el.classList.add('print-visible');
        });

        window.print();

        const cleanup = () => {
            document.querySelectorAll('.judge-score-sheet').forEach(el => {
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
