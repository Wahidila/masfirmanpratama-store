@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <header class="mb-8">
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Dashboard</h1>
        <p class="mt-1 text-sm text-slate-500">Ringkasan operasional store hari ini.</p>
    </header>

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-secondary-200 bg-secondary-50 px-4 py-3 text-sm text-secondary-900">
            {{ session('status') }}
        </div>
    @endif

    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <x-admin.stat-card title="Pesanan Pending" :value="$stats['orders_pending']" hint="Belum upload bukti bayar" tone="amber" />
        <x-admin.stat-card title="Bukti Bayar Menunggu Verifikasi" :value="$stats['payments_to_verify']" hint="Action wajib admin" tone="primary" />
        <x-admin.stat-card title="Cicilan Berjalan" :value="$stats['orders_partial_paid']" hint="Order partial_paid" tone="secondary" />
        <x-admin.stat-card title="Lunas (Belum Kirim)" :value="$stats['orders_paid']" hint="Siap input resi" tone="primary" />
        <x-admin.stat-card title="Total Pesanan" :value="$stats['orders_total']" hint="Semua status" tone="slate" />
        <x-admin.stat-card title="Produk Aktif" :value="$stats['products_active']" hint="status=active" tone="secondary" />
    </section>

    <section class="mt-10">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-semibold text-slate-900">Pesanan terbaru</h2>
            <span class="text-xs text-slate-500">5 terakhir</span>
        </div>

        <div class="mt-4 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Order #</th>
                        <th class="px-4 py-3 font-medium">Customer</th>
                        <th class="px-4 py-3 font-medium">Total</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Dibuat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td class="px-4 py-3 font-mono text-xs">{{ $order->order_number }}</td>
                            <td class="px-4 py-3">{{ $order->customer_name }}</td>
                            <td class="px-4 py-3">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-500">{{ $order->created_at?->diffForHumans() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Belum ada pesanan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
