@props([
    'title' => 'Firman Pratama — Pakar Pikiran No.1 Indonesia',
    'description' => 'Mind Power & Life Mastery dengan metode Alpha Mind Control (AMC) — kelas, buku, dan mentoring untuk transformasi hidup yang nyata.',
    'ogImage' => null,
    'ogType' => 'website',
    'cartCount' => 0,
    'bodyClass' => '',
])

@php
    $canonical = url()->current();
    $ogImageUrl = $ogImage ? (str_starts_with($ogImage, 'http') ? $ogImage : asset($ogImage)) : asset('images/og-default.jpg');
    $cartCount = $cartCount ?: (int) session('cart_count', 0);
@endphp

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImageUrl }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="Firman Pratama">

    {{-- Twitter card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ $ogImageUrl }}">

    {{-- Theme color --}}
    <meta name="theme-color" content="#4f46e5">

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Fonts: preconnect + Inter loaded via async stylesheet (non-render-blocking) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        rel="preload"
        as="style"
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        onload="this.rel='stylesheet'"
    >
    <noscript>
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
    </noscript>

    {{-- Icons: Lucide (PIN VERSION — `@latest` di unpkg redirect ke legacy v1.16.0
         yang ngga punya brand icons facebook/youtube/instagram, bikin createIcons
         loop warn-cycle dan hang Lighthouse di route ber-Alpine berat. Fix t_5e6b03f1.) --}}
    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>

    {{-- Vite assets (Tailwind + Alpine) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Per-page head additions (extra meta, structured data, page CSS) --}}
    {{ $head ?? '' }}
</head>
<body
    x-data="{ pageLoading: false }"
    x-on:beforeunload.window="pageLoading = true"
    class="font-sans antialiased bg-slate-50 text-slate-700 {{ $bodyClass }}"
>
    {{-- Skip to content (a11y) --}}
    <a
        href="#main"
        class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-md"
    >
        Lewati ke konten utama
    </a>

    {{-- Page-transition loading bar --}}
    <div
        x-show="pageLoading"
        x-cloak
        x-transition.opacity
        class="fixed inset-x-0 top-0 z-[60] h-1 bg-gradient-to-r from-primary-500 via-secondary-500 to-accent-500 animate-pulse"
        aria-hidden="true"
    ></div>

    {{-- Navbar (component) --}}
    <x-navbar :cartCount="$cartCount" />

    {{-- Main content slot --}}
    <main id="main" class="pt-20 min-h-[60vh]">
        {{ $slot }}
    </main>

    {{-- Footer (component) --}}
    <x-footer />

    {{-- Per-page scripts (e.g. structured data, page-specific Alpine components) --}}
    {{ $scripts ?? '' }}

    {{-- Lucide init: render on initial load + after Alpine mounts.
         CATATAN PENTING (fix t_5e6b03f1): JANGAN listen `alpine:morphed`.
         createIcons() mutate `<i data-lucide>` jadi `<svg>` → mutation re-trigger
         alpine:morphed → loop infinite (35k+ console errors / 17ms saat icon
         missing, atau silent main-thread block saat icons tersedia).
         alpine:initialized + initial render udah cukup; tab-button per-tab
         re-render dihandle oleh `x-init` di-template. --}}
    <script>
        (function () {
            const renderIcons = () => window.lucide && window.lucide.createIcons();
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', renderIcons);
            } else {
                renderIcons();
            }
            document.addEventListener('alpine:initialized', renderIcons);
        })();
    </script>
</body>
</html>
