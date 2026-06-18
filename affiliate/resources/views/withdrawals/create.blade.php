@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Tarik Saldo</h1>
    <p class="text-slate-500 mt-1">Saldo tersedia: <span class="font-semibold text-secondary-600">Rp {{ number_format($availableBalance, 0, ',', '.') }}</span></p>
</div>

<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        @if($availableBalance <= 0)
            <div class="text-center py-8">
                <i data-lucide="wallet" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-slate-500">Saldo Anda belum tersedia untuk ditarik.</p>
                <a href="{{ route('commissions.index') }}" class="text-sm text-primary-600 hover:text-primary-700 mt-2 inline-block">Lihat status komisi</a>
            </div>
        @else
        <form method="POST" action="{{ route('withdrawals.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="withdrawal_method_id" class="block text-sm font-medium text-slate-700 mb-1">Metode Penarikan</label>
                <select id="withdrawal_method_id" name="withdrawal_method_id" required
                        class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
                    <option value="">Pilih metode...</option>
                    @foreach($methods as $method)
                        <option value="{{ $method->id }}" {{ old('withdrawal_method_id') == $method->id ? 'selected' : '' }}>
                            {{ $method->name }} ({{ $method->type === 'bank_transfer' ? 'Bank' : 'E-Wallet' }}) — Min Rp {{ number_format($method->min_withdrawal, 0, ',', '.') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="amount" class="block text-sm font-medium text-slate-700 mb-1">Jumlah Penarikan</label>
                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" required min="1" max="{{ $availableBalance }}"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <div>
                <label for="account_number" class="block text-sm font-medium text-slate-700 mb-1">Nomor Rekening / No. HP</label>
                <input type="text" id="account_number" name="account_number" value="{{ old('account_number', auth()->user()->bank_account_number) }}" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <div>
                <label for="account_name" class="block text-sm font-medium text-slate-700 mb-1">Nama Pemilik Rekening</label>
                <input type="text" id="account_name" name="account_name" value="{{ old('account_name', auth()->user()->bank_account_name) }}" required
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none">
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">Ajukan Penarikan</button>
                <a href="{{ route('withdrawals.index') }}" class="px-6 py-2.5 text-slate-600 font-medium rounded-xl hover:bg-slate-50 transition">Batal</a>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
