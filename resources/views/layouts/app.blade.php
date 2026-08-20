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

    <!-- Heroicons (via CDN for quick icon usage) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="antialiased">
    @yield('body')

    <script>
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
