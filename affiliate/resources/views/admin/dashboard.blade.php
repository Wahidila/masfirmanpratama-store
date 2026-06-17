@extends('admin.layouts.admin')

@section('content')
<h1 class="text-xl font-bold text-slate-800 mb-6">Dashboard Admin</h1>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Total Affiliator</p>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['total_affiliators'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Pending Approval</p>
        <p class="text-2xl font-bold text-accent-600">{{ $stats['pending_affiliators'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Pending Withdraw</p>
        <p class="text-2xl font-bold text-primary-600">{{ $stats['pending_withdrawals'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Total Komisi</p>
        <p class="text-2xl font-bold text-secondary-600">Rp {{ number_format($stats['total_commissions'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Pending Affiliators --}}
@if($pendingAffiliators->count())
<div class="bg-white rounded-2xl border border-slate-100 p-6 mb-6">
    <h3 class="font-semibold text-slate-800 mb-4">Menunggu Persetujuan</h3>
    @foreach($pendingAffiliators as $aff)
    <div class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
        <div>
            <p class="text-sm font-medium text-slate-700">{{ $aff->name }}</p>
            <p class="text-xs text-slate-400">{{ $aff->email }} · {{ $aff->type->name }}</p>
        </div>
        <form method="POST" action="{{ route('admin.affiliators.approve', $aff) }}">
            @csrf
            <button class="text-xs px-3 py-1.5 bg-secondary-50 text-secondary-700 rounded-lg font-medium hover:bg-secondary-100">Setujui</button>
        </form>
    </div>
    @endforeach
</div>
@endif

{{-- Pending Withdrawals --}}
@if($pendingWithdrawals->count())
<div class="bg-white rounded-2xl border border-slate-100 p-6">
    <h3 class="font-semibold text-slate-800 mb-4">Penarikan Menunggu</h3>
    @foreach($pendingWithdrawals as $wd)
    <div class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
        <div>
            <p class="text-sm font-medium text-slate-700">{{ $wd->affiliator->name }}</p>
            <p class="text-xs text-slate-400">{{ $wd->method->name }} · {{ $wd->account_number }}</p>
        </div>
        <p class="text-sm font-bold text-slate-800">Rp {{ number_format($wd->amount, 0, ',', '.') }}</p>
    </div>
    @endforeach
    <a href="{{ route('admin.withdrawals.index') }}" class="text-sm text-primary-600 hover:text-primary-700 mt-3 inline-block">Lihat semua →</a>
</div>
@endif
@endsection
