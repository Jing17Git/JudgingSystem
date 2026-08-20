<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>JudgingSystem — Reset Password</title>
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

  /* sash */
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
  .form-wrap{width:100%; max-width:360px;}

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
  .brand-logo-wrap.mobile{
    width:68px;height:68px;
    margin:0 auto 16px;
    padding:5px;
    background:var(--white);
    border-radius:50%;
    box-shadow:0 10px 24px -8px rgba(27,138,82,0.35);
    border:2px solid var(--green-200);
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
  .form-desc{font-size:13.5px; line-height:1.5; color:var(--ink-500); margin:0 0 24px;}

  /* Alert styles */
  .alert-banner{
    display:flex;
    align-items:flex-start;
    gap:10px;
    padding:12px 14px;
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
  .field input:read-only{
    background:#f8faf9;
    color:var(--ink-500);
    cursor:not-allowed;
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

  .submit{
    width:100%; padding:13.5px; border:none; border-radius:10px;
    background:linear-gradient(180deg, var(--green-500), var(--green-700));
    color:#fff; font-size:14.5px; font-weight:700;
    cursor:pointer; display:flex; align-items:center; justify-content:center; gap:9px;
    box-shadow:0 10px 22px -10px rgba(27,138,82,0.55);
    transition:transform .12s ease, filter .12s ease;
    margin-top:24px;
  }
  .submit:hover{filter:brightness(1.06); transform:translateY(-1px);}
  .submit:active{transform:translateY(0);}
  .submit svg{width:16px;height:16px;}

  .back-to-login-row{
    text-align:center;
    margin-top:22px;
  }
  .back-to-login-link{
    display:inline-flex;
    align-items:center;
    gap:6px;
    font-size:13.5px;
    font-weight:600;
    color:var(--green-700);
    text-decoration:none;
    transition:color .15s ease;
  }
  .back-to-login-link:hover{
    color:var(--green-900);
    text-decoration:underline;
  }
  .back-to-login-link svg{width:15px;height:15px;}

  .form-foot{text-align:center; font-size:12px; color:var(--ink-300); margin-top:24px;}

  /* ---------- RESPONSIVE ---------- */
  @media (max-width: 860px){
    body{padding:24px 16px;}
    .card{
      max-width:420px;
      box-shadow:0 24px 60px -20px rgba(15,61,46,0.22);
    }
    .brand-panel{display:none;}
    .form-panel{padding:36px 28px 32px; width:100%;}
    .form-wrap{max-width:100%;}
    .mobile-back{display:block; margin-bottom:28px;}
    .mobile-brand{display:block;}
    .form-eyebrow,
    .form-title,
    .form-desc{display:none;}
  }

  @media (prefers-reduced-motion: reduce){ *{transition:none !important;} }
</style>
</head>
<body>

<div class="card">

  <!-- LEFT: BRAND / DESCRIPTION PANEL (hidden on mobile) -->
  <div class="brand-panel">
    <div class="brand-top">
      <a href="{{ route('login') }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Back to Login
      </a>
    </div>

    <div class="brand-mid">
      <div class="seal-row">
        <div class="brand-logo-wrap">
          <img src="{{ asset('images/logo.png') }}" alt="CPSU Logo" class="brand-logo-img">
        </div>
        <div class="seal-text">Central Philippines<br>State University</div>
      </div>

      <h1 class="brand-headline">New<br><em>Password.</em></h1>
      <p class="brand-sub">Create a new, strong password to secure your account and access your judging console.</p>

      <ul class="feature-list">
        <li><span class="num">01</span><div><strong>Minimum 6 Characters</strong>Use a mix of letters, numbers, and symbols.</div></li>
        <li><span class="num">02</span><div><strong>Instant Update</strong>Your credentials will be updated immediately.</div></li>
        <li><span class="num">03</span><div><strong>Secure Session</strong>Old password tokens are permanently invalidated.</div></li>
      </ul>
    </div>

    <div class="brand-foot">
      <span>Pageant Judging &amp; Tabulation</span>
      <span>Est. 2026</span>
    </div>
  </div>

  <!-- RIGHT: RESET PASSWORD FORM PANEL -->
  <div class="form-panel">
    <div class="form-wrap">

      <!-- shown only on mobile -->
      <div class="mobile-back">
        <a href="{{ route('login') }}" class="mobile-back-btn">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Back to Login
        </a>
      </div>
      <div class="mobile-brand">
        <div class="brand-logo-wrap mobile">
          <img src="{{ asset('images/logo.png') }}" alt="CPSU Logo" class="brand-logo-img">
        </div>
        <h1>Create Password</h1>
        <p>Set a new secure password</p>
      </div>

      <!-- desktop headers -->
      <p class="form-eyebrow">JudgingSystem Security</p>
      <h2 class="form-title">Reset Password</h2>
      <p class="form-desc">Enter your email and choose a new password for your account.</p>

      {{-- Error Alert --}}
      @if($errors->any())
        <div class="alert-banner error">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <div>{{ $errors->first() }}</div>
        </div>
      @endif

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="field">
          <label for="email">Email Address</label>
          <input id="email"
                 name="email"
                 type="email"
                 placeholder="name@cpsu.edu.ph"
                 value="{{ old('email', $email) }}"
                 required
                 autocomplete="email">
        </div>

        <div class="field">
          <label for="password">New Password</label>
          <div class="input-wrap">
            <input id="password"
                   name="password"
                   type="password"
                   placeholder="Enter new password (min. 6 chars)"
                   required
                   autocomplete="new-password">
            <button type="button" class="pw-toggle" id="pwToggle1" aria-label="Show password">
              <svg id="eyeIcon1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <div class="field">
          <label for="password_confirmation">Confirm New Password</label>
          <div class="input-wrap">
            <input id="password_confirmation"
                   name="password_confirmation"
                   type="password"
                   placeholder="Re-enter new password"
                   required
                   autocomplete="new-password">
            <button type="button" class="pw-toggle" id="pwToggle2" aria-label="Show password">
              <svg id="eyeIcon2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
          </div>
        </div>

        <button type="submit" class="submit">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Update Password
        </button>
      </form>

      <div class="back-to-login-row">
        <a href="{{ route('login') }}" class="back-to-login-link">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
          Return to Sign In
        </a>
      </div>

      <div class="form-foot">Central Philippines State University &bull; JudgingSystem</div>
    </div>
  </div>

</div>

<script>
  function setupToggle(toggleId, inputId, iconId) {
    const btn = document.getElementById(toggleId);
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (!btn || !input || !icon) return;

    btn.addEventListener('click', function() {
      const isPass = input.type === 'password';
      input.type = isPass ? 'text' : 'password';
      icon.innerHTML = isPass
        ? '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>'
        : '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/><circle cx="12" cy="12" r="3"/>';
    });
  }

  setupToggle('pwToggle1', 'password', 'eyeIcon1');
  setupToggle('pwToggle2', 'password_confirmation', 'eyeIcon2');
</script>

</body>
</html>
