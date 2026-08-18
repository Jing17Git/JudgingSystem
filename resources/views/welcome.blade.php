<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CrownScore — Pageant Judging System</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,600;1,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --bg-page: #ffffff;
    --bg-surface: #f4faf6;
    --bg-card: #ffffff;
    --green-deep: #0f7a4d;
    --green: #16a34a;
    --green-light: #4ade80;
    --green-pale: #e6f7ec;
    --green-pale-2: #f0fbf4;
    --text-dark: #0b2318;
    --text-body: #3f5850;
    --text-dim: #6b8378;
    --border: #dcefe2;
    --radius-lg: 18px;
    --radius-md: 12px;
    --gold: #eab308;
    --silver: #94a3b8;
    --bronze: #cd7f32;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  html { scroll-behavior: smooth; }

  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background-color: var(--bg-page);
    color: var(--text-dark);
    min-height: 100vh;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
  }

  a { text-decoration: none; color: inherit; }
  ul { list-style: none; }

  .wrap {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 40px;
  }

  /* ---------- NAVBAR ---------- */
  header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
  }

  nav.wrap {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 84px;
  }

  .brand {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .crown-logo {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: var(--green-pale);
    color: var(--green-deep);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .brand-text .brand-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-dark);
    letter-spacing: -0.3px;
    line-height: 1.1;
  }

  .brand-text .brand-subtitle {
    font-size: 11px;
    color: var(--green);
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .nav-menu {
    display: flex;
    align-items: center;
    gap: 34px;
  }

  .nav-menu a {
    font-size: 14.5px;
    font-weight: 500;
    color: var(--text-dim);
    transition: color 0.2s ease;
    position: relative;
    padding: 6px 0;
  }

  .nav-menu a:hover {
    color: var(--text-dark);
  }

  .nav-menu a.active {
    color: var(--green-deep);
    font-weight: 700;
  }

  .nav-menu a.active::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--green);
    border-radius: 2px;
  }

  .nav-right {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 600;
    padding: 11px 22px;
    border-radius: 10px;
    transition: all 0.2s ease;
    cursor: pointer;
    border: 1px solid transparent;
    font-family: inherit;
  }

  .btn-green {
    background: var(--green);
    color: #ffffff;
    font-weight: 700;
  }

  .btn-green:hover {
    background: var(--green-deep);
  }

  .btn-green-outline {
    background: #ffffff;
    color: var(--green-deep);
    border: 1.5px solid var(--green);
  }

  .btn-green-outline:hover {
    background: var(--green-pale);
  }

  .btn-sm {
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 8px;
  }

  /* ---------- HERO SECTION ---------- */
  .hero {
    position: relative;
    padding: 64px 0 48px;
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border);
  }

  .hero-grid {
    display: grid;
    grid-template-columns: 1.1fr 0.9fr;
    gap: 48px;
    align-items: center;
  }

  .eyebrow-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 7px 15px;
    border-radius: 999px;
    background: var(--green-pale);
    border: 1px solid var(--green-light);
    color: var(--green-deep);
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 24px;
  }

  .hero h1 {
    font-size: 52px;
    line-height: 1.15;
    font-weight: 800;
    letter-spacing: -1px;
    color: var(--text-dark);
    margin-bottom: 20px;
  }

  .hero h1 .crown-accent {
    color: var(--green);
  }

  .hero p.lead {
    font-size: 17px;
    line-height: 1.7;
    color: var(--text-body);
    max-width: 500px;
    margin-bottom: 32px;
  }

  .hero-ctas {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 36px;
  }

  .trust-bar {
    display: flex;
    align-items: center;
    gap: 22px;
    padding-top: 20px;
    border-top: 1px solid var(--border);
  }

  .trust-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13.5px;
    font-weight: 600;
    color: var(--text-body);
  }

  .trust-item svg {
    color: var(--green);
  }

  /* Hero Visual */
  .hero-visual {
    position: relative;
    height: 460px;
    border-radius: var(--radius-lg);
    overflow: hidden;
    border: 1px solid var(--border);
    background-color: var(--green-pale-2);
  }

  .hero-visual img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
  }

  /* ---------- SECTION HEADINGS ---------- */
  .section-heading {
    text-align: center;
    max-width: 560px;
    margin: 0 auto 40px;
  }

  .section-heading h2 {
    font-size: 30px;
    font-weight: 800;
    color: var(--text-dark);
    letter-spacing: -0.5px;
    margin-bottom: 10px;
  }

  .section-heading p {
    font-size: 15px;
    color: var(--text-dim);
    line-height: 1.6;
  }

  /* ---------- FEATURE CARDS ---------- */
  .features-section {
    padding: 64px 0 20px;
  }

  .feature-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
  }

  .feature-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 26px 22px;
    display: flex;
    align-items: flex-start;
    gap: 16px;
    transition: all 0.2s ease;
  }

  .feature-card:hover {
    border-color: var(--green-light);
    box-shadow: 0 8px 20px rgba(22, 163, 74, 0.08);
  }

  .fc-icon-wrap {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    background: var(--green-pale);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--green-deep);
    flex-shrink: 0;
  }

  .fc-body h4 {
    font-size: 15.5px;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 6px;
  }

  .fc-body p {
    font-size: 13px;
    line-height: 1.55;
    color: var(--text-dim);
  }

  /* ---------- LEADERBOARD SECTION ---------- */
  .leaderboard-section {
    padding: 64px 0;
  }

  .lb-tabs {
    display: flex;
    justify-content: center;
    gap: 6px;
    margin-bottom: 32px;
  }

  .lb-tab {
    padding: 9px 24px;
    border-radius: 999px;
    font-size: 13.5px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    border: 1.5px solid var(--border);
    background: var(--bg-card);
    color: var(--text-dim);
    font-family: inherit;
  }

  .lb-tab:hover {
    border-color: var(--green-light);
    color: var(--text-dark);
  }

  .lb-tab.active {
    background: var(--green);
    color: #ffffff;
    border-color: var(--green);
  }

  .lb-panels {
    position: relative;
  }

  .lb-panel {
    display: none;
  }

  .lb-panel.active {
    display: block;
  }

  .lb-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
  }

  .lb-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
  }

  .lb-card-header {
    padding: 18px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid var(--border);
    background: var(--bg-surface);
  }

  .lb-card-header h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-dark);
  }

  .live-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11.5px;
    font-weight: 700;
    color: var(--green-deep);
    background: var(--green-pale);
    padding: 5px 10px;
    border-radius: 999px;
  }

  .live-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: var(--green);
    animation: pulse 1.6s infinite;
  }

  @keyframes pulse {
    0%,100% { opacity: 1; }
    50% { opacity: 0.3; }
  }

  .lb-table {
    width: 100%;
    border-collapse: collapse;
  }

  .lb-table th {
    text-align: left;
    padding: 10px 22px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-dim);
    font-weight: 600;
    background: var(--bg-surface);
    border-bottom: 1px solid var(--border);
  }

  .lb-table th:last-child,
  .lb-table td:last-child {
    text-align: right;
  }

  .lb-table td {
    padding: 12px 22px;
    font-size: 14px;
    color: var(--text-dark);
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
  }

  .lb-table tr:last-child td {
    border-bottom: none;
  }

  .lb-table tr:hover td {
    background: var(--green-pale-2);
  }

  .rank-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
  }

  .rank-1 { background: var(--gold); }
  .rank-2 { background: var(--silver); }
  .rank-3 { background: var(--bronze); }
  .rank-default { background: #d1d5db; color: var(--text-dim); }

  .candidate-cell {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .candidate-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border);
    background: var(--green-pale);
  }

  .candidate-info .c-name {
    font-weight: 600;
    font-size: 14px;
    color: var(--text-dark);
    line-height: 1.2;
  }

  .candidate-info .c-number {
    font-size: 11.5px;
    color: var(--text-dim);
    font-weight: 500;
  }

  .score-val {
    font-weight: 700;
    font-size: 15px;
    color: var(--green-deep);
  }

  .lb-empty {
    padding: 40px 22px;
    text-align: center;
    color: var(--text-dim);
    font-size: 14px;
  }

  .lb-empty svg {
    display: block;
    margin: 0 auto 12px;
    color: var(--border);
  }

  /* ---------- RESULTS SECTION ---------- */
  .results-section {
    padding: 64px 0;
    background: var(--bg-surface);
    border-top: 1px solid var(--border);
    border-bottom: 1px solid var(--border);
  }

  .results-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 16px;
  }

  .result-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 20px;
    text-align: center;
    transition: all 0.2s ease;
  }

  .result-card:hover {
    border-color: var(--green-light);
    box-shadow: 0 6px 16px rgba(22, 163, 74, 0.08);
  }

  .result-cat-label {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--green-deep);
    margin-bottom: 16px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
  }

  .result-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 0;
  }

  .result-item:not(:last-child) {
    border-bottom: 1px solid var(--border);
  }

  .result-rank {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    color: #fff;
    flex-shrink: 0;
  }

  .result-name {
    flex: 1;
    font-size: 12.5px;
    font-weight: 600;
    color: var(--text-dark);
    text-align: left;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .result-score {
    font-size: 12px;
    font-weight: 700;
    color: var(--green-deep);
  }

  .result-empty {
    font-size: 12px;
    color: var(--text-dim);
    padding: 16px 0;
  }

  /* ---------- STATS BAR ---------- */
  .stats-section {
    padding: 24px 0 70px;
  }

  .stats-bar {
    background: var(--green-deep);
    border-radius: var(--radius-lg);
    padding: 32px 40px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
  }

  .stat-card {
    display: flex;
    align-items: center;
    gap: 18px;
  }

  .stat-card:not(:last-child) {
    border-right: 1px solid rgba(255, 255, 255, 0.18);
  }

  .stat-icon {
    width: 44px;
    height: 44px;
    color: var(--green-light);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .stat-info .stat-val {
    font-size: 28px;
    font-weight: 800;
    color: #ffffff;
    line-height: 1.1;
    letter-spacing: -0.5px;
  }

  .stat-info .stat-lbl {
    font-size: 13px;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.75);
    margin-top: 4px;
  }

  /* ---------- FOOTER ---------- */
  footer {
    border-top: 1px solid var(--border);
    padding: 36px 0;
    text-align: center;
    font-size: 14px;
    color: var(--text-dim);
    background: var(--bg-surface);
  }

  footer .footer-brand {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 10px;
    font-weight: 700;
    color: var(--text-dark);
  }

  /* ---------- RESPONSIVE ---------- */
  @media (max-width: 1120px) {
    .feature-grid { grid-template-columns: repeat(2, 1fr); }
    .results-grid { grid-template-columns: repeat(3, 1fr); }
    .stats-bar { grid-template-columns: repeat(2, 1fr); gap: 24px; }
    .stat-card:not(:last-child) { border-right: none; }
  }

  @media (max-width: 960px) {
    .hero-grid { grid-template-columns: 1fr; }
    .hero h1 { font-size: 40px; }
    .nav-menu { display: none; }
    .hero-visual { height: 320px; }
    .lb-grid { grid-template-columns: 1fr; }
    .results-grid { grid-template-columns: repeat(2, 1fr); }
  }

  @media (max-width: 640px) {
    .wrap { padding: 0 20px; }
    .feature-grid { grid-template-columns: 1fr; }
    .results-grid { grid-template-columns: 1fr; }
    .stats-bar { grid-template-columns: 1fr; padding: 20px; }
    .hero h1 { font-size: 32px; }
    .hero-ctas { flex-direction: column; align-items: stretch; }
    .trust-bar { flex-direction: column; align-items: flex-start; gap: 10px; }
  }
</style>
</head>
<body>

{{-- Header Navigation --}}
<header>
  <nav class="wrap">
    <div class="brand">
      <img src="{{ asset('images/logo.png') }}" alt="CPSU Logo" style="width: 44px; height: 44px; object-fit: contain;">
      <div class="brand-text">
        <div class="brand-title">CrownScore</div>
        <div class="brand-subtitle">Pageant Judging System</div>
      </div>
    </div>

    <ul class="nav-menu">
      <li><a href="#home" class="active">Home</a></li>
      <li><a href="#features">Features</a></li>
      <li><a href="#leaderboard">Leaderboard</a></li>
      <li><a href="#results">Results</a></li>
    </ul>

    <div class="nav-right">
      @auth
        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('judge.dashboard') }}" class="btn btn-green">
          Go to Dashboard &rarr;
        </a>
      @else
        <a href="{{ route('login') }}" class="btn btn-green">
          Login
        </a>
      @endauth
    </div>
  </nav>
