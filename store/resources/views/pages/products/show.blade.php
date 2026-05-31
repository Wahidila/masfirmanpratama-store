@php
    /** @var App\Models\Product|null $productModel */
    /** @var array|null $data */
    /** @var array $related */
    /** @var string|null $template */
    /** @var string|null $slug */
    $slug = $slug ?? null;
@endphp

@if (!isset($data) || !$data)
    <x-page-placeholder
        title="Produk Tidak Ditemukan"
        description="Produk yang Anda cari tidak tersedia."
        icon="package-x"
        :message="$slug ? ('Tidak ada produk dengan slug `' . $slug . '`. Cek kembali link atau lihat katalog lengkap kami.') : 'Produk tidak ditemukan. Cek kembali link atau lihat katalog lengkap kami.'"
        :ctaHref="route('products.index')"
        ctaLabel="Lihat Katalog"
    />
@elseif ($template && view()->exists($template))
    @include($template, ['product' => $data, 'related' => $related])
@else
    <x-page-placeholder
        :title="$data['title'] ?? 'Produk'"
        :description="$data['subtitle'] ?? null"
        icon="construction"
        :message="'Template detail untuk produk ini masih dalam pengembangan.'"
    />
@endif
