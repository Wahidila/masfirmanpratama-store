<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseStorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_shows_courses_and_books(): void
    {
        $course = Course::factory()->active()->create([
            'slug' => 'kelas-amc-reguler',
            'title' => 'Kelas Reguler Alpha Mind Control',
            'price' => 4500000,
            'status' => 'active',
        ]);

        $book1 = Product::factory()->active()->book()->create([
            'title' => 'Buku Alpha Mind Control',
            'slug' => 'buku-amc',
            'price' => 185000,
        ]);

        $book2 = Product::factory()->active()->book()->create([
            'title' => 'Buku Hypno Mind Control',
            'slug' => 'buku-hypno',
            'price' => 150000,
        ]);

        $response = $this->get('/produk');
        $response->assertStatus(200);
        $response->assertSee('Kelas Reguler Alpha Mind Control');
        $response->assertSee('Buku Alpha Mind Control');
        $response->assertSee('Buku Hypno Mind Control');
    }

    public function test_course_detail_page_renders_from_db(): void
    {
        $course = Course::factory()->active()->create([
            'slug' => 'kelas-amc-reguler',
            'title' => 'Kelas Reguler Alpha Mind Control',
            'price' => 4500000,
            'syllabus' => ['Topik A', 'Topik B'],
            'benefits' => [
                ['icon' => 'star', 'title' => 'Benefit X', 'desc' => 'Desc X'],
            ],
            'description' => ['Paragraf 1'],
            'schedule' => [
                ['title' => 'Sabtu', 'detail' => '09:00 WIB'],
            ],
        ]);

        $response = $this->get('/kelas/kelas-amc-reguler');
        $response->assertStatus(200);
        $response->assertSee('Kelas Reguler Alpha Mind Control');
        $response->assertSee('Topik A');
        $response->assertSee('Benefit X');
        $response->assertSee('Paragraf 1');
        $response->assertSee('Sabtu');
    }

    public function test_course_detail_uses_db_not_config(): void
    {
        Course::factory()->active()->create([
            'slug' => 'kelas-amc-reguler',
            'title' => 'DB Course Title Unique',
            'price' => 4500000,
        ]);

        $response = $this->get('/kelas/kelas-amc-reguler');
        $response->assertStatus(200);
        $response->assertSee('DB Course Title Unique');
    }

    // ------------------------------------------------------------------
    // Homepage class section — SYNC-C3
    // ------------------------------------------------------------------

    public function test_homepage_class_section_renders_courses_from_db(): void
    {
        Course::factory()->active()->create([
            'slug' => 'kelas-amc-reguler',
            'title' => 'Kelas AMC Reguler',
            'price' => 4500000,
            'show_on_homepage' => true,
            'sort_order' => 1,
            'card_style' => 'default',
            'card_icon' => 'video',
            'card_icon_color' => 'text-blue-600',
            'card_features' => ['20 Materi AMC', 'Modul materi AMC'],
            'cta_label' => 'Daftar Reguler',
            'installment_available' => true,
        ]);

        Course::factory()->active()->create([
            'slug' => 'kelas-amc-privat',
            'title' => 'Kelas AMC Privat',
            'price' => 7500000,
            'show_on_homepage' => true,
            'sort_order' => 2,
            'card_style' => 'highlight',
            'card_icon' => 'mic',
            'card_icon_color' => 'text-accent-600',
            'card_features' => ['20 Materi AMC', 'Jadwal fleksibel'],
            'cta_label' => 'Daftar Privat',
            'badge' => 'Terlaris',
            'installment_available' => true,
        ]);

        Course::factory()->active()->create([
            'slug' => 'kelas-amc-platinum',
            'title' => 'Kelas AMC Platinum',
            'price' => 22500000,
            'show_on_homepage' => true,
            'sort_order' => 3,
            'card_style' => 'dark',
            'card_icon' => 'gem',
            'card_icon_color' => 'text-secondary-400',
            'card_features' => ['Materi advanced', 'Hotel 3 hari 2 malam'],
            'cta_label' => 'Pilih Platinum',
            'installment_available' => true,
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        // All three course titles visible
        $response->assertSee('Kelas AMC Reguler');
        $response->assertSee('Kelas AMC Privat');
        $response->assertSee('Kelas AMC Platinum');

        // Prices rendered correctly
        $response->assertSee('Rp 7.500.000');
        $response->assertSee('Rp 22.500.000');

        // CTA links point to detail pages (NOT wa.me)
        $response->assertSee('/produk/kelas-amc-privat');
        $response->assertSee('/produk/kelas-amc-platinum');
        $response->assertSee('/produk/kelas-amc-reguler');

        // Verify class section cards use internal links, not WhatsApp
        $content = $response->getContent();
        // Extract the #kelas section and verify no wa.me within it
        preg_match('/<section id="kelas".*?<\/section>/s', $content, $matches);
        $kelasSection = $matches[0] ?? '';
        $this->assertNotEmpty($kelasSection, 'Section #kelas harus ada');
        $this->assertStringNotContainsString('wa.me', $kelasSection, 'Section #kelas tidak boleh ada link WhatsApp');
    }

    public function test_homepage_excludes_course_with_show_on_homepage_false(): void
    {
        Course::factory()->active()->create([
            'slug' => 'kelas-amc-reguler',
            'title' => 'Kelas AMC Reguler Visible',
            'price' => 4500000,
            'show_on_homepage' => true,
            'sort_order' => 1,
            'card_features' => ['Feature A'],
        ]);

        Course::factory()->active()->create([
            'slug' => 'kelas-amc-hidden',
            'title' => 'Kelas AMC Hidden Unique XYZ',
            'price' => 9999999,
            'show_on_homepage' => false,
            'sort_order' => 2,
            'card_features' => ['Feature B'],
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Kelas AMC Reguler Visible');
        $response->assertDontSee('Kelas AMC Hidden Unique XYZ');
    }

    public function test_homepage_class_cards_ordered_by_sort_order(): void
    {
        Course::factory()->active()->create([
            'slug' => 'kelas-c',
            'title' => 'ZZZ Kelas Urutan Tiga',
            'price' => 3000000,
            'show_on_homepage' => true,
            'sort_order' => 3,
            'card_features' => ['C1'],
        ]);

        Course::factory()->active()->create([
            'slug' => 'kelas-a',
            'title' => 'AAA Kelas Urutan Satu',
            'price' => 1000000,
            'show_on_homepage' => true,
            'sort_order' => 1,
            'card_features' => ['A1'],
        ]);

        Course::factory()->active()->create([
            'slug' => 'kelas-b',
            'title' => 'MMM Kelas Urutan Dua',
            'price' => 2000000,
            'show_on_homepage' => true,
            'sort_order' => 2,
            'card_features' => ['B1'],
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);

        $content = $response->getContent();
        $posA = strpos($content, 'AAA Kelas Urutan Satu');
        $posB = strpos($content, 'MMM Kelas Urutan Dua');
        $posC = strpos($content, 'ZZZ Kelas Urutan Tiga');

        $this->assertNotFalse($posA);
        $this->assertNotFalse($posB);
        $this->assertNotFalse($posC);
        $this->assertLessThan($posB, $posA, 'sort_order=1 harus muncul sebelum sort_order=2');
        $this->assertLessThan($posC, $posB, 'sort_order=2 harus muncul sebelum sort_order=3');
    }
}
