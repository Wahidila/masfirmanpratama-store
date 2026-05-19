<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
    }

    public function test_shows_login_page(): void
    {
        $response = $this->get(route('admin.login'));

        $response->assertStatus(200);
        $response->assertSee('Masuk ke Admin');
        $response->assertSee('Email');
    }

    public function test_redirects_unauthenticated_users_from_dashboard_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_logs_in_admin_with_correct_credentials(): void
    {
        $response = $this->post(route('admin.login.attempt'), [
            'email' => 'admin@masfirmanpratama.com',
            'password' => 'admin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertTrue(auth('admin')->check());
        $this->assertSame('admin@masfirmanpratama.com', auth('admin')->user()->email);
    }

    public function test_rejects_login_with_wrong_password(): void
    {
        $response = $this->from(route('admin.login'))
            ->post(route('admin.login.attempt'), [
                'email' => 'admin@masfirmanpratama.com',
                'password' => 'wrong-password',
            ]);

        $response->assertRedirect(route('admin.login'));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(auth('admin')->check());
    }

    public function test_updates_last_login_at_on_successful_login(): void
    {
        $before = Admin::first()->last_login_at;
        $this->assertNull($before);

        $this->post(route('admin.login.attempt'), [
            'email' => 'admin@masfirmanpratama.com',
            'password' => 'admin123',
        ]);

        $after = Admin::first()->last_login_at;
        $this->assertNotNull($after);
    }

    public function test_protects_dashboard_when_authenticated(): void
    {
        $admin = Admin::first();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Pesanan Pending');
    }

    public function test_logs_out_admin_and_clears_session(): void
    {
        $admin = Admin::first();
        $this->actingAs($admin, 'admin');
        $this->assertTrue(auth('admin')->check());

        $response = $this->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertFalse(auth('admin')->check());
    }

    public function test_redirects_authenticated_admin_away_from_login_via_guest_middleware(): void
    {
        $admin = Admin::first();

        $response = $this->actingAs($admin, 'admin')->get(route('admin.login'));

        $response->assertRedirect();
    }
}
