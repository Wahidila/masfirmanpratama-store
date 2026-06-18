@extends('layouts.app')

@section('body')
<div x-data="{ sidebarOpen: false }" class="min-h-full">
    {{-- Mobile sidebar --}}
    <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-40 lg:hidden">
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
        <div x-show="sidebarOpen" x-transition class="relative flex w-72 max-w-xs flex-col bg-slate-900 h-full">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800">
                <span class="text-lg font-bold text-white">Admin Panel</span>
                <button @click="sidebarOpen = false" class="text-slate-400"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            @include('admin.components.sidebar')
        </div>
    </div>

    {{-- Desktop sidebar --}}
    <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-60 lg:flex-col">
        <div class="flex flex-col flex-grow bg-slate-900 overflow-y-auto">
            <div class="flex items-center px-6 py-5 border-b border-slate-800">
                <span class="text-lg font-bold text-white">Admin <span class="text-primary-400">Affiliate</span></span>
            </div>
            @include('admin.components.sidebar')
        </div>
    </div>

    {{-- Main --}}
    <div class="lg:pl-60 flex flex-col min-h-screen">
        <header class="sticky top-0 z-30 flex items-center justify-between h-14 px-4 sm:px-6 bg-white border-b border-slate-100">
            <button @click="sidebarOpen = true" class="lg:hidden text-slate-500"><i data-lucide="menu" class="w-6 h-6"></i></button>
            <div class="ml-auto flex items-center gap-3">
                <span class="text-sm text-slate-500">{{ session('admin_email') }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-rose-600 hover:text-rose-700">Keluar</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            @if(session('success'))
                <div class="mb-4 p-3 bg-secondary-50 border border-secondary-200 rounded-xl text-secondary-800 text-sm">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
                    @foreach($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
@endsection
