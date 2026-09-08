<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>{{ \App\Models\SiteSetting::get('site_name', 'CrownScore') }} — Super-Admin Login</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;1,600&display=swap" rel="stylesheet">
<style>
  :root {
    --purple-900: #2e1065;
    --purple-800: #4c1d95;
    --purple-700: #6d28d9;
    --purple-600: #7c3aed;
    --purple-500: #8b5cf6;
    --purple-400: #a78bfa;
    --purple-200: #ddd6fe;
    --purple-100: #ede9fe;
    --purple-50: #f5f3ff;
    --white: #ffffff;
    --ink-900: #0f172a;
    --ink-500: #475569;
    --ink-300: #94a3b8;
    --line: #e2e8f0;
    --red-600: #e11d48;
    --red-50: #fff1f2;
    --red-200: #fecdd3;
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { height: 100%; }
  body {
    min-height: 100vh;
    font-family: 'Plus Jakarta Sans', sans-serif;
    color: var(--ink-900);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 20px;
    background:
      radial-gradient(60% 55% at 12% 10%, rgba(167,139,250,0.45), transparent 60%),
      radial-gradient(55% 55% at 92% 85%, rgba(139,92,246,0.35), transparent 60%),
      linear-gradient(160deg, #f3e8ff 0%, #faf5ff 45%, #f3e8ff 100%);
  }

  .card {
    width: 100%;
    max-width: 1100px;
    display: flex;
    background: var(--white);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 30px 70px -25px rgba(76,29,149,0.3);
    border: 1px solid rgba(167,139,250,0.3);
  }

  /* LEFT: SUPER ADMIN BRAND PANEL */
  .brand-panel {
    position: relative;
    flex: 0 0 44%;
    background: linear-gradient(155deg, var(--purple-700) 0%, var(--purple-900) 100%);
    color: var(--white);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 44px 40px 36px;
    isolation: isolate;
  }

  .brand-panel::before {
    content: "";
    position: absolute;
    inset: 0;
    background-image:
      repeating-linear-gradient(0deg, rgba(255,255,255,0.05) 0 1px, transparent 1px 48px),
      repeating-linear-gradient(90deg, rgba(255,255,255,0.05) 0 1px, transparent 1px 48px);
    z-index: 0;
  }

  .super-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.25);
    border-radius: 999px;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #fff;
    margin-bottom: 20px;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--white);
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    opacity: 0.85;
    transition: opacity .15s ease;
  }
  .back-link:hover { opacity: 1; }

  .brand-mid { position: relative; z-index: 1; margin-top: 30px; }

  .seal-row { display: flex; align-items: center; gap: 14px; margin-bottom: 24px; }
  .brand-logo-wrap {
    width: 60px; height: 60px;
    border-radius: 16px;
    background: var(--white);
    display: flex; align-items: center; justify-content: center;
    padding: 6px;
    box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);
  }
  .brand-logo-img { width: 100%; height: 100%; object-fit: contain; }

  .brand-headline {
    font-family: 'Playfair Display', serif;
    font-weight: 700;
    font-size: 34px;
    line-height: 1.2;
    margin: 0 0 16px;
  }
  .brand-headline em { font-style: italic; color: var(--purple-200); }

  .brand-sub {
    font-size: 14px;
    line-height: 1.6;
    color: rgba(255,255,255,0.85);
    margin: 0;
  }

  .brand-foot {
    position: relative; z-index: 1;
    display: flex; justify-content: space-between; align-items: center;
    padding-top: 24px;
    border-top: 1px solid rgba(255,255,255,0.15);
    font-size: 11.5px;
    color: rgba(255,255,255,0.65);
  }

  /* RIGHT: FORM PANEL */
  .form-panel {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 44px 40px;
  }
  .form-wrap { width: 100%; max-width: 360px; }

  .form-eyebrow {
    font-size: 11px; font-weight: 800; letter-spacing: 0.16em;
    text-transform: uppercase; color: var(--purple-700); margin: 0 0 8px;
    display: flex; align-items: center; gap: 6px;
  }
  .form-title {
    font-family: 'Playfair Display', serif;
    font-weight: 700; font-size: 30px; margin: 0 0 6px; color: var(--ink-900);
  }
  .form-desc { font-size: 13.5px; color: var(--ink-500); margin: 0 0 24px; }

  .alert-banner {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 14px; border-radius: 12px; font-size: 13px; line-height: 1.45;
    margin-bottom: 20px;
  }
  .alert-banner.error {
    background: var(--red-50); border: 1px solid var(--red-200); color: var(--red-600);
  }

  .field { margin-bottom: 18px; }
  .field label {
    display: block; font-size: 11px; font-weight: 800; letter-spacing: 0.08em;
    text-transform: uppercase; color: var(--ink-900); margin-bottom: 7px;
  }
  .input-wrap { position: relative; }
  .field input {
    width: 100%; padding: 13px 14px; font-size: 14px;
    font-family: 'Plus Jakarta Sans', sans-serif; color: var(--ink-900);
    background: var(--white); border: 1.5px solid var(--line);
    border-radius: 12px; outline: none;
    transition: border-color .15s ease, box-shadow .15s ease;
  }
  .field input:focus {
    border-color: var(--purple-600);
    box-shadow: 0 0 0 4px rgba(124,58,237,0.15);
  }

  .pw-toggle {
    position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
    background: none; border: none; cursor: pointer; color: var(--ink-300);
    padding: 4px; display: flex;
  }

  .row-between { display: flex; align-items: center; justify-content: space-between; margin: 4px 0 24px; }

  .remember { display: flex; align-items: center; gap: 9px; font-size: 13px; color: var(--ink-500); cursor: pointer; }
  .remember input {
    width: 16px; height: 16px; border-radius: 4px; accent-color: var(--purple-600); cursor: pointer;
  }

  .submit-btn {
    width: 100%; padding: 14px; border: none; border-radius: 12px;
    background: linear-gradient(135deg, var(--purple-600), var(--purple-800));
    color: #fff; font-size: 15px; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;
    box-shadow: 0 12px 25px -8px rgba(124,58,237,0.5);
    transition: transform .12s ease, filter .12s ease;
  }
  .submit-btn:hover { filter: brightness(1.08); transform: translateY(-1px); }
  .submit-btn:active { transform: translateY(0); }

  @media (max-width: 860px) {
    .card { max-width: 440px; }
    .brand-panel { display: none; }
    .form-panel { padding: 36px 24px; }
  }
