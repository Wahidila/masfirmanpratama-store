@props([
    'eyebrow' => 'DILIPUT OLEH BERBAGAI MEDIA TERKEMUKA DI INDONESIA',
    'logos' => null,
])

@php
    $defaultLogos = [
        ['src' => 'assets/images/sindonews.webp', 'alt' => 'SindoNews'],
        ['src' => 'assets/images/tribunnews.webp', 'alt' => 'TribunNews'],
        ['src' => 'assets/images/merdeka.webp', 'alt' => 'Merdeka.com'],
        ['src' => 'assets/images/radarsurabaya.webp', 'alt' => 'Radar Surabaya', 'hideOnSm' => true],
        ['src' => 'assets/images/duta.co.webp', 'alt' => 'Duta Nusantara', 'hideOnMd' => true],
    ];

    $logoList = $logos ?? $defaultLogos;
@endphp

<section
    {{ $attributes->merge([
        'class' => 'py-12 border-b border-slate-200 bg-white',
    ]) }}
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($eyebrow)
            <p class="text-center text-sm font-bold text-slate-400 tracking-widest uppercase mb-8">
                {{ $eyebrow }}
            </p>
        @endif

        <div class="flex flex-wrap justify-center items-center gap-8 md:gap-12 lg:gap-16 opacity-60 hover:opacity-100 transition-opacity duration-300 grayscale hover:grayscale-0">
            @foreach ($logoList as $logo)
                @php
                    $hidden = '';
                    if (! empty($logo['hideOnSm'])) {
                        $hidden = 'hidden sm:block';
                    } elseif (! empty($logo['hideOnMd'])) {
                        $hidden = 'hidden md:block';
                    }
                @endphp
                <img
                    src="{{ $logo['src'] }}"
                    alt="{{ $logo['alt'] }}"
                    loading="lazy"
                    class="h-8 md:h-10 lg:h-12 w-auto object-contain cursor-pointer hover:scale-105 transition-transform {{ $hidden }}"
                >
            @endforeach
        </div>
    </div>
</section>
