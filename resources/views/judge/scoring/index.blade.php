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

  /* Finalized Alert Banner */
  .finalized-alert-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
    color: #ecfdf5;
    padding: 16px 22px;
    border-radius: 12px;
    margin-bottom: 22px;
    border: 1px solid #10b981;
    box-shadow: 0 4px 14px rgba(6, 78, 59, 0.18);
  }
  .banner-icon-wrap {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.18);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    color: #ffffff;
  }
  .banner-content { flex: 1; }
  .banner-title {
    font-size: 15px;
    font-weight: 800;
    letter-spacing: 0.02em;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ffffff;
  }
  .banner-subtitle {
    font-size: 12.5px;
    opacity: 0.95;
    margin-top: 3px;
    color: #d1fae5;
  }
  .locked-pill-badge {
    background: #ffffff;
    color: #065f46;
    font-size: 11px;
    font-weight: 800;
    padding: 6px 14px;
    border-radius: 9999px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    white-space: nowrap;
  }

  /* View Switcher Toolbar */
  .sc-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 22px;
    background: #ffffff;
    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid var(--border);
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
  }
  .view-toggle-pills {
    display: inline-flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 9px;
    gap: 4px;
  }
  .view-toggle-btn {
    border: none;
    background: transparent;
    padding: 7px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 700;
    color: var(--text-muted);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
  }
  .view-toggle-btn:hover { color: var(--text-dark); }
  .view-toggle-btn.active {
    background: #ffffff;
    color: var(--text-dark);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.08);
  }

  /* Overall Scoring Table */
  .table-view-section { display: none; }
  .table-view-section.active { display: block; }
  .card-view-section { display: none; }
  .card-view-section.active { display: block; }

  .overall-scoring-card {
    background: #ffffff;
    border: 1px solid var(--border);
    border-radius: 14px;
    box-shadow: 0 1px 4px rgba(20, 33, 61, 0.04);
    overflow: hidden;
    margin-bottom: 24px;
  }
  .table-header-bar {
    padding: 14px 20px;
    background: #f8fafc;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .table-title-group {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 14px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: 0.02em;
  }
  .gender-badge-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
  }
  .male-dot { background: var(--blue); }
  .female-dot { background: var(--pink); }

  .overall-data-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13px;
  }
  .overall-data-table th {
    background: #f8fafc;
    color: #64748b;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    text-align: left;
  }
  .overall-data-table td {
    padding: 12px 18px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
  }
  .overall-data-table tr:last-child td {
    border-bottom: none;
  }
  .overall-data-table tr:hover td {
    background: #fbfcfe;
  }

  .cand-num-pill {
    font-family: "SFMono-Regular", Consolas, monospace;
    font-size: 12px;
    font-weight: 800;
    padding: 4px 9px;
    border-radius: 6px;
    display: inline-block;
  }
  .cand-num-pill.male { background: #eef1fd; color: var(--blue); border: 1px solid #c7d1f7; }
  .cand-num-pill.female { background: #fdeef3; color: var(--pink); border: 1px solid #f3c3d5; }

  .cand-thumb {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    object-fit: cover;
    background: #e2e8f0;
    border: 1px solid var(--border);
  }
  .cand-thumb-fallback {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    background: #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    font-size: 14px;
    font-weight: bold;
    border: 1px solid var(--border);
  }

  .table-score-input-wrap {
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .table-score-input {
    width: 96px;
    padding: 8px 10px;
    border-radius: 7px;
    border: 1.5px solid var(--border);
    font-size: 14px;
    font-weight: 700;
    color: #0f172a;
    background: #ffffff;
    text-align: center;
    outline: none;
    transition: all 0.15s ease;
  }
  .table-score-input:focus {
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
  }
  .table-score-input:disabled {
    background: #f8fafc;
    color: #64748b;
    cursor: not-allowed;
    border-color: #e2e8f0;
  }

  .table-btn-save {
    padding: 7px 13px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    background: #16a34a;
    color: #ffffff;
    border: none;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .table-btn-save:hover:not(:disabled) { background: #15803d; }
  .table-btn-save:disabled { background: #94a3b8; cursor: not-allowed; opacity: 0.6; }

  .table-btn-reset {
    padding: 7px 11px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    transition: all 0.15s ease;
  }
  .table-btn-reset:hover:not(:disabled) { background: #e2e8f0; color: #334155; }
  .table-btn-reset:disabled { opacity: 0.4; cursor: not-allowed; }

  .status-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 9999px;
  }
  .status-chip.scored { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .status-chip.pending { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }

  /* Final Submission Box */
  .final-submission-card {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 14px;
    padding: 24px 28px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    box-shadow: 0 4px 20px rgba(15, 23, 42, 0.12);
    margin-top: 24px;
    border: 1px solid #334155;
  }
  .final-sub-info h3 {
    font-size: 18px;
    font-weight: 800;
    margin: 0 0 6px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #ffffff;
  }
  .final-sub-info p {
    font-size: 12.5px;
    color: #94a3b8;
    margin: 0;
  }
  .btn-final-submit {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: 0.02em;
    padding: 13px 26px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
  }
  .btn-final-submit:hover:not(:disabled) {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
  }
  .btn-final-submit:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    box-shadow: none;
  }
  .btn-final-submit.finalized {
    background: #065f46;
    color: #a7f3d0;
    border: 1px solid #10b981;
    cursor: default;
    opacity: 1;
  }

  /* Modal */
  .sc-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(15, 23, 42, 0.7);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s ease;
    padding: 16px;
  }
  .sc-modal-backdrop.open {
    opacity: 1;
    pointer-events: auto;
  }
  .sc-modal-dialog {
    background: #ffffff;
    width: 100%;
    max-width: 520px;
    border-radius: 16px;
    padding: 26px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    transform: scale(0.96);
    transition: transform 0.2s ease;
  }
  .sc-modal-backdrop.open .sc-modal-dialog {
    transform: scale(1);
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

  {{-- FINALIZED ALERT BANNER --}}
  <div id="finalizedBanner" class="finalized-alert-banner" style="{{ !empty($isFinalized) ? '' : 'display:none;' }}">
    <div class="banner-icon-wrap">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
      </svg>
    </div>
    <div class="banner-content">
      <div class="banner-title">
        <span>Category Scoring Finalized &amp; Locked</span>
      </div>
      <div class="banner-subtitle" id="bannerSubtitleText">
        Your evaluation for <strong>{{ $categoryName }}</strong> was officially submitted{{ !empty($finalizedAt) ? ' on ' . $finalizedAt->format('F d, Y · h:i A') : '' }}. All scoring inputs are locked and disabled.
      </div>
    </div>
    <span class="locked-pill-badge">🔒 LOCKED</span>
  </div>

  {{-- TOOLBAR: VIEW MODE SWITCHER & QUICK STATUS --}}
  <div class="sc-toolbar">
    <div class="view-toggle-pills">
      <button type="button" class="view-toggle-btn active" id="btnModeCard" onclick="switchViewMode('card')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <span>Card / Pair Focus</span>
      </button>
      <button type="button" class="view-toggle-btn" id="btnModeTable" onclick="switchViewMode('table')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
        </svg>
        <span>Overall Scoring Table</span>
      </button>
    </div>

    <div style="display: flex; align-items: center; gap: 12px;">
      <div style="font-size: 12px; font-weight: 700; color: #475569;">
        Evaluation: <span id="toolbarScoredCount" style="color: #16a34a;">{{ $totalSubmitted }}</span> / {{ $maleCandidates->count() + $femaleCandidates->count() }} Scored
      </div>

      <div id="quickActionArea">
        @if(!empty($isFinalized))
          <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">
            🔒 Finalized
          </span>
        @else
          <button type="button" id="quickFinalizeBtn" class="btn btn-green btn-sm" onclick="openFinalSubmissionModal()" style="padding: 6px 14px; font-size: 11px; font-weight: 800; border-radius: 7px;">
            ✓ Final Submission
          </button>
        @endif
      </div>
    </div>
  </div>

  @if($totalPairs === 0)
    <div class="bg-white rounded-xl p-12 text-center border border-[var(--border)]">
      <p class="text-sm font-semibold text-[var(--text-muted)] uppercase tracking-wider">No candidates found in this category.</p>
    </div>
  @else
    {{-- CARD VIEW CONTAINER --}}
    <div id="cardViewSection" class="card-view-section active">
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

      <div class="nav-row">
        <button type="button" class="nav-btn" id="prevBtn" onclick="goPrev()">← PREV</button>
        <button type="button" class="nav-btn {{ $totalPairs > 1 ? 'visible' : '' }}" id="nextBtn" onclick="goNext()">NEXT →</button>
      </div>
    </div> {{-- /cardViewSection --}}

    {{-- OVERALL SCORING TABLE CONTAINER --}}
    <div id="tableViewSection" class="table-view-section">

      {{-- MALE CONTESTANTS TABLE --}}
      @if($maleCandidates->isNotEmpty())
        <div class="overall-scoring-card">
          <div class="table-header-bar">
            <div class="table-title-group">
              <span class="gender-badge-dot male-dot"></span>
              <span>Male Contestants ({{ $maleCandidates->count() }})</span>
            </div>
            <span class="text-xs font-semibold text-slate-500">Evaluation Range: 1.00 – 10.00</span>
          </div>
          <div class="overflow-x-auto">
            <table class="overall-data-table">
              <thead>
                <tr>
                  <th style="width: 85px;">No.</th>
                  <th style="width: 65px;">Photo</th>
                  <th>Candidate Name</th>
                  <th style="width: 220px;">Score (1–10)</th>
                  <th style="width: 140px;">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($maleCandidates as $cand)
                  @php
                    $cScore = $scores[$cand->id] ?? null;
                    $cHasScore = $cScore !== null;
                  @endphp
                  <tr id="table-row-{{ $cand->id }}">
                    <td>
                      <span class="cand-num-pill male">#{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td>
                      @if($cand->photo_url)
                        <img src="{{ asset('storage/' . $cand->photo_url) }}" alt="{{ $cand->display_name }}" class="cand-thumb">
                      @else
                        <div class="cand-thumb-fallback">♂</div>
                      @endif
                    </td>
                    <td>
                      <div class="font-bold text-slate-900">{{ $cand->display_name }}</div>
                      @if(!empty($cand->origin))
                        <div class="text-xs text-slate-500">{{ $cand->origin }}</div>
                      @endif
                    </td>
                    <td>
                      <div class="table-score-input-wrap">
                        <input type="number" step="0.01" min="1" max="10"
                          class="table-score-input"
                          id="table-score-{{ $cand->id }}"
                          value="{{ $cHasScore ? number_format((float)$cScore, 2, '.', '') : '' }}"
                          placeholder="1.00–10"
                          {{ ($cHasScore || !empty($isFinalized)) ? 'disabled' : '' }}
                          data-candidate-id="{{ $cand->id }}"
                          data-cand-num="{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}"
                          onkeydown="if(event.key==='Enter') submitTableScore({{ $cand->id }}, '{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}')">

                        <button type="button" class="table-btn-save" id="table-save-{{ $cand->id }}"
                          onclick="submitTableScore({{ $cand->id }}, '{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}')"
                          {{ ($cHasScore || !empty($isFinalized)) ? 'disabled' : '' }}>
                          Save
                        </button>
                        <button type="button" class="table-btn-reset" id="table-reset-{{ $cand->id }}"
                          onclick="resetTableScore({{ $cand->id }}, '{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}')"
                          {{ (!$cHasScore || !empty($isFinalized)) ? 'disabled' : '' }}>
                          Clear
                        </button>
                      </div>
                    </td>
                    <td>
                      <span class="status-chip {{ $cHasScore ? 'scored' : 'pending' }}" id="table-status-chip-{{ $cand->id }}">
                        {{ $cHasScore ? '✓ ' . number_format((float)$cScore, 2) : '○ Pending' }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      {{-- FEMALE CONTESTANTS TABLE --}}
      @if($femaleCandidates->isNotEmpty())
        <div class="overall-scoring-card">
          <div class="table-header-bar">
            <div class="table-title-group">
              <span class="gender-badge-dot female-dot"></span>
              <span>Female Contestants ({{ $femaleCandidates->count() }})</span>
            </div>
            <span class="text-xs font-semibold text-slate-500">Evaluation Range: 1.00 – 10.00</span>
          </div>
          <div class="overflow-x-auto">
            <table class="overall-data-table">
              <thead>
                <tr>
                  <th style="width: 85px;">No.</th>
                  <th style="width: 65px;">Photo</th>
                  <th>Candidate Name</th>
                  <th style="width: 220px;">Score (1–10)</th>
                  <th style="width: 140px;">Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($femaleCandidates as $cand)
                  @php
                    $cScore = $scores[$cand->id] ?? null;
                    $cHasScore = $cScore !== null;
                  @endphp
                  <tr id="table-row-{{ $cand->id }}">
                    <td>
                      <span class="cand-num-pill female">#{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td>
                      @if($cand->photo_url)
                        <img src="{{ asset('storage/' . $cand->photo_url) }}" alt="{{ $cand->display_name }}" class="cand-thumb">
                      @else
                        <div class="cand-thumb-fallback">♀</div>
                      @endif
                    </td>
                    <td>
                      <div class="font-bold text-slate-900">{{ $cand->display_name }}</div>
                      @if(!empty($cand->origin))
                        <div class="text-xs text-slate-500">{{ $cand->origin }}</div>
                      @endif
                    </td>
                    <td>
                      <div class="table-score-input-wrap">
                        <input type="number" step="0.01" min="1" max="10"
                          class="table-score-input"
                          id="table-score-{{ $cand->id }}"
                          value="{{ $cHasScore ? number_format((float)$cScore, 2, '.', '') : '' }}"
                          placeholder="1.00–10"
                          {{ ($cHasScore || !empty($isFinalized)) ? 'disabled' : '' }}
                          data-candidate-id="{{ $cand->id }}"
                          data-cand-num="{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}"
                          onkeydown="if(event.key==='Enter') submitTableScore({{ $cand->id }}, '{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}')">

                        <button type="button" class="table-btn-save" id="table-save-{{ $cand->id }}"
                          onclick="submitTableScore({{ $cand->id }}, '{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}')"
                          {{ ($cHasScore || !empty($isFinalized)) ? 'disabled' : '' }}>
                          Save
                        </button>
                        <button type="button" class="table-btn-reset" id="table-reset-{{ $cand->id }}"
                          onclick="resetTableScore({{ $cand->id }}, '{{ str_pad($cand->candidate_number, 2, '0', STR_PAD_LEFT) }}')"
                          {{ (!$cHasScore || !empty($isFinalized)) ? 'disabled' : '' }}>
                          Clear
                        </button>
                      </div>
                    </td>
                    <td>
                      <span class="status-chip {{ $cHasScore ? 'scored' : 'pending' }}" id="table-status-chip-{{ $cand->id }}">
                        {{ $cHasScore ? '✓ ' . number_format((float)$cScore, 2) : '○ Pending' }}
                      </span>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      @endif

      {{-- FINAL SUBMISSION PANEL --}}
      <div class="final-submission-card">
        <div class="final-sub-info">
          <h3>
            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Final Category Submission
          </h3>
          <p>
            Review and finalize your evaluation for <strong>{{ $categoryName }}</strong>. Once submitted, all scoring inputs will be permanently locked and disabled.
          </p>
        </div>

        <div>
          @if(!empty($isFinalized))
            <button type="button" id="btnFinalSubmission" class="btn-final-submit finalized" disabled>
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
              <span>Category Finalized &amp; Locked</span>
            </button>
          @else
            <button type="button" id="btnFinalSubmission" class="btn-final-submit" onclick="openFinalSubmissionModal()">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              <span>Finalize &amp; Submit Category Scores</span>
            </button>
          @endif
        </div>
      </div>

    </div> {{-- /tableViewSection --}}
  @endif

  <div class="footer-note">EACH ROW PAIRS ONE MALE &amp; ONE FEMALE CANDIDATE · SCORES ARE RECORDED INDEPENDENTLY</div>

  {{-- FINAL SUBMISSION CONFIRMATION MODAL --}}
  <div id="finalSubmissionModal" class="sc-modal-backdrop" onclick="if(event.target===this) closeFinalSubmissionModal()">
    <div class="sc-modal-dialog">
      <div style="display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px;">
        <div style="display: flex; align-items: center; gap: 12px;">
          <div style="width: 44px; height: 44px; border-radius: 12px; background: #fef2f2; color: #dc2626; display: flex; align-items: center; justify-content: center;">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
          </div>
          <div>
            <h3 style="font-size: 17px; font-weight: 800; color: #0f172a; margin: 0;">Finalize Category Scoring</h3>
            <p style="font-size: 12px; color: #64748b; margin: 2px 0 0 0;">{{ $categoryName }} Evaluation</p>
          </div>
        </div>
        <button type="button" onclick="closeFinalSubmissionModal()" style="border: none; background: transparent; color: #94a3b8; font-size: 20px; cursor: pointer; padding: 4px;">✕</button>
      </div>

      {{-- Unscored Warning Box (Dynamically populated if incomplete) --}}
      <div id="modalIncompleteWarning" style="display: none; background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px 16px; margin-bottom: 16px;">
        <div style="display: flex; align-items: flex-start; gap: 8px;">
          <svg style="width: 18px; height: 18px; color: #d97706; flex-shrink: 0; margin-top: 1px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          <div>
            <div style="font-size: 12px; font-weight: 800; color: #92400e;">Pending Scores Detected!</div>
            <div style="font-size: 11.5px; color: #b45309; margin-top: 2px;" id="modalIncompleteMsg"></div>
          </div>
        </div>
      </div>

      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 16px; margin-bottom: 20px;">
        <div style="font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 6px;">Important Confirmation Notice:</div>
        <ul style="font-size: 12px; color: #475569; margin: 0; padding-left: 18px; line-height: 1.6;">
          <li>All candidate scores in <strong>{{ $categoryName }}</strong> will be submitted to the official tabulation ledger.</li>
          <li>Once submitted, <strong>this category will be immediately disabled and locked</strong> from any further changes.</li>
          <li>Only system administrators can reset or unlock categories after finalization.</li>
        </ul>
      </div>

      <div style="display: flex; align-items: center; justify-content: flex-end; gap: 10px;">
        <button type="button" onclick="closeFinalSubmissionModal()" style="padding: 10px 18px; font-size: 12.5px; font-weight: 700; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; cursor: pointer;">
          Cancel / Review
        </button>
        <button type="button" id="btnConfirmFinalize" onclick="confirmFinalSubmission()" class="btn-final-submit" style="padding: 10px 20px; font-size: 13px;">
          ✓ Confirm &amp; Lock Category
        </button>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
    const SAVE_URL     = "{{ route('judge.save-score') }}";
    const RESET_URL    = "{{ route('judge.reset-score') }}";
    const FINALIZE_URL = "{{ route('judge.finalize-category') }}";
    const CATEGORY     = "{{ $categorySlug }}";
    const CSRF         = "{{ csrf_token() }}";
    const totalPairs   = {{ $totalPairs }};
    const totalContestants = {{ $maleCandidates->count() + $femaleCandidates->count() }};
    let currentPairIndex = {{ $initialPairIndex ?? 0 }};
    let totalScored    = {{ $totalSubmitted }};
    let isCategoryFinalized = {{ !empty($isFinalized) ? 'true' : 'false' }};

    function updateScoredCountDisplay() {
        const el = document.getElementById('scoredCount');
        if (el) {
            el.textContent = String(totalScored).padStart(2, '0');
        }
        const toolbarEl = document.getElementById('toolbarScoredCount');
        if (toolbarEl) {
            toolbarEl.textContent = totalScored;
        }
    }

    function switchViewMode(mode) {
        const cardSection  = document.getElementById('cardViewSection');
        const tableSection = document.getElementById('tableViewSection');
        const btnCard      = document.getElementById('btnModeCard');
        const btnTable     = document.getElementById('btnModeTable');

        if (mode === 'table') {
            if (cardSection) cardSection.classList.remove('active');
            if (tableSection) tableSection.classList.add('active');
            if (btnCard) btnCard.classList.remove('active');
            if (btnTable) btnTable.classList.add('active');
            try { localStorage.setItem('judge_scoring_view_mode', 'table'); } catch(e) {}
        } else {
            if (tableSection) tableSection.classList.remove('active');
            if (cardSection) cardSection.classList.add('active');
            if (btnTable) btnTable.classList.remove('active');
            if (btnCard) btnCard.classList.add('active');
            try { localStorage.setItem('judge_scoring_view_mode', 'card'); } catch(e) {}
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

    // Submit score from Card View
    function submitScore(candidateId, side, candNum) {
        if (isCategoryFinalized) {
            alert('Scoring for this category has been finalized and locked.');
            return;
        }

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

                // Sync with Table View
                syncTableAfterSave(candidateId, parseFloat(data.score));
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

    // Reset score from Card View
    function resetScore(candidateId, side, candNum) {
        if (isCategoryFinalized) {
            alert('Scoring for this category has been finalized and locked.');
            return;
        }

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

                // Sync with Table View
                syncTableAfterReset(candidateId);
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

    // Submit score directly from Overall Scoring Table
    function submitTableScore(candidateId, candNum) {
        if (isCategoryFinalized) {
            alert('Scoring for this category has been finalized and locked.');
            return;
        }

        const input    = document.getElementById(`table-score-${candidateId}`);
        const btnSave  = document.getElementById(`table-save-${candidateId}`);
        const btnReset = document.getElementById(`table-reset-${candidateId}`);
        const chip     = document.getElementById(`table-status-chip-${candidateId}`);

        if (!input) return;

        const val = parseFloat(input.value);
        if (isNaN(val) || val < 1 || val > 10) {
            alert(`Please enter a valid score between 1.00 and 10.00 for Candidate #${candNum}.`);
            input.focus();
            return;
        }

        btnSave.disabled = true;
        btnSave.textContent = '...';

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
            btnSave.textContent = 'Save';
            if (data.success) {
                const formattedScore = parseFloat(data.score).toFixed(2);
                input.value = formattedScore;
                input.disabled = true;
                btnSave.disabled = true;
                if (btnReset) btnReset.disabled = false;

                if (chip) {
                    chip.className = 'status-chip scored';
                    chip.textContent = `✓ ${formattedScore}`;
                }

                // Sync with Card View
                syncCardAfterSave(candidateId, parseFloat(data.score), candNum);

                const cardSelect = document.getElementById(`score-select-${candidateId}`);
                const hadScore = cardSelect && cardSelect.dataset.hasScore;
                if (!hadScore) {
                    if (cardSelect) cardSelect.dataset.hasScore = "true";
                    totalScored++;
                    updateScoredCountDisplay();
                }
            } else {
                btnSave.disabled = false;
                alert(data.message || 'Failed to save score');
            }
        })
        .catch(() => {
            btnSave.disabled = false;
            btnSave.textContent = 'Save';
            alert('Network error while saving score. Please try again.');
        });
    }

    // Reset score directly from Overall Scoring Table
    function resetTableScore(candidateId, candNum) {
        if (isCategoryFinalized) {
            alert('Scoring for this category has been finalized and locked.');
            return;
        }

        if (!confirm(`Are you sure you want to clear score for Candidate #${candNum}?`)) {
            return;
        }

        const input    = document.getElementById(`table-score-${candidateId}`);
        const btnSave  = document.getElementById(`table-save-${candidateId}`);
        const btnReset = document.getElementById(`table-reset-${candidateId}`);
        const chip     = document.getElementById(`table-status-chip-${candidateId}`);

        if (btnReset) btnReset.disabled = true;

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
                if (input) {
                    input.value = '';
                    input.disabled = false;
                    input.focus();
                }
                if (btnSave) btnSave.disabled = false;
                if (btnReset) btnReset.disabled = true;

                if (chip) {
                    chip.className = 'status-chip pending';
                    chip.textContent = '○ Pending';
                }

                // Sync with Card View
                syncCardAfterReset(candidateId);

                const cardSelect = document.getElementById(`score-select-${candidateId}`);
                if (cardSelect && cardSelect.dataset.hasScore) {
                    delete cardSelect.dataset.hasScore;
                    totalScored = Math.max(0, totalScored - 1);
                    updateScoredCountDisplay();
                }
            } else {
                if (btnReset) btnReset.disabled = false;
                alert(data.message || 'Failed to reset score');
            }
        })
        .catch(() => {
            if (btnReset) btnReset.disabled = false;
            alert('Network error while resetting score. Please try again.');
        });
    }

    function syncTableAfterSave(candidateId, score) {
        const input    = document.getElementById(`table-score-${candidateId}`);
        const btnSave  = document.getElementById(`table-save-${candidateId}`);
        const btnReset = document.getElementById(`table-reset-${candidateId}`);
        const chip     = document.getElementById(`table-status-chip-${candidateId}`);

        if (input) {
            input.value = score.toFixed(2);
            input.disabled = true;
        }
        if (btnSave) btnSave.disabled = true;
        if (btnReset) btnReset.disabled = isCategoryFinalized;
        if (chip) {
            chip.className = 'status-chip scored';
            chip.textContent = `✓ ${score.toFixed(2)}`;
        }
    }

    function syncTableAfterReset(candidateId) {
        const input    = document.getElementById(`table-score-${candidateId}`);
        const btnSave  = document.getElementById(`table-save-${candidateId}`);
        const btnReset = document.getElementById(`table-reset-${candidateId}`);
        const chip     = document.getElementById(`table-status-chip-${candidateId}`);

        if (input) {
            input.value = '';
            input.disabled = isCategoryFinalized;
        }
        if (btnSave) btnSave.disabled = isCategoryFinalized;
        if (btnReset) btnReset.disabled = true;
        if (chip) {
            chip.className = 'status-chip pending';
            chip.textContent = '○ Pending';
        }
    }

    function syncCardAfterSave(candidateId, score, candNum) {
        const select    = document.getElementById(`score-select-${candidateId}`);
        const btnSubmit = document.getElementById(`btn-submit-${candidateId}`);
        const btnReset  = document.getElementById(`btn-reset-${candidateId}`);
        const status    = document.getElementById(`status-${candidateId}`);

        if (select) {
            // Find option matching score, or set custom
            let opt = Array.from(select.options).find(o => parseFloat(o.value) === score);
            if (!opt) {
                opt = new Option(score.toFixed(2), score, true, true);
                select.add(opt);
            }
            select.value = score;
            select.disabled = true;
        }
        if (btnSubmit) btnSubmit.disabled = true;
        if (btnReset) btnReset.disabled = isCategoryFinalized;
        if (status) {
            status.className = 'confirm-msg';
            status.textContent = `✓ score ${score} recorded for #${candNum}`;
        }
    }

    function syncCardAfterReset(candidateId) {
        const select    = document.getElementById(`score-select-${candidateId}`);
        const btnSubmit = document.getElementById(`btn-submit-${candidateId}`);
        const btnReset  = document.getElementById(`btn-reset-${candidateId}`);
        const status    = document.getElementById(`status-${candidateId}`);

        if (select) {
            select.value = '';
            select.disabled = isCategoryFinalized;
        }
        if (btnSubmit) btnSubmit.disabled = isCategoryFinalized;
        if (btnReset) btnReset.disabled = true;
        if (status) {
            status.className = 'confirm-msg';
            status.textContent = '';
        }
    }

    // Modal Handlers
    function openFinalSubmissionModal() {
        if (isCategoryFinalized) {
            alert('This category is already finalized.');
            return;
        }

        const modal = document.getElementById('finalSubmissionModal');
        const warnBox = document.getElementById('modalIncompleteWarning');
        const warnMsg = document.getElementById('modalIncompleteMsg');

        // Check for missing scores
        const tableInputs = document.querySelectorAll('.table-score-input');
        const missingNumbers = [];
        tableInputs.forEach(inp => {
            if (!inp.value || parseFloat(inp.value) <= 0) {
                const cNum = inp.dataset.candNum || inp.dataset.candidateId;
                if (cNum) missingNumbers.push('#' + cNum);
            }
        });

        if (missingNumbers.length > 0) {
            warnBox.style.display = 'block';
            warnMsg.textContent = `You have scored ${totalScored} of ${totalContestants} contestants. Unscored contestants: ${missingNumbers.join(', ')}. You may still submit, but all unentered contestants will receive no score.`;
        } else {
            warnBox.style.display = 'none';
            warnMsg.textContent = '';
        }

        modal.classList.add('open');
    }

    function closeFinalSubmissionModal() {
        const modal = document.getElementById('finalSubmissionModal');
        if (modal) modal.classList.remove('open');
    }

    function confirmFinalSubmission() {
        const btnConfirm = document.getElementById('btnConfirmFinalize');
        btnConfirm.disabled = true;
        btnConfirm.textContent = 'Submitting & Locking...';

        fetch(FINALIZE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                category: CATEGORY,
            })
        })
        .then(res => res.json())
        .then(data => {
            btnConfirm.disabled = false;
            btnConfirm.textContent = '✓ Confirm & Lock Category';

            if (data.success) {
                isCategoryFinalized = true;
                closeFinalSubmissionModal();
                applyCategoryLockedUI(data.finalized_at);

                if (window.showToast) {
                    window.showToast('Category Finalized', 'Category scores have been locked and submitted to the ledger.', 'success', 5000);
                } else {
                    alert('Category scores have been finalized and locked successfully!');
                }
            } else {
                alert(data.message || 'Failed to finalize category.');
            }
        })
        .catch(() => {
            btnConfirm.disabled = false;
            btnConfirm.textContent = '✓ Confirm & Lock Category';
            alert('Network error while finalizing category. Please try again.');
        });
    }

    function applyCategoryLockedUI(finalizedAt) {
        // Show banner
        const banner = document.getElementById('finalizedBanner');
        if (banner) {
            banner.style.display = 'flex';
            if (finalizedAt) {
                const sub = document.getElementById('bannerSubtitleText');
                if (sub) {
                    sub.innerHTML = `Your evaluation for <strong>{{ $categoryName }}</strong> was officially submitted on ${finalizedAt}. All scoring inputs are locked and disabled.`;
                }
            }
        }

        // Disable all inputs and action buttons in Table View
        document.querySelectorAll('.table-score-input, .table-btn-save, .table-btn-reset').forEach(el => {
            el.disabled = true;
        });

        // Disable all inputs and action buttons in Card View
        document.querySelectorAll('.score-select, .btn-submit, .btn-reset').forEach(el => {
            el.disabled = true;
        });

        // Update Final Submission Button in Table
        const btnSub = document.getElementById('btnFinalSubmission');
        if (btnSub) {
            btnSub.className = 'btn-final-submit finalized';
            btnSub.disabled = true;
            btnSub.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <span>Category Finalized &amp; Locked</span>
            `;
        }

        // Update toolbar button
        const quickArea = document.getElementById('quickActionArea');
        if (quickArea) {
            quickArea.innerHTML = `<span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-300">🔒 Finalized</span>`;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        showPair(currentPairIndex, false);
        setupJudgeEcho();

        // Restore preferred view mode
        try {
            const savedMode = localStorage.getItem('judge_scoring_view_mode');
            if (savedMode === 'table') {
                switchViewMode('table');
            }
        } catch(e) {}

        // Enforce lock if already finalized on load
        if (isCategoryFinalized) {
            applyCategoryLockedUI();
        }
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
                                syncTableAfterSave(e.candidate_id, parseFloat(e.score));
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
                                syncTableAfterReset(e.candidate_id);
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