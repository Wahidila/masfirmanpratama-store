@extends('layouts.admin', ['active' => 'commissions'])

@section('title', 'Komisi')

@section('content')
    <x-admin.page-header
        title="Komisi"
        subtitle="Kelola komisi affiliator. Setujui atau tolak komisi yang masuk.">
    </x-admin.page-header>

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>{{ session('status') }}</x-admin.alert>
        </div>
    @endif

    {{-- Stats --}}
    <section class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <x-admin.stat-card title="Total" :value="$stats['total']" tone="slate" />
        <x-admin.stat-card title="Pending" :value="$stats['pending']" tone="amber" />
        <x-admin.stat-card title="Approved" :value="$stats['approved']" tone="secondary" />
        <x-admin.stat-card title="Total Approved" :value="'Rp ' . number_format($stats['total_approved_amount'], 0, ',', '.')" tone="primary" />
    </section>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.commissions.index') }}"
        class="mb-6 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs sm:flex-row sm:items-end dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex-1">
            <label for="search" class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Cari Affiliator</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nama atau email affiliator…"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
        </div>

        <div class="sm:w-40">
            <label for="status" class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Status</label>
            <select id="status" name="status"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">Semua</option>
                @foreach (['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <x-admin.button type="submit" size="sm">Filter</x-admin.button>

        @if (request('search') || request('status'))
            <x-admin.button href="{{ route('admin.commissions.index') }}" variant="outline" size="sm">Reset</x-admin.button>
        @endif
    </form>

    {{-- Table --}}
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Affiliator</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Jumlah</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Rate</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Tanggal</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($commissions as $commission)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $commission->affiliator->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-gray-900 dark:text-white/90">Rp {{ number_format($commission->amount, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $commission->rate }}%</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'approved' => 'bg-secondary-50 text-secondary-700 dark:bg-secondary-500/15 dark:text-secondary-400',
                                        'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
                                        'rejected' => 'bg-rose-50 text-rose-700 dark:bg-rose-500/15 dark:text-rose-400',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColors[$commission->status] ?? '' }}">
                                    {{ ucfirst($commission->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $commission->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($commission->status === 'pending')
                                    <div class="flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.commissions.approve', $commission) }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-secondary-600 hover:text-secondary-800 dark:text-secondary-400">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.commissions.reject', $commission) }}">
                                            @csrf
                                            <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Tolak</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada komisi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($commissions->hasPages())
            <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                {{ $commissions->links() }}
            </div>
        @endif
    </x-admin.card>
@endsection
