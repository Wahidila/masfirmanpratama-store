@props([
    'cartCount' => 0,
    'logoText' => 'Firman',
    'logoAccent' => 'Pratama',
    'ctaLabel' => 'Konsultasi',
    'ctaHref' => 'https://wa.me/6281230633464',
])

<nav
    x-data="{ open: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => scrolled = window.scrollY > 8)"
    :class="scrolled ? 'shadow-md' : ''"
    {{ $attributes->merge([
        'class' => 'fixed inset-x-0 top-0 z-50 glass border-b border-slate-100 transition-shadow duration-300',
    ]) }}
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2 cursor-pointer">
                <span class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-primary-500/30">
                    <i data-lucide="brain-circuit" class="w-6 h-6"></i>
                </span>
                <span class="font-bold text-xl sm:text-2xl tracking-tight text-slate-900">
                    {{ $logoText }}<span class="text-primary-600">{{ $logoAccent }}</span>
                </span>
            </a>

            {{-- Desktop nav --}}
            <div class="hidden md:flex items-center space-x-8">
                <a href="{{ url('/') }}" class="text-slate-600 hover:text-primary-600 font-medium transition-colors">Beranda</a>
                <a href="{{ url('/produk') }}" class="text-slate-600 hover:text-primary-600 font-medium transition-colors">Produk</a>
                <a href="{{ url('/tentang') }}" class="text-slate-600 hover:text-primary-600 font-medium transition-colors">Tentang</a>
                <a href="{{ url('/kontak') }}" class="text-slate-600 hover:text-primary-600 font-medium transition-colors">Kontak</a>

                <span class="h-6 w-px bg-slate-200" aria-hidden="true"></span>

                {{-- Cart icon --}}
                <a
                    href="{{ url('/keranjang') }}"
                    class="relative inline-flex items-center justify-center w-10 h-10 rounded-full text-slate-600 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                    aria-label="Keranjang belanja"
                >
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1 rounded-full bg-accent-500 text-white text-[10px] font-bold ring-2 ring-white">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>

                <x-button :href="$ctaHref" size="sm" icon="message-circle" iconPosition="left">
                    {{ $ctaLabel }}
                </x-button>
            </div>

            {{-- Mobile toggle --}}
            <div class="md:hidden flex items-center gap-2">
                <a
                    href="{{ url('/keranjang') }}"
                    class="relative inline-flex items-center justify-center w-10 h-10 rounded-full text-slate-600 hover:text-primary-600 hover:bg-primary-50 transition-colors"
                    aria-label="Keranjang belanja"
                >
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 inline-flex items-center justify-center min-w-[20px] h-5 px-1 rounded-full bg-accent-500 text-white text-[10px] font-bold ring-2 ring-white">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>
                <button
                    type="button"
                    @click="open = !open"
                    :aria-expanded="open"
                    aria-controls="mobile-nav"
                    class="text-slate-600 hover:text-primary-600 focus:outline-none p-2 rounded-lg hover:bg-slate-100 transition-colors"
                    aria-label="Buka menu navigasi"
                >
                    <i data-lucide="menu" class="w-6 h-6" x-show="!open"></i>
                    <i data-lucide="x" class="w-6 h-6" x-show="open" x-cloak></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div
        id="mobile-nav"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        class="md:hidden bg-white border-t border-slate-100 shadow-xl"
    >
        <div class="px-4 pt-3 pb-6 space-y-1">
            <a href="{{ url('/') }}" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Beranda</a>
            <a href="{{ url('/produk') }}" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Produk</a>
            <a href="{{ url('/tentang') }}" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Tentang</a>
            <a href="{{ url('/kontak') }}" class="block px-3 py-3 rounded-md text-base font-medium text-slate-700 hover:text-primary-600 hover:bg-primary-50 transition-colors">Kontak</a>

            <div class="pt-2">
                <x-button :href="$ctaHref" size="md" icon="message-circle" iconPosition="left" class="w-full">
                    {{ $ctaLabel }}
                </x-button>
            </div>
        </div>
    </div>
</nav>
