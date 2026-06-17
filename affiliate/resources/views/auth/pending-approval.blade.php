@extends('layouts.app')

@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 via-white to-secondary-50 px-4">
    <div class="w-full max-w-md text-center">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <div class="w-16 h-16 bg-accent-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="clock" class="w-8 h-8 text-accent-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-slate-800 mb-2">Menunggu Persetujuan</h2>
            <p class="text-slate-500 text-sm mb-6">
                Akun Anda sedang dalam proses review oleh admin. Anda akan menerima notifikasi ketika akun disetujui.
            </p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Keluar</button>
            </form>
        </div>
    </div>
</div>
@endsection
