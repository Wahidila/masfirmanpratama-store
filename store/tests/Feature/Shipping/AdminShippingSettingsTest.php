<?php

namespace Tests\Feature\Shipping;

use App\Models\Admin;
use App\Models\Product;
use App\Services\Settings;
use App\Services\Shipping\ShippingRateService;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminShippingSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
        $this->admin = Admin::first();
    }

    public function test_admin_can_view_shipping_tab(): void
    {
        Http::fake([
            '*/license/activate' => Http::response([
                'data' => ['type' => 'exclusive', 'expire_date' => '2026-06-01'],
                'message' => 'OK',
            ], 200),
        ]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.settings.index', ['tab' => 'shipping']));

        $response->assertStatus(200);
        $response->assertSee('Kota asal pengiriman');
    }

    public function test_admin_can_update_shipping_settings(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.settings.shipping.update'), [
                'origin' => 'bandung',
                'origin_zipcode' => '40111',
                'couriers' => ['jne', 'jnt'],
                'service_markup' => "jne_reg:5000\njnt_reg:3000",
                'shipping_enabled' => '1',
                'default_weight_kg' => '1.5',
            ]);

        $response->assertRedirect(route('admin.settings.index', ['tab' => 'shipping']));

        $this->assertSame('bandung', Settings::get('shipping.origin'));
        $this->assertSame('40111', Settings::get('shipping.origin_zipcode'));
        $this->assertSame(['jne', 'jnt'], Settings::get('shipping.couriers'));
        $this->assertSame(['jne_reg' => 5000, 'jnt_reg' => 3000], Settings::get('shipping.service_markup'));
        $this->assertTrue(Settings::get('shipping.shipping_enabled'));
    }

    public function test_non_admin_cannot_access_shipping_settings(): void
    {
        $response = $this->get(route('admin.settings.index', ['tab' => 'shipping']));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_shipping_rate_service_uses_db_settings(): void
    {
        // Set DB origin to bandung
        Settings::set('shipping.origin', 'bandung', 'string');
        Settings::set('shipping.origin_zipcode', '40111', 'string');
        Settings::set('shipping.couriers', ['jne'], 'array');

        Http::fake([
            '*/shipping/price' => Http::response([
                'message' => 'Success',
                'data' => [
                    [
                        'courier' => 'jne',
                        'service' => 'jne_reg',
                        'service_name' => 'REG',
                        'price' => '20000',
                        'etd' => '2-3 days',
                    ],
                ],
            ], 200),
        ]);

        // Create a shippable product
        Product::create([
            'slug' => 'test-book',
            'type' => 'book',
            'title' => 'Test Book',
            'price' => 50000,
            'stock' => 10,
            'status' => 'active',
            'weight_kg' => 0.5,
            'is_shippable' => true,
        ]);

        $service = app(ShippingRateService::class);
        $rates = $service->getRates(
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => '', 'zipcode' => '12110'],
            [['slug' => 'test-book', 'qty' => 1]]
        );

        $this->assertNotEmpty($rates);

        // Verify the API was called with bandung origin (from DB) not surabaya (from config)
        Http::assertSent(function ($request) {
            return $request['origin'] === 'bandung'
                && $request['origin_zipcode'] === '40111'
                && $request['courier'] === 'jne';
        });
    }

    public function test_shipping_disabled_returns_empty_rates(): void
    {
        Settings::set('shipping.shipping_enabled', false, 'bool');

        Product::create([
            'slug' => 'test-book-2',
            'type' => 'book',
            'title' => 'Test Book 2',
            'price' => 50000,
            'stock' => 10,
            'status' => 'active',
            'weight_kg' => 0.5,
            'is_shippable' => true,
        ]);

        $service = app(ShippingRateService::class);
        $rates = $service->getRates(
            ['province' => 'DKI Jakarta', 'city' => 'Jakarta Selatan', 'district' => '', 'zipcode' => '12110'],
            [['slug' => 'test-book-2', 'qty' => 1]]
        );

        $this->assertEmpty($rates);
    }

    public function test_cannot_save_site_url_or_license_via_form(): void
    {
        // Attempt to sneak in site_url and license fields
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.settings.shipping.update'), [
                'origin' => 'surabaya',
                'origin_zipcode' => '60111',
                'couriers' => ['jne'],
                'shipping_enabled' => '1',
                'default_weight_kg' => '1',
                'site_url' => 'https://evil.com',
                'license' => 'HACKED_LICENSE',
            ]);

        $response->assertRedirect();

        // Verify these were NOT saved
        $this->assertNull(Settings::get('shipping.site_url'));
        $this->assertNull(Settings::get('shipping.license'));
        $this->assertNull(Settings::get('site_url'));
        $this->assertNull(Settings::get('license'));
    }

    public function test_update_validates_couriers_in_allowed_list(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.settings.shipping.update'), [
                'origin' => 'surabaya',
                'origin_zipcode' => '60111',
                'couriers' => ['invalid_courier'],
                'shipping_enabled' => '1',
                'default_weight_kg' => '1',
            ]);

        $response->assertSessionHasErrors('couriers.0');
    }
}