</header>

{{-- ==================== HOME ==================== --}}
<section class="hero" id="home">
  <div class="wrap hero-grid">
    <div class="hero-content">
      <div class="eyebrow-pill">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M3 8l3.5 3L12 5l5.5 6L21 8l-1.6 9.5a1 1 0 01-1 .8H5.6a1 1 0 01-1-.8L3 8z"/>
        </svg>
        Fair. Transparent. Real-Time.
      </div>

      <h1>Where Excellence Meets the <span class="crown-accent">Crown.</span></h1>

      <p class="lead">CrownScore is a web-based real-time pageant judging and tabulation system built for accuracy, fairness, and speed.</p>

      <div class="hero-ctas">
        @auth
          <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('judge.dashboard') }}" class="btn btn-green">
            View Live Rankings &rarr;
          </a>
        @else
          <a href="{{ route('login') }}" class="btn btn-green">
            View Live Rankings &rarr;
          </a>
          <a href="#leaderboard" class="btn btn-green-outline">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 8l3.5 3L12 5l5.5 6L21 8l-1.6 9.5a1 1 0 01-1 .8H5.6a1 1 0 01-1-.8L3 8z"/>
            </svg>
            Explore Pageant
          </a>
        @endauth
      </div>

      <div class="trust-bar">
        <div class="trust-item">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
          Real-Time Scoring
        </div>
        <div class="trust-item">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
          </svg>
          Secure &amp; Reliable
        </div>
        <div class="trust-item">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Transparent Results
        </div>
      </div>
    </div>

    <div class="hero-visual">
      <img src="{{ asset('images/pageant_stage_hero.png') }}" alt="CrownScore Pageant Stage">
    </div>
  </div>
