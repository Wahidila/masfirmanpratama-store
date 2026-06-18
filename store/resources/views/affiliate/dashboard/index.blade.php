@extends('layouts.affiliate-dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Metric Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-6">
        {{-- Total Klik --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-10 h-10 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                    <i data-lucide="mouse-pointer-click" class="w-5 h-5"></i>
                </span>
                <span class="text-sm font-medium text-slate-500">Total Klik</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($totalClicks, 0, ',', '.') }}</p>
        </div>

        {{-- Total Order --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-10 h-10 rounded-xl bg-secondary-50 text-secondary-600 flex items-center justify-center">
                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                </span>
                <span class="text-sm font-medium text-slate-500">Total Order</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">{{ number_format($totalOrders, 0, ',', '.') }}</p>
        </div>

        {{-- Komisi Pending --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </span>
                <span class="text-sm font-medium text-slate-500">Komisi Pending</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($commissionPending, 0, ',', '.') }}</p>
        </div>

        {{-- Saldo Bisa Ditarik --}}
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <div class="flex items-center gap-3 mb-3">
                <span class="w-10 h-10 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5"></i>
                </span>
                <span class="text-sm font-medium text-slate-500">Saldo Tersedia</span>
            </div>
            <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($saldoAvailable, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Komisi Breakdown --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Komisi Approved</h3>
            <p class="text-xl font-bold text-primary-600">Rp {{ number_format($commissionApproved, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <h3 class="text-sm font-medium text-slate-500 mb-2">Komisi Sudah Dibayar</h3>
            <p class="text-xl font-bold text-secondary-600">Rp {{ number_format($commissionPaid, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Kode Referral Utama --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <h3 class="text-sm font-medium text-slate-500 mb-3">Kode Referral Utama</h3>
        @if ($primaryCode)
            <div x-data="{ copied: false }" class="flex items-center gap-3">
                <code class="flex-1 px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-mono text-slate-800 truncate">
                    {{ url('/affiliate?ref=' . $primaryCode->code) }}
                </code>
                <button
                    type="button"
                    @click="navigator.clipboard.writeText('{{ url('/affiliate?ref=' . $primaryCode->code) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                    class="shrink-0 inline-flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                    :class="copied ? 'bg-secondary-50 text-secondary-700' : 'bg-primary-50 text-primary-700 hover:bg-primary-100'"
                >
                    <i data-lucide="copy" class="w-4 h-4" x-show="!copied"></i>
                    <i data-lucide="check" class="w-4 h-4" x-show="copied" x-cloak></i>
                    <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                </button>
            </div>
        @else
            <p class="text-sm text-slate-500">Belum ada kode referral. <a href="{{ route('affiliate.referral-links.index') }}" class="text-primary-600 hover:underline">Buat sekarang</a>.</p>
        @endif
    </div>
@endsection
