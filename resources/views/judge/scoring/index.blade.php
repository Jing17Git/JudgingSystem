@extends('layouts.judge')

@section('title', 'Judge Scorecard — ' . $categoryName)

@push('styles')
<style>
  :root {
    --bg: #eef3ee;
    --card-bg: #ffffff;
    --blue: #2e4bd9;
    --blue-dark: #26399e;
    --pink: #d81159;
    --pink-dark: #a30d47;
    --text-dark: #14213d;
    --text-muted: #7c8698;
    --green: #16a34a;
    --border: #e3e7ee;
  }

  .scorecard-page {
    max-width: 1000px;
    margin: 0 auto;
    color: var(--text-dark);
  }

  .sc-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    padding-bottom: 18px;
    margin-bottom: 28px;
  }

  .sc-eyebrow {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: var(--green);
    margin-bottom: 6px;
    text-transform: uppercase;
  }

  .sc-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 24px;
    font-weight: 700;
    color: #0f1b33;
    margin: 0;
  }

  .sc-title .icon {
    color: var(--green);
    font-family: "SFMono-Regular", Consolas, monospace;
    font-size: 20px;
  }

  .status-pill {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    letter-spacing: 0.04em;
    background: #fff;
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 8px 14px;
    white-space: nowrap;
  }

  .pair-slide {
    display: none;
  }
  .pair-slide.active {
    display: block;
  }

  .cand-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
  }

  .cand-card {
    background: var(--card-bg);
    border-radius: 14px;
    overflow: hidden;
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(20, 33, 61, 0.06);
    display: flex;
    flex-direction: column;
  }

  .cand-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    color: #fff;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.03em;
  }

  .cand-card-header .left {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .cand-card-header .avatar {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: rgba(255,255,255,0.25);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: bold;
  }

  .cand-card-header .pair-tag {
    font-size: 11px;
    font-weight: 600;
    opacity: 0.9;
  }

  .male .cand-card-header { background: linear-gradient(90deg, #2e4bd9, #3a5aec); }
  .female .cand-card-header { background: linear-gradient(90deg, #d81159, #e8226f); }

  .photo-wrap {
    position: relative;
    height: 260px;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
  }

  .photo-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  .candidate-tag {
    position: absolute;
    bottom: 10px;
    left: 10px;
    background: rgba(0,0,0,0.65);
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    letter-spacing: 0.04em;
    padding: 4px 8px;
    border-radius: 4px;
    backdrop-filter: blur(4px);
  }

  .cand-card-body {
    padding: 18px;
    display: flex;
    flex-direction: column;
    flex: 1;
    justify-content: space-between;
  }

  .field-row {
    display: grid;
    grid-template-columns: 90px 1fr;
    gap: 12px;
    margin-bottom: 16px;
  }

  .field label {
    display: block;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    color: var(--text-muted);
    margin-bottom: 6px;
    text-transform: uppercase;
  }

  .field input {
    width: 100%;
    padding: 9px 10px;
    border-radius: 7px;
    border: 1px solid var(--border);
    font-size: 14px;
    font-weight: 600;
    color: var(--text-dark);
    outline: none;
    background: #ffffff;
  }

  .male .field.no input {
    background: #eef1fd;
    border-color: #c7d1f7;
    color: var(--blue);
  }

  .female .field.no input {
    background: #fdeef3;
    border-color: #f3c3d5;
    color: var(--pink);
  }

  .score-actions-row {
    display: flex;
    gap: 12px;
    align-items: stretch;
    margin-bottom: 10px;
  }

  .score-box {
    border-radius: 10px;
    padding: 12px 16px;
    color: #fff;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .male .score-box { background: var(--blue-dark); }
  .female .score-box { background: var(--pink-dark); }

  .score-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.06em;
    opacity: 0.85;
    margin-bottom: 4px;
  }

  .score-value {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
  }

  .score-select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.35);
    border-radius: 8px;
    color: #fff;
    font-size: 26px;
    font-weight: 700;
    padding: 4px 34px 4px 10px;
    cursor: pointer;
    outline: none;
    font-family: inherit;
    width: auto;
    min-width: 75px;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='3'><polyline points='6 9 12 15 18 9'/></svg>");
    background-repeat: no-repeat;
    background-position: right 10px center;
  }

  .score-select:disabled {
    opacity: 0.85;
    cursor: not-allowed;
  }

  .score-select option {
    color: #14213d;
    font-weight: 600;
    font-size: 16px;
  }

  .score-value .max {
    font-size: 13px;
    opacity: 0.75;
    font-weight: 600;
  }

  .btn-row {
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex: 0 0 130px;
  }

  .btn {
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    border: 1px solid transparent;
    user-select: none;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s ease, opacity 0.15s ease;
  }

  .male .btn-submit { background: #2e4bd9; color: #fff; }
  .male .btn-submit:hover:not(:disabled) { background: #233cae; }
  .male .btn-submit:disabled { background: #9fb0ee; color: #fff; cursor: not-allowed; opacity: 0.7; }

  .female .btn-submit { background: #d81159; color: #fff; }
  .female .btn-submit:hover:not(:disabled) { background: #ad0e47; }
  .female .btn-submit:disabled { background: #f0a6c0; color: #fff; cursor: not-allowed; opacity: 0.7; }

  .btn-reset {
    background: #fff;
    border: 1px solid var(--border);
    color: var(--text-dark);
  }
  .btn-reset:disabled {
    opacity: 0.4;
    cursor: not-allowed;
  }

  .male .btn-reset { border-color: #c7d1f7; color: var(--blue); }
  .male .btn-reset:hover:not(:disabled) { background: #f0f3fe; }
  .female .btn-reset { border-color: #f3c3d5; color: var(--pink); }
  .female .btn-reset:hover:not(:disabled) { background: #fdf0f4; }

  .confirm-msg {
    font-size: 12px;
    color: var(--green);
    display: flex;
    align-items: center;
    gap: 6px;
    min-height: 18px;
    font-weight: 500;
  }
  .confirm-msg.error {
    color: #dc2626;
  }

  .footer-note {
    text-align: center;
    font-size: 10px;
    color: var(--text-muted);
    letter-spacing: 0.03em;
    margin-top: 34px;
    text-transform: uppercase;
  }

  .nav-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin-top: 28px;
  }

  .nav-btn {
    display: none;
    align-items: center;
    gap: 6px;
    padding: 11px 22px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.03em;
    cursor: pointer;
    user-select: none;
    border: 1px solid var(--border);
    background: #fff;
    color: var(--text-dark);
    transition: background 0.15s ease, transform 0.1s ease;
  }

  .nav-btn:active { transform: scale(0.97); }
  .nav-btn.visible { display: inline-flex; }

  #nextBtn {
    background: #14213d;
    color: #fff;
    border-color: #14213d;
    margin-left: auto;
  }

  #nextBtn:hover { background: #223057; }
  #prevBtn:hover { background: #f4f6fa; }

  @media (max-width: 760px) {
    .cand-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush

@section('content')
@php
    $inQa = in_array($categorySlug, ['qa', 'qanda']);
    if ($inQa) {
        $maleList = $maleCandidates->values();
        $femaleList = $femaleCandidates->values();
        $totalPairs = max($maleList->count(), $femaleList->count());
    } else {
        $maleByNum   = $maleCandidates->keyBy('candidate_number');
        $femaleByNum = $femaleCandidates->keyBy('candidate_number');
        $allNumbers  = $maleCandidates->pluck('candidate_number')
                        ->concat($femaleCandidates->pluck('candidate_number'))
                        ->unique()
                        ->sort()
                        ->values();
        $totalPairs = $allNumbers->count();
    }

    $totalSubmitted = count($scores);
@endphp

<div class="scorecard-page">

  {{-- HEADER --}}
  <div class="sc-header">
    <div>
      <div class="sc-eyebrow">{{ strtoupper($categoryName) }} · LIVE SCORING</div>
      <h1 class="sc-title"><span class="icon">&lt;/&gt;</span> {{ $categoryName }} — Scoring Pad</h1>
    </div>
    <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 6px;">
      <div class="status-pill">
        PAIRS <span id="pairIndexText">{{ str_pad($totalPairs > 0 ? 1 : 0, 2, '0', STR_PAD_LEFT) }}</span> / {{ str_pad($totalPairs, 2, '0', STR_PAD_LEFT) }} &nbsp;·&nbsp; SCORED <span id="scoredCount">{{ str_pad($totalSubmitted, 2, '0', STR_PAD_LEFT) }}</span>
      </div>
      <div style="font-size: 11px; font-weight: 700; letter-spacing: 0.06em; color: #16a34a; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 5px 12px;">
        JUDGE {{ auth()->user()->judge_number ?? auth()->user()->id }} · {{ strtoupper(auth()->user()->name) }}
      </div>
    </div>
  </div>

  @if($totalPairs === 0)
    <div class="bg-white rounded-xl p-12 text-center border border-[var(--border)]">
      <p class="text-sm font-semibold text-[var(--text-muted)] uppercase tracking-wider">No candidates found in this category.</p>
    </div>
  @else
    {{-- CANDIDATE PAIRS --}}
    @for($i = 0; $i < $totalPairs; $i++)
      @php
          if ($inQa) {
              $mCand = $maleList->get($i);
              $fCand = $femaleList->get($i);
              $cNum = $i + 1;
          } else {
              $cNum = $allNumbers->get($i);
              $mCand = $maleByNum->get($cNum);
              $fCand = $femaleByNum->get($cNum);
          }
      @endphp

      <div class="pair-slide {{ $i === ($initialPairIndex ?? 0) ? 'active' : '' }}" data-index="{{ $i }}">
        <div class="cand-grid">

          {{-- MALE CANDIDATE --}}
          @if($mCand)
            @php
                $mScore = $scores[$mCand->id] ?? null;
                $mHasScore = $mScore !== null;
            @endphp
            <div class="cand-card male">
              <div class="cand-card-header">
                <div class="left">
                  <span class="avatar">♂</span>
                  <span>CANDIDATE NO: {{ str_pad($mCand->candidate_number, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <span class="pair-tag">{{ $mCand->display_name }}</span>
              </div>

              <div class="photo-wrap">
                @if($mCand->photo_url)
                  <img src="{{ asset('storage/' . $mCand->photo_url) }}" alt="{{ $mCand->display_name }}">
                @else
                  <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-100">
                    <svg class="w-12 h-12 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M3 17l5-5 4 4 3-3 6 6"/>
                    </svg>
                  </div>
                @endif
                <span class="candidate-tag">CANDIDATE #{{ str_pad($mCand->candidate_number, 2, '0', STR_PAD_LEFT) }}</span>
              </div>

              <div class="cand-card-body">
                <div class="score-actions-row">
                  <div class="score-box">
                    <div class="score-label">SCORE (1–10)</div>
                    <div class="score-value">
                      <select class="score-select" id="score-select-{{ $mCand->id }}" {{ $mHasScore ? 'disabled' : '' }} {!! $mHasScore ? 'data-has-score="true"' : '' !!}>
                        <option value="" disabled {{ !$mHasScore ? 'selected' : '' }}>--</option>
                        @for($s = 1; $s <= 10; $s++)
                          <option value="{{ $s }}" {{ ($mHasScore && (float)$mScore == $s) ? 'selected' : '' }}>{{ $s }}</option>
                        @endfor
                        @if($mHasScore && !in_array((float)$mScore, [1,2,3,4,5,6,7,8,9,10]))
                          <option value="{{ $mScore }}" selected>{{ number_format((float)$mScore, 2) }}</option>
                        @endif
                      </select>
                      <span class="max">/ 10</span>
                    </div>
                  </div>

                  <div class="btn-row">
                    <button type="button" class="btn btn-submit" id="btn-submit-{{ $mCand->id }}" onclick="submitScore({{ $mCand->id }}, 'm', '{{ str_pad($mCand->candidate_number, 2, '0', STR_PAD_LEFT) }}')" {{ $mHasScore ? 'disabled' : '' }}>✓ SUBMIT</button>
                    <button type="button" class="btn btn-reset" id="btn-reset-{{ $mCand->id }}" onclick="resetScore({{ $mCand->id }}, 'm', '{{ str_pad($mCand->candidate_number, 2, '0', STR_PAD_LEFT) }}')" {{ !$mHasScore ? 'disabled' : '' }}>↻ RESET</button>
                  </div>
                </div>

                <div class="confirm-msg" id="status-{{ $mCand->id }}">
                  @if($mHasScore)
                    ✓ score {{ (float)$mScore }} recorded for #{{ str_pad($mCand->candidate_number, 2, '0', STR_PAD_LEFT) }}
                  @endif
                </div>
              </div>
            </div>
          @else
            <div class="cand-card male opacity-50">
              <div class="cand-card-header">
                <div class="left">
                  <span class="avatar">♂</span>
                  <span>CANDIDATE NO: --</span>
                </div>
                <span class="pair-tag">No Candidate</span>
              </div>
              <div class="photo-wrap">
                <span class="text-slate-400 text-xs font-semibold">No Male Candidate for Pair #{{ str_pad($cNum, 2, '0', STR_PAD_LEFT) }}</span>
              </div>
              <div class="cand-card-body">
                <p class="text-xs text-slate-400 text-center py-4">No scoring available</p>
              </div>
            </div>
          @endif

          {{-- FEMALE CANDIDATE --}}
          @if($fCand)
            @php
                $fScore = $scores[$fCand->id] ?? null;
                $fHasScore = $fScore !== null;
            @endphp
            <div class="cand-card female">
              <div class="cand-card-header">
                <div class="left">
                  <span class="avatar">♀</span>
                  <span>CANDIDATE NO: {{ str_pad($fCand->candidate_number, 2, '0', STR_PAD_LEFT) }}</span>
                </div>
                <span class="pair-tag">{{ $fCand->display_name }}</span>
              </div>

              <div class="photo-wrap">
                @if($fCand->photo_url)
                  <img src="{{ asset('storage/' . $fCand->photo_url) }}" alt="{{ $fCand->display_name }}">
                @else
                  <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 bg-slate-100">
                    <svg class="w-12 h-12 opacity-30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="M3 17l5-5 4 4 3-3 6 6"/>
                    </svg>
                  </div>
                @endif
                <span class="candidate-tag">CANDIDATE #{{ str_pad($fCand->candidate_number, 2, '0', STR_PAD_LEFT) }}</span>
              </div>

              <div class="cand-card-body">
                <div class="score-actions-row">
                  <div class="score-box">
                    <div class="score-label">SCORE (1–10)</div>
                    <div class="score-value">
                      <select class="score-select" id="score-select-{{ $fCand->id }}" {{ $fHasScore ? 'disabled' : '' }} {!! $fHasScore ? 'data-has-score="true"' : '' !!}>
                        <option value="" disabled {{ !$fHasScore ? 'selected' : '' }}>--</option>
                        @for($s = 1; $s <= 10; $s++)
                          <option value="{{ $s }}" {{ ($fHasScore && (float)$fScore == $s) ? 'selected' : '' }}>{{ $s }}</option>
                        @endfor
                        @if($fHasScore && !in_array((float)$fScore, [1,2,3,4,5,6,7,8,9,10]))
                          <option value="{{ $fScore }}" selected>{{ number_format((float)$fScore, 2) }}</option>
                        @endif
                      </select>
                      <span class="max">/ 10</span>
                    </div>
                  </div>

                  <div class="btn-row">
                    <button type="button" class="btn btn-submit" id="btn-submit-{{ $fCand->id }}" onclick="submitScore({{ $fCand->id }}, 'f', '{{ str_pad($fCand->candidate_number, 2, '0', STR_PAD_LEFT) }}')" {{ $fHasScore ? 'disabled' : '' }}>✓ SUBMIT</button>
                    <button type="button" class="btn btn-reset" id="btn-reset-{{ $fCand->id }}" onclick="resetScore({{ $fCand->id }}, 'f', '{{ str_pad($fCand->candidate_number, 2, '0', STR_PAD_LEFT) }}')" {{ !$fHasScore ? 'disabled' : '' }}>↻ RESET</button>
                  </div>
                </div>

                <div class="confirm-msg" id="status-{{ $fCand->id }}">
                  @if($fHasScore)
                    ✓ score {{ (float)$fScore }} recorded for #{{ str_pad($fCand->candidate_number, 2, '0', STR_PAD_LEFT) }}
                  @endif
                </div>
              </div>
            </div>
          @else
            <div class="cand-card female opacity-50">
              <div class="cand-card-header">
                <div class="left">
                  <span class="avatar">♀</span>
                  <span>CANDIDATE NO: --</span>
                </div>
                <span class="pair-tag">No Candidate</span>
              </div>
              <div class="photo-wrap">
                <span class="text-slate-400 text-xs font-semibold">No Female Candidate for Pair #{{ str_pad($cNum, 2, '0', STR_PAD_LEFT) }}</span>
              </div>
              <div class="cand-card-body">
                <p class="text-xs text-slate-400 text-center py-4">No scoring available</p>
              </div>
            </div>
          @endif

        </div>
      </div>
    @endfor

    {{-- NAVIGATION ROW --}}
    <div class="nav-row">
      <button type="button" class="nav-btn" id="prevBtn" onclick="goPrev()">← PREV</button>
      <button type="button" class="nav-btn {{ $totalPairs > 1 ? 'visible' : '' }}" id="nextBtn" onclick="goNext()">NEXT →</button>
    </div>
  @endif

  <div class="footer-note">EACH ROW PAIRS ONE MALE &amp; ONE FEMALE CANDIDATE · SCORES ARE RECORDED INDEPENDENTLY</div>

</div>
@endsection

@push('scripts')
<script>
    const SAVE_URL  = "{{ route('judge.save-score') }}";
    const RESET_URL = "{{ route('judge.reset-score') }}";
    const CATEGORY  = "{{ $categorySlug }}";
    const CSRF      = "{{ csrf_token() }}";
    const totalPairs = {{ $totalPairs }};
    let currentPairIndex = {{ $initialPairIndex ?? 0 }};
    let totalScored = {{ $totalSubmitted }};

    function updateScoredCountDisplay() {
        const el = document.getElementById('scoredCount');
        if (el) {
            el.textContent = String(totalScored).padStart(2, '0');
        }
    }

    function updateNavButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pairIndexText = document.getElementById('pairIndexText');

        if (pairIndexText) {
            pairIndexText.textContent = totalPairs > 0 ? String(currentPairIndex + 1).padStart(2, '0') : '00';
        }

        if (prevBtn) {
            if (currentPairIndex > 0) {
                prevBtn.classList.add('visible');
            } else {
                prevBtn.classList.remove('visible');
            }
        }

        if (nextBtn) {
            if (totalPairs > 1 && currentPairIndex < totalPairs - 1) {
                nextBtn.classList.add('visible');
            } else {
                nextBtn.classList.remove('visible');
            }
        }
    }

    function showPair(index, updateUrl = true) {
        const slides = document.querySelectorAll('.pair-slide');
        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });
        currentPairIndex = index;
        updateNavButtons();

        if (updateUrl && window.history && window.history.pushState) {
            const url = new URL(window.location.href);
            url.searchParams.set('pair', index + 1);
            if (url.searchParams.has('candidate_id')) {
                url.searchParams.delete('candidate_id');
            }
            if (window.location.search !== url.search) {
                window.history.pushState({ pair: index + 1 }, '', url.toString());
            }
        }
    }

    function goNext() {
        if (currentPairIndex < totalPairs - 1) {
            currentPairIndex++;
            showPair(currentPairIndex);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    function goPrev() {
        if (currentPairIndex > 0) {
            currentPairIndex--;
            showPair(currentPairIndex);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // Handle browser Back / Forward navigation
    window.addEventListener('popstate', (e) => {
        const params = new URLSearchParams(window.location.search);
        let p = parseInt(params.get('pair'));
        if (!isNaN(p) && p >= 1 && p <= totalPairs) {
            showPair(p - 1, false);
        } else {
            showPair(0, false);
        }
    });

    function submitScore(candidateId, side, candNum) {
        const select    = document.getElementById(`score-select-${candidateId}`);
        const btnSubmit = document.getElementById(`btn-submit-${candidateId}`);
        const btnReset  = document.getElementById(`btn-reset-${candidateId}`);
        const status    = document.getElementById(`status-${candidateId}`);

        const val = parseFloat(select.value);
        if (isNaN(val) || val < 1 || val > 10) {
            status.className = 'confirm-msg error';
            status.textContent = '⚠ Please select a score (1–10)';
            select.focus();
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
                select.disabled = true;
                btnSubmit.disabled = true;
                btnReset.disabled = false;

                status.className = 'confirm-msg';
                status.textContent = `✓ score ${parseFloat(data.score)} recorded for #${candNum}`;

                if (!select.dataset.hasScore) {
                    select.dataset.hasScore = "true";
                    totalScored++;
                    updateScoredCountDisplay();
                }
            } else {
                btnSubmit.disabled = false;
                status.className = 'confirm-msg error';
                status.textContent = `⚠ ${data.message || 'Failed to save score'}`;
            }
        })
        .catch(() => {
            btnSubmit.disabled = false;
            status.className = 'confirm-msg error';
            status.textContent = '⚠ Network error while submitting score';
        });
    }

    function resetScore(candidateId, side, candNum) {
        if (!confirm(`Are you sure you want to reset score for Candidate #${candNum}?`)) {
            return;
        }

        const select    = document.getElementById(`score-select-${candidateId}`);
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
                select.value = '';
                select.disabled = false;
                select.focus();

                btnSubmit.disabled = false;
                btnReset.disabled = true;

                status.className = 'confirm-msg';
                status.textContent = '';

                if (select.dataset.hasScore) {
                    delete select.dataset.hasScore;
                    totalScored = Math.max(0, totalScored - 1);
                    updateScoredCountDisplay();
                }
            } else {
                btnReset.disabled = false;
                status.className = 'confirm-msg error';
                status.textContent = `⚠ ${data.message || 'Failed to reset score'}`;
            }
        })
        .catch(() => {
            btnReset.disabled = false;
            status.className = 'confirm-msg error';
            status.textContent = '⚠ Network error while resetting score';
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        showPair(currentPairIndex, false);
        setupJudgeEcho();
    });

    const CURRENT_JUDGE_ID = {{ auth()->id() }};

    function setupJudgeEcho() {
        if (!window.Echo) {
            setTimeout(setupJudgeEcho, 400);
            return;
        }

        try {
            window.Echo.private('judge.scores')
                .listen('.score.submitted', (e) => {
                    // If current judge updated score in this category (e.g. from another tab/device)
                    if (e.judge_id === CURRENT_JUDGE_ID && e.category === CATEGORY) {
                        const select    = document.getElementById(`score-select-${e.candidate_id}`);
                        const btnSubmit = document.getElementById(`btn-submit-${e.candidate_id}`);
                        const btnReset  = document.getElementById(`btn-reset-${e.candidate_id}`);
                        const status    = document.getElementById(`status-${e.candidate_id}`);
                        const candNum   = String(e.candidate_number).padStart(2, '0');

                        if (select && btnSubmit && btnReset && status) {
                            if (e.action === 'saved' && e.score !== null) {
                                select.value = parseFloat(e.score);
                                select.disabled = true;
                                btnSubmit.disabled = true;
                                btnReset.disabled = false;
                                status.className = 'confirm-msg';
                                status.textContent = `✓ score ${parseFloat(e.score)} recorded for #${candNum}`;

                                if (!select.dataset.hasScore) {
                                    select.dataset.hasScore = "true";
                                    totalScored++;
                                    updateScoredCountDisplay();
                                }
                            } else if (e.action === 'reset') {
                                select.value = '';
                                select.disabled = false;
                                btnSubmit.disabled = false;
                                btnReset.disabled = true;
                                status.className = 'confirm-msg';
                                status.textContent = '';

                                if (select.dataset.hasScore) {
                                    delete select.dataset.hasScore;
                                    totalScored = Math.max(0, totalScored - 1);
                                    updateScoredCountDisplay();
                                }
                            }
                        }
                    } else if (e.judge_id !== CURRENT_JUDGE_ID && window.showToast) {
                        // Toast notification for judge (without leaking numerical score to preserve judging confidentiality)
                        if (e.action === 'saved') {
                            window.showToast(
                                'Judging Activity',
                                `${e.judge_name} submitted a score in ${e.category_name}`,
                                'info',
                                3500
                            );
                        }
                    }
                });
        } catch (err) {
            console.error('Judge Echo setup error:', err);
        }
    }
</script>
@endpush