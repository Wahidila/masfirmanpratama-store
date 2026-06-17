@extends('admin.layouts.admin')

@section('content')
<h1 class="text-xl font-bold text-slate-800 mb-6">Semua Komisi</h1>

<div class="mb-4">
    <form method="GET" class="flex items-center gap-2">
        <select name="status" onchange="this.form.submit()" class="text-sm border border-slate-200 rounded-xl px-3 py-2">
            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua</option>
            <option value="cooling" {{ request('status') === 'cooling' ? 'selected' : '' }}>Cooling</option>
            <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
            <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
        </select>
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Affiliator</th>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Order</th>
                <th class="text-right px-4 py-3 font-medium text-slate-600">Jumlah</th>
                <th class="text-center px-4 py-3 font-medium text-slate-600">Rate</th>
                <th class="text-center px-4 py-3 font-medium text-slate-600">Status</th>
                <th class="text-left px-4 py-3 font-medium text-slate-600">Tersedia</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($commissions as $c)
            <tr>
                <td class="px-4 py-3 text-slate-700">{{ $c->affiliator->name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $c->referralOrder->buyer_name ?? '-' }}</td>
                <td class="px-4 py-3 text-right font-medium">Rp {{ number_format($c->amount, 0, ',', '.') }}</td>
                <td class="px-4 py-3 text-center">{{ $c->rate_applied }}%</td>
                <td class="px-4 py-3 text-center">
                    <span class="text-xs px-2 py-1 rounded-full font-medium
                        {{ $c->status === 'available' ? 'bg-secondary-50 text-secondary-700' : '' }}
                        {{ $c->status === 'cooling' ? 'bg-accent-50 text-accent-700' : '' }}
                        {{ $c->status === 'withdrawn' ? 'bg-slate-100 text-slate-600' : '' }}">
                        {{ ucfirst($c->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-slate-500 text-xs">{{ $c->available_at->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada komisi</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($commissions->hasPages())
    <div class="px-4 py-3 border-t border-slate-100">{{ $commissions->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
