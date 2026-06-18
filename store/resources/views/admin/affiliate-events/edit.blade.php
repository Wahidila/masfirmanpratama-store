@extends('layouts.admin', ['active' => 'affiliate-events'])

@section('title', 'Edit Event - ' . $affiliateEvent->title)

@section('content')
    <x-admin.page-header
        title="Edit Event"
        :subtitle="$affiliateEvent->title">
        <x-slot name="actions">
            <x-admin.button href="{{ route('admin.affiliate-events.index') }}" size="sm" variant="outline">Kembali</x-admin.button>
        </x-slot>
    </x-admin.page-header>

    <x-admin.card>
        <form method="POST" action="{{ route('admin.affiliate-events.update', $affiliateEvent) }}" class="p-5 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Judul</label>
                <input type="text" id="title" name="title" value="{{ old('title', $affiliateEvent->title) }}" required
                    class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                <textarea id="description" name="description" rows="4"
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('description', $affiliateEvent->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Mulai</label>
                    <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at', $affiliateEvent->starts_at->format('Y-m-d\TH:i')) }}" required
                        class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @error('starts_at')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Selesai</label>
                    <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at', $affiliateEvent->ends_at->format('Y-m-d\TH:i')) }}" required
                        class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @error('ends_at')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="reward_note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Catatan Reward</label>
                <textarea id="reward_note" name="reward_note" rows="2"
                    class="mt-1 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ old('reward_note', $affiliateEvent->reward_note) }}</textarea>
                @error('reward_note')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                <select id="status" name="status" required
                    class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    @foreach (['draft' => 'Draft', 'active' => 'Aktif', 'ended' => 'Selesai'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $affiliateEvent->status) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-admin.button type="submit" size="sm">Simpan Perubahan</x-admin.button>
            </div>
        </form>
    </x-admin.card>
@endsection
