<?php

namespace Tests\Feature\Shipping;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class TrackingTest extends TestCase
{
    use RefreshDatabase;

    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        config(['shipping.license' => 'test-license-key']);

        $this->order = Order::factory()->create([
            'status' => 'shipped',
            'shipping_courier' => 'JNE',
            'shipping_resi' => 'JNE1234567890',
            'shipped_at' => now()->subDay(),
        ]);
    }

    private function signedTrack(string $orderNumber): string
    {
        return URL::temporarySignedRoute(
            'track.show',
            now()->addDays(30),
            ['order_number' => $orderNumber],
        );
    }

    private function fakeTrackingApi(): void
    {
        Http::fake([
            '*/shipping/tracking' => Http::response([
                'data' => [
                    [
                        'datetime' => '2026-05-30 10:00',
                        'status' => 'Paket diterima di agen JNE',
                        'location' => 'Surabaya',
                    ],
                    [
                        'datetime' => '2026-05-30 14:30',
                        'status' => 'Paket dalam pengiriman',
                        'location' => 'Surabaya',
                    ],
                    [
                        'datetime' => '2026-05-31 09:15',
                        'status' => 'Paket telah sampai',
                        'location' => 'Jakarta',
                    ],
                ],
                'message' => 'OK',
            ], 200),
        ]);
    }

    // ─── Test (a): Order with tracking number shows tracking history ───

    public function test_tracking_page_shows_tracking_history_for_order_with_resi(): void
    {
        $this->fakeTrackingApi();

        $response = $this->get($this->signedTrack($this->order->order_number));

        $response->assertStatus(200);
        $response->assertSee('data-testid="tracking-history-card"', false);
        $response->assertSee('Paket diterima di agen JNE', false);
        $response->assertSee('Paket dalam pengiriman', false);
        $response->assertSee('Paket telah sampai', false);
        $response->assertSee('2026-05-30 10:00', false);
        $response->assertSee('2026-05-31 09:15', false);
        $response->assertSee('Surabaya', false);
        $response->assertSee('Jakarta', false);
    }

    // ─── Test (b): Order without tracking number shows 'Resi belum tersedia' ───

    public function test_tracking_page_shows_resi_belum_tersedia_when_order_has_no_resi(): void
    {
        $orderNoResi = Order::factory()->create([
            'status' => 'pending',
            'shipping_courier' => null,
            'shipping_resi' => null,
            'shipped_at' => null,
        ]);

        $response = $this->get($this->signedTrack($orderNoResi->order_number));

        $response->assertStatus(200);
        $response->assertSee('data-testid="tracking-history-card"', false);
        $response->assertSee('Resi belum tersedia', false);
        $response->assertSee('Nomor resi akan muncul setelah admin menginput pengiriman', false);
    }

    // ─── Test (c): Signed URL required ───

    public function test_track_page_without_signature_returns_403(): void
    {
        $this->get('/track/'.$this->order->order_number)
            ->assertStatus(403);
    }

    // ─── Test (d): Tracking API error → page still renders ───

    public function test_tracking_page_renders_gracefully_when_api_errors(): void
    {
        Http::fake([
            '*/shipping/tracking' => Http::response([], 500),
        ]);

        $response = $this->get($this->signedTrack($this->order->order_number));

        $response->assertStatus(200);
        $response->assertSee('data-testid="tracking-history-card"', false);
        $response->assertSee('Melacak paket', false);
    }

    // ─── Test (e): Tracking results are cached ───

    public function test_tracking_results_are_cached(): void
    {
        Http::fake([
            '*/shipping/tracking' => Http::response([
                'data' => [
                    [
                        'datetime' => '2026-05-30 10:00',
                        'status' => 'Paket diterima',
                        'location' => 'Surabaya',
                    ],
                ],
                'message' => 'OK',
            ], 200),
        ]);

        $url = $this->signedTrack($this->order->order_number);

        $this->get($url)->assertStatus(200);
        $this->get($url)->assertStatus(200);

        Http::assertSentCount(1);
    }
}
