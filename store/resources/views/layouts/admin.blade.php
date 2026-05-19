@php
    $title ??= 'Admin · MasFirmanPratama.com';
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
</head>
<body class="min-h-full bg-slate-50 antialiased text-slate-800">
    <div class="min-h-screen flex">

        {{-- Sidebar --}}
        <aside class="hidden lg:flex lg:w-64 flex-col border-r border-slate-200 bg-white">
            <div class="flex h-16 items-center px-6 border-b border-slate-100">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-semibold tracking-tight text-slate-900">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-secondary-500 text-white text-sm">F</span>
                    Admin Panel
                </a>
            </div>
            <nav class="flex-1 px-3 py-6 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 text-primary-700' : 'text-slate-700 hover:bg-slate-100' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                    Dashboard
                </a>
                <span class="block px-3 py-2 text-xs font-medium text-slate-400 uppercase">Coming soon (M2 sprint)</span>
                <span class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-400 cursor-not-allowed">Produk</span>
                <span class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-400 cursor-not-allowed">Pesanan</span>
                <span class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-400 cursor-not-allowed">Verifikasi Bayar</span>
                <span class="flex items-center gap-2 rounded-xl px-3 py-2 text-slate-400 cursor-not-allowed">Settings</span>
            </nav>
            <div class="border-t border-slate-100 p-4">
                <div class="text-xs text-slate-500">Login sebagai</div>
                <div class="mt-1 text-sm font-medium text-slate-900 truncate">{{ $admin->name ?? 'Unknown' }}</div>
                <div class="text-xs text-slate-500 truncate">{{ $admin->email ?? '' }}</div>
                <form method="POST" action="{{ route('admin.logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="inline-flex w-full items-center justify-center gap-1.5 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-100 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="lg:hidden flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-semibold text-slate-900">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-br from-primary-500 to-secondary-500 text-white text-sm">F</span>
                    Admin
                </a>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-slate-700">Logout</button>
                </form>
            </header>

            <main class="flex-1 px-4 py-8 sm:px-8 lg:px-10">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
