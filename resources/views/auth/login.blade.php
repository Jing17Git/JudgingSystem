<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>JudgingSystem — Sign In</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root{
    --green-900:#0F3D2E;
    --green-800:#146341;
    --green-700:#1B8A52;
    --green-600:#22A85F;
    --green-500:#34C776;
    --green-400:#52D68C;
    --green-300:#8CE0AC;
    --green-200:#B9EECB;
    --green-100:#DDF7E5;
    --green-50:#F0FBF3;
    --white:#FFFFFF;
    --ink-900:#16281F;
    --ink-500:#5C7568;
    --ink-300:#98AEA1;
    --line:#DCF0E2;
    --red-600:#E11D48;
    --red-50:#FFF1F2;
    --red-200:#FECDD3;
  }

  *{box-sizing:border-box;}
  html,body{height:100%;}
  body{
    margin:0;
    min-height:100vh;
    font-family:'Inter',sans-serif;
    color:var(--ink-900);
    display:flex;
    align-items:center;
    justify-content:center;
    padding:32px 20px;
    background:
      radial-gradient(60% 55% at 12% 10%, rgba(140,224,172,0.55), transparent 60%),
      radial-gradient(55% 55% at 92% 85%, rgba(140,224,172,0.4), transparent 60%),
      linear-gradient(160deg, #EAFBF0 0%, #F6FEF8 45%, #EFFBF3 100%);
  }

  /* ---------- CARD SHELL ---------- */
  .card{
    width:100%;
    max-width:1200px;
    display:flex;
    background:var(--white);
    border-radius:22px;
    overflow:hidden;
    box-shadow:0 30px 70px -25px rgba(15,61,46,0.25);
  }

  /* ---------- LEFT: BRAND / DESCRIPTION PANEL ---------- */
  .brand-panel{
    position:relative;
    flex:0 0 44%;
    background:
      radial-gradient(120% 90% at 90% 0%, rgba(255,255,255,0.5), transparent 55%),
      linear-gradient(165deg, var(--green-600) 0%, var(--green-800) 100%);
    color:var(--white);
    overflow:hidden;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    padding:44px 40px 36px;
    isolation:isolate;
  }

  .brand-panel::before{
    content:"";
    position:absolute;
    inset:0;
    background-image:
      repeating-linear-gradient(0deg, rgba(255,255,255,0.06) 0 1px, transparent 1px 56px),
      repeating-linear-gradient(90deg, rgba(255,255,255,0.06) 0 1px, transparent 1px 56px);
    z-index:0;
  }

  /* sash — signature element */
  .sash{
    position:absolute;
    top:96px;
    left:-110px;
    width:460px;
    padding:10px 0;
    background:linear-gradient(90deg, #FFFFFF, var(--green-100) 55%, var(--green-200));
    color:var(--green-800);
    font-weight:700;
    font-size:11.5px;
    letter-spacing:.2em;
    text-align:center;
    transform:rotate(-38deg);
    box-shadow:0 16px 34px -12px rgba(0,0,0,0.3);
    z-index:0;
  }
  .sash::before,.sash::after{
    content:"";
    position:absolute;
    top:0; bottom:0;
    width:12px;
    background:rgba(15,61,46,0.12);
  }
  .sash::before{left:0;}
  .sash::after{right:0;}

  .brand-top{position:relative;z-index:1;}

  .back-link{
    display:inline-flex;
    align-items:center;
    gap:8px;
    color:var(--white);
    text-decoration:none;
    font-size:13px;
    font-weight:600;
    opacity:.85;
    transition:opacity .15s ease, transform .15s ease;
  }
  .back-link:hover{opacity:1; transform:translateX(-2px);}
  .back-link svg{width:14px;height:14px;}

  .brand-mid{position:relative; z-index:1; margin-top:44px;}

  .seal-row{display:flex; align-items:center; gap:14px; margin-bottom:28px;}
  .brand-logo-wrap{
    width:56px;height:56px;
    border-radius:50%;
    background:var(--white);
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;
    padding:4px;
    box-shadow:0 0 0 3px rgba(255,255,255,0.25), 0 8px 16px -4px rgba(0,0,0,0.2);
  }
  .brand-logo-img{
    width:100%;
    height:100%;
    object-fit:contain;
  }
  .brand-logo-wrap.mobile{
    width:68px;height:68px;
    margin:0 auto 16px;
    padding:5px;
    background:var(--white);
    border-radius:50%;
    box-shadow:0 10px 24px -8px rgba(27,138,82,0.35);
    border:2px solid var(--green-200);
  }
  .seal-text{
    font-size:11px;
    letter-spacing:.14em;
    text-transform:uppercase;
    color:var(--green-100);
    font-weight:600;
    line-height:1.5;
  }

  .brand-headline{
    font-family:'Playfair Display',serif;
    font-weight:600;
    font-size:34px;
    line-height:1.15;
    margin:0 0 16px;
  }
  .brand-headline em{font-style:italic; color:var(--green-100);}

  .brand-sub{
    font-size:14.5px;
    line-height:1.6;
    color:rgba(255,255,255,0.82);
    margin:0;
  }

  .feature-list{
    position:relative; z-index:1;
    list-style:none; padding:0;
    margin:32px 0 0;
    display:flex; flex-direction:column; gap:16px;
  }
  .feature-list li{display:flex; align-items:flex-start; gap:12px; font-size:13.5px; color:rgba(255,255,255,0.88);}
  .feature-list .num{
    font-family:'Playfair Display',serif;
    font-style:italic; font-weight:600;
    color:var(--green-100);
    font-size:14px; width:20px; flex-shrink:0;
  }
  .feature-list strong{display:block; color:var(--white); font-weight:600; font-size:14px; margin-bottom:2px;}

  .brand-foot{
    position:relative; z-index:1;
    display:flex; justify-content:space-between; align-items:flex-end;
    padding-top:28px;
    border-top:1px solid rgba(255,255,255,0.2);
    font-size:11.5px;
    color:rgba(255,255,255,0.65);
  }

  /* ---------- RIGHT: FORM PANEL ---------- */
  .form-panel{
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:44px 40px;
  }
  .form-wrap{width:100%; max-width:340px;}

  .mobile-back{display:none;}
  .mobile-back-btn{
    display:inline-flex; align-items:center; gap:8px;
    padding:9px 16px;
    border:1.5px solid var(--green-200);
    border-radius:999px;
    color:var(--green-700);
    font-weight:700;
    font-size:13.5px;
    text-decoration:none;
    background:var(--white);
  }
  .mobile-back-btn svg{width:15px;height:15px;}

  .mobile-brand{display:none; text-align:center; margin-bottom:30px;}
  .mobile-seal{
    width:64px;height:64px;
    margin:0 auto 16px;
    border-radius:50%;
    background:linear-gradient(160deg, var(--green-500), var(--green-700));
    display:flex; align-items:center; justify-content:center;
    font-family:'Playfair Display',serif;
    font-weight:700; color:#fff; font-size:22px;
    box-shadow:0 10px 24px -10px rgba(27,138,82,0.5);
  }
  .mobile-brand h1{
    font-family:'Playfair Display',serif;
    font-weight:600;
    font-size:24px;
    margin:0 0 4px;
    color:var(--green-900);
  }
  .mobile-brand p{margin:0; font-size:13.5px; color:var(--ink-500);}

  .form-eyebrow{
    font-size:11px; font-weight:700; letter-spacing:.16em;
    text-transform:uppercase; color:var(--green-700); margin:0 0 8px;
  }
  .form-title{
    font-family:'Playfair Display',serif;
    font-weight:600; font-size:27px; margin:0 0 6px; color:var(--green-900);
  }
  .form-desc{font-size:13.5px; color:var(--ink-500); margin:0 0 24px;}

  /* Alert styles */
  .alert-banner{
    display:flex;
    align-items:flex-start;
    gap:10px;
    padding:11px 14px;
    border-radius:10px;
    font-size:13px;
    line-height:1.45;
    margin-bottom:20px;
  }
  .alert-banner.error{
    background:var(--red-50);
    border:1px solid var(--red-200);
    color:var(--red-600);
  }
  .alert-banner.success{
    background:var(--green-50);
    border:1px solid var(--green-200);
    color:var(--green-800);
  }
  .alert-banner svg{width:18px;height:18px;flex-shrink:0;margin-top:1px;}

  .field{margin-bottom:18px;}
  .field label{
    display:block; font-size:11px; font-weight:700; letter-spacing:.08em;
    text-transform:uppercase; color:var(--ink-900); margin-bottom:7px;
  }
  .input-wrap{position:relative;}
  .field input{
    width:100%; padding:12px 14px; font-size:14px;
    font-family:'Inter',sans-serif; color:var(--ink-900);
    background:var(--white); border:1.5px solid var(--line);
    border-radius:10px; outline:none;
    transition:border-color .15s ease, box-shadow .15s ease;
  }
  .field input::placeholder{color:var(--ink-300);}
  .field input:focus{
    border-color:var(--green-500);
    box-shadow:0 0 0 3.5px rgba(52,199,118,0.16);
  }

  .pw-toggle{
    position:absolute; right:12px; top:50%; transform:translateY(-50%);
    background:none; border:none; cursor:pointer; color:var(--ink-300);
    padding:4px; display:flex; line-height:0;
  }
  .pw-toggle:hover{color:var(--ink-500);}
  .pw-toggle svg{width:17px;height:17px;}

  .row-between{display:flex; align-items:center; justify-content:space-between; margin:2px 0 26px;}

  .remember{display:flex; align-items:center; gap:9px; font-size:13px; color:var(--ink-500); cursor:pointer; user-select:none;}
  .remember input{
    appearance:none; width:16px;height:16px;
    border:1.5px solid var(--line); border-radius:5px;
    cursor:pointer; position:relative; background:var(--white); flex-shrink:0;
    transition:background .15s ease, border-color .15s ease;
  }
  .remember input:checked{background:var(--green-600); border-color:var(--green-600);}
  .remember input:hover{border-color:var(--green-400, var(--green-500));}
  .remember input:checked::after{
    content:""; position:absolute; left:4.5px; top:1.5px;
    width:4px; height:8px; border:solid white; border-width:0 2px 2px 0;
    transform:rotate(45deg);
  }

  .forgot{font-size:13px; color:var(--green-700); text-decoration:none; font-weight:600;}
  .forgot:hover{text-decoration:underline;}

  .submit{
    width:100%; padding:13.5px; border:none; border-radius:10px;
    background:linear-gradient(180deg, var(--green-500), var(--green-700));
    color:#fff; font-size:14.5px; font-weight:700;
    cursor:pointer; display:flex; align-items:center; justify-content:center; gap:9px;
    box-shadow:0 10px 22px -10px rgba(27,138,82,0.55);
    transition:transform .12s ease, filter .12s ease;
  }
  .submit:hover{filter:brightness(1.06); transform:translateY(-1px);}
  .submit:active{transform:translateY(0);}
  .submit svg{width:16px;height:16px;}

  .form-foot{text-align:center; font-size:12px; color:var(--ink-300); margin-top:24px;}

  /* ---------- RESPONSIVE: ANDROID / MOBILE — FORM ONLY ---------- */
  @media (max-width: 860px){
    body{padding:24px 16px;}
    .card{
      max-width:420px;
      box-shadow:0 24px 60px -20px rgba(15,61,46,0.22);
    }
    .brand-panel{display:none;}          /* hide description panel entirely */
    .form-panel{padding:36px 28px 32px; width:100%;}
    .form-wrap{max-width:100%;}
    .mobile-back{display:block; margin-bottom:28px;}
    .mobile-brand{display:block;}
    .form-eyebrow,
    .form-title,
    .form-desc{display:none;}            /* replaced by mobile-brand block */
  }

  @media (prefers-reduced-motion: reduce){ *{transition:none !important;} }
</style>
</head>
<body>

<div class="card" >

  <!-- LEFT: BRAND / DESCRIPTION PANEL (hidden on mobile) -->
  <div class="brand-panel">
  
    <div class="brand-top">
      <a href="{{ route('home') }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Home
      </a>
    </div>

    <div class="brand-mid">
      <div class="seal-row">
        <div class="brand-logo-wrap">
          <img src="{{ asset(\App\Models\SiteSetting::get('site_logo', 'images/logo.png')) }}" alt="Logo" class="brand-logo-img">
        </div>
        <div class="seal-text">Central Philippines<br>State University</div>
      </div>

      <h1 class="brand-headline">Every score,<br><em>tallied live.</em></h1>
      <p class="brand-sub">JudgingSystem keeps every judge, every criterion, and every result in sync — from the first walk to the final tally.</p>

      <ul class="feature-list">
        <li><span class="num">01</span><div><strong>Live scorekeeping</strong>Judges submit scores in real time, no paper sheets to reconcile.</div></li>
        <li><span class="num">02</span><div><strong>Tamper-evident tabulation</strong>Every entry is timestamped and locked once submitted.</div></li>
        <li><span class="num">03</span><div><strong>Instant results</strong>Rankings compute automatically the moment scoring closes.</div></li>
      </ul>
    </div>

    <div class="brand-foot">
      <span>Pageant Judging &amp; Tabulation</span>
      <span>Est. 2026</span>
    </div>
  </div>

  <!-- RIGHT: LOGIN FORM PANEL (always visible) -->
  <div class="form-panel">
    <div class="form-wrap">

      <!-- shown only on mobile, since brand panel is hidden -->
      <div class="mobile-back">
        <a href="{{ route('home') }}" class="mobile-back-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Back to Home
        </a>
      </div>
      <div class="mobile-brand">
        <div class="brand-logo-wrap mobile">
          <img src="{{ asset(\App\Models\SiteSetting::get('site_logo', 'images/logo.png')) }}" alt="Logo" class="brand-logo-img">
        </div>
        <h1>JudgingSystem</h1>
        <p>Sign in to your account</p>
      </div>

      <!-- shown only on desktop, alongside brand panel -->
      <p class="form-eyebrow">JudgingSystem</p>
      <h2 class="form-title">Sign in</h2>
      <p class="form-desc">Enter your credentials to access your judging panel.</p>

      {{-- Error Alert --}}
      @if($errors->any())
        <div class="alert-banner error">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div>{{ $errors->first() }}</div>
        </div>
      @endif

      {{-- Success Alert --}}
      @if(session('success'))
        <div class="alert-banner success">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <div>{{ session('success') }}</div>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
          <label for="username">Username</label>
          <input id="username"
                 name="username"
                 type="text"
                 placeholder="Enter your username"
                 value="{{ old('username') }}"
                 required
                 autofocus
                 autocomplete="username">
        </div>

        <div class="field">
          <label for="password">Password</label>
          <div class="input-wrap">
            <input id="password"
                   name="password"
                   type="password"
                   placeholder="Enter your password"
                   required
                   autocomplete="current-password">
            <button type="button" class="pw-toggle" id="pwToggle" aria-label="Show password">
              <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <div class="row-between">
          <label class="remember">
            <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
            Remember me
          </label>
          <a href="{{ route('password.request') }}" class="forgot">Forgot password?</a>
        </div>

        <button type="submit" class="submit">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
          Sign in
        </button>
      </form>

      <p class="form-foot">Real-Time Pageant Judging &amp; Tabulation System</p>
    </div>
  </div>

</div>

<script>
  const pwInput = document.getElementById('password');
  const pwToggle = document.getElementById('pwToggle');
  const eyeIcon = document.getElementById('eyeIcon');
  const eyeOff = '<path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a19.9 19.9 0 0 1 5.06-5.94M9.9 4.24A10.6 10.6 0 0 1 12 4c7 0 11 7 11 7a19.9 19.9 0 0 1-4.22 5.06M14.12 14.12a3 3 0 1 1-4.24-4.24"/><path d="M1 1l22 22"/>';
  const eyeOn = '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>';
  if (pwToggle && pwInput && eyeIcon) {
    pwToggle.addEventListener('click', () => {
      const isPw = pwInput.type === 'password';
      pwInput.type = isPw ? 'text' : 'password';
      eyeIcon.innerHTML = isPw ? eyeOff : eyeOn;
      pwToggle.setAttribute('aria-label', isPw ? 'Hide password' : 'Show password');
    });
  }
</script>

</body>
</html>
