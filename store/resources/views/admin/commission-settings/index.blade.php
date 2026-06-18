@extends('layouts.admin', ['active' => 'commissions'])

@section('title', 'Pengaturan Komisi')

@section('content')
    <x-admin.page-header
        title="Pengaturan Komisi"
        subtitle="Atur persentase komisi global dan minimum pencairan untuk affiliator.">
    </x-admin.page-header>

    @if (session('status'))
        <div class="mb-6">
            <x-admin.alert tone="success" dismissible>{{ session('status') }}</x-admin.alert>
        </div>
    @endif

    @forelse ($settings as $setting)
        <x-admin.card class="mb-6">
            <form method="POST" action="{{ route('admin.commission-settings.update', $setting) }}" class="p-5 space-y-5">
                @csrf
                @method('PUT')

                <h3 class="text-sm font-semibold text-gray-800 dark:text-white/90">
                    Scope: {{ ucfirst($setting->scope) }}
                </h3>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="rate_percent_{{ $setting->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Persentase Komisi (%)</label>
                        <input type="number" id="rate_percent_{{ $setting->id }}" name="rate_percent" value="{{ old('rate_percent', $setting->rate_percent) }}" required step="0.01" min="0" max="100"
                            class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @error('rate_percent')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="min_payout_{{ $setting->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Pencairan (Rp)</label>
                        <input type="number" id="min_payout_{{ $setting->id }}" name="min_payout" value="{{ old('min_payout', $setting->min_payout) }}" required step="1000" min="0"
                            class="mt-1 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @error('min_payout')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <x-admin.button type="submit" size="sm">Simpan</x-admin.button>
                </div>
            </form>
        </x-admin.card>
    @empty
        <x-admin.card>
            <div class="p-5 text-center text-gray-500 dark:text-gray-400">
                Belum ada pengaturan komisi. Jalankan seeder untuk membuat pengaturan default.
            </div>
        </x-admin.card>
    @endforelse
@endsection
