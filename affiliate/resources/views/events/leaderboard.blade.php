@extends('layouts.dashboard')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Leaderboard</h1>
    <p class="text-slate-500 mt-1">Top affiliator berdasarkan total pendapatan</p>
</div>

<div class="bg-white rounded-2xl border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-100">
                <tr>
                    <th class="text-center px-6 py-3 font-medium text-slate-600 w-16">#</th>
                    <th class="text-left px-6 py-3 font-medium text-slate-600">Nama</th>
                    <th class="text-center px-6 py-3 font-medium text-slate-600">Total Order</th>
                    <th class="text-right px-6 py-3 font-medium text-slate-600">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($topAffiliators as $i => $aff)
                <tr class="hover:bg-slate-50/50 {{ $i < 3 ? 'bg-accent-50/30' : '' }}">
                    <td class="px-6 py-4 text-center">
                        @if($i === 0) 🥇
                        @elseif($i === 1) 🥈
                        @elseif($i === 2) 🥉
                        @else <span class="text-slate-400">{{ $i + 1 }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-medium text-slate-700">{{ $aff->name }}</td>
                    <td class="px-6 py-4 text-center text-slate-600">{{ $aff->referral_orders_count }}</td>
                    <td class="px-6 py-4 text-right font-medium text-slate-800">Rp {{ number_format($aff->total_earned ?? 0, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
