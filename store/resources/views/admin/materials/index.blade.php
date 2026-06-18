@extends('layouts.admin', ['active' => 'materials'])

@section('title', 'Materi Affiliate')

@section('content')
    <x-admin.page-header
        title="Materi Affiliate"
        subtitle="Kelola materi marketing untuk affiliator (banner, brosur, video, template WA).">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.materials.create') }}" size="sm">Tambah Materi</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>{{ session('status') }}</x-admin.alert>
        </div>
    @endif

    {{-- Stats --}}
    <section class="mb-6 grid grid-cols-2 gap-3 sm:grid-cols-5">
        <x-admin.stat-card title="Total" :value="$stats['total']" tone="slate" />
        <x-admin.stat-card title="Banner" :value="$stats['banner']" tone="primary" />
        <x-admin.stat-card title="Brosur" :value="$stats['brosur']" tone="secondary" />
        <x-admin.stat-card title="Video" :value="$stats['video']" tone="amber" />
        <x-admin.stat-card title="Template WA" :value="$stats['template_wa']" tone="slate" />
    </section>

    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.materials.index') }}"
        class="mb-6 flex flex-col gap-3 rounded-2xl border border-gray-200 bg-white p-4 shadow-theme-xs sm:flex-row sm:items-end dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="flex-1">
            <label for="search" class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Cari</label>
            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Judul materi…"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
        </div>

        <div class="sm:w-40">
            <label for="type" class="block text-xs font-medium text-gray-700 mb-1 dark:text-gray-300">Tipe</label>
            <select id="type" name="type"
                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">Semua</option>
                @foreach (['banner' => 'Banner', 'brosur' => 'Brosur', 'video' => 'Video', 'template_wa' => 'Template WA'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <x-admin.button type="submit" size="sm">Filter</x-admin.button>

        @if (request('search') || request('type'))
            <x-admin.button href="{{ route('admin.materials.index') }}" variant="outline" size="sm">Reset</x-admin.button>
        @endif
    </form>

    {{-- Table --}}
    <x-admin.card>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Judul</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Tipe</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">File</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Dibuat</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($materials as $material)
                        <tr class="hover:bg-gray-50/50 dark:hover:bg-white/[0.02]">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white/90">{{ $material->title }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full bg-primary-50 px-2 py-0.5 text-xs font-medium text-primary-700 dark:bg-primary-500/15 dark:text-primary-400">
                                    {{ str_replace('_', ' ', ucfirst($material->type)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                                @if ($material->file_path)
                                    <a href="{{ asset($material->file_path) }}" target="_blank" class="text-primary-600 hover:underline dark:text-primary-400">Lihat</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $material->created_at->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.materials.edit', $material) }}" class="text-xs font-medium text-gray-600 hover:text-gray-800 dark:text-gray-400">Edit</a>
                                    <form method="POST" action="{{ route('admin.materials.destroy', $material) }}" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-rose-600 hover:text-rose-800 dark:text-rose-400">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada materi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($materials->hasPages())
            <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-700">
                {{ $materials->links() }}
            </div>
        @endif
    </x-admin.card>
@endsection
