@php
    $affiliator = auth('affiliator')->user();
    $currentRoute = request()->route()?->getName() ?? '';
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — FirmanAffiliate</title>

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

    {{-- Icons: Lucide pinned --}}
    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>

    {{-- Vite assets (Tailwind + Alpine) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- x-cloak --}}
    <style>[x-cloak] { display: none !important; }</style>

    @yield('head')
</head>
<body class="h-full font-sans antialiased bg-slate-50 text-slate-700">
    {{-- Skip to content (a11y) --}}
    <a
        href="#main-content"
        class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:px-4 focus:py-2 focus:bg-primary-600 focus:text-white focus:rounded-md"
    >
        Lewati ke konten utama
    </a>

    <div x-data="{ sidebarOpen: false }" class="min-h-full lg:flex">
        {{-- Mobile backdrop --}}
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-slate-900/50 lg:hidden"
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:z-auto"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            @keydown.escape.window="sidebarOpen = false"
        >
            <div class="flex flex-col h-full">
                {{-- Brand --}}
                <div class="flex items-center gap-2 px-5 h-16 border-b border-slate-100">
                    <span class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </span>
                    <span class="font-bold text-lg tracking-tight text-slate-900">
                        Firman<span class="text-primary-600">Affiliate</span>
                    </span>
                </div>

                {{-- Navigation --}}
                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1" aria-label="Sidebar navigasi">
                    @php
                        $navItems = [
                            ['route' => 'affiliate.dashboard', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
                            ['route' => 'affiliate.referral-links.index', 'icon' => 'link', 'label' => 'Link Referral'],
                            ['route' => 'affiliate.commissions.index', 'icon' => 'coins', 'label' => 'Komisi'],
                            ['route' => 'affiliate.withdrawals.index', 'icon' => 'wallet', 'label' => 'Penarikan'],
                            ['route' => 'affiliate.materials.index', 'icon' => 'file-text', 'label' => 'Materi'],
                        ];

                        // Event/Leaderboard hanya untuk alumni + peserta
                        $showEvents = in_array($affiliator->type ?? '', ['alumni', 'peserta']);
                    @endphp

                    @foreach ($navItems as $nav)
                        <a
                            href="{{ route($nav['route']) }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ str_starts_with($currentRoute, $nav['route']) ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                            @if(str_starts_with($currentRoute, $nav['route'])) aria-current="page" @endif
                        >
                            <i data-lucide="{{ $nav['icon'] }}" class="w-5 h-5 shrink-0"></i>
                            <span>{{ $nav['label'] }}</span>
                        </a>
                    @endforeach

                    @if ($showEvents)
                        <a
                            href="{{ route('affiliate.events.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ str_starts_with($currentRoute, 'affiliate.events') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                            @if(str_starts_with($currentRoute, 'affiliate.events')) aria-current="page" @endif
                        >
                            <i data-lucide="trophy" class="w-5 h-5 shrink-0"></i>
                            <span>Event & Leaderboard</span>
                        </a>
                    @endif

                    <div class="border-t border-slate-100 my-3"></div>

                    <a
                        href="{{ route('affiliate.profile.edit') }}"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ str_starts_with($currentRoute, 'affiliate.profile') ? 'bg-primary-50 text-primary-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        @if(str_starts_with($currentRoute, 'affiliate.profile')) aria-current="page" @endif
                    >
                        <i data-lucide="user-cog" class="w-5 h-5 shrink-0"></i>
                        <span>Profil</span>
                    </a>
                </nav>

                {{-- User footer --}}
                <div class="border-t border-slate-100 px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-sm font-bold shrink-0">
                            {{ strtoupper(substr($affiliator->name ?? 'A', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-900 truncate">{{ $affiliator->name ?? '-' }}</p>
                            <p class="text-xs text-slate-500 truncate capitalize">{{ str_replace('_', ' ', $affiliator->type ?? '-') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main area --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 sm:px-6 bg-white border-b border-slate-200">
                {{-- Mobile menu toggle --}}
                <button
                    type="button"
                    @click="sidebarOpen = true"
                    class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg text-slate-600 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    aria-label="Buka menu navigasi"
                >
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>

                {{-- Page title slot --}}
                <h1 class="text-lg font-semibold text-slate-900 hidden lg:block">@yield('page-title', 'Dashboard')</h1>

                {{-- Right side --}}
                <div class="flex items-center gap-3">
                    <span class="text-sm text-slate-600 hidden sm:block">{{ $affiliator->name ?? '-' }}</span>
                    <form method="POST" action="{{ route('affiliate.logout') }}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-slate-600 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                        >
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Flash messages --}}
            @if (session('success'))
                <div class="mx-4 sm:mx-6 mt-4">
                    <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-secondary-50 border border-secondary-200 text-secondary-800 text-sm">
                        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 sm:mx-6 mt-4">
                    <div class="flex items-center gap-2 px-4 py-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            {{-- Page content --}}
            <main id="main-content" class="flex-1 p-4 sm:p-6">
                @yield('content')
            </main>
        </div>
    </div>

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
