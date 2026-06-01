<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Map config/products.php → tabel products.
     *
     * B1: hanya seed BUKU. Kelas sudah dipisah ke CourseSeeder.
     */

    /**
     * Realistic weight (kg) mapping based on page count / thickness.
     */
    private const WEIGHT_MAP = [
        '10-keajaiban-pikiran' => 0.3,
        'alpha-telepathy' => 0.5,
        'instan-hypnosis' => 0.5,
        'kitab-101-kalimat-sugesti-ajaib' => 0.3,
        'kitab-kunci-penarik-rezeki' => 0.5,
        'formula-amc-firman-pratama' => 0.5,
    ];

    public function run(): void
    {
        $items = config('products.items', []);

        foreach ($items as $slug => $p) {
            // B1: skip kelas — sudah di CourseSeeder
            if (($p['type'] ?? '') === 'kelas') {
                continue;
            }

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
                    'type' => 'book',
                    'title' => $p['title'] ?? $slug,
                    'price' => $p['price'] ?? 0,
                    'stock' => 50,
                    'status' => 'active',
                    'image_path' => $p['image'] ?? null,
                    'description' => $description,
                    'meta_seo' => $metaSeo ?: null,
                    'weight_kg' => self::WEIGHT_MAP[$slug] ?? 0.3,
                    'is_shippable' => true,
                ],
            );
        }
    }
}
