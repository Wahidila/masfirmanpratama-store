@extends('layouts.app')

@section('body')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary-50 via-white to-secondary-50 px-4 py-12">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gradient">Affiliate Program</h1>
            <p class="text-slate-500 mt-2">Bergabung dan mulai hasilkan komisi</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
            <h2 class="text-xl font-semibold text-slate-800 mb-6">Daftar Affiliate</h2>

            @if($errors->any())
                <div class="mb-4 p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-700 text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">No. WhatsApp <span class="text-slate-400">(opsional)</span></label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="08xxx"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                </div>
                <div>
                    <label for="affiliator_type_id" class="block text-sm font-medium text-slate-700 mb-1">Tipe Affiliator</label>
                    <select id="affiliator_type_id" name="affiliator_type_id" required
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                        <option value="">Pilih tipe...</option>
                        @foreach($types as $type)
                            <option value="{{ $type->id }}" {{ old('affiliator_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }} — Komisi {{ $type->default_commission_rate }}%
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                </div>
                <button type="submit"
                        class="w-full py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition shadow-sm shadow-primary-500/20">
                    Daftar Sekarang
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-primary-600 font-medium hover:text-primary-700">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
