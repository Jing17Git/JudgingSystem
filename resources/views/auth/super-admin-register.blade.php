<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>JudgingSystem — Secret Super-Admin Portal</title>
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;1,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --purple-950: #0D0814;
    --purple-900: #1B0F2E;
    --purple-800: #2C1847;
    --purple-700: #4C2180;
    --purple-600: #6B21A8;
    --purple-500: #9333EA;
    --purple-400: #C084FC;
    --purple-200: #E9D5FF;
    --purple-50: #FAF5FF;
    --white: #FFFFFF;
    --red-600: #E11D48;
    --red-50: #FFF1F2;
    --red-200: #FECDD3;
    --slate-400: #94A3B8;
    --slate-700: #334155;
    --slate-800: #1E293B;
  }

  * { box-sizing: border-box; }
  html, body { height: 100%; margin: 0; }
  body {
    font-family: 'Inter', sans-serif;
    color: var(--white);
    background:
      radial-gradient(55% 55% at 20% 15%, rgba(147, 51, 234, 0.25), transparent 60%),
      radial-gradient(60% 60% at 85% 85%, rgba(99, 102, 241, 0.2), transparent 65%),
      linear-gradient(160deg, #090511 0%, #150A26 50%, #0B0418 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 32px 20px;
  }

  .card {
    width: 100%;
    max-width: 1000px;
    display: flex;
    background: rgba(23, 14, 38, 0.85);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(192, 132, 252, 0.2);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 35px 80px -25px rgba(0, 0, 0, 0.6), 0 0 40px rgba(147, 51, 234, 0.15);
  }

  .brand-panel {
    position: relative;
    flex: 0 0 42%;
    background: linear-gradient(165deg, rgba(76, 33, 128, 0.9) 0%, rgba(27, 15, 46, 0.95) 100%);
    padding: 44px 40px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-right: 1px solid rgba(255, 255, 255, 0.1);
  }

  .secret-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 14px;
    border-radius: 999px;
    background: rgba(225, 29, 72, 0.2);
    border: 1px solid rgba(225, 29, 72, 0.4);
    color: #FF85A1;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    margin-bottom: 24px;
  }

  .brand-headline {
    font-family: 'Playfair Display', serif;
    font-size: 32px;
    font-weight: 700;
    line-height: 1.2;
    margin: 0 0 16px;
    color: var(--white);
  }
  .brand-headline em {
    font-style: italic;
    color: var(--purple-400);
  }

  .brand-sub {
    font-size: 14px;
    color: rgba(255, 255, 255, 0.75);
    line-height: 1.6;
    margin: 0;
  }

  .security-info {
    margin-top: 36px;
    padding: 20px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.08);
  }
  .security-info h4 {
    margin: 0 0 6px;
    font-size: 13px;
    font-weight: 700;
    color: var(--purple-200);
  }
  .security-info p {
    margin: 0;
    font-size: 12.5px;
    color: var(--slate-400);
    line-height: 1.5;
  }

  .form-panel {
    flex: 1;
    padding: 44px 44px;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }

  .form-title {
    font-family: 'Playfair Display', serif;
    font-size: 26px;
    font-weight: 700;
    margin: 0 0 6px;
    color: var(--white);
  }
  .form-desc {
    font-size: 13.5px;
    color: var(--slate-400);
    margin: 0 0 24px;
  }

  .alert-banner {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 13px;
    margin-bottom: 20px;
    background: var(--red-50);
    border: 1px solid var(--red-200);
    color: var(--red-600);
  }

  .form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
  }

  .field {
    margin-bottom: 16px;
  }
  .field.full {
    grid-column: span 2;
  }
  .field label {
    display: block;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--purple-200);
    margin-bottom: 6px;
  }
  .field input {
    width: 100%;
    padding: 11px 14px;
    font-size: 13.5px;
    font-family: 'Inter', sans-serif;
    color: var(--white);
    background: rgba(15, 9, 26, 0.8);
    border: 1.5px solid rgba(192, 132, 252, 0.25);
    border-radius: 10px;
    outline: none;
    transition: all 0.15s ease;
  }
  .field input:focus {
    border-color: var(--purple-400);
    box-shadow: 0 0 0 3.5px rgba(192, 132, 252, 0.2);
    background: rgba(20, 12, 36, 0.95);
  }

  .submit {
    width: 100%;
    padding: 13px;
    margin-top: 8px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--purple-600), var(--purple-800));
    color: var(--white);
    font-size: 14.5px;
    font-weight: 700;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    box-shadow: 0 10px 25px -8px rgba(147, 51, 234, 0.5);
    transition: transform 0.15s ease, filter 0.15s ease;
  }
  .submit:hover {
    filter: brightness(1.1);
    transform: translateY(-1px);
  }

  .back-login {
    text-align: center;
    margin-top: 20px;
    font-size: 13px;
  }
  .back-login a {
    color: var(--purple-400);
    text-decoration: none;
    font-weight: 600;
  }
  .back-login a:hover {
    text-decoration: underline;
  }

  @media (max-width: 768px) {
    .card { flex-direction: column; max-width: 480px; }
    .brand-panel { display: none; }
    .form-grid { grid-template-columns: 1fr; }
    .field.full { grid-column: span 1; }
  }
</style>
</head>
<body>

<div class="card">
  {{-- Left Brand Panel --}}
  <div class="brand-panel">
    <div>
      <div class="secret-badge">
        <svg class="w-3.5 h-3.5" style="width:14px;height:14px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Restricted Access
      </div>

      <h1 class="brand-headline">Super-Admin<br><em>Secret Portal.</em></h1>
      <p class="brand-sub">Creation of super-administrative accounts requires system passcode authorization.</p>
    </div>

    <div class="security-info">
      <h4>Passcode Protection</h4>
      <p>Enter the system-configured secret key to elevate this account to Super-Administrator privileges.</p>
    </div>
  </div>

  {{-- Right Form Panel --}}
  <div class="form-panel">
    <h2 class="form-title">Register Super-Admin</h2>
    <p class="form-desc">Provide your credentials and secret authorization key.</p>

    @if($errors->any())
      <div class="alert-banner">
        <svg style="width:18px;height:18px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div>{{ $errors->first() }}</div>
      </div>
    @endif

    <form method="POST" action="{{ route('super-admin.secret-register.store') }}">
      @csrf

      <div class="form-grid">
        <div class="field">
          <label for="name">Full Name</label>
          <input id="name" name="name" type="text" placeholder="e.g. Master Admin" value="{{ old('name') }}" required autofocus>
        </div>

        <div class="field">
          <label for="username">Username</label>
          <input id="username" name="username" type="text" placeholder="e.g. superadmin2" value="{{ old('username') }}" required>
        </div>

        <div class="field full">
          <label for="email">Email Address</label>
          <input id="email" name="email" type="email" placeholder="superadmin@pageant.com" value="{{ old('email') }}" required>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" placeholder="••••••••" required>
        </div>

        <div class="field">
          <label for="password_confirmation">Confirm Password</label>
          <input id="password_confirmation" name="password_confirmation" type="password" placeholder="••••••••" required>
        </div>

        <div class="field full">
          <label for="secret_key">Secret Security Key</label>
          <input id="secret_key" name="secret_key" type="password" placeholder="Enter SUPER_ADMIN_SECRET_KEY" required>
        </div>
      </div>

      <button type="submit" class="submit">
        <svg style="width:18px;height:18px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
        </svg>
        Register Super-Admin Account
      </button>

      <div class="back-login">
        Already registered? <a href="{{ route('login') }}">Return to Sign In</a>
      </div>
    </form>
  </div>
</div>

</body>
</html>