</style>
</head>
<body>

<div class="card">
  <!-- LEFT PANEL -->
  <div class="brand-panel">
    <div>
      <div class="super-badge">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        Super-Admin Gateway
      </div>
      <div>
        <a href="{{ route('home') }}" class="back-link">
          <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Back to Home Page
        </a>
      </div>
    </div>

    <div class="brand-mid">
      <div class="seal-row">
        <div class="brand-logo-wrap">
          <img src="{{ asset(\App\Models\SiteSetting::get('site_logo', 'images/logo.png')) }}" alt="Logo" class="brand-logo-img">
        </div>
        <div>
          <h3 style="font-size:18px; font-weight:800; color:#fff;">{{ \App\Models\SiteSetting::get('site_name', 'CrownScore') }}</h3>
          <p style="font-size:12px; color:var(--purple-200);">System Management Portal</p>
        </div>
      </div>

      <h1 class="brand-headline">Master Control &amp;<br><em>System Administration.</em></h1>
      <p class="brand-sub">Access the central super-admin console to manage global settings, category weightings, tabulations, and system accounts.</p>
    </div>

    <div class="brand-foot">
      <span>Restricted Access Portal</span>
      <span>Super-Admin Security v2.0</span>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="form-panel">
    <div class="form-wrap">
      <div class="form-eyebrow">
        <span style="width:8px; height:8px; border-radius:50%; background:var(--purple-600);"></span>
        Super Admin Login
      </div>
      <h2 class="form-title">Sign In</h2>
      <p class="form-desc">Enter your super-admin credentials to authenticate.</p>

      @if($errors->any())
        <div class="alert-banner error">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div>{{ $errors->first() }}</div>
        </div>
      @endif

      @if(session('success'))
        <div class="alert-banner" style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534;">
          <div>{{ session('success') }}</div>
        </div>
      @endif

      <form method="POST" action="{{ route('super-admin.login.store') }}">
        @csrf

        <div class="field">
          <label for="username">Username or Email</label>
          <input id="username" name="username" type="text" placeholder="Super-admin username" value="{{ old('username') }}" required autofocus autocomplete="username">
        </div>

        <div class="field">
          <label for="password">Password</label>
          <div class="input-wrap">
            <input id="password" name="password" type="password" placeholder="••••••••••••" required autocomplete="current-password">
            <button type="button" class="pw-toggle" id="pwToggle">
              <svg id="eyeIcon" width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <div class="row-between">
          <label class="remember">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            Remember session
          </label>
        </div>

        <button type="submit" class="submit-btn">
          <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path stroke-linecap="round" stroke-linejoin="round" d="M10 17l5-5-5-5"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12H3"/></svg>
          Authenticate Super Admin
        </button>
      </form>
    </div>
  </div>
</div>

<script>
  const pwInput = document.getElementById('password');
  const pwToggle = document.getElementById('pwToggle');
  const eyeIcon = document.getElementById('eyeIcon');
  if (pwToggle && pwInput && eyeIcon) {
    pwToggle.addEventListener('click', () => {
      const isPw = pwInput.type === 'password';
      pwInput.type = isPw ? 'text' : 'password';
      pwToggle.style.color = isPw ? '#7c3aed' : '#94a3b8';
    });
  }
</script>

</body>
</html>
