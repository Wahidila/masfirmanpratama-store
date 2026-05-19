<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Order;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
        $this->admin = Admin::first();
    }

    public function test_redirects_unauthenticated_to_login(): void
    {
        $this->get(route('admin.orders.index'))->assertRedirect(route('admin.login'));
    }

    public function test_renders_orders_index_for_authenticated_admin(): void
    {
        Order::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Pesanan');
        $response->assertSee('Cari order number');
    }

    public function test_shows_empty_state_when_no_orders(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada pesanan');
    }

    public function test_filters_by_status(): void
    {
        Order::factory()->status('pending')->create(['order_number' => 'MFP-PENDING1']);
        Order::factory()->status('paid')->create(['order_number' => 'MFP-PAID111']);
        Order::factory()->status('shipped')->create(['order_number' => 'MFP-SHIP111']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.index', ['status' => 'paid']));

        $response->assertStatus(200);
        $response->assertSee('MFP-PAID111');
        $response->assertDontSee('MFP-PENDING1');
        $response->assertDontSee('MFP-SHIP111');
    }

    public function test_search_by_order_number(): void
    {
        Order::factory()->create(['order_number' => 'MFP-AAAA1111']);
        Order::factory()->create(['order_number' => 'MFP-BBBB2222']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.index', ['q' => 'AAAA']));

        $response->assertStatus(200);
        $response->assertSee('MFP-AAAA1111');
        $response->assertDontSee('MFP-BBBB2222');
    }

    public function test_search_by_customer_name(): void
    {
        Order::factory()->create(['customer_name' => 'Sari Lestari', 'order_number' => 'MFP-X11111111']);
        Order::factory()->create(['customer_name' => 'Budi Santoso', 'order_number' => 'MFP-Y22222222']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.index', ['q' => 'Sari']));

        $response->assertStatus(200);
        $response->assertSee('MFP-X11111111');
        $response->assertDontSee('MFP-Y22222222');
    }

    public function test_search_by_phone(): void
    {
        Order::factory()->create(['phone' => '081234567890', 'order_number' => 'MFP-PHONE001']);
        Order::factory()->create(['phone' => '089876543210', 'order_number' => 'MFP-OTHER001']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.index', ['q' => '081234567890']));

        $response->assertStatus(200);
        $response->assertSee('MFP-PHONE001');
        $response->assertDontSee('MFP-OTHER001');
    }

    public function test_filter_by_date_range(): void
    {
        Order::factory()
            ->create(['order_number' => 'MFP-OLD0001', 'created_at' => now()->subDays(10)]);
        Order::factory()
            ->create(['order_number' => 'MFP-NEW0001', 'created_at' => now()->subDay()]);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.index', [
                'date_from' => now()->subDays(3)->format('Y-m-d'),
            ]));

        $response->assertStatus(200);
        $response->assertSee('MFP-NEW0001');
        $response->assertDontSee('MFP-OLD0001');
    }

    public function test_pagination_25_per_page(): void
    {
        Order::factory()->count(30)->create();

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.index'));

        $response->assertStatus(200);
        // Tailwind paginator: "Next" link muncul kalau ada page kedua
        $response->assertSeeText('Next');
    }

    public function test_invalid_status_filter_silently_ignored(): void
    {
        Order::factory()->status('pending')->create(['order_number' => 'MFP-PENDING1']);

        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.orders.index', ['status' => 'definitely-not-a-status']));

        $response->assertStatus(200);
        $response->assertSee('MFP-PENDING1');
    }

    public function test_sort_by_created_at_desc(): void
    {
        Order::factory()->create(['order_number' => 'MFP-OLD0001', 'created_at' => now()->subDays(5)]);
        Order::factory()->create(['order_number' => 'MFP-NEW0001', 'created_at' => now()->subHour()]);

        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.orders.index'));

        $response->assertStatus(200);
        $content = $response->getContent();
        $newPos = strpos($content, 'MFP-NEW0001');
        $oldPos = strpos($content, 'MFP-OLD0001');

        $this->assertNotFalse($newPos);
        $this->assertNotFalse($oldPos);
        $this->assertLessThan($oldPos, $newPos, 'Newest order should appear before older one');
    }
}
