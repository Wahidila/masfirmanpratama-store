@extends('layouts.affiliate')

@section('title', 'Verifikasi Email')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center">
    <div class="mb-4">
        <svg class="mx-auto h-12 w-12 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-slate-900 mb-2">Verifikasi Email Anda</h1>
    <p class="text-slate-500 mb-6">
        Kami telah mengirimkan link verifikasi ke email Anda.
        Silakan cek inbox (dan folder spam) untuk menyelesaikan pendaftaran.
    </p>

    @if (session('status'))
        <div class="mb-4 p-3 rounded-lg bg-teal-50 text-teal-700 text-sm">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('affiliate.verification.resend') }}">
        @csrf
        <button type="submit"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-6 rounded-full shadow-sm transition">
            Kirim Ulang Email Verifikasi
        </button>
    </form>

    <form method="POST" action="{{ route('affiliate.logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="text-sm text-slate-500 hover:text-slate-700 underline">
            Logout
        </button>
    </form>
</div>
@endsection
