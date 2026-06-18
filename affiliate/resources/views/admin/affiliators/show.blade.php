@extends('admin.layouts.admin')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.affiliators.index') }}" class="text-sm text-primary-600">&larr; Kembali</a>
    <h1 class="text-xl font-bold text-slate-800 mt-2">{{ $affiliator->name }}</h1>
    <p class="text-slate-500 text-sm">{{ $affiliator->email }} &middot; {{ $affiliator->type->name }} &middot;
        <span class="font-medium {{ $affiliator->status === 'active' ? 'text-secondary-600' : ($affiliator->status === 'pending' ? 'text-accent-600' : 'text-rose-600') }}">{{ ucfirst($affiliator->status) }}</span>
    </p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Total Pendapatan</p>
        <p class="text-lg font-bold">Rp {{ number_format($stats['total_earnings'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Saldo Tersedia</p>
        <p class="text-lg font-bold">Rp {{ number_format($stats['available_balance'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Total Order</p>
        <p class="text-lg font-bold">{{ $stats['total_orders'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Total Klik</p>
        <p class="text-lg font-bold">{{ $stats['total_clicks'] }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6">
    <h3 class="font-semibold text-slate-800 mb-3">Informasi</h3>
    <div class="grid sm:grid-cols-2 gap-4 text-sm">
        <div><span class="text-slate-500">Telepon:</span> {{ $affiliator->phone ?: '-' }}</div>
        <div><span class="text-slate-500">Bank:</span> {{ $affiliator->bank_name ?: '-' }} {{ $affiliator->bank_account_number }}</div>
        <div><span class="text-slate-500">Terdaftar:</span> {{ $affiliator->created_at->format('d M Y H:i') }}</div>
        <div><span class="text-slate-500">Disetujui:</span> {{ $affiliator->approved_at ? $affiliator->approved_at->format('d M Y') : '-' }}</div>
    </div>
</div>

<div class="flex gap-3">
    @if($affiliator->status === 'pending')
    <form method="POST" action="{{ route('admin.affiliators.approve', $affiliator) }}">@csrf
        <button class="px-4 py-2 bg-secondary-600 text-white text-sm rounded-xl hover:bg-secondary-700">Approve</button>
    </form>
    @elseif($affiliator->status === 'active')
    <form method="POST" action="{{ route('admin.affiliators.suspend', $affiliator) }}">@csrf
        <button class="px-4 py-2 bg-rose-600 text-white text-sm rounded-xl hover:bg-rose-700">Suspend</button>
    </form>
    @elseif($affiliator->status === 'suspended')
    <form method="POST" action="{{ route('admin.affiliators.reactivate', $affiliator) }}">@csrf
        <button class="px-4 py-2 bg-primary-600 text-white text-sm rounded-xl hover:bg-primary-700">Reactivate</button>
    </form>
    @endif
    <form method="POST" action="{{ route('admin.affiliators.destroy', $affiliator) }}" onsubmit="return confirm('Yakin hapus affiliator ini?')">
        @csrf @method('DELETE')
        <button class="px-4 py-2 border border-rose-200 text-rose-600 text-sm rounded-xl hover:bg-rose-50">Hapus</button>
    </form>
</div>
@endsection
