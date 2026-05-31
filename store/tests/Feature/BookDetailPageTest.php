<?php

namespace Tests\Feature;

use Tests\TestCase;

class BookDetailPageTest extends TestCase
{
    protected bool $seed = true;

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function bookSlugs(): array
    {
        return [
            '10-keajaiban-pikiran' => ['10-keajaiban-pikiran', 'Buku 10 Keajaiban Pikiran'],
            'alpha-telepathy' => ['alpha-telepathy', 'Buku Alpha Telepathy'],
            'instan-hypnosis' => ['instan-hypnosis', 'Buku Instan Hypnosis'],
            'kitab-101-kalimat-sugesti-ajaib' => ['kitab-101-kalimat-sugesti-ajaib', 'Kitab 101 Kalimat Sugesti Ajaib'],
            'kitab-kunci-penarik-rezeki' => ['kitab-kunci-penarik-rezeki', 'Kitab Kunci Penarik Rezeki'],
            'formula-amc-firman-pratama' => ['formula-amc-firman-pratama', 'Formula AMC'],
        ];
    }

    /**
     * @dataProvider bookSlugs
     */
    public function test_book_detail_page_renders_for_each_book(string $slug, string $expectedTitle): void
    {
        $response = $this->get('/produk/'.$slug);

        $response->assertStatus(200);
        $response->assertSee($expectedTitle, false);

        // Layout chrome.
        $response->assertSeeInOrder(['/build/assets/app-', '.css'], false);
        $response->assertSeeInOrder(['/build/assets/app-', '.js'], false);
        $response->assertSee('csrf-token', false);
        $response->assertSee('lucide@0.469.0', false);

        // Book template specific markers.
        $response->assertSee('Spesifikasi Buku', false);
        $response->assertSee('bookDetailPage', false);                  // Alpine page component
        $response->assertSee('data-testid="add-to-cart"', false);       // CTA exists
        $response->assertSee('Karya Buku', false);                      // breadcrumb
        $response->assertSee('Karya Lain dari', false);                 // related section heading
        $response->assertSee('"@type":"Book"', false);                  // structured data

        // Should NOT spill into course template.
        $response->assertDontSee('Apa yang Akan Anda Dapatkan', false);
    }

    public function test_unknown_slug_renders_placeholder_not_book_template(): void
    {
        $response = $this->get('/produk/slug-yang-tidak-terdaftar');

        $response->assertStatus(200);
        $response->assertSee('Produk Tidak Ditemukan', false);
        $response->assertDontSee('Spesifikasi Buku', false);
        $response->assertDontSee('bookDetailPage', false);
    }

    public function test_course_slug_uses_course_template_not_book_template(): void
    {
        $response = $this->get('/produk/kelas-amc-reguler');

        $response->assertStatus(200);
        // Book-only marker must be absent.
        $response->assertDontSee('Spesifikasi Buku', false);
        $response->assertDontSee('bookDetailPage', false);
    }

    public function test_book_cover_images_are_served(): void
    {
        $covers = [
            'images/10-keajaiban-pikiran.webp',
            'images/alpha-telepathy.webp',
            'images/instan-hypnosis.webp',
            'images/kitab-101-kalimat-sugesti-ajaib.webp',
            'images/kitab-kunci-penarik-rezeki.webp',
            'images/formula-amc-firman-pratama.webp',
        ];

        foreach ($covers as $cover) {
            $path = public_path($cover);
            $this->assertFileExists($path, "Missing book cover: {$cover}");
            $this->assertGreaterThan(1024, filesize($path), "Suspiciously small cover: {$cover}");
        }
    }

    public function test_book_detail_includes_cart_alpine_dependency(): void
    {
        $response = $this->get('/produk/10-keajaiban-pikiran');

        $response->assertStatus(200);
        // The page binds to $store.cart via the bookDetailPage component.
        $response->assertSee('$store.cart', false);
    }
}