</section>

{{-- ==================== FEATURES ==================== --}}
<section class="features-section" id="features">
  <div class="wrap">
    <div class="section-heading">
      <h2>Everything judging day needs</h2>
      <p>A simple, dependable system for scoring, tabulating, and sharing results.</p>
    </div>
    <div class="feature-grid">
      <div class="feature-card">
        <div class="fc-icon-wrap">
          <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
          </svg>
        </div>
        <div class="fc-body">
          <h4>Real-Time Tabulation</h4>
          <p>Scores are computed instantly with absolute accuracy.</p>
        </div>
      </div>

      <div class="feature-card">
        <div class="fc-icon-wrap">
          <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
          </svg>
        </div>
        <div class="fc-body">
          <h4>Fair &amp; Transparent</h4>
          <p>Every score is secure, fair, and accountable.</p>
        </div>
      </div>

      <div class="feature-card">
        <div class="fc-icon-wrap">
          <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
          </svg>
        </div>
        <div class="fc-body">
          <h4>Live Leaderboard</h4>
          <p>Track rankings and results as they happen.</p>
        </div>
      </div>

      <div class="feature-card">
        <div class="fc-icon-wrap">
          <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
          </svg>
        </div>
        <div class="fc-body">
          <h4>Secure &amp; Reliable</h4>
          <p>Your data is protected every step of the way.</p>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ==================== LEADERBOARD (Database-Driven) ==================== --}}
