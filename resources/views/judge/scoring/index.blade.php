@extends('layouts.judge')

@section('title', 'Judge Scorecard — ' . $categoryName)

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  .scorecard-container {
    font-family: inherit;
    color: var(--text-primary);
  }

  /* ---------- MASTHEAD ---------- */
  .masthead {
    max-width: 1180px;
    margin: 0 auto 24px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    border-bottom: 2px solid var(--border-default);
    padding-bottom: 16px;
    flex-wrap: wrap;
    gap: 14px;
  }
  .masthead h1 {
    font-size: clamp(24px, 3.5vw, 34px);
    font-weight: 800;
    margin: 0;
    color: var(--text-primary);
    line-height: 1.1;
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .masthead .eyebrow {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--green-600);
    font-weight: 700;
    margin: 0 0 4px;
  }

  .row-nav {
    display: flex;
    align-items: center;
    gap: 14px;
    font-family: 'IBM Plex Mono', monospace;
  }
  .row-nav .row-label {
    font-size: 12px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-secondary);
    background: var(--bg-card);
    padding: 8px 16px;
    border-radius: 10px;
    border: 1px solid var(--border-default);
    box-shadow: 0 1px 2px rgba(0,0,0,0.05);
  }
  .row-nav .row-label b { color: var(--green-600); font-weight: 700; }

  /* ---------- CARD PAIR LAYOUT ---------- */
  .card-pair {
    max-width: 1180px;
    margin: 0 auto 32px;
    display: grid;
    grid-template-columns: 1fr 64px 1fr;
    align-items: stretch;
    gap: 0;
  }

  .divider {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    padding-top: 12px;
    position: relative;
  }
  .divider .vs {
    font-family: 'Bebas Neue', sans-serif;
    font-size: 20px;
    letter-spacing: 0.08em;
    color: var(--text-muted);
    background: #f3f4f6;
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid var(--border-default);
    z-index: 2;
    box-shadow: 0 2px 4px rgba(0,0,0,0.04);
  }
  .divider .perf {
    flex: 1;
    width: 0;
    border-left: 2px dashed var(--border-default);
    margin-top: 8px;
  }

  .sc-panel {
    background: #ffffff;
    color: var(--text-primary);
    border-radius: 20px;
    padding: 24px;
    position: relative;
    box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border: 2px solid var(--border-default);
  }
  .sc-panel.male { border-color: #bfdbfe; background: linear-gradient(180deg, #f0f7ff 0%, #ffffff 160px); }
  .sc-panel.female { border-color: #fbcfe8; background: linear-gradient(180deg, #fdf2f8 0%, #ffffff 160px); }

  .panel-banner {
    padding: 12px 18px;
    border-radius: 14px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  }
  .sc-panel.male .panel-banner { background: #2563eb; }
  .sc-panel.female .panel-banner { background: #db2777; }

  .panel-banner h2 {
    font-size: 16px;
    font-weight: 800;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    margin: 0;
  }

  .sc-frame {
    aspect-ratio: 4/3;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    background: #f8fafc;
    border: 1px solid var(--border-default);
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.04);
  }
  .sc-frame img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .sc-frame .placeholder-icon {
    width: 56px; height: 56px;
    opacity: 0.4;
  }
  .sc-panel.male .sc-frame .placeholder-icon { color: #2563eb; }
  .sc-panel.female .sc-frame .placeholder-icon { color: #db2777; }
  .frame-caption {
    position: absolute;
    bottom: 10px; left: 10px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    background: rgba(15, 23, 42, 0.85);
    color: #ffffff;
    padding: 4px 10px;
    border-radius: 8px;
    backdrop-filter: blur(4px);
  }

  .field-row {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 12px;
    margin-bottom: 16px;
  }
  .sc-panel label.field-lbl {
    display: block;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    margin-bottom: 6px;
    color: var(--text-muted);
    font-weight: 700;
  }
  .sc-panel input[type="text"] {
    width: 100%;
    font-size: 15px;
    font-weight: 700;
    padding: 10px 14px;
    border-radius: 10px;
    background: #ffffff;
    color: var(--text-primary);
    outline: none;
    border: 1.5px solid var(--border-default);
  }
  .sc-panel.male input[type="text"].cand-num { font-family: 'IBM Plex Mono', monospace; color: #1e40af; background: #eff6ff; border-color: #93c5fd; }
  .sc-panel.female input[type="text"].cand-num { font-family: 'IBM Plex Mono', monospace; color: #9d174d; background: #fdf2f8; border-color: #f9a8d4; }

  .score-block {
    display: flex;
    align-items: center;
    gap: 14px;
    margin: 18px 0 20px;
    padding: 16px 18px;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
  }
  .sc-panel.male .score-block { background: #1e3a8a; }
  .sc-panel.female .score-block { background: #831843; }
  .score-block label { color: rgba(255,255,255,0.8); font-family: 'IBM Plex Mono', monospace; font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 2px; }
  .score-block input[type="number"] {
    background: transparent !important;
    border: none !important;
    color: #ffffff !important;
    font-family: 'Bebas Neue', sans-serif !important;
    font-size: 40px !important;
    letter-spacing: 0.04em !important;
    width: 100% !important;
    padding: 0 !important;
    text-align: left !important;
    box-shadow: none !important;
    line-height: 1 !important;
  }
  .score-block input[type="number"]:focus { box-shadow: none !important; outline: none !important; }
  .score-max {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 14px;
    color: rgba(255,255,255,0.7);
    white-space: nowrap;
    font-weight: 700;
  }

  .sc-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }
  button.act {
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    padding: 12px 10px;
    border-radius: 10px;
    cursor: pointer;
    border: 1.5px solid transparent;
    transition: all .15s ease;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
  }
  button.act:active { transform: translateY(1px); }

  /* Submit / Reset Styling */
  .sc-panel.male .submit { background: #2563eb; color: #ffffff; box-shadow: 0 4px 10px rgba(37,99,235,0.25); }
  .sc-panel.male .submit:hover { background: #1d4ed8; }
  .sc-panel.male .submit:disabled { opacity: 0.45; cursor: not-allowed; shadow: none; }
  .sc-panel.male .reset { background: #ffffff; border-color: #93c5fd; color: #1e40af; }
  .sc-panel.male .reset:hover { background: #eff6ff; }
  .sc-panel.male .reset:disabled { opacity: 0.45; cursor: not-allowed; }

  .sc-panel.female .submit { background: #db2777; color: #ffffff; box-shadow: 0 4px 10px rgba(219,39,119,0.25); }
  .sc-panel.female .submit:hover { background: #be185d; }
  .sc-panel.female .submit:disabled { opacity: 0.45; cursor: not-allowed; shadow: none; }
  .sc-panel.female .reset { background: #ffffff; border-color: #f9a8d4; color: #9d174d; }
  .sc-panel.female .reset:hover { background: #fdf2f8; }
  .sc-panel.female .reset:disabled { opacity: 0.45; cursor: not-allowed; }

  .sc-status {
    margin-top: 12px;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 12px;
    min-height: 18px;
    font-weight: 700;
  }

  .sc-footer {
    max-width: 1180px;
    margin: 24px auto 0;
    text-align: center;
    font-family: 'IBM Plex Mono', monospace;
    font-size: 11px;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--text-muted);
  }

  @media (max-width: 820px) {
    .card-pair { grid-template-columns: 1fr; gap: 20px; }
    .divider { flex-direction: row; justify-content: center; padding: 6px 0; }
    .divider .perf { display: none; }
  }
</style>
@endpush

@section('content')
@php
    $maleByNum   = $maleCandidates->keyBy('candidate_number');
    $femaleByNum = $femaleCandidates->keyBy('candidate_number');
    $allNumbers  = $maleCandidates->pluck('candidate_number')
                    ->concat($femaleCandidates->pluck('candidate_number'))
                    ->unique()
                    ->sort()
                    ->values();

    $totalPairs = $allNumbers->count();
    $totalSubmitted = count($scores);
@endphp

<div class="scorecard-container">

  {{-- MASTHEAD --}}
  <div class="masthead">
    <div>
      <p class="eyebrow">{{ strtoupper($categoryName) }} · LIVE SCORING</p>
      <h1>
        <svg class="w-8 h-8 text-[var(--green-600)]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $iconPath }}"/>
        </svg>
        {{ $categoryName }} — Scoring Pad
      </h1>
    </div>
    <div class="row-nav">
      <div class="row-label">
        PAIRS <b>{{ str_pad($totalPairs, 2, '0', STR_PAD_LEFT) }}</b> &nbsp;·&nbsp; SCORED <b>{{ str_pad($totalSubmitted, 2, '0', STR_PAD_LEFT) }}</b>
      </div>
    </div>
  </div>

  @if($allNumbers->isEmpty())
      <div class="sc-panel text-center py-16" style="max-width:1180px; margin:0 auto;">
          <p class="font-mono text-sm tracking-widest text-gray-500 uppercase">No candidates found in this category.</p>
      </div>
  @else
      {{-- CARD PAIRS --}}
      @foreach($allNumbers as $index => $cNum)
          @php
              $mCand = $maleByNum->get($cNum);
              $fCand = $femaleByNum->get($cNum);
          @endphp

          <div class="card-pair">

            {{-- MALE PANEL --}}
            <section class="sc-panel male">
              <div>
                <div class="panel-banner">
                  <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center font-black text-sm">♂</span>
                    <h2>Male Candidate</h2>
                  </div>
                  <span class="font-mono text-xs font-bold tracking-wider opacity-90">PAIR #{{ str_pad($cNum, 2, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="sc-frame">
                  @if($mCand && $mCand->photo_url)
                    <img src="{{ asset('storage/' . $mCand->photo_url) }}" alt="{{ $mCand->display_name }}">
                    <span class="frame-caption">Candidate #{{ str_pad($mCand->candidate_number, 2, '0', STR_PAD_LEFT) }}</span>
                  @elseif($mCand)
                    <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="3" y="4" width="18" height="16" rx="1"/><circle cx="9" cy="10" r="2"/><path d="M3 17l5-5 4 4 3-3 6 6"/></svg>
                    <span class="frame-caption">Candidate photo</span>
                  @else
                    <span class="frame-caption">No Male Candidate</span>
                  @endif
                </div>

                @if($mCand)
                  @php
                      $mScore = $scores[$mCand->id] ?? null;
                      $mHasScore = $mScore !== null;
                  @endphp
                  <div class="field-row">
                    <div>
                      <label class="field-lbl" for="m-num-{{ $mCand->id }}">No.</label>
                      <input type="text" class="cand-num" id="m-num-{{ $mCand->id }}" value="#{{ str_pad($mCand->candidate_number, 2, '0', STR_PAD_LEFT) }}" readonly>
                    </div>
                    <div>
                      <label class="field-lbl" for="m-name-{{ $mCand->id }}">Name</label>
                      <input type="text" id="m-name-{{ $mCand->id }}" value="{{ $mCand->display_name }}" readonly>
                    </div>
                  </div>

                  <div class="score-block">
                    <div style="flex:1">
                      <label for="score-input-{{ $mCand->id }}">Score (1–10)</label>
                      <input type="number"
                             id="score-input-{{ $mCand->id }}"
                             min="1" max="10" step="0.01"
                             value="{{ $mHasScore ? number_format($mScore, 2) : '' }}"
                             placeholder="0.00"
                             {{ $mHasScore ? 'disabled' : '' }}>
                    </div>
                    <span class="score-max">/ 10.00</span>
                  </div>
                @else
                  <div class="text-center py-8 font-mono text-xs text-gray-400 uppercase tracking-widest">
                    No male candidate assigned for Pair #{{ str_pad($cNum, 2, '0', STR_PAD_LEFT) }}
                  </div>
                @endif
              </div>

              @if($mCand)
                <div>
                  <div class="sc-actions">
                    <button class="act submit" id="btn-submit-{{ $mCand->id }}" onclick="submitScore({{ $mCand->id }}, 'm')" {{ $mHasScore ? 'disabled' : '' }}>
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                      Submit
                    </button>
                    <button class="act reset" id="btn-reset-{{ $mCand->id }}" onclick="resetScore({{ $mCand->id }}, 'm')" {{ !$mHasScore ? 'disabled' : '' }}>
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                      Reset
                    </button>
                  </div>
                  <p class="sc-status text-blue-700" id="status-{{ $mCand->id }}">
                    @if($mHasScore)
                      ✓ score {{ number_format($mScore, 2) }} recorded for #{{ str_pad($mCand->candidate_number, 2, '0', STR_PAD_LEFT) }}
                    @endif
                  </p>
                </div>
              @endif
            </section>

            {{-- DIVIDER --}}
            <div class="divider">
              <span class="vs">VS</span>
              <div class="perf"></div>
            </div>

            {{-- FEMALE PANEL --}}
            <section class="sc-panel female">
              <div>
                <div class="panel-banner">
                  <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full bg-white/20 flex items-center justify-center font-black text-sm">♀</span>
                    <h2>Female Candidate</h2>
                  </div>
                  <span class="font-mono text-xs font-bold tracking-wider opacity-90">PAIR #{{ str_pad($cNum, 2, '0', STR_PAD_LEFT) }}</span>
                </div>

                <div class="sc-frame">
                  @if($fCand && $fCand->photo_url)
                    <img src="{{ asset('storage/' . $fCand->photo_url) }}" alt="{{ $fCand->display_name }}">
                    <span class="frame-caption">Candidate #{{ str_pad($fCand->candidate_number, 2, '0', STR_PAD_LEFT) }}</span>
                  @elseif($fCand)
                    <svg class="placeholder-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4"><rect x="3" y="4" width="18" height="16" rx="1"/><circle cx="9" cy="10" r="2"/><path d="M3 17l5-5 4 4 3-3 6 6"/></svg>
                    <span class="frame-caption">Candidate photo</span>
                  @else
                    <span class="frame-caption">No Female Candidate</span>
                  @endif
                </div>

                @if($fCand)
                  @php
                      $fScore = $scores[$fCand->id] ?? null;
                      $fHasScore = $fScore !== null;
                  @endphp
                  <div class="field-row">
                    <div>
                      <label class="field-lbl" for="f-num-{{ $fCand->id }}">No.</label>
                      <input type="text" class="cand-num" id="f-num-{{ $fCand->id }}" value="#{{ str_pad($fCand->candidate_number, 2, '0', STR_PAD_LEFT) }}" readonly>
                    </div>
                    <div>
                      <label class="field-lbl" for="f-name-{{ $fCand->id }}">Name</label>
                      <input type="text" id="f-name-{{ $fCand->id }}" value="{{ $fCand->display_name }}" readonly>
                    </div>
                  </div>

                  <div class="score-block">
                    <div style="flex:1">
                      <label for="score-input-{{ $fCand->id }}">Score (1–10)</label>
                      <input type="number"
                             id="score-input-{{ $fCand->id }}"
                             min="1" max="10" step="0.01"
                             value="{{ $fHasScore ? number_format($fScore, 2) : '' }}"
                             placeholder="0.00"
                             {{ $fHasScore ? 'disabled' : '' }}>
                    </div>
                    <span class="score-max">/ 10.00</span>
                  </div>
                @else
                  <div class="text-center py-8 font-mono text-xs text-gray-400 uppercase tracking-widest">
                    No female candidate assigned for Pair #{{ str_pad($cNum, 2, '0', STR_PAD_LEFT) }}
                  </div>
                @endif
              </div>

              @if($fCand)
                <div>
                  <div class="sc-actions">
                    <button class="act submit" id="btn-submit-{{ $fCand->id }}" onclick="submitScore({{ $fCand->id }}, 'f')" {{ $fHasScore ? 'disabled' : '' }}>
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                      Submit
                    </button>
                    <button class="act reset" id="btn-reset-{{ $fCand->id }}" onclick="resetScore({{ $fCand->id }}, 'f')" {{ !$fHasScore ? 'disabled' : '' }}>
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                      Reset
                    </button>
                  </div>
                  <p class="sc-status text-pink-700" id="status-{{ $fCand->id }}">
                    @if($fHasScore)
                      ✓ score {{ number_format($fScore, 2) }} recorded for #{{ str_pad($fCand->candidate_number, 2, '0', STR_PAD_LEFT) }}
                    @endif
                  </p>
                </div>
              @endif
            </section>

          </div>
      @endforeach
  @endif

  <footer class="sc-footer">
    Each row pairs one male &amp; one female candidate · scores are recorded independently
  </footer>

</div>
@endsection

@push('scripts')
<script>
    const SAVE_URL  = "{{ route('judge.save-score') }}";
    const RESET_URL = "{{ route('judge.reset-score') }}";
    const CATEGORY  = "{{ $categorySlug }}";
    const CSRF      = "{{ csrf_token() }}";

    function submitScore(candidateId, side) {
        const input     = document.getElementById(`score-input-${candidateId}`);
        const btnSubmit = document.getElementById(`btn-submit-${candidateId}`);
        const btnReset  = document.getElementById(`btn-reset-${candidateId}`);
        const status    = document.getElementById(`status-${candidateId}`);
        const numInput  = document.getElementById(`${side}-num-${candidateId}`);

        const val = parseFloat(input.value);
        if (isNaN(val) || val < 1 || val > 10) {
            status.textContent = '⚠ enter a score between 1.00 and 10.00';
            status.style.color = '#dc2626';
            input.focus();
            return;
        }

        btnSubmit.disabled = true;

        fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                category: CATEGORY,
                candidate_id: candidateId,
                score: val,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = data.score;
                input.disabled = true;

                btnSubmit.disabled = true;
                btnReset.disabled = false;

                status.style.color = side === 'm' ? '#1d4ed8' : '#be185d';
                status.textContent = `✓ score ${data.score} recorded for ${numInput.value}`;
            } else {
                btnSubmit.disabled = false;
                status.textContent = `⚠ ${data.message || 'Failed to save score'}`;
                status.style.color = '#dc2626';
            }
        })
        .catch(() => {
            btnSubmit.disabled = false;
            status.textContent = '⚠ network error while submitting score';
            status.style.color = '#dc2626';
        });
    }

    function resetScore(candidateId, side) {
        if (!confirm('Are you sure you want to reset this candidate\'s score?')) {
            return;
        }

        const input     = document.getElementById(`score-input-${candidateId}`);
        const btnSubmit = document.getElementById(`btn-submit-${candidateId}`);
        const btnReset  = document.getElementById(`btn-reset-${candidateId}`);
        const status    = document.getElementById(`status-${candidateId}`);

        btnReset.disabled = true;

        fetch(RESET_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                category: CATEGORY,
                candidate_id: candidateId,
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                input.disabled = false;
                input.focus();

                btnSubmit.disabled = false;
                btnReset.disabled = true;

                status.style.color = side === 'm' ? '#1d4ed8' : '#be185d';
                status.textContent = 'reset';
            } else {
                btnReset.disabled = false;
                status.textContent = `⚠ ${data.message || 'Failed to reset score'}`;
                status.style.color = '#dc2626';
            }
        })
        .catch(() => {
            btnReset.disabled = false;
            status.textContent = '⚠ network error while resetting score';
            status.style.color = '#dc2626';
        });
    }
</script>
@endpush