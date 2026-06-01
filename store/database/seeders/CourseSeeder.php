<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed courses from config/products.php (key 'kelas-amc-reguler').
     * Courses are separate from book products since B1 split.
     */
    public function run(): void
    {
        $items = config('products.items', []);
        $kelasData = $items['kelas-amc-reguler'] ?? null;

        if (! $kelasData) {
            return;
        }

        $metaSeo = array_filter([
            'subtitle' => $kelasData['subtitle'] ?? null,
            'tagline' => $kelasData['tagline'] ?? null,
            'badge' => $kelasData['badge'] ?? null,
            'category_label' => $kelasData['category_label'] ?? null,
            'image_alt' => $kelasData['image_alt'] ?? null,
            'rating' => $kelasData['rating'] ?? null,
            'student_count' => $kelasData['student_count'] ?? null,
        ]);

        Course::updateOrCreate(
            ['slug' => 'kelas-amc-reguler'],
            [
                'title' => $kelasData['title'] ?? 'Kelas Reguler Alpha Mind Control',
                'subtitle' => $kelasData['subtitle'] ?? null,
                'price' => $kelasData['price'] ?? 4500000,
                'original_price' => $kelasData['original_price'] ?? null,
                'status' => 'active',
                'image_path' => $kelasData['image'] ?? null,
                'badge' => $kelasData['badge'] ?? null,
                'badge_icon' => $kelasData['badge_icon'] ?? null,
                'category_label' => $kelasData['category_label'] ?? null,
                'rating' => $kelasData['rating'] ?? null,
                'student_count' => $kelasData['student_count'] ?? null,
                'tagline' => $kelasData['tagline'] ?? null,
                'installment_available' => $kelasData['installment_available'] ?? true,
                'description' => $kelasData['description'] ?? null,
                'syllabus' => $kelasData['syllabus'] ?? null,
                'schedule' => $kelasData['schedule'] ?? null,
                'benefits' => $kelasData['benefits'] ?? null,
                'testimonials' => $kelasData['testimonials'] ?? null,
                'related' => $kelasData['related'] ?? null,
                'meta_seo' => $metaSeo ?: null,
            ],
        );
    }
}
