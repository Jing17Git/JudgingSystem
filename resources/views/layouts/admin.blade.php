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
            <img src="{{ asset('images/logo.png') }}" alt="CPSU Logo" class="w-10 h-10 object-contain drop-shadow-sm flex-shrink-0">
            <div>
                <h1 class="text-sm font-bold text-[var(--text-primary)] leading-tight">JudgingSystem</h1>
                <p class="text-xs text-[var(--text-muted)]">Admin Panel</p>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            <div class="sidebar-section" style="font-size: 12px;">Main</div>

            <a href="{{ route('admin.dashboard') }}"
               class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </span>
                Dashboard
            </a>

            <div class="sidebar-section" style="font-size: 12px;" >Management</div>

            <a href="{{ route('admin.admins.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.admins.*') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                Admin Accounts
            </a>

            {{-- Placeholder links for future modules --}}
            <a href="{{ route('admin.judges.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.judges.*') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </span>
                Judges
            </a>

            <a href="{{ route('admin.candidates.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.candidates.*') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </span>
                Candidates
            </a>

 <div class="sidebar-section" style="font-size: 12px;" >Categories</div>

  <div class="sidebar-section" style="font-size: 11px">  ->Pre-Judging </div>

            @if(isset($sidebarPrelimCategories) && $sidebarPrelimCategories->isNotEmpty())
                @foreach($sidebarPrelimCategories as $sidebarCat)
                    @php
                        $catKey = $sidebarCat->key;
                        // Map known category keys to their dedicated named routes
                        $knownRouteMap = [
                            'production'        => 'admin.production.index',
                            'fitness'           => 'admin.fitness.index',
                            'indigenous-attire' => 'admin.indigenous-attire.index',
                            'indigenous_attire' => 'admin.indigenous-attire.index',
                            'traditional-attire'=> 'admin.traditional-attire.index',
                            'traditional_attire'=> 'admin.traditional-attire.index',
                        ];
                        $knownRoutePatternMap = [
                            'production'        => 'admin.production.*',
                            'fitness'           => 'admin.fitness.*',
                            'indigenous-attire' => 'admin.indigenous-attire.*',
                            'indigenous_attire' => 'admin.indigenous-attire.*',
                            'traditional-attire'=> 'admin.traditional-attire.*',
                            'traditional_attire'=> 'admin.traditional-attire.*',
                        ];
                        // Icon SVG paths keyed by category key
                        $iconPaths = [
                            'production'        => 'M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5',
                            'fitness'           => 'M3.75 13.5l10.5-10.5m0 0L18 6.75M14.25 3l3.75 3.75M3 14.25l3.75 3.75m0 0l10.5-10.5M6.75 18L3 14.25',
                            'indigenous-attire' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z',
                            'indigenous_attire' => 'M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 21v-2.25m-6.364-.386l1.591-1.591M3 12h2.25m.386-6.364l1.591 1.591M12 18.75a6.75 6.75 0 100-13.5 6.75 6.75 0 000 13.5z',
                            'traditional-attire'=> 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z',
                            'traditional_attire'=> 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z',
                        ];
                        // Default icon for custom/unknown categories
                        $defaultIconPath = 'M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z';

                        $hasNamedRoute = isset($knownRouteMap[$catKey]);
                        $routeUrl = $hasNamedRoute
                            ? route($knownRouteMap[$catKey])
                            : route('admin.category.index', ['key' => $catKey]);
                        $isActive = $hasNamedRoute
                            ? request()->routeIs($knownRoutePatternMap[$catKey])
                            : (request()->routeIs('admin.category.index') && request()->route('key') === $catKey);
                        $iconPath = $iconPaths[$catKey] ?? $defaultIconPath;
                    @endphp
                    <a href="{{ $routeUrl }}"
                       class="sidebar-link {{ $isActive ? 'active' : '' }}">
                        <span class="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $iconPath }}"/>
                            </svg>
                        </span>
                        {{ $sidebarCat->name }}
                    </a>
                @endforeach
            @endif
             <div class="sidebar-section" style="font-size: 11px">  ->Final-Judging </div>

                         <a href="{{ route('admin.qa.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.qa.*') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09z"/>
                    </svg>
                </span>
                Q & A
            </a>


            <div class="sidebar-section" style="font-size: 12px;">Results</div>

            <a href="{{ route('admin.overall.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.overall.index') || (request()->routeIs('admin.overall.candidate-votes') && request('from', 'overall') !== 'final') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </span>
                Preliminary Overall
            </a>

            <a href="{{ route('admin.overall.final') }}"
               class="sidebar-link {{ request()->routeIs('admin.overall.final') || (request()->routeIs('admin.overall.candidate-votes') && request('from') === 'final') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/>
                    </svg>
                </span>
                Final Overall (Q & A)
            </a>

            <div class="sidebar-section" style="font-size: 12px;">System</div>

            <div x-data="{
                open: localStorage.getItem('sidebar_settings_open') !== null
                    ? localStorage.getItem('sidebar_settings_open') === 'true'
                    : {{ request()->routeIs('admin.settings.*') ? 'true' : 'true' }},
                toggle() {
                    this.open = !this.open;
                    localStorage.setItem('sidebar_settings_open', this.open);
                }
            }">
                <button type="button"
                        @click.prevent.stop="toggle()"
                        class="sidebar-link w-full text-left justify-between cursor-pointer {{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.cache.*') ? 'text-[var(--green-700)] bg-[var(--green-50)]' : '' }}">
                    <div class="flex items-center gap-3">
                        <span class="icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        Settings
                    </div>
                    <svg class="w-4 h-4 text-[var(--text-muted)] transition-transform duration-200"
                         :class="open ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown Submenu items --}}
                <div x-show="open"
                     x-collapse
                     class="pl-6 pr-1 pt-1 pb-2 space-y-1">
                    <a href="{{ route('admin.settings.categories') }}"
                       class="sidebar-link text-xs py-2 px-3 {{ request()->routeIs('admin.settings.categories*') ? 'active font-bold' : '' }}">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ request()->routeIs('admin.settings.categories*') ? 'bg-[var(--green-600)]' : 'bg-gray-400' }}"></span>
                        <span>Manage Categories</span>
                    </a>
                    <a href="{{ route('admin.settings.preliminary') }}"
                       class="sidebar-link text-xs py-2 px-3 {{ request()->routeIs('admin.settings.preliminary') || (request()->routeIs('admin.settings.index') && !request()->routeIs('admin.settings.final')) ? 'active font-bold' : '' }}">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ request()->routeIs('admin.settings.preliminary') || (request()->routeIs('admin.settings.index') && !request()->routeIs('admin.settings.final')) ? 'bg-[var(--green-600)]' : 'bg-gray-400' }}"></span>
                        <span>Preliminary</span>
                    </a>
                    <a href="{{ route('admin.settings.final') }}"
                       class="sidebar-link text-xs py-2 px-3 {{ request()->routeIs('admin.settings.final') ? 'active font-bold' : '' }}">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ request()->routeIs('admin.settings.final') ? 'bg-[var(--green-600)]' : 'bg-gray-400' }}"></span>
                        <span>Final</span>
                    </a>
                    <a href="{{ route('admin.settings.judge-scores') }}"
                       class="sidebar-link text-xs py-2 px-3 {{ request()->routeIs('admin.settings.judge-scores') ? 'active font-bold' : '' }}">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ request()->routeIs('admin.settings.judge-scores') ? 'bg-[var(--green-600)]' : 'bg-gray-400' }}"></span>
                        <span>Judge Score Sheets</span>
                    </a>
                    <a href="{{ route('admin.settings.audit_record') }}"
                       class="sidebar-link text-xs py-2 px-3 {{ request()->routeIs('admin.settings.audit_record') ? 'active font-bold' : '' }}">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ request()->routeIs('admin.settings.audit_record') ? 'bg-[var(--green-600)]' : 'bg-gray-400' }}"></span>
                        <span>Audit Record</span>
                    </a>

                    <a href="{{ route('admin.cache.index') }}"
                       class="sidebar-link text-xs py-2 px-3 {{ request()->routeIs('admin.cache.*') || request()->routeIs('admin.settings.cache') ? 'active font-bold' : '' }}">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ request()->routeIs('admin.cache.*') || request()->routeIs('admin.settings.cache') ? 'bg-[var(--green-600)]' : 'bg-gray-400' }}"></span>
                        <span>Cache Management</span>
                    </a>
                    <a href="{{ route('admin.logs.index') }}"
                       class="sidebar-link text-xs py-2 px-3 {{ request()->routeIs('admin.logs.*') || request()->routeIs('admin.settings.logs') ? 'active font-bold' : '' }}">
                        <span class="w-2 h-2 rounded-full flex-shrink-0 {{ request()->routeIs('admin.logs.*') || request()->routeIs('admin.settings.logs') ? 'bg-[var(--green-600)]' : 'bg-gray-400' }}"></span>
                        <span>Logs Management</span>
                    </a>
                </div>
            </div>
                        <div class="sidebar-section" style="font-size: 12px;">Account Settings</div>
                                    <a href="{{ route('admin.account.index') }}"
               class="sidebar-link {{ request()->routeIs('admin.account.index') ? 'active' : '' }}">
                <span class="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                    </svg>
                </span>
                Account Settings
            </a>
        </nav>

        {{-- User info --}}
        <div class="border-t border-[var(--border-default)] px-4 py-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-green-500 to-emerald-700 flex items-center justify-center text-white text-sm font-bold">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[var(--text-primary)] truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-[var(--text-muted)]">Administrator</p>
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
    <div class="main-content flex-1 min-w-0">
        {{-- Top bar for mobile --}}
        <div class="lg:hidden flex items-center justify-between mb-6">
            <button @click="sidebarOpen = !sidebarOpen" class="text-[var(--text-secondary)] hover:text-[var(--text-primary)] p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <span class="text-sm font-bold text-[var(--green-700)]">JudgingSystem</span>
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
