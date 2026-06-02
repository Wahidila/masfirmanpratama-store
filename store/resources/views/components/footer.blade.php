@props([
    'brandText' => 'Firman',
    'brandAccent' => 'Pratama',
    'tagline' => 'Pakar Pikiran No.1 Indonesia. Penulis Buku, Konsultan Bisnis & Pencipta Metode AMC.',
    'address' => 'Wahana Sejati, Jakarta - Surabaya HQ',
    'phone' => '081.2306.33.464',
    'email' => 'admin@masfirmanpratama.com',
    'socials' => null,
    'sitemap' => null,
])

@php
    $defaultSocials = [
        ['icon' => 'facebook', 'href' => 'https://facebook.com/wahanasejati', 'label' => 'Facebook'],
        ['icon' => 'youtube', 'href' => 'https://youtube.com/@CahayaKehidupan', 'label' => 'YouTube'],
        ['icon' => 'instagram', 'href' => 'https://instagram.com/firmanpratama_pakarpikiran', 'label' => 'Instagram'],
    ];

    $defaultSitemap = [
        'Layanan' => [
            ['label' => 'Kelas Biasa AMC', 'href' => url('/produk?kategori=kelas')],
            ['label' => 'Kelas Privat AMC', 'href' => url('/produk?kategori=privat')],
            ['label' => 'Kelas Platinum', 'href' => url('/produk?kategori=platinum')],
            ['label' => 'Pembelian Karya', 'href' => url('/produk?kategori=buku')],
        ],
        'Komunitas' => [
            ['label' => 'Profil Pribadi', 'href' => route('pages.tentang')],
            ['label' => 'Testimoni Alumni', 'href' => url('/#testimoni')],
            ['label' => 'Artikel Keajaiban', 'href' => url('/blog')],
            ['label' => 'Afiliasi Program', 'href' => 'https://affiliate.masfirmanpratama.com'],
        ],
    ];

    $socialLinks = $socials ?? $defaultSocials;
    $sitemapLinks = $sitemap ?? $defaultSitemap;
    $year = now()->year;
@endphp

<footer
    {{ $attributes->merge([
        'class' => 'bg-slate-950 text-slate-300 pt-16 pb-10 border-t border-slate-800 mt-20',
    ]) }}
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-2 mb-6">
                    <span class="w-9 h-9 bg-primary-600 rounded-lg flex items-center justify-center text-white">
                        <i data-lucide="brain-circuit" class="w-5 h-5"></i>
                    </span>
                    <span class="font-bold text-xl text-white">
                        {{ $brandText }}<span class="text-primary-500">{{ $brandAccent }}</span>
                    </span>
                </div>
                <p class="text-sm mb-6 text-slate-300 leading-relaxed">{{ $tagline }}</p>

                <div class="flex gap-3">
                    @foreach ($socialLinks as $social)
                        <a
                            href="{{ $social['href'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            aria-label="{{ $social['label'] ?? ucfirst($social['icon']) }}"
                            class="w-10 h-10 rounded-full bg-slate-800 hover:bg-primary-600 text-slate-300 hover:text-white flex items-center justify-center transition-colors"
                        >
                            <i data-lucide="{{ $social['icon'] }}" class="w-5 h-5"></i>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Sitemap groups --}}
            @foreach ($sitemapLinks as $heading => $items)
                <div>
                    <h3 class="text-white font-bold mb-6">{{ $heading }}</h3>
                    <ul class="space-y-3 text-sm">
                        @foreach ($items as $item)
                            <li>
                                <a
                                    href="{{ $item['href'] }}"
                                    class="hover:text-primary-400 transition-colors"
                                >
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach

            {{-- Contact --}}
            <div>
                <h3 class="text-white font-bold mb-6">Pusat Layanan</h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="w-5 h-5 text-primary-500 flex-shrink-0 mt-0.5"></i>
                        <span>{{ $address }}</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="phone" class="w-5 h-5 text-primary-500 flex-shrink-0"></i>
                        <a href="tel:{{ preg_replace('/\D+/', '', $phone) }}" class="hover:text-white transition-colors">{{ $phone }}</a>
                    </li>
                    <li class="flex items-center gap-3">
                        <i data-lucide="mail" class="w-5 h-5 text-primary-500 flex-shrink-0"></i>
                        <a href="mailto:{{ $email }}" class="hover:text-white transition-colors break-all">{{ $email }}</a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm">
            <p>&copy; {{ $year }} {{ $brandText }} {{ $brandAccent }} - AMC. All rights reserved.</p>
            <div class="flex gap-6">
                <a href="{{ url('/privacy') }}" class="hover:text-white transition-colors">Kebijakan Privasi</a>
                <a href="{{ url('/terms') }}" class="hover:text-white transition-colors">Syarat &amp; Ketentuan</a>
            </div>
        </div>
    </div>
</footer>
