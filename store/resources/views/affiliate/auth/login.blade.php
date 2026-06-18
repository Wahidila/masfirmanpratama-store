@extends('layouts.affiliate')

@section('title', 'Login Affiliator')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Login Affiliator</h1>
    <p class="text-slate-500 mb-6">Masuk ke dashboard affiliate Anda.</p>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-teal-50 text-teal-700 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('affiliate.login.attempt') }}">
        @csrf

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('password')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-full shadow-sm transition">
            Masuk
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Belum punya akun?
        <a href="{{ route('affiliate.register') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Daftar sekarang</a>
    </p>
</div>
@endsection
