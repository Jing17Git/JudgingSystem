@extends('layouts.app')

@section('body')
<div x-data="{ sidebarOpen: false }" class="flex min-h-screen">
    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-30 bg-black/50 lg:hidden"
         style="display: none;"></div>

    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'open' : ''" class="sidebar flex flex-col lg:translate-x-0">
        {{-- Logo area --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-[var(--border-default)]">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l3.5 7L12 6l3.5 4L19 3M5 21h14M5 17h14M8 13h8"/>
                </svg>
            </div>
            <div>
                <h1 class="text-sm font-bold text-[var(--text-primary)]">JudgingSystem</h1>
                <p class="text-xs font-semibold text-[var(--green-600)]">Judge Panel</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <div class="sidebar-section">Dashboard</div>

            <a href="{{ route('judge.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('judge.dashboard') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </span>
                Judge Dashboard
            </a>

            <div class="sidebar-section">Scoring</div>

            <a href="{{ route('judge.production.index') }}"
               class="sidebar-link {{ request()->routeIs('judge.production.*') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"/>
                    </svg>
                </span>
                Production
            </a>

            <a href="{{ route('judge.fitness.index') }}"
               class="sidebar-link {{ request()->routeIs('judge.fitness.*') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 13.5l10.5-10.5m0 0L18 6.75M14.25 3l3.75 3.75M3 14.25l3.75 3.75m0 0l10.5-10.5M6.75 18L3 14.25"/>
                    </svg>
                </span>
                Fitness
            </a>

            <a href="{{ route('judge.traditional-attire.index') }}"
               class="sidebar-link {{ request()->routeIs('judge.traditional-attire.*') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z"/>
                    </svg>
                </span>
                Traditional Attire
            </a>

            <a href="{{ route('judge.indigenous-attire.index') }}"
               class="sidebar-link {{ request()->routeIs('judge.indigenous-attire.*') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z"/>
                    </svg>
                </span>
                Indigenous Attire
            </a>

            <div class="sidebar-section">Account</div>

            <a href="{{ route('judge.profile') }}"
               class="sidebar-link {{ request()->routeIs('judge.profile') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                Profile
            </a>
        </nav>

        {{-- User info --}}
        <div class="border-t border-[var(--border-default)] px-4 py-4">
            <div class="flex items-center gap-3">
                @if(auth()->user()->photo_url)
                    <img src="{{ asset('storage/' . auth()->user()->photo_url) }}" class="w-9 h-9 rounded-lg object-cover border border-[var(--border-default)] shadow-sm flex-shrink-0">
                @else
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0 shadow-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[var(--text-primary)] truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[var(--text-muted)]">Official Judge</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[var(--text-muted)] hover:text-red-500 transition-colors p-1" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Main content --}}
    <div class="main-content flex-1">
        {{-- Top bar for mobile --}}
        <div class="lg:hidden flex items-center justify-between mb-6">
            <button @click="sidebarOpen = !sidebarOpen" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)]">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-sm font-semibold text-[var(--green-600)]">JudgingSystem — Judge Panel</span>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>
</div>
@endsection