<section class="leaderboard-section" id="leaderboard">
  <div class="wrap">
    <div class="section-heading">
      <h2>Live Leaderboard</h2>
      <p>Real-time candidate rankings pulled from judge scores across all categories.</p>
    </div>

    {{-- Tab buttons --}}
    <div class="lb-tabs">
      <button class="lb-tab active" onclick="switchLBTab('all', this)">All Candidates</button>
      <button class="lb-tab" onclick="switchLBTab('male', this)">Male</button>
      <button class="lb-tab" onclick="switchLBTab('female', this)">Female</button>
    </div>

    <div class="lb-panels">
      {{-- ALL tab --}}
      <div class="lb-panel active" id="lb-all">
        <div class="lb-grid">
          {{-- Male Card --}}
          <div class="lb-card">
            <div class="lb-card-header">
              <h3>♂ Male Division</h3>
              <span class="live-badge"><span class="live-dot"></span> Live</span>
            </div>
            @if(count($maleLB) > 0)
            <table class="lb-table">
              <thead>
                <tr>
                  <th style="width:50px;">Rank</th>
                  <th>Candidate</th>
                  <th>Total Score</th>
                </tr>
              </thead>
              <tbody>
                @foreach($maleLB as $i => $row)
                <tr>
                  <td>
                    <span class="rank-num {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">
                      {{ $i + 1 }}
                    </span>
                  </td>
                  <td>
                    <div class="candidate-cell">
                      @if($row['photo_url'])
                        <img class="candidate-avatar" src="{{ asset('storage/' . $row['photo_url']) }}" alt="{{ $row['name'] }}">
                      @else
                        <img class="candidate-avatar" src="https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}&background=e6f7ec&color=0f7a4d&bold=true&size=36" alt="{{ $row['name'] }}">
                      @endif
                      <div class="candidate-info">
                        <div class="c-name">{{ $row['name'] }}</div>
                        <div class="c-number">#{{ str_pad($row['number'], 2, '0', STR_PAD_LEFT) }}</div>
                      </div>
                    </div>
                  </td>
                  <td><span class="score-val">{{ number_format($row['total'], 2) }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
            @else
            <div class="lb-empty">
              <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              No male candidates yet
            </div>
            @endif
          </div>

          {{-- Female Card --}}
          <div class="lb-card">
            <div class="lb-card-header">
              <h3>♀ Female Division</h3>
              <span class="live-badge"><span class="live-dot"></span> Live</span>
            </div>
            @if(count($femaleLB) > 0)
            <table class="lb-table">
              <thead>
                <tr>
                  <th style="width:50px;">Rank</th>
                  <th>Candidate</th>
                  <th>Total Score</th>
                </tr>
              </thead>
              <tbody>
                @foreach($femaleLB as $i => $row)
                <tr>
                  <td>
                    <span class="rank-num {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">
                      {{ $i + 1 }}
                    </span>
                  </td>
                  <td>
                    <div class="candidate-cell">
                      @if($row['photo_url'])
                        <img class="candidate-avatar" src="{{ asset('storage/' . $row['photo_url']) }}" alt="{{ $row['name'] }}">
                      @else
                        <img class="candidate-avatar" src="https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}&background=e6f7ec&color=0f7a4d&bold=true&size=36" alt="{{ $row['name'] }}">
                      @endif
                      <div class="candidate-info">
                        <div class="c-name">{{ $row['name'] }}</div>
                        <div class="c-number">#{{ str_pad($row['number'], 2, '0', STR_PAD_LEFT) }}</div>
                      </div>
                    </div>
                  </td>
                  <td><span class="score-val">{{ number_format($row['total'], 2) }}</span></td>
                </tr>
                @endforeach
              </tbody>
            </table>
            @else
            <div class="lb-empty">
              <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
              No female candidates yet
            </div>
            @endif
          </div>
        </div>
      </div>

      {{-- MALE-ONLY tab --}}
      <div class="lb-panel" id="lb-male">
        <div class="lb-card">
          <div class="lb-card-header">
            <h3>♂ Male Division — Full Rankings</h3>
            <span class="live-badge"><span class="live-dot"></span> Live</span>
          </div>
          @if(count($maleLB) > 0)
          <table class="lb-table">
            <thead>
              <tr>
                <th style="width:50px;">Rank</th>
                <th>Candidate</th>
                <th>Production</th>
                <th>Fitness</th>
                <th>Traditional</th>
                <th>Indigenous</th>
                <th>Q&amp;A</th>
                <th>Grand Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($maleLB as $i => $row)
              <tr>
                <td>
                  <span class="rank-num {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">
                    {{ $i + 1 }}
                  </span>
                </td>
                <td>
                  <div class="candidate-cell">
                    @if($row['photo_url'])
                      <img class="candidate-avatar" src="{{ asset('storage/' . $row['photo_url']) }}" alt="{{ $row['name'] }}">
                    @else
                      <img class="candidate-avatar" src="https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}&background=e6f7ec&color=0f7a4d&bold=true&size=36" alt="{{ $row['name'] }}">
                    @endif
                    <div class="candidate-info">
                      <div class="c-name">{{ $row['name'] }}</div>
                      <div class="c-number">#{{ str_pad($row['number'], 2, '0', STR_PAD_LEFT) }}</div>
                    </div>
                  </div>
                </td>
                <td>{{ number_format($row['production'], 2) }}</td>
                <td>{{ number_format($row['fitness'], 2) }}</td>
                <td>{{ number_format($row['traditional'], 2) }}</td>
                <td>{{ number_format($row['indigenous'], 2) }}</td>
                <td>{{ number_format($row['qa'], 2) }}</td>
                <td><span class="score-val">{{ number_format($row['total'], 2) }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <div class="lb-empty">No male candidates scored yet.</div>
          @endif
        </div>
      </div>

      {{-- FEMALE-ONLY tab --}}
      <div class="lb-panel" id="lb-female">
        <div class="lb-card">
          <div class="lb-card-header">
            <h3>♀ Female Division — Full Rankings</h3>
            <span class="live-badge"><span class="live-dot"></span> Live</span>
          </div>
          @if(count($femaleLB) > 0)
          <table class="lb-table">
            <thead>
              <tr>
                <th style="width:50px;">Rank</th>
                <th>Candidate</th>
                <th>Production</th>
                <th>Fitness</th>
                <th>Traditional</th>
                <th>Indigenous</th>
                <th>Q&amp;A</th>
                <th>Grand Total</th>
              </tr>
            </thead>
            <tbody>
              @foreach($femaleLB as $i => $row)
              <tr>
                <td>
                  <span class="rank-num {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">
                    {{ $i + 1 }}
                  </span>
                </td>
                <td>
                  <div class="candidate-cell">
                    @if($row['photo_url'])
                      <img class="candidate-avatar" src="{{ asset('storage/' . $row['photo_url']) }}" alt="{{ $row['name'] }}">
                    @else
                      <img class="candidate-avatar" src="https://ui-avatars.com/api/?name={{ urlencode($row['name']) }}&background=e6f7ec&color=0f7a4d&bold=true&size=36" alt="{{ $row['name'] }}">
                    @endif
                    <div class="candidate-info">
                      <div class="c-name">{{ $row['name'] }}</div>
                      <div class="c-number">#{{ str_pad($row['number'], 2, '0', STR_PAD_LEFT) }}</div>
                    </div>
                  </div>
                </td>
                <td>{{ number_format($row['production'], 2) }}</td>
                <td>{{ number_format($row['fitness'], 2) }}</td>
                <td>{{ number_format($row['traditional'], 2) }}</td>
                <td>{{ number_format($row['indigenous'], 2) }}</td>
                <td>{{ number_format($row['qa'], 2) }}</td>
                <td><span class="score-val">{{ number_format($row['total'], 2) }}</span></td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <div class="lb-empty">No female candidates scored yet.</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ==================== RESULTS (Per-Category Top 3) ==================== --}}
