@extends('layouts.app')

@section('title', 'Login')

@section('body')
<div class="login-bg">
    {{-- Decorative background elements --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-yellow-500/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 right-1/4 w-72 h-72 bg-amber-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="login-card glass rounded-2xl animate-fade-in-up relative z-10">
        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-20 h-20 mx-auto mb-4 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="CPSU Logo" class="w-20 h-20 object-contain drop-shadow-md">
            </div>
            <h1 class="text-xl font-bold text-[var(--text-primary)]">JudgingSystem</h1>
            <p class="text-sm text-[var(--text-secondary)] mt-1">Sign in to your account</p>
        </div>

        {{-- Error messages --}}
        @if($errors->any())
            <div class="alert alert-error mb-6">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success mb-6">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Login form --}}
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label for="username" class="form-label">Username</label>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-input"
                       placeholder="Enter your username"
                       value="{{ old('username') }}"
                       required
                       autofocus>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input"
                       placeholder="Enter your password"
                       required>
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox"
                           name="remember"
                           class="w-4 h-4 rounded border-[var(--border-default)] bg-transparent text-green-600 focus:ring-green-500/30">
                    <span class="text-sm text-[var(--text-secondary)]">Remember me</span>
                </label>
            </div>

            <button type="submit" class="btn btn-green w-full justify-center text-base py-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                </svg>
                Sign In
            </button>
        </form>

        {{-- Footer --}}
        <p class="text-center text-xs text-[var(--text-muted)] mt-6">
            Real-Time Pageant Judging &amp; Tabulation System
        </p>
    </div>
</div>
@endsection
