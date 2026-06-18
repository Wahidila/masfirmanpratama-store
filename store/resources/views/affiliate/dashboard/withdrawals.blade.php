@extends('layouts.affiliate-dashboard')

@section('title', 'Penarikan')
@section('page-title', 'Penarikan')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Request Withdraw --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Ajukan Penarikan</h3>

                <div class="mb-4 p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500">Saldo tersedia</p>
                    <p class="text-lg font-bold text-slate-900">Rp {{ number_format($saldoAvailable, 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 mt-1">Minimum: Rp {{ number_format($minPayout, 0, ',', '.') }}</p>
                </div>

                @if (empty($affiliator->bank_name) || empty($affiliator->bank_account))
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                        <p class="font-medium mb-1">Informasi bank belum lengkap</p>
                        <p class="text-xs">Silakan lengkapi di <a href="{{ route('affiliate.profile.edit') }}" class="underline font-medium">halaman profil</a> terlebih dahulu.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('affiliate.withdrawals.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="amount" class="block text-sm font-medium text-slate-700 mb-1">Jumlah (Rp)</label>
                            <input
                                type="number"
                                id="amount"
                                name="amount"
                                min="{{ $minPayout }}"
                                max="{{ $saldoAvailable }}"
                                step="1000"
                                value="{{ old('amount') }}"
                                class="w-full px-3 py-2.5 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 @error('amount') border-rose-300 @enderror"
                                placeholder="Masukkan jumlah"
                                required
                            >
                            @error('amount')
                                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-4 p-3 bg-slate-50 rounded-lg text-xs text-slate-600">
                            <p><strong>Transfer ke:</strong></p>
                            <p>{{ $affiliator->bank_name }} — {{ $affiliator->bank_account }}</p>
                            <p>a.n. {{ $affiliator->bank_holder }}</p>
                        </div>

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-full hover:bg-primary-700 shadow-lg shadow-primary-500/30 transition-all"
                            {{ $saldoAvailable < $minPayout ? 'disabled' : '' }}
                        >
                            <i data-lucide="send" class="w-4 h-4"></i>
                            Ajukan Penarikan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- Riwayat Penarikan --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
                <h3 class="text-base font-semibold text-slate-900 mb-4">Riwayat Penarikan</h3>

                @if ($withdrawals->isEmpty())
                    <div class="text-center py-12">
                        <i data-lucide="wallet" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                        <p class="text-sm text-slate-500">Belum ada riwayat penarikan.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="text-left py-3 px-2 font-medium text-slate-500">Tanggal</th>
                                    <th class="text-right py-3 px-2 font-medium text-slate-500">Jumlah</th>
                                    <th class="text-left py-3 px-2 font-medium text-slate-500">Bank</th>
                                    <th class="text-center py-3 px-2 font-medium text-slate-500">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($withdrawals as $withdrawal)
                                    @php
                                        $wBadge = match($withdrawal->status) {
                                            'requested' => 'bg-amber-50 text-amber-700 border-amber-200',
                                            'approved' => 'bg-primary-50 text-primary-700 border-primary-200',
                                            'paid' => 'bg-green-50 text-green-700 border-green-200',
                                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                                        };
                                        $wLabel = match($withdrawal->status) {
                                            'requested' => 'Diajukan',
                                            'approved' => 'Disetujui',
                                            'paid' => 'Dibayar',
                                            'rejected' => 'Ditolak',
                                            default => $withdrawal->status,
                                        };
                                    @endphp
                                    <tr class="border-b border-slate-50">
                                        <td class="py-3 px-2 whitespace-nowrap text-slate-600">{{ $withdrawal->requested_at?->format('d M Y') }}</td>
                                        <td class="py-3 px-2 whitespace-nowrap text-right font-medium text-slate-900">Rp {{ number_format((float) $withdrawal->amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-2 whitespace-nowrap text-slate-600">{{ $withdrawal->bank_name }}</td>
                                        <td class="py-3 px-2 text-center">
                                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full border {{ $wBadge }}">
                                                {{ $wLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $withdrawals->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
