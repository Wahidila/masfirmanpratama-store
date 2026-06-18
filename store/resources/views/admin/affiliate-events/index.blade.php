@extends('layouts.admin', ['active' => 'affiliate-events'])

@section('title', 'Event Affiliate')

@section('content')
    <x-admin.page-header
        title="Event Affiliate"
        subtitle="Kelola event dan gamifikasi program affiliate.">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.affiliate-events.create') }}" size="sm">Tambah Event</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>{{ session('status') }}</x-admin.alert>
        </div>
    @endif

    {{-- Stats --}}
    <section class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <x-admin.stat-card title="Total" :value="$stats['total']" tone="slate" />
        <x-admin.stat-card title="Draft" :value="$stats['draft']" tone="amber" />
        <x-admin.stat-card title="Aktif" :value="$stats['active']" tone="secondary" />
        <x-admin.stat-card title="Selesai" :value="$stats['ended']" tone="primary" />
    </section>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.affiliate-events.index') }}"
        class="mb-6 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs sm:flex-row sm:items-end dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex-1">
            <label for="search" class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Cari</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Judul event…"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
        </div>

        <div class="sm:w-40">
            <label for="status" class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Status</label>
            <select id="status" name="status"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">Semua</option>
                @foreach (['draft' => 'Draft', 'active' => 'Aktif', 'ended' => 'Selesai'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <x-admin.button type="submit" size="sm">Filter</x-admin.button>

        @if (request('search') || request('status'))
            <x-admin.button href="{{ route('admin.affiliate-events.index') }}" variant="outline" size="sm">Reset</x-admin.button>
        @endif
    </form>

    {{-- Table --}}
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Judul</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Mulai</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Selesai</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Status</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($events as $event)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $event->title }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $event->starts_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $event->ends_at->format('d M Y') }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-secondary-50 text-secondary-700 dark:bg-secondary-500/15 dark:text-secondary-400',
                                        'draft' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/15 dark:text-amber-400',
                                        'ended' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    ];
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $statusColors[$event->status] ?? '' }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.affiliate-events.edit', $event) }}" class="text-xs font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400">Edit</a>
                                    <form method="POST" action="{{ route('admin.affiliate-events.destroy', $event) }}" onsubmit="return confirm('Yakin ingin menghapus event ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada event.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($events->hasPages())
            <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                {{ $events->links() }}
            </div>
        @endif
    </x-admin.card>
@endsection
