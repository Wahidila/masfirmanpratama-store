<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Affiliate Program') — MasFirmanPratama</title>
    <meta name="description" content="@yield('description', 'Program affiliate MasFirmanPratama — dapatkan komisi dengan mempromosikan kelas & buku Mind Power.')">

    {{-- Theme color --}}
    <meta name="theme-color" content="#4f46e5">

    {{-- Fonts --}}
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

    {{-- Icons: Lucide pinned (fix t_5e6b03f1) --}}
    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>

    {{-- Vite assets (Tailwind + Alpine) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('head')
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-700 @yield('body-class')">
    {{-- Skip to content (a11y) --}}
    <a
        href="#main"
        class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-md"
    >
        Lewati ke konten utama
    </a>

    {{-- Navbar --}}
    <nav
        x-data="{ open: false, scrolled: false }"
        x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 8)"
        :class="scrolled ? 'shadow-md' : ''"
        class="fixed inset-x-0 top-0 z-50 bg-white/85 backdrop-blur-xl border-b border-slate-100 transition-shadow duration-300"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                {{-- Logo --}}
                <a href="{{ route('affiliate.landing') }}" class="flex items-center gap-2">
                    <span class="w-9 h-9 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </span>
                    <span class="font-bold text-lg sm:text-xl tracking-tight text-slate-900">
                        Firman<span class="text-primary-600">Affiliate</span>
                    </span>
                </a>

                {{-- Desktop nav --}}
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('affiliate.landing') }}#benefit" class="text-slate-600 hover:text-primary-600 font-medium transition-colors text-sm">Benefit</a>
                    <a href="{{ route('affiliate.landing') }}#cara-kerja" class="text-slate-600 hover:text-primary-600 font-medium transition-colors text-sm">Cara Kerja</a>
                    <a href="{{ route('affiliate.landing') }}#faq" class="text-slate-600 hover:text-primary-600 font-medium transition-colors text-sm">FAQ</a>

                    <span class="h-5 w-px bg-slate-200" aria-hidden="true"></span>

                    <a href="{{ route('affiliate.login') }}" class="text-slate-600 hover:text-primary-600 font-medium transition-colors text-sm">Masuk</a>
                    <a href="{{ route('affiliate.register') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-full hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all">
                        Daftar
                    </a>
                </div>

                {{-- Mobile toggle --}}
                <button
                    type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    aria-controls="affiliate-mobile-nav"
                    class="md:hidden inline-flex items-center justify-center w-11 h-11 rounded-lg text-slate-600 hover:text-primary-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 transition-colors"
                    aria-label="Buka menu navigasi"
                >
                    <i data-lucide="menu" class="w-6 h-6" x-show="!open"></i>
                    <i data-lucide="x" class="w-6 h-6" x-show="open" x-cloak></i>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div
            id="affiliate-mobile-nav"
            x-show="open"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden bg-white border-t border-slate-100 shadow-xl origin-top"
            @click.outside="open = false"
            @keydown.escape.window="open = false"
        >
            <div class="px-4 pt-3 pb-6 space-y-1">
                <a href="{{ route('affiliate.landing') }}#benefit" @click="open = false" class="flex items-center min-h-[44px] px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Benefit</a>
                <a href="{{ route('affiliate.landing') }}#cara-kerja" @click="open = false" class="flex items-center min-h-[44px] px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Cara Kerja</a>
                <a href="{{ route('affiliate.landing') }}#faq" @click="open = false" class="flex items-center min-h-[44px] px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">FAQ</a>

                <div class="border-t border-slate-100 mt-3 pt-3 space-y-2">
                    <a href="{{ route('affiliate.login') }}" class="flex items-center justify-center min-h-[44px] px-4 py-3 rounded-lg text-base font-medium text-slate-700 hover:bg-slate-100 transition-colors">Masuk</a>
                    <a href="{{ route('affiliate.register') }}" class="flex items-center justify-center min-h-[44px] px-4 py-3 rounded-full bg-primary-600 text-white font-semibold hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Main content --}}
    <main id="main" class="pt-16 sm:pt-20">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-slate-950 text-slate-300 pt-12 pb-8 border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start gap-8 mb-8">
                {{-- Brand --}}
                <div class="max-w-xs">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white">
                            <i data-lucide="users" class="w-4 h-4"></i>
                        </span>
                        <span class="font-bold text-lg text-white">
                            Firman<span class="text-primary-500">Affiliate</span>
                        </span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">Program affiliate resmi MasFirmanPratama. Promosikan kelas & buku Mind Power, dapatkan komisi menarik.</p>
                </div>

                {{-- Links --}}
                <div class="flex gap-12 text-sm">
                    <div>
                        <h4 class="text-white font-semibold mb-3">Program</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('affiliate.landing') }}#benefit" class="hover:text-primary-400 transition-colors">Benefit</a></li>
                            <li><a href="{{ route('affiliate.landing') }}#cara-kerja" class="hover:text-primary-400 transition-colors">Cara Kerja</a></li>
                            <li><a href="{{ route('affiliate.landing') }}#tipe-affiliator" class="hover:text-primary-400 transition-colors">Tipe Affiliator</a></li>
                            <li><a href="{{ route('affiliate.landing') }}#faq" class="hover:text-primary-400 transition-colors">FAQ</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-white font-semibold mb-3">Akun</h4>
                        <ul class="space-y-2">
                            <li><a href="{{ route('affiliate.register') }}" class="hover:text-primary-400 transition-colors">Daftar</a></li>
                            <li><a href="{{ route('affiliate.login') }}" class="hover:text-primary-400 transition-colors">Masuk</a></li>
                            <li><a href="{{ url('/') }}" class="hover:text-primary-400 transition-colors">Kembali ke Store</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-800 pt-6 text-center text-sm text-slate-500">
                <p>&copy; {{ now()->year }} MasFirmanPratama. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- Lucide init --}}
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

    @yield('scripts')
</body>
</html>
