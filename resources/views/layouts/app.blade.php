<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Real-Time Pageant Judging and Tabulation System — Professional scoring, rankings, and live results.">

    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'JudgingSystem') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Alpine Plugins & Alpine JS -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="antialiased">
    @yield('body')

    {{-- Global Real-Time Toast Notification System --}}
    <div x-data="toastManager()" 
         @live-toast.window="add($event.detail)"
         class="fixed top-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm w-full pointer-events-none">
        <template x-for="t in toasts" :key="t.id">
            <div x-show="t.visible"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-[-12px] scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 translate-y-[-10px] scale-95"
                 class="pointer-events-auto flex items-start gap-3 p-3.5 rounded-xl shadow-xl border backdrop-blur-md transition-all"
                 :class="{
                    'bg-slate-900/95 text-white border-slate-700': t.type === 'info',
                    'bg-emerald-950/95 text-emerald-100 border-emerald-600': t.type === 'success',
                    'bg-rose-950/95 text-rose-100 border-rose-600': t.type === 'error',
                    'bg-amber-950/95 text-amber-100 border-amber-600': t.type === 'warning'
                 }">
                <div class="mt-0.5 flex-shrink-0">
                    <template x-if="t.type === 'success'">
                        <span class="flex h-5 w-5 rounded-full bg-emerald-500/20 text-emerald-400 items-center justify-center font-bold text-xs">✓</span>
                    </template>
                    <template x-if="t.type === 'info'">
                        <span class="flex h-5 w-5 rounded-full bg-blue-500/20 text-blue-400 items-center justify-center font-bold text-xs">⚡</span>
                    </template>
                    <template x-if="t.type === 'warning'">
                        <span class="flex h-5 w-5 rounded-full bg-amber-500/20 text-amber-400 items-center justify-center font-bold text-xs">↻</span>
                    </template>
                    <template x-if="t.type === 'error'">
                        <span class="flex h-5 w-5 rounded-full bg-rose-500/20 text-rose-400 items-center justify-center font-bold text-xs">✕</span>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold leading-tight" x-text="t.title"></p>
                    <p class="text-[11px] opacity-90 mt-0.5 leading-snug" x-text="t.message"></p>
                </div>
                <button @click="remove(t.id)" class="text-slate-400 hover:text-white text-xs opacity-60 hover:opacity-100 px-1 py-0.5">✕</button>
            </div>
        </template>
    </div>

    <script>
        // Global Toast Dispatcher
        window.showToast = function(title, message, type = 'success', timeout = 4500) {
            window.dispatchEvent(new CustomEvent('live-toast', {
                detail: { title, message, type, timeout }
            }));
        };

        function toastManager() {
            return {
                toasts: [],
                add(item) {
                    const id = Date.now() + Math.random();
                    const toast = {
                        id,
                        title: item.title || 'Notification',
                        message: item.message || '',
                        type: item.type || 'success',
                        visible: true
                    };
                    this.toasts.push(toast);

                    setTimeout(() => {
                        this.remove(id);
                    }, item.timeout || 4500);
                },
                remove(id) {
                    const t = this.toasts.find(x => x.id === id);
                    if (t) {
                        t.visible = false;
                        setTimeout(() => {
                            this.toasts = this.toasts.filter(x => x.id !== id);
                        }, 250);
                    }
                }
            };
        }

        // Global handler to auto-capitalize name inputs smoothly without quote collisions
        document.addEventListener('input', function(e) {
            if (e.target && (e.target.matches('input[name="name"], input[name="full_name"], input[name="first_name"], input[name="last_name"]') || e.target.classList.contains('auto-capitalize'))) {
                const input = e.target;
                const start = input.selectionStart;
                const end = input.selectionEnd;
                input.value = input.value.replace(/(^|[\s\-\.'])[a-z]/g, function(letter) {
                    return letter.toUpperCase();
                });
                input.setSelectionRange(start, end);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
