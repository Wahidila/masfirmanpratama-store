<x-layouts.store
    title="Katalog Produk — Firman Pratama"
    description="Kelas Alpha Mind Control (AMC) dan buku-buku karya Mas Firman Pratama. Mind Power & Life Mastery untuk transformasi hidup yang nyata."
>
    <section class="bg-gradient-to-b from-white to-slate-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16 text-center">
            <p class="text-xs tracking-[0.2em] font-extrabold text-accent-600 uppercase mb-3">Katalog</p>
            <h1 class="text-3xl md:text-5xl font-extrabold text-slate-900 leading-tight">
                Kelas &amp; Buku <span class="text-gradient">Alpha Mind Control</span>
            </h1>
            <p class="mt-5 text-base md:text-lg text-slate-600 max-w-2xl mx-auto">
                Pilih jalur transformasi Anda — ikut kelas langsung bersama Mas Firman atau pelajari secara otodidak lewat karya buku bestseller.
            </p>
        </div>
    </section>

    <section
        x-data="productCatalog()"
        x-init="init()"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16"
    >
        {{-- Filter + search bar --}}
        <div class="mb-10 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            {{-- Filter pills --}}
            <div
                role="tablist"
                aria-label="Filter kategori produk"
                class="inline-flex items-center gap-2 p-1.5 bg-white border border-slate-200 rounded-full shadow-sm self-start"
            >
                <template x-for="opt in filters" :key="opt.value">
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="filter === opt.value"
                        :tabindex="filter === opt.value ? 0 : -1"
                        @click="setFilter(opt.value)"
                        :class="filter === opt.value
                            ? 'bg-primary-600 text-white shadow-sm'
                            : 'text-slate-600 hover:text-primary-700 hover:bg-primary-50'"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2"
                    >
                        <i :data-lucide="opt.icon" class="w-4 h-4"></i>
                        <span x-text="opt.label"></span>
                        <span
                            x-show="opt.value !== 'all'"
                            class="text-xs font-bold opacity-70"
                            x-text="`(${counts[opt.value] ?? 0})`"
                        ></span>
                    </button>
                </template>
            </div>

            {{-- Search --}}
            <div class="relative md:w-80">
                <label for="product-search" class="sr-only">Cari produk</label>
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"></i>
                <input
                    id="product-search"
                    type="search"
                    x-model="searchInput"
                    @input.debounce.300ms="search = searchInput.trim().toLowerCase()"
                    placeholder="Cari kelas atau buku…"
                    autocomplete="off"
                    class="w-full pl-10 pr-10 py-2.5 bg-white border border-slate-200 rounded-full text-sm text-slate-700 placeholder:text-slate-500 shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                >
                <button
                    type="button"
                    x-show="searchInput.length > 0"
                    @click="searchInput = ''; search = ''"
                    class="absolute right-3 top-1/2 -translate-y-1/2 inline-flex items-center justify-center w-6 h-6 rounded-full text-slate-500 hover:text-slate-600 hover:bg-slate-100 transition"
                    aria-label="Hapus pencarian"
                >
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>

        {{-- Result counter (live region for a11y) --}}
        <div
            class="mb-6 text-sm text-slate-500"
            aria-live="polite"
            aria-atomic="true"
        >
            <span x-show="visibleCount > 0">
                Menampilkan <span class="font-semibold text-slate-700" x-text="visibleCount"></span>
                dari {{ count($products) }} produk
                <template x-if="search">
                    <span> untuk "<span class="font-semibold text-slate-700" x-text="search"></span>"</span>
                </template>
            </span>
            <span x-show="visibleCount === 0">Tidak ada produk yang cocok dengan filter.</span>
        </div>

        {{-- Product grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @foreach ($products as $product)
                <div
                    x-show="matches({{ \Illuminate\Support\Js::from([
                        'type' => $product['type'],
                        'name' => mb_strtolower($product['name']),
                    ]) }})"
                    x-transition.opacity.duration.200ms
                >
                    <x-product-card
                        :title="$product['name']"
                        :price="$product['price']"
                        :image="$product['image']"
                        :imageAlt="$product['name']"
                        :category="$product['type'] === 'kelas' ? 'Kelas' : 'Buku'"
                        :categoryVariant="$product['type'] === 'kelas' ? 'info' : 'category'"
                        :badge="$product['badge']"
                        :href="route('products.show', $product['slug'])"
                        class="h-full"
                    />
                </div>
            @endforeach
        </div>

        {{-- Empty state --}}
        <div
            x-show="visibleCount === 0"
            x-cloak
            x-transition.opacity
            class="mt-2 rounded-2xl border border-dashed border-slate-200 bg-white px-6 py-16 text-center"
        >
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-slate-100 text-slate-500 mb-5">
                <i data-lucide="search-x" class="w-8 h-8"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Tidak ada produk yang cocok</h2>
            <p class="mt-2 text-slate-500 max-w-md mx-auto">
                Coba ubah kata kunci pencarian atau pilih kategori lain. Anda juga bisa menghubungi tim kami untuk konsultasi langsung.
            </p>
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                <button
                    type="button"
                    @click="reset()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-primary-600 text-white text-sm font-semibold shadow-sm hover:bg-primary-700 transition"
                >
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    Reset filter
                </button>
                <a
                    href="https://wa.me/6281230633464"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-white border border-slate-200 text-slate-700 text-sm font-semibold hover:border-primary-200 hover:text-primary-700 transition"
                >
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    Tanya admin
                </a>
            </div>
        </div>
    </section>

    <x-slot name="scripts">
        <script>
            function productCatalog() {
                return {
                    filter: 'all',
                    search: '',
                    searchInput: '',
                    visibleCount: 0,
                    filters: [
                        { value: 'all',    label: 'Semua', icon: 'layout-grid' },
                        { value: 'kelas',  label: 'Kelas', icon: 'graduation-cap' },
                        { value: 'buku',   label: 'Buku',  icon: 'book-open' },
                    ],
                    counts: @json($productCounts),
                    init() {
                        this.recalc();
                        this.$watch('filter', () => this.recalc());
                        this.$watch('search', () => this.recalc());
                        // Re-render Lucide icons after Alpine mounts pills/inputs
                        this.$nextTick(() => window.lucide && window.lucide.createIcons());
                    },
                    setFilter(v) {
                        this.filter = v;
                    },
                    matches(p) {
                        const typeOk = this.filter === 'all' || p.type === this.filter;
                        const searchOk = !this.search || p.name.includes(this.search);
                        return typeOk && searchOk;
                    },
                    recalc() {
                        const items = @json($productIndex);
                        this.visibleCount = items.filter(p => this.matches(p)).length;
                    },
                    reset() {
                        this.filter = 'all';
                        this.search = '';
                        this.searchInput = '';
                    },
                };
            }
        </script>
    </x-slot>
</x-layouts.store>
