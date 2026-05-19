@props([])

@php
    $admin = auth('admin')->user();
@endphp

<header {{ $attributes->class(['hidden lg:flex h-16 items-center justify-between border-b border-slate-200 bg-white px-6']) }}>
    <div class="flex items-center gap-3">
        {{-- Slot kiri: breadcrumb / page meta --}}
        {{ $start ?? '' }}
    </div>

    <div x-data="{ open: false }" class="relative">
        <button
            type="button"
            @click="open = !open"
            class="flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-primary-500 to-secondary-500 text-white text-xs">
                {{ strtoupper(substr($admin->name ?? '?', 0, 1)) }}
            </span>
            <span class="max-w-[140px] truncate">{{ $admin->name ?? 'Admin' }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
        </button>

        <div
            x-show="open"
            x-transition.opacity
            @click.outside="open = false"
            x-cloak
            class="absolute right-0 mt-2 w-56 origin-top-right rounded-2xl border border-slate-100 bg-white p-1.5 shadow-lg shadow-slate-200/60 z-30">
            <div class="px-3 py-2">
                <p class="text-sm font-medium text-slate-900 truncate">{{ $admin->name ?? '' }}</p>
                <p class="text-xs text-slate-500 truncate">{{ $admin->email ?? '' }}</p>
            </div>
            <div class="my-1 h-px bg-slate-100"></div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-slate-700 hover:bg-slate-100 transition">
                    <x-admin.icon name="logout" class="h-4 w-4 shrink-0" />
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>
