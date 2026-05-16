<?php

namespace Tests\Feature;

use Tests\TestCase;

class CartPageTest extends TestCase
{
    public function test_cart_page_returns_200(): void
    {
        $this->get('/cart')->assertStatus(200);
    }

    public function test_cart_page_uses_store_layout_assets(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
        // Vite-injected CSS + JS markers — production build emits hashed
        // assets under /build/, dev server serves /resources/* directly.
        $response->assertSeeInOrder(['/build/assets/app-', '.css'], false);
        $response->assertSeeInOrder(['/build/assets/app-', '.js'], false);
        // Layout chrome present
        $response->assertSee('Keranjang Belanja', false);
        $response->assertSee('csrf-token', false);
        // Lucide stays loaded for icons
        $response->assertSee('unpkg.com/lucide', false);
    }

    public function test_cart_page_exposes_alpine_store_bindings(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
        // Alpine $store.cart wiring — render-time markers proving the page is
        // bound to the global store rather than server-rendered cart state.
        $response->assertSee('$store.cart.isEmpty', false);
        $response->assertSee('$store.cart.items', false);
        $response->assertSee('$store.cart.subtotal', false);
        $response->assertSee('$store.cart.total', false);
        $response->assertSee('$store.cart.shipping', false);
        // Stepper handlers
        $response->assertSee('$store.cart.increment', false);
        $response->assertSee('$store.cart.decrement', false);
        $response->assertSee('$store.cart.remove', false);
    }

    public function test_cart_page_renders_summary_and_checkout_cta(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
        // Summary copy + checkout CTA wired to named route
        $response->assertSee('Ringkasan', false);
        $response->assertSee('Subtotal', false);
        $response->assertSee('Ongkir', false);
        $response->assertSee('Lanjut Checkout', false);
        $response->assertSee(route('checkout.index'), false);
    }

    public function test_cart_page_empty_state_links_to_products(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
        $response->assertSee('Keranjang masih kosong', false);
        $response->assertSee(route('products.index'), false);
    }

    public function test_navbar_cart_link_uses_named_route_not_legacy_path(): void
    {
        $response = $this->get('/cart');

        $response->assertStatus(200);
        $response->assertSee(route('cart.index'), false);
        // Legacy prototype path must NOT leak into the rendered navbar
        $response->assertDontSee('href="/keranjang"', false);
    }
}
