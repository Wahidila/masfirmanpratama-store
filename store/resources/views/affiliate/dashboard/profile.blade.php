@extends('layouts.affiliate-dashboard')

@section('title', 'Profil')
@section('page-title', 'Profil')

@section('content')
    <div class="max-w-xl">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
            <h2 class="text-lg font-semibold text-slate-900 mb-1">Informasi Profil</h2>
            <p class="text-sm text-slate-500 mb-6">Lengkapi data bank untuk proses penarikan komisi.</p>

            <form method="POST" action="{{ route('affiliate.profile.update') }}">
                @csrf
                @method('PUT')

                {{-- Phone --}}
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1">Nomor Telepon</label>
                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $affiliator->phone) }}"
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('phone') border-rose-300 @enderror"
                        placeholder="08xxxxxxxxxx"
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-slate-100 my-5"></div>
                <h3 class="text-sm font-semibold text-slate-800 mb-3">Informasi Bank</h3>

                {{-- Bank Name --}}
                <div class="mb-4">
                    <label for="bank_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Bank <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        id="bank_name"
                        name="bank_name"
                        value="{{ old('bank_name', $affiliator->bank_name) }}"
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bank_name') border-rose-300 @enderror"
                        placeholder="BCA, BNI, BRI, Mandiri..."
                        required
                    >
                    @error('bank_name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bank Account --}}
                <div class="mb-4">
                    <label for="bank_account" class="block text-sm font-medium text-slate-700 mb-1">Nomor Rekening <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        id="bank_account"
                        name="bank_account"
                        value="{{ old('bank_account', $affiliator->bank_account) }}"
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bank_account') border-rose-300 @enderror"
                        placeholder="1234567890"
                        required
                    >
                    @error('bank_account')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bank Holder --}}
                <div class="mb-6">
                    <label for="bank_holder" class="block text-sm font-medium text-slate-700 mb-1">Nama Pemilik Rekening <span class="text-rose-500">*</span></label>
                    <input
                        type="text"
                        id="bank_holder"
                        name="bank_holder"
                        value="{{ old('bank_holder', $affiliator->bank_holder) }}"
                        class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('bank_holder') border-rose-300 @enderror"
                        placeholder="Nama sesuai rekening"
                        required
                    >
                    @error('bank_holder')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-full hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all"
                >
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>
@endsection
