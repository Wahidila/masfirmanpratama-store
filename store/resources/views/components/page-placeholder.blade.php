@props([
    'title' => 'Halaman Sedang Disiapkan',
    'description' => 'Halaman ini sedang dalam pengembangan.',
    'icon' => 'construction',
    'message' => 'Halaman ini sedang disiapkan oleh tim Malang Creative Agency.',
    'ctaHref' => '/',
    'ctaLabel' => 'Kembali ke Beranda',
])

<x-layouts.store :title="$title" :description="$description">
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-primary-50 text-primary-600 mb-6">
            <i data-lucide="{{ $icon }}" class="w-10 h-10"></i>
        </div>

        <p class="text-xs tracking-[0.2em] font-extrabold text-accent-600 uppercase mb-3">Coming Soon</p>
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">
            {{ $title }}
        </h1>
        <p class="mt-5 text-lg text-slate-600 max-w-xl mx-auto">{{ $message }}</p>

        @if (! empty($slot) && trim($slot) !== '')
            <div class="mt-8 text-left bg-white border border-slate-100 rounded-2xl p-6 shadow-sm">
                {{ $slot }}
            </div>
        @endif

        <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
            <x-button :href="$ctaHref" variant="primary" icon="arrow-right">{{ $ctaLabel }}</x-button>
            <x-button :href="url('/produk')" variant="outline" icon="shopping-bag" iconPosition="left">Lihat Produk</x-button>
        </div>
    </section>
</x-layouts.store>
