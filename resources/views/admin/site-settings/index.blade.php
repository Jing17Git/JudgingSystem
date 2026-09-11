@extends('layouts.admin')

@section('title', 'Site Branding & Settings')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b border-[var(--border-default)]">
        <div>
            <h1 class="text-2xl font-bold text-[var(--text-primary)] flex items-center gap-2">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Site Branding &amp; Settings
            </h1>
            <p class="text-sm text-[var(--text-muted)] mt-1">
                Customize the system name, logos, landing page text, hero images, and feature cards across the entire application.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" target="_blank" class="px-4 py-2 text-sm font-medium text-[var(--text-secondary)] bg-[var(--bg-card)] border border-[var(--border-default)] rounded-xl hover:bg-gray-100 transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Preview Landing Page
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-500/10 border border-red-500/30 rounded-xl text-red-600 text-sm space-y-1">
            <p class="font-bold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Please correct the errors below:
            </p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        {{-- 1. System Branding Section --}}
        <div class="bg-[var(--bg-card)] border border-[var(--border-default)] rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-[var(--border-default)]">
                <div class="w-10 h-10 rounded-xl bg-green-600/10 text-green-600 flex items-center justify-center font-bold">
                    1
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[var(--text-primary)]">System Identity &amp; Branding</h2>
                    <p class="text-xs text-[var(--text-muted)]">Control system title, tagline, and main header logo.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- System Name --}}
                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">
                        System Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']->value ?? 'CrownScore') }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm font-semibold">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Appears in header, title tags, sidebars, and footers.</p>
                </div>

                {{-- Tagline --}}
                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">
                        Tagline / Subtitle
                    </label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']->value ?? 'Pageant Judging System') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Appears under system name on landing page and logins.</p>
                </div>

                {{-- Logo Upload & Preview --}}
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 items-center p-4 bg-green-50/50 dark:bg-green-950/20 border border-green-100 dark:border-green-900/30 rounded-xl">
                    <div>
                        <span class="block text-sm font-semibold text-[var(--text-primary)] mb-1">System Logo</span>
                        <p class="text-xs text-[var(--text-muted)]">Upload PNG, JPG, SVG, or WEBP (Max 4MB).</p>
                    </div>
                    <div class="flex items-center gap-4">
                        @php
                            $currentLogo = $settings['site_logo']->value ?? 'images/logo.png';
                        @endphp
                        <img src="{{ asset($currentLogo) }}" alt="Current Logo" class="w-14 h-14 object-contain bg-white dark:bg-gray-800 p-2 rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs">
                        <div>
                            <span class="text-xs font-semibold text-[var(--text-primary)]">Current Logo</span>
                            <span class="block text-[11px] text-[var(--text-muted)] truncate max-w-[150px]">{{ $currentLogo }}</span>
                        </div>
                    </div>
                    <div>
                        <input type="file" name="site_logo" accept="image/*" class="text-xs text-[var(--text-secondary)] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 cursor-pointer">
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Landing Page Hero Section --}}
        <div class="bg-[var(--bg-card)] border border-[var(--border-default)] rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-[var(--border-default)]">
                <div class="w-10 h-10 rounded-xl bg-green-600/10 text-green-600 flex items-center justify-center font-bold">
                    2
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[var(--text-primary)]">Landing Page Hero Banner</h2>
                    <p class="text-xs text-[var(--text-muted)]">Manage the hero headline, badge, description, and hero stage image.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Hero Badge Text --}}
                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">
                        Hero Badge Text
                    </label>
                    <input type="text" name="hero_badge_text" value="{{ old('hero_badge_text', $settings['hero_badge_text']->value ?? 'Fair. Transparent. Real-Time.') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Small pill badge above main headline.</p>
                </div>

                {{-- Credits Text --}}
                <div>
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">
                        Credits / Author Text
                    </label>
                    <input type="text" name="hero_credits" value="{{ old('hero_credits', $settings['hero_credits']->value ?? 'Made by Team MISO.') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Credits displayed at the bottom of the hero block.</p>
                </div>

                {{-- Hero Headline --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">
                        Hero Main Headline
                    </label>
                    <textarea name="hero_headline" rows="2"
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm font-semibold">{{ old('hero_headline', $settings['hero_headline']->value ?? "Where Excellence\nMeets the Crown.") }}</textarea>
                    <p class="text-xs text-[var(--text-muted)] mt-1">Main title text (supports line breaks).</p>
                </div>

                {{-- Hero Description --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">
                        Hero Description
                    </label>
                    <textarea name="hero_description" rows="3"
                        class="w-full px-4 py-2.5 rounded-xl border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none transition-all text-sm">{{ old('hero_description', $settings['hero_description']->value ?? 'CrownScore is a web-based real-time pageant judging and tabulation system built for accuracy, fairness, and speed.') }}</textarea>
                    <p class="text-xs text-[var(--text-muted)] mt-1">Paragraph text directly under headline.</p>
                </div>

                {{-- Hero Image Upload & Preview --}}
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4 items-center p-4 bg-green-50/50 dark:bg-green-950/20 border border-green-100 dark:border-green-900/30 rounded-xl">
                    <div>
                        <span class="block text-sm font-semibold text-[var(--text-primary)] mb-1">Hero Stage Image</span>
                        <p class="text-xs text-[var(--text-muted)]">Upload high-res stage / hero graphic (Max 8MB).</p>
                    </div>
                    <div class="flex items-center gap-4">
                        @php
                            $currentHeroImg = $settings['hero_image']->value ?? 'images/pageant_stage_hero.png';
                        @endphp
                        <img src="{{ asset($currentHeroImg) }}" alt="Current Hero Image" class="h-20 max-w-[150px] object-cover rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs">
                        <div>
                            <span class="text-xs font-semibold text-[var(--text-primary)]">Current Image</span>
                            <span class="block text-[11px] text-[var(--text-muted)] truncate max-w-[140px]">{{ $currentHeroImg }}</span>
                        </div>
                    </div>
                    <div>
                        <input type="file" name="hero_image" accept="image/*" class="text-xs text-[var(--text-secondary)] file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700 cursor-pointer">
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Landing Page Mini Feature Cards --}}
        <div class="bg-[var(--bg-card)] border border-[var(--border-default)] rounded-2xl p-6 shadow-sm">
            <div class="flex items-center gap-3 pb-4 mb-6 border-b border-[var(--border-default)]">
                <div class="w-10 h-10 rounded-xl bg-green-600/10 text-green-600 flex items-center justify-center font-bold">
                    3
                </div>
                <div>
                    <h2 class="text-lg font-bold text-[var(--text-primary)]">Landing Page Mini Feature Cards</h2>
                    <p class="text-xs text-[var(--text-muted)]">Edit the 3 feature highlight cards at the bottom of the landing page.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Feature 1 --}}
                <div class="p-4 border border-[var(--border-default)] rounded-xl space-y-3 bg-gray-50/50 dark:bg-gray-900/30">
                    <div class="flex items-center gap-2 font-bold text-sm text-green-600">
                        <span>⚡</span> Feature Card 1
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1">Title</label>
                        <input type="text" name="feature_1_title" value="{{ old('feature_1_title', $settings['feature_1_title']->value ?? 'Real-Time Scoring') }}"
                            class="w-full px-3 py-2 rounded-lg border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] text-xs font-semibold focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1">Description</label>
                        <textarea name="feature_1_text" rows="2"
                            class="w-full px-3 py-2 rounded-lg border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] text-xs focus:ring-2 focus:ring-green-500 outline-none">{{ old('feature_1_text', $settings['feature_1_text']->value ?? 'Instant, accurate results as it happens.') }}</textarea>
                    </div>
                </div>

                {{-- Feature 2 --}}
                <div class="p-4 border border-[var(--border-default)] rounded-xl space-y-3 bg-gray-50/50 dark:bg-gray-900/30">
                    <div class="flex items-center gap-2 font-bold text-sm text-green-600">
                        <span>🛡️</span> Feature Card 2
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1">Title</label>
                        <input type="text" name="feature_2_title" value="{{ old('feature_2_title', $settings['feature_2_title']->value ?? 'Secure & Reliable') }}"
                            class="w-full px-3 py-2 rounded-lg border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] text-xs font-semibold focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1">Description</label>
                        <textarea name="feature_2_text" rows="2"
                            class="w-full px-3 py-2 rounded-lg border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] text-xs focus:ring-2 focus:ring-green-500 outline-none">{{ old('feature_2_text', $settings['feature_2_text']->value ?? 'Your data is safe with enterprise-grade security.') }}</textarea>
                    </div>
                </div>

                {{-- Feature 3 --}}
                <div class="p-4 border border-[var(--border-default)] rounded-xl space-y-3 bg-gray-50/50 dark:bg-gray-900/30">
                    <div class="flex items-center gap-2 font-bold text-sm text-green-600">
                        <span>✅</span> Feature Card 3
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1">Title</label>
                        <input type="text" name="feature_3_title" value="{{ old('feature_3_title', $settings['feature_3_title']->value ?? 'Transparent Results') }}"
                            class="w-full px-3 py-2 rounded-lg border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] text-xs font-semibold focus:ring-2 focus:ring-green-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-primary)] mb-1">Description</label>
                        <textarea name="feature_3_text" rows="2"
                            class="w-full px-3 py-2 rounded-lg border border-[var(--border-default)] bg-[var(--bg-input)] text-[var(--text-primary)] text-xs focus:ring-2 focus:ring-green-500 outline-none">{{ old('feature_3_text', $settings['feature_3_text']->value ?? 'Clear, auditable, and built for fairness.') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- Save Button Bar --}}
        <div class="flex items-center justify-end gap-4 p-4 bg-[var(--bg-card)] border border-[var(--border-default)] rounded-2xl shadow-lg sticky bottom-4 z-20">
            <span class="text-xs text-[var(--text-muted)]">Changes apply immediately across all layouts and landing page.</span>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold text-sm rounded-xl shadow-md hover:shadow-green-500/25 transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Save All Site Settings
            </button>
        </div>
    </form>
</div>
@endsection
