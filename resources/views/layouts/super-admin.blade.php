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

    {{-- Dedicated Super-Admin Sidebar --}}
    <aside :class="sidebarOpen ? 'open' : ''" class="sidebar flex flex-col lg:translate-x-0">
        {{-- Logo area --}}
        <div class="flex items-center gap-3 px-5 py-5 border-b border-[var(--border-default)]">
            <img src="{{ asset('images/logo.png') }}" alt="CPSU Logo" class="w-10 h-10 object-contain drop-shadow-sm flex-shrink-0">
            <div>
                <h1 class="text-sm font-bold text-[var(--text-primary)] leading-tight flex items-center gap-1.5">
                    JudgingSystem
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-purple-600 text-white uppercase tracking-wider">Super</span>
                </h1>
                <p class="text-xs text-[var(--text-muted)]">Super-Admin Console</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <div class="sidebar-section" style="font-size: 12px;">Super Admin Core</div>

            <a href="{{ route('super-admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('super-admin.dashboard') ? 'active font-bold' : '' }}">
                <span class="icon text-purple-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                Super Dashboard
            </a>

        </nav>

        {{-- User info --}}
        <div class="border-t border-[var(--border-default)] px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-purple-600 to-indigo-800 flex items-center justify-center text-white text-sm font-bold shadow-xs">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[var(--text-primary)] truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-purple-600 font-semibold flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500 animate-pulse"></span>
                        Super Admin
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-[var(--text-muted)] hover:text-red-400 transition-colors" title="Logout">
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
            <button @click="sidebarOpen = !sidebarOpen" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <div class="flex items-center gap-1.5">
                <span class="text-sm font-bold text-purple-700">JudgingSystem</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-purple-600 text-white uppercase">Super</span>
            </div>
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

<script>
    // Global Parameter-Based Navigation Helper
    window.UrlNav = {
        getParam(key) {
            return new URLSearchParams(window.location.search).get(key);
        },
        setParam(key, value, push = true) {
            if (!window.history || !window.history.pushState) return;
            const url = new URL(window.location.href);
            if (value === null || value === undefined || value === '') {
                url.searchParams.delete(key);
            } else {
                url.searchParams.set(key, value);
            }
            if (window.location.search !== url.search) {
                if (push) {
                    window.history.pushState({ [key]: value }, '', url.toString());
                } else {
                    window.history.replaceState({ [key]: value }, '', url.toString());
                }
            }
        },
        setParams(paramsObj, push = true) {
            if (!window.history || !window.history.pushState) return;
            const url = new URL(window.location.href);
            for (const [key, value] of Object.entries(paramsObj)) {
                if (value === null || value === undefined || value === '') {
                    url.searchParams.delete(key);
                } else {
                    url.searchParams.set(key, value);
                }
            }
            if (window.location.search !== url.search) {
                if (push) {
                    window.history.pushState(paramsObj, '', url.toString());
                } else {
                    window.history.replaceState(paramsObj, '', url.toString());
                }
            }
        },
        onPopState(callback) {
            window.addEventListener('popstate', (e) => {
                const params = new URLSearchParams(window.location.search);
                callback(params, e);
            });
        }
    };

    // Preserve sidebar navigation scroll position across page transitions
    document.addEventListener('DOMContentLoaded', function() {
        const nav = document.querySelector('.sidebar nav');
        if (nav) {
            const savedPos = sessionStorage.getItem('sidebar_scroll_pos');
            if (savedPos !== null) {
                nav.scrollTop = parseInt(savedPos, 10);
            }
            nav.addEventListener('scroll', function() {
                sessionStorage.setItem('sidebar_scroll_pos', nav.scrollTop);
            }, { passive: true });
        }
    });
</script>
@endsection
