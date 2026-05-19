<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Map config/products.php → tabel products.
     *
     * Config pakai type 'kelas'/'buku', schema enum pakai 'course'/'book'.
     */
    private const TYPE_MAP = [
        'kelas' => 'course',
        'buku' => 'book',
    ];

    public function run(): void
    {
        $items = config('products.items', []);

        foreach ($items as $slug => $p) {
            $type = self::TYPE_MAP[$p['type'] ?? 'buku'] ?? 'book';

            $description = isset($p['description']) && is_array($p['description'])
                ? implode("\n\n", $p['description'])
                : ($p['subtitle'] ?? null);

            $metaSeo = array_filter([
                'subtitle' => $p['subtitle'] ?? null,
                'tagline' => $p['tagline'] ?? null,
                'badge' => $p['badge'] ?? null,
                'category_label' => $p['category_label'] ?? null,
                'image_alt' => $p['image_alt'] ?? null,
                'rating' => $p['rating'] ?? null,
                'student_count' => $p['student_count'] ?? null,
            ]);

            Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'type' => $type,
                    'title' => $p['title'] ?? $slug,
                    'price' => $p['price'] ?? 0,
                    'stock' => $type === 'course' ? 99 : 50,
                    'status' => 'active',
                    'image_path' => $p['image'] ?? null,
                    'description' => $description,
                    'meta_seo' => $metaSeo ?: null,
                ],
            );
        }
    }
}
