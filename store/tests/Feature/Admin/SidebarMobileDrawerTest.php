<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use Database\Seeders\AdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TailAdmin shell regression guard (B1 — admin shell swap).
 *
 * Verifies the TailAdmin verbatim shell layout: sidebar + header + backdrop
 * are wired correctly, navigation from config renders, and mobile support
 * exists via Alpine $store.sidebar.
 */
class SidebarMobileDrawerTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AdminSeeder::class);
        $this->admin = Admin::first();
    }

    public function test_admin_layout_has_mobile_drawer_root(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $body = $response->getContent();

        // TailAdmin: sidebar with Alpine $store.sidebar binding for mobile
        $this->assertStringContainsString(
            '$store.sidebar.isMobileOpen',
            $body,
            'TailAdmin shell: $store.sidebar.isMobileOpen binding tidak ditemukan.'
        );

        // Backdrop overlay for mobile
        $this->assertStringContainsString(
            '$store.sidebar.setMobileOpen(false)',
            $body,
            'TailAdmin backdrop: setMobileOpen(false) click handler tidak ditemukan.'
        );
    }

    public function test_admin_layout_has_hamburger_button_with_aria(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $body = $response->getContent();

        // Mobile toggle button calls $store.sidebar.toggleMobileOpen()
        $this->assertStringContainsString(
            '$store.sidebar.toggleMobileOpen()',
            $body,
            'Mobile sidebar toggle button dengan toggleMobileOpen() tidak ditemukan.'
        );

        // Desktop toggle button calls $store.sidebar.toggleExpanded()
        $this->assertStringContainsString(
            '$store.sidebar.toggleExpanded()',
            $body,
            'Desktop sidebar toggle button dengan toggleExpanded() tidak ditemukan.'
        );

        // Toggle buttons have aria-label
        $this->assertStringContainsString(
            'aria-label="Toggle',
            $body,
            'Sidebar toggle button aria-label hilang.'
        );
    }

    public function test_admin_layout_has_sidebar_aside_with_tailadmin_structure(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $body = $response->getContent();

        // Sidebar aside with TailAdmin structure
        $this->assertStringContainsString(
            'id="sidebar"',
            $body,
            'TailAdmin sidebar <aside id="sidebar"> tidak ditemukan.'
        );

        // Sidebar responsive width binding
        $this->assertStringContainsString(
            'w-[290px]',
            $body,
            'TailAdmin sidebar width binding w-[290px] tidak ditemukan.'
        );

        $this->assertStringContainsString(
            'w-[90px]',
            $body,
            'TailAdmin sidebar collapsed width w-[90px] tidak ditemukan.'
        );
    }

    public function test_sidebar_renders_all_primary_nav_links(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $body = $response->getContent();

        // Verify semua primary nav link ada di sidebar
        foreach (config('admin-nav.primary', []) as $item) {
            $href = route($item['route']);
            $this->assertStringContainsString(
                $href,
                $body,
                "Sidebar hilang link '{$item['label']}' (href {$href})."
            );
        }
    }

    public function test_admin_layout_loads_admin_css_and_js(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $body = $response->getContent();

        // Admin CSS loaded via asset (not Vite for CSS)
        $this->assertStringContainsString(
            'admin/admin.css',
            $body,
            'Admin CSS (admin/admin.css) tidak di-load di layout.'
        );

        // Admin JS loaded via Vite
        $this->assertStringContainsString(
            'admin-',
            $body,
            'Admin JS bundle (admin-*.js) tidak di-load via Vite.'
        );
    }

    public function test_admin_layout_has_theme_toggle(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $body = $response->getContent();

        // Theme toggle button calls $store.theme.toggle()
        $this->assertStringContainsString(
            '$store.theme.toggle()',
            $body,
            'Theme toggle button dengan $store.theme.toggle() tidak ditemukan.'
        );
    }

    public function test_admin_layout_has_user_info_and_logout(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $body = $response->getContent();

        // User name rendered
        $this->assertStringContainsString(
            $this->admin->name,
            $body,
            'User name tidak ditampilkan di header user dropdown.'
        );

        // Logout form present
        $this->assertStringContainsString(
            route('admin.logout'),
            $body,
            'Logout form action tidak ditemukan di header.'
        );
    }
}
