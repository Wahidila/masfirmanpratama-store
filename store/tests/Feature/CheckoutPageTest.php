<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutPageTest extends TestCase
{
    // ─── GET /checkout ──────────────────────────────────────────────────────

    public function test_checkout_page_returns_200(): void
    {
        $this->get('/checkout')->assertStatus(200);
    }

    public function test_checkout_page_uses_store_layout_assets(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        // Vite-injected CSS + JS markers
        $response->assertSeeInOrder(['/build/assets/app-', '.css'], false);
        $response->assertSeeInOrder(['/build/assets/app-', '.js'], false);
        $response->assertSee('csrf-token', false);
        $response->assertSee('unpkg.com/lucide', false);
        // Page chrome
        $response->assertSee('Checkout Pembelian', false);
    }

    public function test_checkout_page_renders_customer_form_fields(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        // Required fields per task spec (nama, email, HP, alamat)
        $response->assertSee('id="customer_name"', false);
        $response->assertSee('id="customer_email"', false);
        $response->assertSee('id="customer_phone"', false);
        $response->assertSee('id="address_line"', false);
        $response->assertSee('id="address_city"', false);
        $response->assertSee('id="address_province"', false);

        // Static city/province dropdowns
        $response->assertSee('Surabaya', false);
        $response->assertSee('Jawa Timur', false);
        $response->assertSee('DKI Jakarta', false);
    }

    public function test_checkout_page_renders_shipping_method_dropdown(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        // Shipping method select (M1 hardcoded, M2 wire ke Agenwebsite.com)
        $response->assertSee('id="shipping_method"', false);
        // Dropdown rendered via Alpine x-for over shippingMethods, not server-rendered <option>s.
        // Assert the codes/labels are injected as JSON config to Alpine instead.
        $response->assertSee('"REG"', false);
        $response->assertSee('"YES"', false);
        $response->assertSee('"OKE"', false);
        $response->assertSee('JNE Reguler', false);
    }

    public function test_checkout_page_renders_payment_type_radio(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        // Lunas vs Cicilan radios
        $response->assertSee('name="payment_type"', false);
        $response->assertSee('value="lunas"', false);
        $response->assertSee('value="cicilan"', false);
        $response->assertSee('Down Payment', false); // schedule label
    }

    public function test_checkout_page_injects_dynamic_installment_schemes_from_config(): void
    {
        // Drop in a custom scheme set so we can prove the page is config-driven,
        // not hardcoded. KRITIS per task: skema bebas diatur admin.
        config()->set('store.installment_schemes', [
            ['name' => '4x Custom', 'n' => 4, 'dp_pct' => 25],
            ['name' => '12x Custom', 'n' => 12, 'dp_pct' => 10],
        ]);

        $response = $this->get('/checkout');

        $response->assertStatus(200);
        // Both custom schemes serialize into Alpine config.
        $response->assertSee('"4x Custom"', false);
        $response->assertSee('"12x Custom"', false);
        $response->assertSee('"dp_pct":25', false);
        $response->assertSee('"dp_pct":10', false);
        $response->assertSee('"n":12', false);
    }

    public function test_checkout_page_exposes_alpine_store_cart_bindings(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        // Page reads cart from $store.cart, not server-rendered cart state.
        $response->assertSee('$store.cart.isEmpty', false);
        $response->assertSee('$store.cart.items', false);
        $response->assertSee('$store.cart.subtotal', false);
        // Cart payload pushed to backend on submit.
        $response->assertSee('name="cart_json"', false);
    }

    public function test_checkout_page_renders_summary_with_subtotal_total_and_cta(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        $response->assertSee('Ringkasan Pesanan', false);
        $response->assertSee('Subtotal', false);
        $response->assertSee('Ongkir', false);
        $response->assertSee('Proses Pembayaran', false);
    }

    public function test_checkout_page_form_posts_to_checkout_store_route(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        // Form action wired to named POST route, not legacy prototype URL.
        $response->assertSee('action="'.route('checkout.store').'"', false);
        $response->assertSee('method="POST"', false);
        $response->assertDontSee('action="checkout-success.html"', false);
    }

    public function test_checkout_page_exposes_schedule_table_for_installment(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        // Schedule table rendered via Alpine x-for over `schedule` computed.
        $response->assertSee('data-testid="installment-schedule"', false);
        $response->assertSee('Jadwal Pembayaran', false);
        $response->assertSee('Jatuh Tempo', false);
    }

    // ─── POST /checkout (M1 stub) ───────────────────────────────────────────

    public function test_checkout_post_redirects_to_success_with_dummy_order_number(): void
    {
        $response = $this->post('/checkout', [
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'address_line' => 'Jl. Mawar No. 12 RT 03 RW 04',
            'address_city' => 'Surabaya',
            'address_province' => 'Jawa Timur',
            'shipping_method' => 'REG',
            'payment_type' => 'lunas',
            'cart_json' => '[]',
            'cart_total' => 4500000,
        ]);

        $response->assertStatus(302);
        // Redirects to /checkout/success/{order} with MFP- prefix.
        $response->assertRedirectContains('/checkout/success/MFP-');
    }

    public function test_checkout_success_page_shows_order_number(): void
    {
        $response = $this->get('/checkout/success/MFP-20260516-ABC123');

        $response->assertStatus(200);
        $response->assertSee('MFP-20260516-ABC123', false);
        // Order number rendered in the prominent slot with copy button.
        $response->assertSee('data-testid="order-number"', false);
        $response->assertSee('Salin nomor pesanan', false);
    }

    // ─── Checkout success page (port checkout-success.html) ─────────────────

    public function test_checkout_success_page_uses_store_layout_assets(): void
    {
        $response = $this->get('/checkout/success/MFP-20260516-ABC123');

        $response->assertStatus(200);
        // Layout chrome
        $response->assertSee('Order berhasil dibuat', false);
        $response->assertSee('csrf-token', false);
        $response->assertSeeInOrder(['/build/assets/app-', '.css'], false);
        $response->assertSeeInOrder(['/build/assets/app-', '.js'], false);
        $response->assertSee('unpkg.com/lucide', false);
    }

    public function test_checkout_success_page_renders_dummy_bank_accounts_from_config(): void
    {
        $response = $this->get('/checkout/success/MFP-20260516-ABC123');

        $response->assertStatus(200);
        // 2 dummy bank accounts dari config/store.php
        $response->assertSee('BCA', false);
        $response->assertSee('Mandiri', false);
        $response->assertSee('PT. Dummy AMC', false);
        // Format nomor rekening (dash-separated)
        $response->assertSee('1234-5678-9012', false);
        $response->assertSee('0987-6543-2109', false);
        // Card markers
        $response->assertSee('data-testid="bank-account"', false);
    }

    public function test_checkout_success_page_renders_lunas_total_when_payment_type_lunas(): void
    {
        // Sanity: the POST stub redirects (302) before we follow.
        $this
            ->post('/checkout', [
                'customer_name' => 'Budi Santoso',
                'customer_email' => 'budi@example.com',
                'customer_phone' => '081234567890',
                'address_line' => 'Jl. Mawar No. 12 RT 03 RW 04',
                'address_city' => 'Surabaya',
                'address_province' => 'Jawa Timur',
                'shipping_method' => 'REG',
                'payment_type' => 'lunas',
                'cart_json' => '[]',
                'cart_total' => 4525000,
            ])
            ->assertStatus(302);

        // Re-issue and follow the redirect to read the rendered page.
        $response = $this->followingRedirects()->post('/checkout', [
            'customer_name' => 'Budi Santoso',
            'customer_email' => 'budi@example.com',
            'customer_phone' => '081234567890',
            'address_line' => 'Jl. Mawar No. 12 RT 03 RW 04',
            'address_city' => 'Surabaya',
            'address_province' => 'Jawa Timur',
            'shipping_method' => 'REG',
            'payment_type' => 'lunas',
            'cart_json' => '[]',
            'cart_total' => 4525000,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Total Transfer (Lunas)', false);
        // Indonesian locale formatting (dot thousand sep)
        $response->assertSee('Rp 4.525.000', false);
        $response->assertDontSee('Total Transfer (Down Payment)', false);
    }

    public function test_checkout_success_page_renders_dp_total_and_schedule_when_cicilan(): void
    {
        $schedule = json_encode([
            ['label' => 'Down Payment', 'note' => 'Bayar sekarang (30% dari total)', 'due_label' => 'Hari ini', 'amount' => 1357500],
            ['label' => 'Cicilan ke-1 dari 2', 'note' => '', 'due_label' => '16 Jun 2026', 'amount' => 1583750],
            ['label' => 'Cicilan ke-2 dari 2', 'note' => 'Cicilan terakhir', 'due_label' => '16 Jul 2026', 'amount' => 1583750],
        ]);

        $response = $this->followingRedirects()->post('/checkout', [
            'customer_name' => 'Siti Aminah',
            'customer_email' => 'siti@example.com',
            'customer_phone' => '081298765432',
            'address_line' => 'Jl. Melati No. 7 RT 01 RW 02',
            'address_city' => 'Bandung',
            'address_province' => 'Jawa Barat',
            'shipping_method' => 'REG',
            'payment_type' => 'cicilan',
            'installment_scheme' => 0,
            'schedule_json' => $schedule,
            'cart_json' => '[]',
            'cart_total' => 4525000,
        ]);

        $response->assertStatus(200);
        // DP total dipakai sebagai total transfer.
        $response->assertSee('Total Transfer (Down Payment)', false);
        $response->assertSee('Rp 1.357.500', false);
        // Schedule preview rendered.
        $response->assertSee('Jadwal Pembayaran', false);
        $response->assertSee('Cicilan ke-1 dari 2', false);
        $response->assertSee('Cicilan ke-2 dari 2', false);
        $response->assertSee('16 Jul 2026', false);
        $response->assertSee('Rp 1.583.750', false);
    }

    public function test_checkout_success_page_links_to_upload_and_track(): void
    {
        $response = $this->get('/checkout/success/MFP-20260516-ABC123');

        $response->assertStatus(200);
        // Upload CTA wired to upload route — query string carries M1 payment
        // context (type/total/n/seq) so upload page can pre-fill, so we check
        // the path-only prefix rather than a fully literal href.
        $response->assertSee('href="'.url('/upload/MFP-20260516-ABC123'), false);
        $response->assertSee('Upload bukti bayar sekarang', false);
        $response->assertSee('href="'.url('/track/MFP-20260516-ABC123').'"', false);
        $response->assertSee('Track order', false);
    }

    public function test_checkout_success_page_renders_wa_admin_link(): void
    {
        $response = $this->get('/checkout/success/MFP-20260516-ABC123');

        $response->assertStatus(200);
        $waNumber = config('store.wa_admin.number');
        $response->assertSee('https://wa.me/'.$waNumber, false);
        $response->assertSee('Chat admin di WhatsApp', false);
    }

    // ─── Cart link integrity (regression) ───────────────────────────────────

    public function test_navbar_checkout_url_uses_named_route(): void
    {
        $response = $this->get('/checkout');

        $response->assertStatus(200);
        $response->assertSee(route('cart.index'), false); // navbar link
    }
}
