<?php

namespace Tests\Feature;

use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Regression guard untuk task t_5e6b03f1 — Lighthouse timeout di
 * /kelas/kelas-amc-reguler. Investigation lengkap: docs/qc/M1/lighthouse/timeout-investigation.md.
 *
 * Bug bar (3 separable issues yang stack jadi PROTOCOL_TIMEOUT):
 *
 *   1. `unpkg.com/lucide@latest` redirect ke legacy v1.16.0 yang ngga punya
 *      brand icons (facebook/youtube/instagram). Pin ke versi 0.x.
 *   2. `alpine:morphed` listener trigger createIcons() → mutate DOM →
 *      morphed lagi → loop infinite.
 *   3. `<template x-for>` di tabs course pakai per-element x-init createIcons
 *      → 5 full DOM scan multiplier.
 *
 * Test ini static-source-only (cepat, ngga butuh browser). Untuk smoke
 * end-to-end Lighthouse: lihat docs/qc/M1/lighthouse/timeout-investigation.md
 * §"Verification command".
 */
class CourseDetailLighthouseGuardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();

        Course::factory()->create([
            'slug' => 'kelas-amc-reguler',
            'status' => 'active',
            'title' => 'AMC Reguler',
            'price' => 2500000,
        ]);
    }

    public function test_layout_pins_lucide_cdn_to_specific_version(): void
    {
        $response = $this->get('/kelas/kelas-amc-reguler');

        $response->assertStatus(200);

        // Bug #1 guard: pinning version, ngga pakai @latest tag.
        $response->assertDontSee(
            'lucide@latest',
            false,
            'Lucide CDN reverted ke `@latest` — itu redirect ke legacy v1.16.0 '
                .'tanpa brand icons. Pin ke specific 0.x version. Detail: '
                .'docs/qc/M1/lighthouse/timeout-investigation.md §Root cause #1.'
        );
        $response->assertSee('lucide@0.469.0', false);
    }

    public function test_layout_does_not_listen_for_alpine_morphed(): void
    {
        $response = $this->get('/kelas/kelas-amc-reguler');

        // Bug #2 guard: alpine:morphed → createIcons → mutate → morphed loop.
        $response->assertDontSee(
            "addEventListener('alpine:morphed'",
            false,
            'Layout listening to alpine:morphed event lagi. createIcons() di-call '
                .'dari listener ini bakal trigger mutation yang fire morphed lagi → '
                .'main thread block. Detail: timeout-investigation.md §Root cause #2.'
        );
    }

    public function test_course_tab_buttons_dont_call_createicons_per_render(): void
    {
        $response = $this->get('/kelas/kelas-amc-reguler');

        // Bug #3 guard: per-tab x-init createIcons di <template x-for>.
        // Pattern lama: <i :data-lucide="t.icon" x-init="$nextTick(() => ... createIcons())">
        $body = $response->getContent();

        // Specifically guard the x-for tab template — `:data-lucide` binding
        // dengan x-init createIcons sebelahnya = patogen pattern.
        $hasTemplateXForLucide = preg_match(
            '/x-for=["\'][^"\']*\bt\s+in\s+tabs[^"\']*["\'].*?:data-lucide.*?x-init=["\'][^"\']*createIcons/s',
            $body,
        );

        $this->assertFalse(
            (bool) $hasTemplateXForLucide,
            'Tab template di course detail page punya `<i :data-lucide x-init createIcons>` '
                .'pattern. Setiap tab item bakal scan-ulang seluruh document — multiplier '
                .'untuk lucide icon error loop. Detail: timeout-investigation.md §Root cause #3.'
        );
    }

    public function test_course_detail_renders_required_lucide_brand_icons(): void
    {
        $response = $this->get('/kelas/kelas-amc-reguler');

        // Footer footer punya 3 brand icon socmed yang missing di lucide v1.16.0.
        // Kalau test ini fail di body markup, fine — tapi kalau page rendered tanpa
        // icons sama sekali (lucide CDN dead), itu signal lain.
        $response->assertSee('data-lucide="facebook"', false);
        $response->assertSee('data-lucide="youtube"', false);
        $response->assertSee('data-lucide="instagram"', false);
    }
}
