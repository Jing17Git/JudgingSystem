<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CrownScore — Pageant Judging System</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<style>
  :root {
    --bg-page: #ffffff;
    --green-deep: #0f7a4d;
    --green-primary: #16a34a;
    --green-hover: #15803d;
    --green-light: #4ade80;
    --green-pale: #f0fdf4;
    --green-badge: #dcfce7;
    --green-border: #bbf7d0;
    --text-dark: #0f172a;
    --text-body: #4b5563;
    --text-muted: #64748b;
    --border-light: #e2e8f0;
    --radius-pill: 9999px;
  }

  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  html, body {
    width: 100%;
    min-height: 100vh;
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    background-color: var(--bg-page);
    color: var(--text-dark);
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    overflow-x: hidden;
    position: relative;
  }

  /* Decorative Ambient Glow Layer */
  .bg-ambient-layer {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 0;
    overflow: hidden;
  }

  .bg-wave-left {
    position: absolute;
    top: 40px;
    left: -100px;
    width: 600px;
    height: 700px;
    background: radial-gradient(circle at 30% 40%, rgba(220, 252, 231, 0.5), rgba(240, 253, 244, 0.15) 60%, transparent 80%);
    filter: blur(40px);
  }

  .bg-wave-right {
    position: absolute;
    top: 20px;
    right: -120px;
    width: 700px;
    height: 700px;
    background: radial-gradient(circle at 70% 30%, rgba(187, 247, 208, 0.45), rgba(240, 253, 244, 0.1) 65%, transparent 80%);
    filter: blur(50px);
  }

  .bg-lines-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
      radial-gradient(ellipse at top left, rgba(74, 222, 128, 0.08) 0%, transparent 50%),
      radial-gradient(ellipse at top right, rgba(34, 197, 94, 0.06) 0%, transparent 60%);
  }

  .app-container {
    max-width: 1320px;
    margin: 0 auto;
    padding: 0 36px;
    position: relative;
    z-index: 10;
  }

  a {
    text-decoration: none;
    color: inherit;
  }

  /* Header / Navbar */
  header {
    width: 100%;
    padding: 16px 0;
    position: sticky;
    top: 0;
    z-index: 50;
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    background:transparent;
    border-bottom: 1px solid rgba(226, 232, 240, 0.8);
  }

  nav.nav-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .brand-group {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .brand-logo-wrap {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
  }

  .brand-logo-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
  }

  .brand-text .brand-title {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-dark);
    letter-spacing: -0.4px;
    line-height: 1.1;
  }

  .brand-text .brand-subtitle {
    font-size: 11px;
    font-weight: 700;
    color: var(--green-primary);
    letter-spacing: 0.04em;
    text-transform: uppercase;
    margin-top: 2px;
  }

  .btn-pill-login {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background-color: var(--green-primary);
    color: #ffffff;
    font-size: 14px;
    font-weight: 700;
    padding: 10px 24px;
    border-radius: var(--radius-pill);
    box-shadow: 0 4px 14px rgba(22, 163, 74, 0.28);
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
  }

  .btn-pill-login:hover {
    background-color: var(--green-hover);
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(22, 163, 74, 0.38);
  }

  /* Hero Section */
  .hero-wrapper {
    padding: 56px 0 60px;
  }

  .hero-grid {
    display: grid;
    grid-template-columns: 1.12fr 0.88fr;
    gap: 48px;
    align-items: center;
  }

  /* Left Hero Column */
  .hero-left {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .eyebrow-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 16px;
    border-radius: var(--radius-pill);
    background-color: var(--green-pale);
    border: 1px solid var(--green-border);
    color: var(--green-deep);
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.01em;
    margin-bottom: 22px;
  }

  .eyebrow-badge svg {
    color: var(--green-primary);
    flex-shrink: 0;
  }

  .hero-headline {
    font-size: 54px;
    line-height: 1.08;
    font-weight: 900;
    letter-spacing: -1.6px;
    color: var(--text-dark);
    margin-bottom: 18px;
  }

  .hero-headline .accent-crown {
    color: var(--green-primary);
  }

  .hero-description {
    font-size: 16.5px;
    line-height: 1.6;
    color: var(--text-body);
    max-width: 520px;
  }

  .hero-credits {
    font-size: 14.5px;
    color: var(--text-body);
    margin-top: 12px;
    margin-bottom: 32px;
  }

  .hero-credits strong {
    color: var(--green-primary);
    font-weight: 700;
  }

  /* 3 Mini Feature Cards Grid */
  .mini-features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
    width: 100%;
    max-width: 580px;
  }

  .mini-feature-card {
    background: #ffffff;
    border: 1px solid var(--border-light);
    border-radius: 16px;
    padding: 18px 16px;
    box-shadow: 0 4px 16px -4px rgba(15, 23, 42, 0.05);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  }

  .mini-feature-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px -4px rgba(15, 23, 42, 0.1);
    border-color: var(--green-border);
  }

  .mini-feature-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    background-color: var(--green-primary);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(22, 163, 74, 0.3);
  }

  .mini-feature-title {
    font-size: 13.5px;
    font-weight: 800;
    color: var(--text-dark);
    margin-bottom: 4px;
    line-height: 1.25;
  }

  .mini-feature-text {
    font-size: 12px;
    line-height: 1.45;
    color: var(--text-muted);
  }

  /* Right Column (Hero Visual + Frame) */
  .hero-right {
    position: relative;
    width: 100%;
  }

  .stage-image-container {
    position: relative;
    width: 100%;
    border-radius: 28px;
    overflow: hidden;
    box-shadow: 0 24px 50px -12px rgba(15, 23, 42, 0.22);
    background-color: #0c1813;
    border: 3px solid #ffffff;
  }

  .stage-image {
    width: 100%;
    height: 100%;
    aspect-ratio: 16 / 12;
    object-fit: cover;
    display: block;
  }

  /* Responsiveness */
  @media (max-width: 1024px) {
    .hero-grid {
      grid-template-columns: 1fr;
      gap: 40px;
    }
    .hero-headline {
      font-size: 44px;
      letter-spacing: -1.2px;
    }
    .stage-image-container {
      max-width: 600px;
      margin: 0 auto;
    }
  }

  @media (max-width: 640px) {
    .app-container {
      padding: 0 20px;
    }
    .hero-wrapper {
      padding: 36px 0 48px;
    }
    .hero-headline {
      font-size: 34px;
      letter-spacing: -0.8px;
    }
    .mini-features-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
</head>
<body>

{{-- Ambient Background Glow Layer --}}
<div class="bg-ambient-layer" aria-hidden="true">
  <div class="bg-wave-left"></div>
  <div class="bg-wave-right"></div>
  <div class="bg-lines-overlay"></div>
</div>

<div class="app-container">

  {{-- Header / Navigation --}}
  <header>
    <div class="app-container" style="padding:0;">
      <nav class="nav-bar">
        {{-- Brand --}}
        <a href="{{ route('home') }}" class="brand-group" aria-label="CrownScore Home">
          <div class="brand-logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="CPSU CrownScore Logo" class="brand-logo-img">
          </div>
          <div class="brand-text">
            <div class="brand-title">CrownScore</div>
            <div class="brand-subtitle">Pageant Judging System</div>
          </div>
        </a>

        {{-- Right Login Pill Button --}}
        <div>
          <a href="{{ route('login') }}" class="btn-pill-login" aria-label="Login to CrownScore">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Login
          </a>
        </div>
      </nav>
    </div>
  </header>

  {{-- Hero Section --}}
  <main class="hero-wrapper">
    <div class="hero-grid">
      
      {{-- Left Hero Column --}}
      <div class="hero-left">
        {{-- Eyebrow Badge --}}
        <div class="eyebrow-badge">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l3.5 3L12 5l5.5 6L21 8l-1.6 9.5a1 1 0 01-1 .8H5.6a1 1 0 01-1-.8L3 8z"/>
          </svg>
          Fair. Transparent. Real-Time.
        </div>

        {{-- Headline --}}
        <h1 class="hero-headline">
          Where Excellence<br>
          Meets the <span class="accent-crown">Crown.</span>
        </h1>

        {{-- Subtitle --}}
        <p class="hero-description">
          CrownScore is a web-based real-time pageant judging and tabulation system built for accuracy, fairness, and speed.
        </p>

        {{-- Credit --}}
        <p class="hero-credits">
          Made by <strong>Team MISO.</strong>
        </p>

        {{-- 3 Mini Feature Cards --}}
        <div class="mini-features-grid">
          {{-- Feature 1 --}}
          <div class="mini-feature-card">
            <div class="mini-feature-icon">
              <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <div class="mini-feature-title">Real-Time Scoring</div>
            <div class="mini-feature-text">Instant, accurate results as it happens.</div>
          </div>

          {{-- Feature 2 --}}
          <div class="mini-feature-card">
            <div class="mini-feature-icon">
              <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
              </svg>
            </div>
            <div class="mini-feature-title">Secure &amp; Reliable</div>
            <div class="mini-feature-text">Your data is safe with enterprise-grade security.</div>
          </div>

          {{-- Feature 3 --}}
          <div class="mini-feature-card">
            <div class="mini-feature-icon">
              <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="mini-feature-title">Transparent Results</div>
            <div class="mini-feature-text">Clear, auditable, and built for fairness.</div>
          </div>
        </div>

      </div>

      {{-- Right Hero Column (Stage Image) --}}
      <div class="hero-right">
        <div class="stage-image-container">
          <img src="{{ asset('images/pageant_stage_hero.png') }}" 
               alt="CrownScore Grand Pageant Stage" 
               class="stage-image"
               onerror="this.src='https://images.unsplash.com/photo-1580889240911-931ce4dfa11e?w=900&q=80'">
        </div>
      </div>

    </div>

  </main>
</div>

</body>
</html>