<section class="results-section" id="results">
  <div class="wrap">
    <div class="section-heading">
      <h2>Category Results</h2>
      <p>Top 5 candidates per scoring category, updated in real-time from judge submissions.</p>
    </div>

    <div class="results-grid">
      {{-- Production --}}
      <div class="result-card">
        <div class="result-cat-label">Production</div>
        @php
          $prodRank = collect($leaderboard)->sortByDesc('production')->take(5)->values();
        @endphp
        @forelse($prodRank as $i => $r)
          <div class="result-item">
            <span class="result-rank {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">{{ $i + 1 }}</span>
            <span class="result-name">{{ $r['name'] }}</span>
            <span class="result-score">{{ number_format($r['production'], 1) }}</span>
          </div>
        @empty
          <div class="result-empty">No scores yet</div>
        @endforelse
      </div>

      {{-- Fitness --}}
      <div class="result-card">
        <div class="result-cat-label">Fitness</div>
        @php
          $fitRank = collect($leaderboard)->sortByDesc('fitness')->take(5)->values();
        @endphp
        @forelse($fitRank as $i => $r)
          <div class="result-item">
            <span class="result-rank {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">{{ $i + 1 }}</span>
            <span class="result-name">{{ $r['name'] }}</span>
            <span class="result-score">{{ number_format($r['fitness'], 1) }}</span>
          </div>
        @empty
          <div class="result-empty">No scores yet</div>
        @endforelse
      </div>

      {{-- Traditional Attire --}}
      <div class="result-card">
        <div class="result-cat-label">Traditional</div>
        @php
          $tradRank = collect($leaderboard)->sortByDesc('traditional')->take(5)->values();
        @endphp
        @forelse($tradRank as $i => $r)
          <div class="result-item">
            <span class="result-rank {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">{{ $i + 1 }}</span>
            <span class="result-name">{{ $r['name'] }}</span>
            <span class="result-score">{{ number_format($r['traditional'], 1) }}</span>
          </div>
        @empty
          <div class="result-empty">No scores yet</div>
        @endforelse
      </div>

      {{-- Indigenous Attire --}}
      <div class="result-card">
        <div class="result-cat-label">Indigenous</div>
        @php
          $indigRank = collect($leaderboard)->sortByDesc('indigenous')->take(5)->values();
        @endphp
        @forelse($indigRank as $i => $r)
          <div class="result-item">
            <span class="result-rank {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">{{ $i + 1 }}</span>
            <span class="result-name">{{ $r['name'] }}</span>
            <span class="result-score">{{ number_format($r['indigenous'], 1) }}</span>
          </div>
        @empty
          <div class="result-empty">No scores yet</div>
        @endforelse
      </div>

      {{-- Q&A --}}
      <div class="result-card">
        <div class="result-cat-label">Q&amp;A</div>
        @php
          $qaRank = collect($leaderboard)->sortByDesc('qa')->take(5)->values();
        @endphp
        @forelse($qaRank as $i => $r)
          <div class="result-item">
            <span class="result-rank {{ $i === 0 ? 'rank-1' : ($i === 1 ? 'rank-2' : ($i === 2 ? 'rank-3' : 'rank-default')) }}">{{ $i + 1 }}</span>
            <span class="result-name">{{ $r['name'] }}</span>
            <span class="result-score">{{ number_format($r['qa'], 1) }}</span>
          </div>
        @empty
          <div class="result-empty">No scores yet</div>
        @endforelse
      </div>
    </div>
  </div>
