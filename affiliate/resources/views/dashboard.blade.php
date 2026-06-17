@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Dashboard</h1>
    <p class="text-slate-500 mt-1">Selamat datang kembali, {{ $affiliator->name }}!</p>
</div>

{{-- Stats Grid --}}
<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-secondary-50 rounded-xl flex items-center justify-center">
                <i data-lucide="wallet" class="w-5 h-5 text-secondary-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($stats['available_balance'], 0, ',', '.') }}</p>
        <p class="text-xs text-slate-500 mt-1">Saldo Tersedia</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-primary-50 rounded-xl flex items-center justify-center">
                <i data-lucide="trending-up" class="w-5 h-5 text-primary-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($stats['total_earnings'], 0, ',', '.') }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Pendapatan</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-accent-50 rounded-xl flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5 text-accent-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">Rp {{ number_format($stats['pending_commissions'], 0, ',', '.') }}</p>
        <p class="text-xs text-slate-500 mt-1">Dalam Cooling (7 hari)</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                <i data-lucide="link" class="w-5 h-5 text-blue-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['total_referrals'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Link Referral</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center">
                <i data-lucide="mouse-pointer-click" class="w-5 h-5 text-purple-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ number_format($stats['total_clicks']) }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Klik</p>
    </div>
    <div class="bg-white rounded-2xl border border-slate-100 p-5">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                <i data-lucide="shopping-bag" class="w-5 h-5 text-emerald-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">{{ $stats['total_orders'] }}</p>
        <p class="text-xs text-slate-500 mt-1">Total Order</p>
    </div>
</div>

{{-- Recent Activity --}}
<div class="grid lg:grid-cols-2 gap-6">
    {{-- Recent Commissions --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800">Komisi Terbaru</h3>
            <a href="{{ route('commissions.index') }}" class="text-sm text-primary-600 hover:text-primary-700">Lihat semua</a>
        </div>
        @forelse($recentCommissions as $commission)
        <div class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
            <div>
                <p class="text-sm font-medium text-slate-700">Rp {{ number_format($commission->amount, 0, ',', '.') }}</p>
                <p class="text-xs text-slate-400">{{ $commission->created_at->diffForHumans() }}</p>
            </div>
            <span class="text-xs px-2.5 py-1 rounded-full font-medium
                {{ $commission->status === 'available' ? 'bg-secondary-50 text-secondary-700' : '' }}
                {{ $commission->status === 'cooling' ? 'bg-accent-50 text-accent-700' : '' }}
                {{ $commission->status === 'withdrawn' ? 'bg-slate-100 text-slate-600' : '' }}">
                {{ ucfirst($commission->status) }}
            </span>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-4">Belum ada komisi</p>
        @endforelse
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800">Order Referral Terbaru</h3>
        </div>
        @forelse($recentOrders as $order)
        <div class="flex items-center justify-between py-3 border-b border-slate-50 last:border-0">
            <div>
                <p class="text-sm font-medium text-slate-700">{{ $order->buyer_name }}</p>
                <p class="text-xs text-slate-400">{{ $order->referralCode->code }} · {{ $order->ordered_at->diffForHumans() }}</p>
            </div>
            <p class="text-sm font-medium text-slate-800">Rp {{ number_format($order->order_total, 0, ',', '.') }}</p>
        </div>
        @empty
        <p class="text-sm text-slate-400 text-center py-4">Belum ada order referral</p>
        @endforelse
    </div>
</div>
@endsection
