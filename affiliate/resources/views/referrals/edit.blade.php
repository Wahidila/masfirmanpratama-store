@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Edit Link Referral</h1>
    <p class="text-slate-500 mt-1">Kode: <code class="bg-slate-100 px-2 py-0.5 rounded text-xs font-mono">{{ $referral->code }}</code></p>
</div>

<div class="max-w-lg">
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <form method="POST" action="{{ route('referrals.update', $referral) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label for="label" class="block text-sm font-medium text-slate-700 mb-1">Label</label>
                <input type="text" id="label" name="label" value="{{ old('label', $referral->label) }}"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
            </div>
            <div>
                <label for="target_url" class="block text-sm font-medium text-slate-700 mb-1">Target URL</label>
                <input type="url" id="target_url" name="target_url" value="{{ old('target_url', $referral->target_url) }}"
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none transition">
            </div>
            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 text-white font-medium rounded-xl hover:bg-primary-700 transition">Simpan</button>
                <a href="{{ route('referrals.index') }}" class="px-6 py-2.5 text-slate-600 font-medium rounded-xl hover:bg-slate-50 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
