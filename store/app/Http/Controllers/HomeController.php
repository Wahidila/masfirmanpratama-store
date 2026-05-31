<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        $products = Product::where('status', 'active')
            ->where('type', 'book')
            ->orderBy('id')
            ->get()
            ->map(fn (Product $p) => [
                'image' => asset($p->image_path ?? 'images/placeholder.webp'),
                'title' => $p->title,
                'price' => (float) $p->price,
                'originalPrice' => $p->meta_seo['original_price'] ?? null,
                'category' => $p->meta_seo['category_label'] ?? 'Buku',
                'badge' => $p->meta_seo['badge'] ?? null,
                'href' => url('/produk/'.$p->slug),
            ]);

        return view('pages.home', compact('products'));
    }
}