</section>

{{-- ==================== STATS BAR (Database-Driven) ==================== --}}
<section class="stats-section" id="stats">
  <div class="wrap">
    <div class="stats-bar">
      <div class="stat-card">
        <div class="stat-icon">
          <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-val">{{ $totalCandidates }}</div>
          <div class="stat-lbl">Contestants</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-val">{{ $totalJudges }}</div>
          <div class="stat-lbl">Judges</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-val">{{ $totalCategories }}</div>
          <div class="stat-lbl">Major Criteria</div>
        </div>
      </div>

      <div class="stat-card">
        <div class="stat-icon">
          <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
          </svg>
        </div>
        <div class="stat-info">
          <div class="stat-val">100%</div>
          <div class="stat-lbl">Real-Time</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- Footer --}}
<footer>
  <div class="wrap">
    <div class="footer-brand" style="display: flex; align-items: center; gap: 8px;">
      <img src="{{ asset('images/logo.png') }}" alt="CPSU Logo" style="width: 28px; height: 28px; object-fit: contain;">
      CrownScore
    </div>
    <p>Empowering pageant judging with precision, speed, and real-time transparency.</p>
  </div>
</footer>

{{-- JavaScript for Leaderboard Tabs & Active Nav --}}
<script>
  // Leaderboard tab switching
  function switchLBTab(tabId, btn) {
    document.querySelectorAll('.lb-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.lb-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('lb-' + tabId).classList.add('active');
    btn.classList.add('active');
  }

  // Active nav highlighting on scroll
  document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-menu a');

    function setActive() {
      let scrollY = window.scrollY + 120;
      sections.forEach(section => {
        const top = section.offsetTop;
        const height = section.offsetHeight;
        const id = section.getAttribute('id');
        if (scrollY >= top && scrollY < top + height) {
          navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + id) {
              link.classList.add('active');
            }
          });
        }
      });
    }

    window.addEventListener('scroll', setActive);
    setActive();
  });
</script>

</body>
</html>