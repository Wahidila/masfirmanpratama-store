@props([
    'product',
    'related' => [],
])

@php
    /** @var array $product */
    /** @var array $related */
    $title = $product['title'] ?? 'Buku';
    $subtitle = $product['subtitle'] ?? '';
    $tagline = $product['tagline'] ?? null;
    $price = (int) ($product['price'] ?? 0);
    $originalPrice = $product['original_price'] ?? null;
    $hasDiscount = $originalPrice && (int) $originalPrice > $price;
    $discountPercent = $hasDiscount ? (int) round((((int) $originalPrice - $price) / (int) $originalPrice) * 100) : 0;
    $formattedPrice = 'Rp' . number_format($price, 0, ',', '.');
    $formattedOriginal = $originalPrice ? 'Rp' . number_format((int) $originalPrice, 0, ',', '.') : null;
    $image = $product['image'] ?? null;
    $imageAlt = $product['image_alt'] ?? ('Cover buku ' . $title);
    $badge = $product['badge'] ?? null;
    $badgeIcon = $product['badge_icon'] ?? 'star';
    $categoryLabel = $product['category_label'] ?? 'Buku';
    $description = $product['description'] ?? [];
    if (is_string($description)) {
        $description = [$description];
    }
    $specs = $product['specs'] ?? [];
    $previewPages = $product['preview_pages'] ?? [];
    $ctaLabel = $product['cta_label'] ?? 'Pesan Buku Sekarang';

    // Alpine x-data payload — escape via Js::from supaya aman dari quote/HTML.
    $alpineProduct = [
        'slug' => $product['slug'] ?? '',
        'title' => $title,
        'price' => $price,
        'image' => $image,
        'category_label' => $categoryLabel,
        'is_shippable' => true, // buku fisik — butuh pengiriman
    ];
@endphp

<x-layouts.store
    :title="$title . ' | Buku Firman Pratama'"
    :description="\Illuminate\Support\Str::limit($subtitle, 160)"
    :ogImage="$image"
    ogType="product"
    bodyClass="pb-24 lg:pb-0"
