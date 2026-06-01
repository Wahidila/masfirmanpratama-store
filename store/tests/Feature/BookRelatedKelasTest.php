<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Anti-regression tests for split-kelas-buku B4.
 *
 * Guards:
 *  - Book detail pages resolve related kelas from Course DB (not config).
 *  - Kelas detail page renders from DB even though config entry is removed.
 *  - Config no longer contains kelas-amc-reguler.
 */
class BookRelatedKelasTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Book page that references 'kelas-amc-reguler' in related must resolve
     * the kelas card from the courses table, NOT from config.
     */
    public function test_book_detail_resolves_related_kelas_from_course_db(): void
    {
        // Seed the kelas as a Course (mirrors CourseSeeder).
        $course = Course::factory()->active()->create([
            'slug' => 'kelas-amc-reguler',
            'title' => 'Kelas Reguler Alpha Mind Control',
            'price' => 4500000,
            'status' => 'active',
        ]);

        // Seed the book as a Product so the page resolves from DB.
        $book = Product::factory()->active()->book()->create([
            'slug' => '10-keajaiban-pikiran',
            'title' => 'Buku 10 Keajaiban Pikiran',
            'price' => 150000,
            'status' => 'active',
        ]);

        // Config still lists 'kelas-amc-reguler' in this book's related array.
        $configRelated = config('products.items.10-keajaiban-pikiran.related', []);
        $this->assertContains('kelas-amc-reguler', $configRelated,
            'Precondition: config must still reference kelas-amc-reguler in related.');

        $response = $this->get('/produk/10-keajaiban-pikiran');

        $response->assertStatus(200);

        // The related section must contain the kelas title resolved from DB.
        $response->assertSee('Kelas Reguler Alpha Mind Control', false);

        // And the link should point to the kelas slug.
        $response->assertSee('/produk/kelas-amc-reguler', false);
    }

    /**
     * Kelas detail page must still render 200 from DB even though
     * config('products.items.kelas-amc-reguler') no longer exists.
     */
    public function test_kelas_detail_renders_without_config_entry(): void
    {
        $course = Course::factory()->active()->create([
            'slug' => 'kelas-amc-reguler',
            'title' => 'Kelas Reguler Alpha Mind Control',
            'price' => 4500000,
            'syllabus' => ['Topik A: Mind Power Dasar', 'Topik B: Telepati'],
            'description' => ['Paragraf deskripsi kelas.'],
            'status' => 'active',
        ]);

        // Guard: config entry must be gone.
        $this->assertNull(
            config('products.items.kelas-amc-reguler'),
            'Config entry kelas-amc-reguler must be removed (B4-B).'
        );

        $response = $this->get('/produk/kelas-amc-reguler');

        $response->assertStatus(200);
        $response->assertSee('Kelas Reguler Alpha Mind Control', false);
        // Syllabus from DB renders.
        $response->assertSee('Topik A: Mind Power Dasar', false);
        // Uses course template, not book template.
        $response->assertDontSee('Spesifikasi Buku', false);
    }

    /**
     * Guard: config no longer contains the kelas entry at all.
     */
    public function test_config_kelas_entry_is_removed(): void
    {
        $this->assertEmpty(
            config('products.items.kelas-amc-reguler'),
            'Entry kelas-amc-reguler must be fully removed from config/products.php.'
        );
    }

    /**
     * Guard: all 6 buku entries remain intact in config.
     */
    public function test_config_buku_entries_still_exist(): void
    {
        $expectedBooks = [
            '10-keajaiban-pikiran',
            'alpha-telepathy',
            'instan-hypnosis',
            'kitab-101-kalimat-sugesti-ajaib',
            'kitab-kunci-penarik-rezeki',
            'formula-amc-firman-pratama',
        ];

        foreach ($expectedBooks as $slug) {
            $this->assertNotEmpty(
                config('products.items.'.$slug),
                "Book entry '{$slug}' must remain in config/products.php."
            );
        }
    }
}
