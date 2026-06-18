@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Komisi Saya</h1>
    <p class="text-slate-500 mt-1">Riwayat dan status komisi dari referral Anda</p>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Cooling (7 hari)</p>
        <p class="text-lg font-bold text-accent-600">Rp {{ number_format($summary['cooling'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Tersedia</p>
        <p class="text-lg font-bold text-secondary-600">Rp {{ number_format($summary['available'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Sudah Ditarik</p>
        <p class="text-lg font-bold text-slate-600">Rp {{ number_format($summary['withdrawn'], 0, ',', '.') }}</p>
    </div>
    <div class="bg-white rounded-xl border border-slate-100 p-4">
        <p class="text-xs text-slate-500">Total Pendapatan</p>
        <p class="text-lg font-bold text-primary-600">Rp {{ number_format($summary['total'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="mb-4">
    <form method="GET" class="flex items-center gap-2">
        <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl px-3 py-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
            <option value="cooling" {{ request('status') === 'cooling' ? 'selected' : '' }}>Cooling</option>
            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
            <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Ditarik</option>
        </select>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Tanggal</th>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Order</th>
                    <th class="text-right px-6 py-3 font-medium text-slate-600">Jumlah</th>
                    <th class="text-center px-6 py-3 font-medium text-slate-600">Rate</th>
                    <th class="text-center px-6 py-3 font-medium text-slate-600">Status</th>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Tersedia</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($commissions as $commission)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 text-slate-600">{{ $commission->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $commission->referralOrder->buyer_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">Rp {{ number_format($commission->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center text-slate-600">{{ $commission->rate_applied }}%</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $commission->status === 'available' ? 'bg-secondary-50 text-secondary-700' : '' }}
                            {{ $commission->status === 'cooling' ? 'bg-accent-50 text-accent-700' : '' }}
                            {{ $commission->status === 'withdrawn' ? 'bg-slate-100 text-slate-600' : '' }}
                            {{ $commission->status === 'cancelled' ? 'bg-rose-50 text-rose-700' : '' }}">
                            {{ ucfirst($commission->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-slate-500 text-xs">{{ $commission->available_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-400">Belum ada data komisi</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($commissions->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">{{ $commissions->links() }}</div>
    @endif
</div>
@endsection