>
    {{-- Structured data: Book schema --}}
    <x-slot name="head">
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $title,
            'author' => [
                '@type' => 'Person',
                'name' => $specs['penulis'] ?? 'Firman Pratama',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => $specs['penerbit'] ?? 'Wahana Sejati',
            ],
            'inLanguage' => 'id',
            'description' => $subtitle,
            'image' => $image ? asset($image) : null,
            'offers' => [
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => 'IDR',
                'availability' => 'https://schema.org/InStock',
                'url' => url()->current(),
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    </x-slot>

    <section class="pt-10 pb-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative overflow-hidden">

        {{-- Breadcrumbs --}}
        <nav
            aria-label="Breadcrumb"
            class="flex flex-wrap text-sm text-slate-500 mb-8 items-center gap-2 relative z-10 w-fit bg-white/50 px-4 py-2 rounded-full border border-slate-100 backdrop-blur-sm"
        >
            <a
                href="{{ route('home') }}"
                class="hover:text-primary-600 transition-colors font-medium flex items-center gap-1"
            >
                <i data-lucide="home" class="w-4 h-4"></i>
                Beranda
            </a>
            <i data-lucide="chevron-right" class="w-4 h-4" aria-hidden="true"></i>
            <a
                href="{{ route('products.index') }}"
                class="hover:text-primary-600 transition-colors font-medium"
            >
                Karya Buku
            </a>
            <i data-lucide="chevron-right" class="w-4 h-4" aria-hidden="true"></i>
            <span class="text-primary-600 font-bold line-clamp-1">{{ $title }}</span>
        </nav>

        {{-- Decorative gradient blobs --}}
        <div
            class="pointer-events-none absolute -top-24 -right-24 w-96 h-96 bg-gradient-to-tr from-accent-500/20 to-primary-500/20 rounded-full blur-3xl mix-blend-multiply"
            aria-hidden="true"
        ></div>
        <div
            class="pointer-events-none absolute top-1/2 -left-24 w-80 h-80 bg-gradient-to-tr from-primary-500/15 to-secondary-500/15 rounded-full blur-3xl mix-blend-multiply"
            aria-hidden="true"
        ></div>

        <div
            x-data="bookDetailPage({{ \Illuminate\Support\Js::from($alpineProduct) }})"
            class="grid grid-cols-1 lg:grid-cols-12 gap-12 relative z-10"
        >

            {{-- LEFT: Cover + preview gallery --}}
            <div class="lg:col-span-5 relative">
                <div class="lg:sticky lg:top-28 flex flex-col gap-6">

                    {{-- Hero cover --}}
                    <div class="group bg-slate-100/50 p-8 sm:p-12 rounded-[2.5rem] border border-slate-100 flex items-center justify-center shadow-inner overflow-hidden img-zoom-container relative">
                        <div
                            class="absolute inset-0 bg-gradient-to-tr from-accent-500/20 to-primary-500/20 mix-blend-multiply blur-2xl"
                            aria-hidden="true"
                        ></div>

                        @if ($image)
                            <img
                                src="{{ asset($image) }}"
                                alt="{{ $imageAlt }}"
                                width="600"
                                height="800"
                                loading="eager"
                                fetchpriority="high"
                                decoding="async"
                                class="w-full h-auto max-h-[500px] object-contain img-zoom group-hover:scale-105 transition-transform duration-500 drop-shadow-2xl relative z-10"
                            >
                        @else
                            <div class="w-full aspect-[3/4] max-h-[500px] flex flex-col items-center justify-center text-slate-300 relative z-10">
                                <i data-lucide="book" class="w-24 h-24 mb-3"></i>
                                <span class="text-sm font-semibold uppercase tracking-wider">Cover Coming Soon</span>
                            </div>
                        @endif
                    </div>

                    {{-- Preview pages gallery --}}
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                        <h3 class="text-sm font-extrabold text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <i data-lucide="images" class="w-4 h-4 text-primary-500"></i>
                            Preview Halaman
                        </h3>

                        @if (! empty($previewPages))
                            <div class="grid grid-cols-3 gap-3">
                                @foreach ($previewPages as $i => $page)
                                    <button
                                        type="button"
                                        class="aspect-[3/4] rounded-xl overflow-hidden border border-slate-100 hover:border-primary-300 transition-colors img-zoom-container group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                        aria-label="Lihat preview halaman {{ $i + 1 }}"
                                    >
                                        <img
                                            src="{{ asset($page) }}"
                                            alt="Preview halaman {{ $i + 1 }}"
                                            loading="lazy"
                                            class="w-full h-full object-cover img-zoom"
                                        >
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="grid grid-cols-3 gap-3">
                                @for ($i = 0; $i < 3; $i++)
                                    <div
                                        class="aspect-[3/4] rounded-xl bg-slate-50 border border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-300"
                                        aria-label="Preview belum tersedia"
                                    >
                                        <i data-lucide="image-off" class="w-6 h-6 mb-1"></i>
                                        <span class="text-[10px] font-semibold">Soon</span>
                                    </div>
                                @endfor
                            </div>
                            <p class="mt-4 text-xs text-slate-500">
                                Preview halaman akan tersedia setelah proses scanning selesai.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT: Details + checkout --}}
            <div class="lg:col-span-7 flex flex-col gap-10">

                {{-- Title --}}
                <div>
                    @if ($badge)
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold uppercase tracking-wider mb-4 border border-amber-100 shadow-sm">
                            <i data-lucide="{{ $badgeIcon }}" class="w-4 h-4"></i>
                            {{ $badge }}
                        </div>
                    @endif

                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 mb-6 leading-tight">
                        {{ $title }}
                    </h1>

                    @if ($subtitle)
                        <p class="text-lg md:text-xl text-slate-600 leading-relaxed font-medium">
                            {{ $subtitle }}
                        </p>
                    @endif
                </div>

                {{-- Description (multi-paragraph) --}}
                @if (! empty($description))
                    <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4 flex items-center gap-3">
                            <i data-lucide="book-open" class="w-6 h-6 text-primary-500"></i>
                            Tentang Buku Ini
                        </h2>
                        <div class="space-y-4 text-slate-600 leading-relaxed">
                            @foreach ($description as $paragraph)
                                <p>{{ $paragraph }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Specifications --}}
                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <h2 class="text-2xl font-bold text-slate-900 mb-6 border-b border-slate-100 pb-4 flex items-center gap-3">
                        <i data-lucide="info" class="w-6 h-6 text-primary-500"></i>
                        Spesifikasi Buku
                    </h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-8 text-sm text-slate-600">
                        @forelse ($specs as $label => $value)
                            <div class="flex justify-between items-center py-2 border-b border-slate-50 gap-4">
                                <dt class="text-slate-500 capitalize">{{ str_replace('_', ' ', $label) }}</dt>
                                <dd class="font-bold text-slate-800 text-right">{{ $value }}</dd>
                            </div>
                        @empty
                            <div class="text-slate-500 italic col-span-full">Spesifikasi belum tersedia.</div>
                        @endforelse
                    </dl>
                </div>

                {{-- Checkout interactive card --}}
                <div class="bg-white p-8 md:p-10 rounded-[2.5rem] border border-slate-100 shadow-2xl shadow-slate-200/50 hover-lift relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-primary-50 rounded-bl-full -z-0 mix-blend-multiply opacity-50"
                        aria-hidden="true"
                    ></div>

                    <div class="mb-6 relative z-10">
                        <p class="text-slate-500 font-medium mb-1 flex items-center gap-2">
                            Harga Spesial
                            @if ($hasDiscount)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-rose-50 text-rose-600 text-xs font-extrabold border border-rose-100">
                                    -{{ $discountPercent }}%
                                </span>
                            @endif
                        </p>
                        <div class="flex items-end gap-3 flex-wrap">
                            <div class="text-4xl lg:text-5xl font-extrabold text-slate-900 leading-none">
                                {{ $formattedPrice }}
                            </div>
                            @if ($formattedOriginal)
                                <span class="text-slate-500 line-through font-medium text-lg mb-1">
                                    {{ $formattedOriginal }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <p class="text-sm text-slate-500 mb-6 font-medium bg-slate-50 p-3 rounded-xl border border-slate-100 flex gap-2 items-center relative z-10">
                        <i data-lucide="truck" class="w-4 h-4 text-primary-500 shrink-0"></i>
                        Dikirim ke seluruh Indonesia (belum termasuk ongkir).
                    </p>

                    {{-- Quantity selector --}}
                    <div class="mb-6 flex items-center gap-4 relative z-10">
                        <span class="text-sm font-semibold text-slate-700">Jumlah</span>
                        <div class="inline-flex items-stretch rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                            <button
                                type="button"
                                @click="decreaseQty()"
                                class="inline-flex h-11 w-11 items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                aria-label="Kurangi jumlah"
                            >
                                <i data-lucide="minus" class="w-4 h-4"></i>
                            </button>
                            <input
                                type="number"
                                min="1"
                                inputmode="numeric"
                                x-model.number="qty"
                                @input="qty = Math.max(1, parseInt($event.target.value, 10) || 1)"
                                class="h-11 w-14 text-center border-x border-slate-200 font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset bg-white"
                                aria-label="Jumlah"
                            >
                            <button
                                type="button"
                                @click="increaseQty()"
                                class="inline-flex h-11 w-11 items-center justify-center text-slate-600 hover:bg-slate-50 hover:text-primary-600 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                                aria-label="Tambah jumlah"
                            >
                                <i data-lucide="plus" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Add to cart --}}
                    <button
                        type="button"
                        @click="addToCart()"
                        class="ripple block w-full text-center bg-primary-600 hover:bg-primary-700 text-white rounded-2xl py-4 font-extrabold text-lg transition-all shadow-[0_10px_30px_-5px_rgba(79,70,229,0.4)] hover:shadow-[0_15px_30px_-5px_rgba(79,70,229,0.5)] transform hover:-translate-y-1 mb-3 relative z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                        data-testid="add-to-cart"
                    >
                        <span class="inline-flex items-center justify-center gap-2">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                            <span x-text="justAdded ? 'Ditambahkan ke keranjang ✓' : '{{ $ctaLabel }}'"></span>
                        </span>
                    </button>

                    {{-- Buy now --}}
                    <a
                        href="{{ route('checkout.index') }}"
                        @click="addToCart({ silent: true })"
                        class="block w-full text-center bg-white border border-slate-200 hover:border-primary-300 hover:text-primary-600 text-slate-700 rounded-2xl py-3 font-semibold transition-all shadow-sm mb-4 relative z-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                    >
                        <span class="inline-flex items-center justify-center gap-2">
                            <i data-lucide="zap" class="w-4 h-4"></i>
                            Beli Sekarang
                        </span>
                    </a>

                    <div class="flex gap-4 relative z-10">
                        <button
                            type="button"
                            class="w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl py-3 font-semibold transition-all shadow-sm flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            aria-label="Tambahkan ke favorit"
                        >
                            <i data-lucide="heart" class="w-4 h-4"></i> Favorit
                        </button>
                        <button
                            type="button"
                            @click="shareProduct()"
                            class="w-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl py-3 font-semibold transition-all shadow-sm flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                            aria-label="Bagikan produk"
                        >
                            <i data-lucide="share-2" class="w-4 h-4"></i> Bagikan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Related books --}}
        @if (! empty($related))
            <div class="mt-24 relative z-10">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
                    <div>
                        <p class="text-xs tracking-[0.2em] font-extrabold text-accent-600 uppercase mb-2">Buku & Karya Terkait</p>
                        <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight">
                            Karya Lain dari <span class="text-gradient">Firman Pratama</span>
                        </h2>
                    </div>
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-semibold"
                    >
                        Lihat semua karya
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($related as $rel)
                        <x-product-card
                            :title="$rel['title']"
                            :price="$rel['price']"
                            :originalPrice="$rel['original_price'] ?? null"
                            :image="! empty($rel['image']) ? asset($rel['image']) : null"
                            :imageAlt="$rel['image_alt'] ?? ('Cover ' . $rel['title'])"
                            :category="$rel['category_label'] ?? null"
                            categoryVariant="category"
                            :badge="$rel['badge'] ?? null"
                            :href="route('products.show', $rel['slug'])"
                        />
                    @endforeach
                </div>
            </div>
        @endif
    </section>

    {{-- ─── Sticky CTA bar (mobile only) ──────────────────────────────── --}}
    <div
        x-data="{
            visible: false,
            justAdded: false,
            _t: null,
            product: {{ \Illuminate\Support\Js::from($alpineProduct) }},
            addToCartQuick(redirect = false) {
                const store = this.$store && this.$store.cart;
                if (store && typeof store.add === 'function') {
                    store.add(this.product, 1);
                }
                window.dispatchEvent(new CustomEvent('cart:add', {
                    detail: { product: this.product, qty: 1 },
                }));
                if (redirect) {
                    window.location.href = '{{ route('checkout.index') }}';
                    return;
                }
                this.justAdded = true;
                clearTimeout(this._t);
                this._t = setTimeout(() => { this.justAdded = false; }, 1800);
            },
        }"
        x-init="window.addEventListener('scroll', () => visible = window.scrollY > 400)"
        x-show="visible"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-cloak
        class="lg:hidden fixed inset-x-0 bottom-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-[0_-10px_30px_-15px_rgba(15,23,42,0.2)]"
        role="region"
        aria-label="Pesan buku"
    >
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center gap-3">
            <div class="flex-1 min-w-0">
                <p class="text-[11px] text-slate-500 font-medium leading-none mb-1">Harga</p>
                <div class="flex items-baseline gap-2">
                    <span class="text-lg font-extrabold text-slate-900 leading-none">{{ $formattedPrice }}</span>
                    @if ($hasDiscount)
                        <span class="text-xs text-slate-500 line-through">{{ $formattedOriginal }}</span>
                    @endif
                </div>
            </div>
            <button
                type="button"
                @click="addToCartQuick(false)"
                class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-700 transition hover:border-primary-300 hover:text-primary-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500"
                :aria-label="justAdded ? 'Ditambahkan ke keranjang' : 'Tambah ke keranjang'"
            >
                <i :data-lucide="justAdded ? 'check' : 'shopping-cart'" class="w-5 h-5"></i>
            </button>
            <button
                type="button"
                @click="addToCartQuick(true)"
                class="ripple inline-flex min-h-[44px] shrink-0 items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white rounded-full px-5 py-3 font-bold text-sm shadow-lg shadow-primary-500/30 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-300"
            >
                Beli Sekarang
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    {{-- Page-scoped Alpine component --}}
    <x-slot name="scripts">
        <script>
            window.bookDetailPage = function (product) {
                return {
                    qty: 1,
                    justAdded: false,
                    _resetTimer: null,

                    increaseQty() {
                        this.qty = (parseInt(this.qty, 10) || 1) + 1;
                    },

                    decreaseQty() {
                        this.qty = Math.max(1, (parseInt(this.qty, 10) || 1) - 1);
                    },

                    addToCart({ silent = false } = {}) {
                        const store = this.$store && this.$store.cart;
                        if (store && typeof store.add === 'function') {
                            store.add(product, this.qty);
                        }
                        // Always emit event so other listeners (mini-cart, debug) can hook in.
                        window.dispatchEvent(new CustomEvent('cart:add', {
                            detail: { product: product, qty: this.qty },
                        }));

                        if (silent) return;

                        this.justAdded = true;
                        clearTimeout(this._resetTimer);
                        this._resetTimer = setTimeout(() => {
                            this.justAdded = false;
                        }, 1800);
                    },

                    async shareProduct() {
                        const url = window.location.href;
                        const title = product.title;
                        if (navigator.share) {
                            try {
                                await navigator.share({ title, url });
                                return;
                            } catch (e) {
                                // user cancel — fall through ke clipboard
                            }
                        }
                        try {
                            await navigator.clipboard.writeText(url);
                            alert('Link produk disalin ke clipboard.');
                        } catch (e) {
                            window.prompt('Salin link produk:', url);
                        }
                    },
                };
            };
        </script>
    </x-slot>
</x-layouts.store>
