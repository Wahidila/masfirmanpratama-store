@php
    /** @var string $slug */
    $slug = $slug ?? null;
    $product = $slug ? config('products.items.' . $slug) : null;

    // Resolve related products by slug → full array (filter null/missing)
    $related = [];
    if ($product && ! empty($product['related'])) {
        foreach ((array) $product['related'] as $relatedSlug) {
            $rel = config('products.items.' . $relatedSlug);
            if ($rel) {
                $related[] = $rel;
            }
        }
    }

    $type = $product['type'] ?? null;

    // Map type → template name. Fallback ke placeholder kalau template
    // belum ada (misal book.blade.php belum dibikin task #8).
    $templateMap = [
        'kelas' => 'pages.products.course',
        'buku' => 'pages.products.book',
    ];
    $template = $type && isset($templateMap[$type]) ? $templateMap[$type] : null;
@endphp

@if (! $product)
    {{-- 404-style empty state untuk slug yang tidak terdaftar --}}
    <x-page-placeholder
        title="Produk Tidak Ditemukan"
        description="Produk yang Anda cari tidak tersedia."
        icon="package-x"
        :message="'Tidak ada produk dengan slug `' . $slug . '`. Cek kembali link atau lihat katalog lengkap kami.'"
        :ctaHref="route('products.index')"
        ctaLabel="Lihat Katalog"
    />
@elseif ($template && view()->exists($template))
    @include($template, ['product' => $product, 'related' => $related])
@else
    {{-- Template untuk type ini belum di-port — placeholder ringkas --}}
    <x-page-placeholder
        :title="$product['title']"
        :description="$product['subtitle'] ?? null"
        icon="construction"
        :message="'Template detail untuk `' . ($type ?? 'produk') . '` masih dalam pengembangan. Halaman akan otomatis aktif begitu task FE-nya merge.'"
    />
@endif
