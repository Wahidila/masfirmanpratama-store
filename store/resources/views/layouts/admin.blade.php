@php
    $title ??= 'Admin · MasFirmanPratama.com';
    $active ??= null;
    $admin = auth('admin')->user();
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $title)</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Anti-flash: set .dark on <html> before paint (admin-scoped, key: admin-theme) --}}
    <script>
        (function() {
            var t = localStorage.getItem('admin-theme');
            if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <style>[x-cloak] { display: none !important; }</style>

    {{-- Alpine admin theme store (scoped to admin layout only) --}}
    <script>
        document.addEventListener('alpine:init', function() {
            Alpine.store('adminTheme', {
                mode: localStorage.getItem('admin-theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light'),
                get isDark() { return this.mode === 'dark'; },
                toggle() {
                    this.mode = this.mode === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('admin-theme', this.mode);
                    document.documentElement.classList.toggle('dark', this.mode === 'dark');
                },
                init() {
                    // Sync class on init
                    document.documentElement.classList.toggle('dark', this.mode === 'dark');
                }
            });
        });
    </script>
</head>
<body class="min-h-full bg-gray-50 dark:bg-gray-900 antialiased text-gray-800 dark:text-gray-300">
    <div class="min-h-screen flex">

        <x-admin.sidebar :active="$active" />

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <x-admin.navbar />

            {{-- Mobile / tablet header + drawer (replace navbar di < lg) --}}
            <div x-data="{ open: false }" class="lg:hidden">
                <header class="flex h-16 items-center justify-between border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-4">
                    <button type="button"
                        @click="open = true"
                        aria-label="Buka menu navigasi"
                        aria-controls="admin-mobile-drawer"
                        :aria-expanded="open ? 'true' : 'false'"
                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                        <x-admin.icon name="menu" class="h-5 w-5" />
                    </button>

                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-semibold text-gray-900 dark:text-white">
                        <x-admin.logo />
                        Admin
                    </a>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            <x-admin.icon name="logout" class="h-3.5 w-3.5" />
                            Logout
                        </button>
                    </form>
                </header>

                {{-- Drawer overlay --}}
                <div x-show="open"
                     x-cloak
                     x-transition.opacity
                     @keydown.escape.window="open = false"
                     id="admin-mobile-drawer"
                     role="dialog"
                     aria-modal="true"
                     aria-label="Menu navigasi admin"
                     class="fixed inset-0 z-40">

                    {{-- Backdrop --}}
                    <div @click="open = false"
                         class="absolute inset-0 bg-gray-900/60"
                         aria-hidden="true"></div>

                    {{-- Panel --}}
                    <aside x-show="open"
                           x-transition:enter="transition transform ease-out duration-200"
                           x-transition:enter-start="-translate-x-full"
                           x-transition:enter-end="translate-x-0"
                           x-transition:leave="transition transform ease-in duration-150"
                           x-transition:leave-start="translate-x-0"
                           x-transition:leave-end="-translate-x-full"
                           class="relative flex h-full w-[290px] max-w-[80vw] flex-col bg-white dark:bg-gray-900 shadow-xl">

                        <div class="flex h-16 items-center justify-between border-b border-gray-200 dark:border-gray-800 px-4">
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-semibold tracking-tight text-gray-900 dark:text-white">
                                <x-admin.logo />
                                Admin Panel
                            </a>
                            <button type="button"
                                @click="open = false"
                                aria-label="Tutup menu navigasi"
                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/5 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
                                <x-admin.icon name="x" class="h-4 w-4" />
                            </button>
                        </div>

                        <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-1 text-sm" data-admin-nav="mobile">
                            @include('components.admin._nav-links', [
                                'active' => $active,
                                'linkClickHandler' => '@click="open = false"',
                            ])
                        </nav>

                        <div class="border-t border-gray-200 dark:border-gray-800 p-4">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Login sebagai</div>
                            <div class="mt-1 text-sm font-medium text-gray-900 dark:text-white truncate">{{ $admin->name ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $admin->email ?? '' }}</div>
                        </div>
                    </aside>
                </div>
            </div>

            <main class="flex-1 px-4 py-8 sm:px-8 lg:px-10">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
