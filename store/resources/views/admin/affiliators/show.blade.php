@extends('layouts.admin', ['active' => 'affiliators'])

@section('title', 'Detail Affiliator - ' . $affiliator->name)

@section('content')
    <x-admin.page-header
        title="Detail Affiliator"
        :subtitle="$affiliator->name">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.affiliators.edit', $affiliator) }}" size="sm" variant="outline">Edit</x-admin.button>
            <x-admin.button href="{{ route('admin.affiliators.index') }}" size="sm" variant="outline">Kembali</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    {{-- Info Card --}}
    <div class="mb-6 grid gap-6 lg:grid-cols-3">
        <x-admin.card class="lg:col-span-2">
            <div class="p-5">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">Informasi Affiliator</h3>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Nama</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ $affiliator->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ $affiliator->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Telepon</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ $affiliator->phone ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Tipe</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ str_replace('_', ' ', ucfirst($affiliator->type)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ ucfirst($affiliator->status) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Terdaftar</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ $affiliator->created_at->format('d M Y H:i') }}</dd>
                    </div>
                </dl>

                <h3 class="mb-4 mt-6 text-sm font-semibold text-gray-800 dark:text-white/90">Informasi Bank</h3>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Bank</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ $affiliator->bank_name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">No. Rekening</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ $affiliator->bank_account ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400">Atas Nama</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white/90">{{ $affiliator->bank_holder ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </x-admin.card>

        <div class="space-y-4">
            <x-admin.stat-card title="Total Komisi (Approved)" :value="'Rp ' . number_format($totalKomisi, 0, ',', '.')" tone="secondary" />
            <x-admin.stat-card title="Total Penarikan (Paid)" :value="'Rp ' . number_format($totalWithdrawal, 0, ',', '.')" tone="primary" />
            <x-admin.stat-card title="Referral Codes" :value="$affiliator->referral_codes_count" tone="amber" />
        </div>
    </div>

    {{-- Komisi Terakhir --}}
    <x-admin.card class="mb-6">
        <div class="p-5">
            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">Komisi Terakhir</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Tanggal</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Jumlah</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($affiliator->commissions as $commission)
                            <tr>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $commission->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-gray-900 dark:text-white/90">Rp {{ number_format($commission->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $commission->status === 'approved' ? 'bg-secondary-50 text-secondary-700 dark:bg-secondary-500/15 dark:text-secondary-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400' }}">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada komisi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-admin.card>

    {{-- Penarikan Terakhir --}}
    <x-admin.card>
        <div class="p-5">
            <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">Penarikan Terakhir</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Tanggal</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Jumlah</th>
                            <th class="px-4 py-2 text-left font-medium text-gray-700 dark:text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($affiliator->withdrawals as $withdrawal)
                            <tr>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-400">{{ $withdrawal->created_at->format('d M Y') }}</td>
                                <td class="px-4 py-2 text-gray-900 dark:text-white/90">Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $wdColors = [
                                            'paid' => 'bg-secondary-50 text-secondary-700 dark:bg-secondary-500/15 dark:text-secondary-400',
                                            'approved' => 'bg-primary-50 text-primary-700 dark:bg-primary-500/15 dark:text-primary-400',
                                            'requested' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
                                            'rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $wdColors[$withdrawal->status] ?? '' }}">
                                        {{ ucfirst($withdrawal->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Belum ada penarikan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-admin.card>
@endsection
