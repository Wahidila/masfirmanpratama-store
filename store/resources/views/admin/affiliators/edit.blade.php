@extends('layouts.admin', ['active' => 'affiliators'])

@section('title', 'Edit Affiliator - ' . $affiliator->name)

@section('content')
    <x-admin.page-header
        title="Edit Affiliator"
        :subtitle="$affiliator->name">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.affiliators.index') }}" size="sm" variant="outline">Kembali</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    <x-admin.card>
        <form method="POST" action="{{ route('admin.affiliators.update', $affiliator) }}" class="p-5 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select id="status" name="status"
                    class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach (['pending' => 'Pending', 'active' => 'Aktif', 'suspended' => 'Suspended'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $affiliator->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</label>
                <select id="type" name="type"
                    class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach (['alumni' => 'Alumni', 'non_alumni' => 'Non-Alumni', 'peserta' => 'Peserta'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $affiliator->type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-admin.button type="submit" size="sm">Simpan Perubahan</x-admin.button>
            </div>
        </form>
    </x-admin.card>

    {{-- Danger zone --}}
    <x-admin.card class="mt-6">
        <div class="p-5">
            <h3 class="text-sm font-semibold text-rose-700 dark:text-rose-400">Zona Berbahaya</h3>
            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Hapus affiliator ini (soft delete). Data masih tersimpan di database.</p>
            <form method="POST" action="{{ route('admin.affiliators.destroy', $affiliator) }}" class="mt-3"
                onsubmit="return confirm('Yakin ingin menghapus affiliator ini?')">
                @csrf
                @method('DELETE')
                <x-admin.button type="submit" size="sm" variant="outline" class="border-rose-300 text-rose-600 hover:bg-rose-50 dark:border-rose-700 dark:text-rose-400">Hapus Affiliator</x-admin.button>
            </form>
        </div>
    </x-admin.card>
@endsection
