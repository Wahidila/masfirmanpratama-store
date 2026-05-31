@extends('layouts.admin', ['active' => 'dashboard'])

@section('title', 'Dashboard Admin')

@section('content')
    <x-admin.page-header
        title="Dashboard"
        subtitle="Ringkasan operasional store hari ini." />

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>
                {{ session('status') }}
            </x-admin.alert>
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
        <div class="mb-3 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pesanan terbaru</h2>
            <span class="text-xs text-gray-500 dark:text-gray-400">5 terakhir</span>
        </div>

        <x-admin.table
            :columns="[
                ['label' => 'Order #'],
                ['label' => 'Customer'],
                ['label' => 'Total'],
                ['label' => 'Status'],
                ['label' => 'Dibuat'],
            ]"
            :rows="$recentOrders"
            empty="Belum ada pesanan.">
            @foreach ($recentOrders as $order)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">{{ $order->customer_name }}</td>
                    <td class="px-4 py-3">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">
                        <x-admin.status-badge :status="$order->status" />
                    </td>
                    <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $order->created_at?->diffForHumans() }}</td>
                </tr>
            @endforeach
        </x-admin.table>
    </section>
@endsection
