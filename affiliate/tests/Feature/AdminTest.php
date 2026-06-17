<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    protected function loginAdmin()
    {
        $this->post(route('admin.login.submit'), [
            'email' => config('admin.email'),
            'password' => config('admin.password'),
        ]);
    }

    public function test_admin_login_page_loads(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('Admin Panel');
    }

    public function test_admin_can_login(): void
    {
        $response = $this->post(route('admin.login.submit'), [
            'email' => config('admin.email'),
            'password' => config('admin.password'),
        ]);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_admin_login_fails_with_wrong_password(): void
    {
        $response = $this->from(route('admin.login'))->post(route('admin.login.submit'), [
            'email' => config('admin.email'),
            'password' => 'wrong-password',
        ]);
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_dashboard_loads(): void
    {
        $this->loginAdmin();
        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_admin_affiliators_index_loads(): void
    {
        $this->loginAdmin();
        $response = $this->get(route('admin.affiliators.index'));
        $response->assertStatus(200);
    }

    public function test_admin_commissions_index_loads(): void
    {
        $this->loginAdmin();
        $response = $this->get(route('admin.commissions.index'));
        $response->assertStatus(200);
    }

    public function test_admin_commissions_settings_loads(): void
    {
        $this->loginAdmin();
        $response = $this->get(route('admin.commissions.settings'));
        $response->assertStatus(200);
    }

    public function test_admin_withdrawals_index_loads(): void
    {
        $this->loginAdmin();
        $response = $this->get(route('admin.withdrawals.index'));
        $response->assertStatus(200);
    }

    public function test_admin_materials_index_loads(): void
    {
        $this->loginAdmin();
        $response = $this->get(route('admin.materials.index'));
        $response->assertStatus(200);
    }

    public function test_admin_materials_create_loads(): void
    {
        $this->loginAdmin();
        $response = $this->get(route('admin.materials.create'));
        $response->assertStatus(200);
    }

    public function test_admin_can_logout(): void
    {
        $this->loginAdmin();
        $response = $this->post(route('admin.logout'));
        $response->assertRedirect(route('admin.login'));
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));
    }
}
