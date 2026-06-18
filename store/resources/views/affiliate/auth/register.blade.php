@extends('layouts.affiliate')

@section('title', 'Daftar Affiliator')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
    <h1 class="text-2xl font-bold text-slate-900 mb-2">Daftar Affiliator</h1>
    <p class="text-slate-500 mb-6">Bergabung sebagai mitra affiliate MasFirmanPratama.</p>

    <form method="POST" action="{{ route('affiliate.register.store') }}">
        @csrf

        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('name')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('email')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Nomor Telepon</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required
                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('phone')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="type" class="block text-sm font-medium text-slate-700 mb-1">Tipe Affiliator</label>
            <select name="type" id="type" required
                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">— Pilih tipe —</option>
                <option value="alumni" @selected(old('type') === 'alumni')>Alumni</option>
                <option value="non_alumni" @selected(old('type') === 'non_alumni')>Non-Alumni</option>
                <option value="peserta" @selected(old('type') === 'peserta')>Peserta</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
            <input type="password" name="password" id="password" required
                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            @error('password')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                class="w-full rounded-lg border-slate-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-full shadow-sm transition">
            Daftar
        </button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-500">
        Sudah punya akun?
        <a href="{{ route('affiliate.login') }}" class="text-indigo-600 hover:text-indigo-700 font-medium">Masuk di sini</a>
    </p>
</div>
@endsection
