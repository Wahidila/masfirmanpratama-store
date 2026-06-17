<?php

namespace Tests\Feature;

use App\Models\Affiliator;
use Database\Seeders\AffiliatorTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AffiliatorTypeSeeder::class);
    }

    private function activeAffiliator(): Affiliator
    {
        return Affiliator::factory()->create([
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    public function test_dashboard_loads_for_active_user(): void
    {
        $affiliator = $this->activeAffiliator();
        $this->actingAs($affiliator, 'affiliator');

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee($affiliator->name);
    }

    public function test_dashboard_shows_stats(): void
    {
        $affiliator = $this->activeAffiliator();
        $this->actingAs($affiliator, 'affiliator');

        $response = $this->get('/dashboard');
        $response->assertSee('Saldo Tersedia');
        $response->assertSee('Total Pendapatan');
        $response->assertSee('Link Referral');
    }
}
