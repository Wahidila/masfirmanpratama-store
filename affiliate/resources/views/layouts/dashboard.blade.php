@extends('layouts.app')

@section('body')
<div x-data="{ sidebarOpen: false }" class="min-h-full">
    {{-- Mobile sidebar overlay --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

        <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
             class="relative flex w-72 max-w-xs flex-col bg-white shadow-xl h-full">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <span class="text-lg font-bold text-gradient">Affiliate</span>
                <button @click="sidebarOpen = false" class="text-slate-400 hover:text-slate-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            @include('components.sidebar-nav')
        </div>
    </div>

    {{-- Desktop sidebar --}}
    <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
        <div class="flex flex-col flex-grow bg-white border-r border-slate-200 overflow-y-auto">
            <div class="flex items-center px-6 py-5 border-b border-slate-100">
                <span class="text-xl font-bold text-gradient">Affiliate</span>
            </div>
            @include('components.sidebar-nav')
        </div>
    </div>

    {{-- Main content --}}
    <div class="lg:pl-64 flex flex-col min-h-screen">
        {{-- Top bar --}}
        <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8 bg-white/80 backdrop-blur-md border-b border-slate-100">
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>

            <div class="flex items-center gap-4 ml-auto">
                <a href="{{ route('notifications.index') }}" class="relative text-slate-500 hover:text-primary-600">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                </a>
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-slate-700">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center text-primary-700 font-semibold text-xs">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <span class="hidden sm:block">{{ auth()->user()->name }}</span>
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </button>
                    <div x-show="open" @click.away="open = false" x-cloak
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50">
                        <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Profil Saya</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-rose-600 hover:bg-rose-50">Keluar</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-secondary-50 border border-secondary-200 rounded-xl text-secondary-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>
@endsection
