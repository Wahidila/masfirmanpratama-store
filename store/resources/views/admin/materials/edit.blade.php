@extends('layouts.admin', ['active' => 'materials'])

@section('title', 'Edit Materi - ' . $material->title)

@section('content')
    <x-admin.page-header
        title="Edit Materi"
        :subtitle="$material->title">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.materials.index') }}" size="sm" variant="outline">Kembali</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    <x-admin.card>
        <form method="POST" action="{{ route('admin.materials.update', $material) }}" enctype="multipart/form-data" class="p-5 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
                <input type="text" id="title" name="title" value="{{ old('title', $material->title) }}" required
                    class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('description', $material->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipe</label>
                <select id="type" name="type" required
                    class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach (['banner' => 'Banner', 'brosur' => 'Brosur', 'video' => 'Video', 'template_wa' => 'Template WA'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('type', $material->type) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="file" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File (opsional — kosongkan jika tidak ingin ganti)</label>
                <input type="file" id="file" name="file" accept=".pdf,.zip,.png,.jpg,.jpeg,.gif"
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 file:mr-3 file:rounded-md file:border-0 file:bg-primary-50 file:px-3 file:py-1 file:text-xs file:font-medium file:text-primary-700 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: PDF, ZIP, PNG, JPG, JPEG, GIF. Maksimal 10MB.</p>
                @if ($material->file_path)
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">File saat ini: <a href="{{ asset($material->file_path) }}" target="_blank" class="text-primary-600 hover:underline dark:text-primary-400">{{ basename($material->file_path) }}</a></p>
                @endif
                @error('file')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-admin.button type="submit" size="sm">Simpan Perubahan</x-admin.button>
            </div>
        </form>
    </x-admin.card>
@endsection
