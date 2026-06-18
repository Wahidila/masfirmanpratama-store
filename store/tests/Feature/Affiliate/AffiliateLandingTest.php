<?php

namespace Tests\Feature\Affiliate;

use App\Models\Affiliator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateLandingTest extends TestCase
{
    use RefreshDatabase;

    // ─── Akses Halaman ───────────────────────────────────────────────

    /** Landing page affiliate bisa diakses tanpa login (guest) */
    public function test_guest_dapat_mengakses_landing_page(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertStatus(200);
        $response->assertViewIs('affiliate.landing');
    }

    /** Landing page menampilkan teks kunci program */
    public function test_landing_menampilkan_teks_kunci_program(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertSee('Gabung Sekarang');
        $response->assertSee('Komisi');
        $response->assertSee('MasFirmanPratama');
    }

    // ─── Navigasi & Link ─────────────────────────────────────────────

    /** Landing page memiliki link ke halaman register */
    public function test_landing_memiliki_link_ke_register(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertSee(route('affiliate.register'));
    }

    /** Landing page memiliki link ke halaman login */
    public function test_landing_memiliki_link_ke_login(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertSee(route('affiliate.login'));
    }

    // ─── Konten Section ──────────────────────────────────────────────

    /** Section benefit tampil di landing page */
    public function test_landing_menampilkan_section_benefit(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertSee('Keuntungan Menjadi Affiliator');
        $response->assertSee('Komisi 10%');
        $response->assertSee('Tracking Otomatis');
        $response->assertSee('Pencairan Mudah');
        $response->assertSee('Materi Marketing');
    }

    /** Section cara kerja tampil di landing page */
    public function test_landing_menampilkan_section_cara_kerja(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertSee('Cara Kerja Program Affiliate');
        $response->assertSee('Daftar Akun');
        $response->assertSee('Dapatkan Link');
        $response->assertSee('Bagikan');
        $response->assertSee('Terima Komisi');
    }

    /** Section tipe affiliator tampil di landing page */
    public function test_landing_menampilkan_section_tipe_affiliator(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertSee('Pilih Tipe Affiliator');
        $response->assertSee('Alumni');
        $response->assertSee('Non-Alumni');
        $response->assertSee('Peserta');
    }

    /** Section FAQ tampil di landing page */
    public function test_landing_menampilkan_section_faq(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertSee('Pertanyaan yang Sering Diajukan');
        $response->assertSee('Apakah mendaftar program affiliate gratis?');
        $response->assertSee('Berapa besar komisi yang saya dapatkan?');
    }

    /** CTA penutup tampil di landing page */
    public function test_landing_menampilkan_cta_penutup(): void
    {
        $response = $this->get(route('affiliate.landing'));

        $response->assertSee('Siap Mulai Menghasilkan?');
        $response->assertSee('Daftar Sebagai Affiliator');
    }

    // ─── Auth Guard ──────────────────────────────────────────────────

    /** User yang sudah login tetap bisa akses landing page */
    public function test_authenticated_affiliator_tetap_bisa_akses_landing(): void
    {
        $affiliator = Affiliator::factory()->create();

        $response = $this->actingAs($affiliator, 'affiliator')
            ->get(route('affiliate.landing'));

        $response->assertStatus(200);
    }
}
