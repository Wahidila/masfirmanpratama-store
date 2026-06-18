@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Profil Saya</h1>
    <p class="text-slate-500 mt-1">Kelola informasi akun dan data bank Anda</p>
</div>

<div class="max-w-2xl space-y-6">
    {{-- Personal Info --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Informasi Personal</h3>
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $affiliator->name) }}" required
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" value="{{ $affiliator->email }}" disabled
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-500">
                </div>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">No. WhatsApp</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $affiliator->phone) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tipe Affiliator</label>
                    <input type="text" value="{{ $affiliator->type->name }}" disabled
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 text-slate-500">
                </div>
            </div>
            <div>
                <label for="bio" class="block text-sm font-medium text-slate-700 mb-1">Bio</label>
                <textarea id="bio" name="bio" rows="3"
                          class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none resize-none">{{ old('bio', $affiliator->bio) }}</textarea>
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password Baru <span class="text-slate-400">(opsional)</span></label>
                    <input type="password" id="password" name="password"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">Simpan Profil</button>
        </form>
    </div>

    {{-- Bank Info --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Informasi Bank / E-Wallet</h3>
        <form method="POST" action="{{ route('profile.bank') }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="bank_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Bank / E-Wallet</label>
                <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $affiliator->bank_name) }}" placeholder="BCA, Mandiri, Dana, dll"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label for="bank_account_number" class="block text-sm font-medium text-slate-700 mb-1">Nomor Rekening</label>
                    <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number', $affiliator->bank_account_number) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
                <div>
                    <label for="bank_account_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Pemilik</label>
                    <input type="text" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name', $affiliator->bank_account_name) }}"
                           class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                </div>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">Simpan Data Bank</button>
        </form>
    </div>
</div>
@endsection
