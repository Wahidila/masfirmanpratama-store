@extends('layouts.dashboard')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Penarikan</h1>
        <p class="text-slate-500 mt-1">Riwayat penarikan komisi Anda</p>
    </div>
    <a href="{{ route('withdrawals.create') }}" class="px-4 py-2.5 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition">
        + Tarik Saldo
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Tanggal</th>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Metode</th>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Rekening</th>
                    <th class="text-right px-6 py-3 font-medium text-slate-600">Jumlah</th>
                    <th class="text-center px-6 py-3 font-medium text-slate-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($withdrawals as $withdrawal)
                <tr class="hover:bg-slate-50/50">
                    <td class="px-6 py-4 text-slate-600">{{ $withdrawal->created_at->format('d M Y H:i') }}</td>
                    <td class="px-6 py-4 text-slate-700">{{ $withdrawal->method->name }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $withdrawal->account_name }} · {{ $withdrawal->account_number }}</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xs px-2.5 py-1 rounded-full font-medium
                            {{ $withdrawal->status === 'completed' ? 'bg-secondary-50 text-secondary-700' : '' }}
                            {{ $withdrawal->status === 'pending' ? 'bg-accent-50 text-accent-700' : '' }}
                            {{ $withdrawal->status === 'processing' ? 'bg-blue-50 text-blue-700' : '' }}
                            {{ $withdrawal->status === 'rejected' ? 'bg-rose-50 text-rose-700' : '' }}">
                            {{ ucfirst($withdrawal->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">Belum ada riwayat penarikan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($withdrawals->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">{{ $withdrawals->links() }}</div>
    @endif
</div>
@endsection
