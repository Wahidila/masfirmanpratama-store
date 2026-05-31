@extends('layouts.admin', ['active' => 'orders'])

@section('title', 'Pesanan · Admin')

@section('content')
    <x-admin.page-header
        title="Pesanan"
        subtitle="Daftar pesanan dengan filter status & pencarian.">
        <x-slot:actions>
            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $stats['total'] }} total</span>
        </x-slot:actions>
    </x-admin.page-header>

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>
                {{ session('status') }}
            </x-admin.alert>
        </div>
    @endif

    {{-- Stat strip per-status --}}
    @php
        $statusLabel = [
            'pending' => 'Pending',
            'partial_paid' => 'Cicilan',
            'paid' => 'Lunas',
            'shipped' => 'Dikirim',
            'completed' => 'Selesai',
            'cancelled' => 'Batal',
            'refunded' => 'Refund',
        ];
    @endphp

    <section class="grid grid-cols-2 gap-3 mb-6 sm:grid-cols-4 lg:grid-cols-7">
        @foreach ($statuses as $s)
            <a href="{{ route('admin.orders.index', ['status' => $s]) }}"
               class="rounded-xl border px-3 py-2.5 transition {{ $filterStatus === $s ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/15' : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-gray-700' }}">
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $statusLabel[$s] ?? $s }}</div>
                <div class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $stats[$s] ?? 0 }}</div>
            </a>
        @endforeach
    </section>

    {{-- Filter form --}}
    <x-admin.card class="mb-6" :padded="false">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 lg:grid-cols-5">
            <div class="sm:col-span-2 lg:col-span-2">
                <label for="search" class="sr-only">Cari</label>
                <input
                    id="search"
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cari order number, nama, telp, atau email..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
            </div>

            <div>
                <label for="status" class="sr-only">Status</label>
                <select id="status" name="status"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}" @selected($filterStatus === $s)>{{ $statusLabel[$s] ?? $s }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date_from" class="sr-only">Dari tanggal</label>
                <input id="date_from" type="date" name="date_from" value="{{ $dateFrom }}"
                       class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            </div>

            <div class="flex gap-2">
                <input id="date_to" type="date" name="date_to" value="{{ $dateTo }}"
                       class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                       placeholder="Sampai">
                <x-admin.button type="submit" size="sm">
                    Filter
                </x-admin.button>
            </div>

            @if ($filterStatus || $search || $dateFrom || $dateTo)
                <div class="lg:col-span-5">
                    <a href="{{ route('admin.orders.index') }}" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        Reset filter
                    </a>
                </div>
            @endif
        </form>
    </x-admin.card>

    {{-- Tabel orders --}}
    <x-admin.table
        :columns="[
            ['label' => 'Order #'],
            ['label' => 'Customer'],
            ['label' => 'Total'],
            ['label' => 'Status'],
            ['label' => 'Dibuat'],
            ['label' => '', 'align' => 'text-right'],
        ]"
        :rows="$orders"
        empty="Tidak ada pesanan yang cocok dengan filter.">
        @foreach ($orders as $order)
            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.03]">
                <td class="px-4 py-3 font-mono text-xs text-gray-700 dark:text-gray-300">{{ $order->order_number }}</td>
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-800 dark:text-white/90">{{ $order->customer_name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->phone ?? $order->email }}</div>
                </td>
                <td class="px-4 py-3 font-medium text-gray-800 dark:text-white/90">Rp {{ number_format((float) $order->total, 0, ',', '.') }}</td>
                <td class="px-4 py-3">
                    <x-admin.status-badge :status="$order->status" />
                </td>
                <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                    <div>{{ $order->created_at?->format('d M Y') }}</div>
                    <div>{{ $order->created_at?->format('H:i') }} WIB</div>
                </td>
                <td class="px-4 py-3 text-right">
                    <a href="{{ route('admin.orders.show', $order) }}"
                       class="text-xs font-medium text-brand-500 hover:text-brand-600 dark:text-brand-400 dark:hover:text-brand-500">
                        Detail →
                    </a>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    @if ($orders->hasPages())
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    @endif
@endsection
