@props([])

@php
    $admin = auth('admin')->user();
@endphp

<header {{ $attributes->class(['hidden lg:flex h-[70px] items-center justify-between border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 px-6']) }}>
    <div class="flex items-center gap-3">
        {{-- Slot kiri: breadcrumb / page meta --}}
        {{ $start ?? '' }}
    </div>

    <div class="flex items-center gap-3">
        {{-- Dark mode toggle --}}
        <button
            type="button"
            @click="$store.adminTheme.toggle()"
            aria-label="Toggle dark mode"
            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
            {{-- Sun icon (visible in dark mode) --}}
            <x-admin.icon name="sun" class="h-5 w-5 hidden dark:block" />
            {{-- Moon icon (visible in light mode) --}}
            <x-admin.icon name="moon" class="h-5 w-5 block dark:hidden" />
        </button>

        {{-- User dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button
                type="button"
                @click="open = !open"
                class="flex items-center gap-2 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-brand-500 to-secondary-500 text-white text-xs font-semibold">
                    {{ strtoupper(substr($admin->name ?? '?', 0, 1)) }}
                </span>
                <span class="max-w-[140px] truncate">{{ $admin->name ?? 'Admin' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <div
                x-show="open"
                x-transition.opacity
                @click.outside="open = false"
                x-cloak
                class="absolute right-0 mt-2 w-56 origin-top-right rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-1.5 shadow-theme-lg z-30">
                <div class="px-3 py-2">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $admin->name ?? '' }}</p>
                    <p class="text-theme-xs text-gray-500 dark:text-gray-400 truncate">{{ $admin->email ?? '' }}</p>
                </div>
                <div class="my-1 h-px bg-gray-200 dark:bg-gray-700"></div>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition">
                        <x-admin.icon name="logout" class="h-4 w-4 shrink-0" />
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
