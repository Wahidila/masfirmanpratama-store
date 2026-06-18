@extends('layouts.affiliate-dashboard')

@section('title', 'Komisi')
@section('page-title', 'Komisi')

@section('content')
    <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <h2 class="text-lg font-semibold text-slate-900">Riwayat Komisi</h2>

            {{-- Filter status --}}
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('affiliate.commissions.index') }}"
                   class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors {{ !$currentStatus ? 'bg-primary-50 text-primary-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua
                </a>
                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'paid' => 'Paid', 'rejected' => 'Rejected'] as $key => $label)
                    <a href="{{ route('affiliate.commissions.index', ['status' => $key]) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-full transition-colors {{ $currentStatus === $key ? 'bg-primary-50 text-primary-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        @if ($commissions->isEmpty())
            <div class="text-center py-12">
                <i data-lucide="coins" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                <p class="text-sm text-slate-500">Belum ada data komisi.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100">
                            <th class="text-left py-3 px-2 font-medium text-slate-500">Tanggal</th>
                            <th class="text-left py-3 px-2 font-medium text-slate-500">Order</th>
                            <th class="text-left py-3 px-2 font-medium text-slate-500">Pembeli</th>
                            <th class="text-right py-3 px-2 font-medium text-slate-500">Rate</th>
                            <th class="text-right py-3 px-2 font-medium text-slate-500">Komisi</th>
                            <th class="text-center py-3 px-2 font-medium text-slate-500">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($commissions as $commission)
                            @php
                                $order = $commission->referralOrder?->order;
                                $statusBadge = match($commission->status) {
                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'approved' => 'bg-primary-50 text-primary-700 border-primary-200',
                                    'paid' => 'bg-green-50 text-green-700 border-green-200',
                                    'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                                $statusLabel = match($commission->status) {
                                    'pending' => 'Pending',
                                    'approved' => 'Approved',
                                    'paid' => 'Paid',
                                    'rejected' => 'Rejected',
                                    default => $commission->status,
                                };
                            @endphp
                            <tr class="border-b border-slate-50 hover:bg-slate-25">
                                <td class="py-3 px-2 whitespace-nowrap text-slate-600">{{ $commission->created_at?->format('d M Y') }}</td>
                                <td class="py-3 px-2 whitespace-nowrap font-mono text-slate-800">{{ $order?->order_number ?? '-' }}</td>
                                <td class="py-3 px-2 whitespace-nowrap text-slate-800">{{ $order?->customer_name ?? '-' }}</td>
                                <td class="py-3 px-2 whitespace-nowrap text-right text-slate-600">{{ number_format((float) $commission->rate, 1) }}%</td>
                                <td class="py-3 px-2 whitespace-nowrap text-right font-medium text-slate-900">Rp {{ number_format((float) $commission->amount, 0, ',', '.') }}</td>
                                <td class="py-3 px-2 text-center">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded-full border {{ $statusBadge }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $commissions->links() }}
            </div>
        @endif
    </div>
@endsection
