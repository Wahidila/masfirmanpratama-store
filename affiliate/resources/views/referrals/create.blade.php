@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Buat Link Referral</h1>
    <p class="text-slate-500 mt-1">Buat link baru untuk mempromosikan produk</p>
</div>

<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <form method="POST" action="{{ route('referrals.store') }}" class="space-y-4">
            @csrf
            <div>
                <label for="label" class="block text-sm font-medium text-slate-700 mb-1">Label <span class="text-slate-400">(opsional)</span></label>
                <input type="text" id="label" name="label" value="{{ old('label') }}" placeholder="Contoh: Instagram Bio, WhatsApp Group"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                <p class="text-xs text-slate-400 mt-1">Untuk membantu Anda membedakan tiap link</p>
            </div>
            <div>
                <label for="target_url" class="block text-sm font-medium text-slate-700 mb-1">Target URL <span class="text-slate-400">(opsional)</span></label>
                <input type="url" id="target_url" name="target_url" value="{{ old('target_url') }}" placeholder="https://masfirmanpratama.com/produk/..."
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
                <p class="text-xs text-slate-400 mt-1">Kosongkan untuk redirect ke halaman utama store</p>
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">
                    Buat Link
                </button>
                <a href="{{ route('referrals.index') }}" class="px-6 py-2.5 text-slate-600 font-medium rounded-xl hover:bg-slate-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
