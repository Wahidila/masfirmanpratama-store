@props([
    'active' => null,
])

@php
    $admin = auth('admin')->user();
@endphp

<aside {{ $attributes->class(['hidden lg:flex lg:w-[290px] flex-col border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900']) }}>
    {{-- Logo / brand header --}}
    <div class="flex h-[70px] items-center gap-2.5 px-6 border-b border-gray-200 dark:border-gray-800">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 font-semibold tracking-tight text-gray-900 dark:text-white">
            <x-admin.logo />
            <span class="text-lg">Admin Panel</span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-1 text-theme-sm" data-admin-nav="desktop">
        <span class="block px-3 pb-2 text-theme-xs font-medium uppercase tracking-wider text-gray-400 dark:text-gray-500">Menu</span>
        @include('components.admin._nav-links', ['active' => $active])
    </nav>

    {{-- User footer --}}
    <div class="border-t border-gray-200 dark:border-gray-800 p-4">
        <div class="flex items-center gap-3 mb-3">
            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 dark:bg-brand-500/10 text-brand-600 dark:text-brand-400 text-sm font-semibold">
                {{ strtoupper(substr($admin->name ?? '?', 0, 1)) }}
            </span>
            <div class="min-w-0 flex-1">
                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $admin->name ?? 'Unknown' }}</div>
                <div class="text-theme-xs text-gray-500 dark:text-gray-400 truncate">{{ $admin->email ?? '' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-gray-200 dark:border-gray-700 px-3 py-2 text-theme-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                <x-admin.icon name="logout" class="h-4 w-4" />
                Logout
            </button>
        </form>
    </div>
</aside>
