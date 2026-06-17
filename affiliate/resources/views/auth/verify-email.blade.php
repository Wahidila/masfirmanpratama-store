@extends('layouts.app')

@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 via-white to-secondary-50 px-4">
    <div class="w-full max-w-md text-center">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <div class="w-16 h-16 bg-accent-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="mail" class="w-8 h-8 text-accent-600"></i>
            </div>
            <h2 class="text-xl font-semibold text-slate-800 mb-2">Verifikasi Email</h2>
            <p class="text-slate-500 text-sm mb-6">
                Kami telah mengirim link verifikasi ke email Anda. Klik link tersebut untuk mengaktifkan akun.
            </p>

            @if(session('success'))
                <div class="mb-4 p-3 bg-secondary-50 border border-secondary-200 rounded-xl text-secondary-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit"
                        class="w-full py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-sm text-slate-500 hover:text-slate-700">Keluar</button>
            </form>
        </div>
    </div>
</div>
@endsection
