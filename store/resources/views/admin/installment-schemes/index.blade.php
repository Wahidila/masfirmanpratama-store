@extends('layouts.admin', ['active' => 'installments'])

@section('title', 'Skema Cicilan · Admin')

@section('content')
    <x-admin.page-header
        title="Skema Cicilan"
        subtitle="Skema pembayaran yang bisa dipilih customer di checkout. Skema global berlaku untuk semua produk; skema spesifik hanya muncul untuk produk yang ditandai.">
        <x-slot:actions>
            <a href="{{ route('admin.installment-schemes.create') }}"
               class="inline-flex items-center gap-1.5 rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-500 transition">
                + Skema Baru
            </a>
        </x-slot:actions>
    </x-admin.page-header>

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>{{ session('status') }}</x-admin.alert>
        </div>
    @endif

    {{-- Stat strip --}}
    <section class="grid grid-cols-2 gap-3 mb-6 sm:grid-cols-4">
        <a href="{{ route('admin.installment-schemes.index') }}"
           class="rounded-xl border px-3 py-2.5 transition {{ ! $filterScope ? 'border-primary-300 bg-primary-50 dark:bg-brand-500/15 dark:border-brand-500/40' : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-gray-700' }}">
            <div class="text-xs text-gray-500 dark:text-gray-400">Total</div>
            <div class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $stats['total'] }}</div>
        </a>
        <div class="rounded-xl border border-gray-200 bg-white px-3 py-2.5 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="text-xs text-gray-500 dark:text-gray-400">Aktif</div>
            <div class="mt-1 text-lg font-semibold text-secondary-700 dark:text-secondary-400">{{ $stats['active'] }}</div>
        </div>
        <a href="{{ route('admin.installment-schemes.index', ['scope' => 'global']) }}"
           class="rounded-xl border px-3 py-2.5 transition {{ $filterScope === 'global' ? 'border-primary-300 bg-primary-50 dark:bg-brand-500/15 dark:border-brand-500/40' : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-gray-700' }}">
            <div class="text-xs text-gray-500 dark:text-gray-400">Global</div>
            <div class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $stats['global'] }}</div>
        </a>
        <a href="{{ route('admin.installment-schemes.index', ['scope' => 'product']) }}"
           class="rounded-xl border px-3 py-2.5 transition {{ $filterScope === 'product' ? 'border-primary-300 bg-primary-50 dark:bg-brand-500/15 dark:border-brand-500/40' : 'border-gray-200 bg-white hover:border-gray-300 dark:border-gray-800 dark:bg-white/[0.03] dark:hover:border-gray-700' }}">
            <div class="text-xs text-gray-500 dark:text-gray-400">Per Produk</div>
            <div class="mt-1 text-lg font-semibold text-gray-800 dark:text-white/90">{{ $stats['product'] }}</div>
        </a>
    </section>

    {{-- Search --}}
    <x-admin.card class="mb-6" :padded="false">
        <form method="GET" action="{{ route('admin.installment-schemes.index') }}" class="flex gap-2 p-4">
            @if ($filterScope)
                <input type="hidden" name="scope" value="{{ $filterScope }}">
            @endif
            <input type="search" name="q" value="{{ $search }}"
                   placeholder="Cari nama skema atau produk..."
                   class="block w-full rounded-xl border-gray-200 text-sm focus:border-primary-500 focus:ring-primary-500/40 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:placeholder-gray-500">
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-xl bg-primary-600 px-4 py-2 text-sm font-medium text-white hover:bg-primary-500 transition">
                Cari
            </button>
            @if ($search || $filterScope)
                <a href="{{ route('admin.installment-schemes.index') }}"
                   class="inline-flex items-center text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Reset
                </a>
            @endif
        </form>
    </x-admin.card>

    {{-- Tabel --}}
    <x-admin.table
        :columns="[
            ['label' => 'Nama'],
            ['label' => 'Scope'],
            ['label' => 'DP %'],
            ['label' => 'Cicilan'],
            ['label' => 'Interval'],
            ['label' => 'Status'],
            ['label' => '', 'align' => 'text-right'],
        ]"
        :rows="$schemes"
        empty="Belum ada skema. Klik 'Skema Baru' untuk mulai.">
        @foreach ($schemes as $scheme)
            <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.03]">
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-800 dark:text-white/90">{{ $scheme->name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">#{{ $scheme->id }}</div>
                </td>
                <td class="px-4 py-3 text-sm">
                    @if ($scheme->product_id && $scheme->product)
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-primary-50 text-primary-800 ring-primary-200 dark:bg-brand-500/15 dark:text-brand-400 dark:ring-brand-500/40">
                            {{ $scheme->product->title }}
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium ring-1 ring-inset bg-secondary-50 text-secondary-800 ring-secondary-200 dark:bg-secondary-500/15 dark:text-secondary-400 dark:ring-secondary-500/40">
                            Global (semua produk)
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ rtrim(rtrim((string) $scheme->dp_pct, '0'), '.') }}%</td>
                <td class="px-4 py-3 text-sm">
                    @if ($scheme->n_installments <= 1)
                        <span class="text-gray-700 dark:text-gray-300">Lunas</span>
                    @else
                        <span class="text-gray-700 dark:text-gray-300">{{ $scheme->n_installments }}x</span>
                    @endif
                </td>
                <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $scheme->interval_days }} hari</td>
                <td class="px-4 py-3">
                    <form method="POST" action="{{ route('admin.installment-schemes.toggle', $scheme) }}" class="inline">
                        @csrf
                        @if ($scheme->active)
                            <button type="submit"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-secondary-50 text-secondary-800 ring-secondary-200 hover:bg-secondary-100 transition dark:bg-secondary-500/15 dark:text-secondary-400 dark:ring-secondary-500/40 dark:hover:bg-secondary-500/20">
                                ✓ Aktif
                            </button>
                        @else
                            <button type="submit"
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset bg-gray-100 text-gray-600 ring-gray-200 hover:bg-gray-200 transition dark:bg-white/5 dark:text-gray-400 dark:ring-gray-600 dark:hover:bg-white/10">
                                Nonaktif
                            </button>
                        @endif
                    </form>
                </td>
                <td class="px-4 py-3 text-right">
                    <div class="flex items-center justify-end gap-2 text-xs">
                        <a href="{{ route('admin.installment-schemes.edit', $scheme) }}"
                           class="font-medium text-primary-600 hover:text-primary-700">Edit</a>
                        <form method="POST" action="{{ route('admin.installment-schemes.destroy', $scheme) }}"
                              onsubmit="return confirm('Hapus skema {{ addslashes($scheme->name) }}?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="font-medium text-rose-600 hover:text-rose-700">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-admin.table>

    @if ($schemes->hasPages())
        <div class="mt-4">
            {{ $schemes->links() }}
        </div>
    @endif
@endsection
