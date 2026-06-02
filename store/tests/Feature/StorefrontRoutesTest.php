<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Course::factory()->create([
            'slug' => 'amc-reguler',
            'title' => 'AMC Reguler',
            'status' => 'active',
        ]);
        Product::factory()->create([
            'slug' => 'alpha-telepathy',
            'title' => 'Alpha Telepathy',
            'type' => 'book',
            'status' => 'active',
        ]);
    }

    public function test_kelas_route_returns_200(): void
    {
        $response = $this->get('/kelas/amc-reguler');
        $response->assertStatus(200);
        $response->assertSee('AMC Reguler');
    }

    public function test_produk_redirects_to_kelas_for_courses(): void
    {
        $response = $this->get('/produk/amc-reguler');
        $response->assertRedirect('/kelas/amc-reguler');
        $response->assertStatus(301);
    }

    public function test_produk_route_works_for_buku(): void
    {
        $response = $this->get('/produk/alpha-telepathy');
        $response->assertStatus(200);
    }

    public function test_tentang_page_returns_200(): void
    {
        $response = $this->get('/tentang');
        $response->assertStatus(200);
        $response->assertSee('Tentang');
    }

    public function test_kontak_page_returns_200(): void
    {
        $response = $this->get('/kontak');
        $response->assertStatus(200);
        $response->assertSee('Hubungi Kami');
    }
}
