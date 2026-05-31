# M-Shipping Implementation Plan — Store MasFirmanPratama (Laravel 11)

> **For Engineer (opencode):** Implement task-by-task. Each task = TDD cycle (failing test → run → minimal code → run → commit). Branch `feat/m-shipping`. JANGAN push ke main. JANGAN hilangkan `trustProxies` di `bootstrap/app.php`. `php artisan test` harus tetap hijau (baseline 283) di tiap akhir task — jangan ubah test lama biar lolos.

**Goal:** Ongkir real-time dari API Agenwebsite muncul di checkout (origin Surabaya → kurir + harga + ETD), admin bisa generate AWB/resi + terima callback status, customer bisa lacak paket.

**Architecture:** 3 layer. (1) `AgenwebsiteClient` — HTTP client low-level (replikasi `wp_remote_post`: UA WordPress + form-encoded + header plugin). (2) `ShippingRateService` / `FulfillmentService` — business logic (weight calc, dimensi, custom tariff merge, AWB flow). (3) Integrasi: `CheckoutController` (rate di checkout), `Admin\OrderController` (AWB), webhook controller (callback), tracking page. Data persisten: kolom baru di `products` (weight/dimensi) + `orders` (shipping meta) + tabel `shipping_custom_tariffs` (opsional fase lanjut).

**Tech Stack:** Laravel 11, Http client, SQLite (dev) / MySQL (prod), Pest/PHPUnit (283 test baseline), Pint, PHPStan L6. Provider: `api-v2.agenwebsite.com/v2`.

**CRITICAL FACTS (terverifikasi live 2026-05-31):**
- API **butuh User-Agent `WordPress/6.8.3; <site-url>`** + body **form-encoded** (bukan JSON). Tanpa UA → HTTP 500.
- Header wajib: `plugin-version: 2.3.11`, `wordpress-version: 6.8.3`, `woocommerce-version: 10.0`, `php-version`, `site-url`. Body wajib: `license`, `product=agenwebsite-shipping`.
- **Unit berat = KG** (weight=1 → Rp17.000). Kirim kg aktual, minimum 1.
- Response `/shipping/price`: `{message, data:[{courier, service, service_name, price (STRING), etd, etd_from, etd_to}]}`.
- License di `.env` → `AGENWEBSITE_SHIPPING_LICENSE`, `AGENWEBSITE_SHIPPING_API_URL`. ⚠️ License expire 2026-06-01 — konfirmasi user sudah perpanjang sebelum andalkan di produksi.
- Existing: `CheckoutController::resolveShippingCost(?string $code)` baca `config('store.shipping_methods')` (flat statis). Form checkout kirim `shipping_method` (code), `address_city`, `address_province`, `address_postal`, `cart_json`.
- Origin store = **Surabaya** (slug `surabaya`).

---

## PHASE 0 — Config & Environment

### Task 0.1: Tambah config shipping
**Objective:** Sentralisasi setting shipping ke `config/shipping.php` (bukan tunggu DB).

**Files:**
- Create: `config/shipping.php`

**Step 1:** Buat `config/shipping.php`:
```php
<?php
return [
    'api_url' => env('AGENWEBSITE_SHIPPING_API_URL', 'https://api-v2.agenwebsite.com/v2'),
    'license' => env('AGENWEBSITE_SHIPPING_LICENSE', ''),
    'product' => 'agenwebsite-shipping',
    // Header replikasi wp_remote_post — WAJIB supaya API tidak balik 500
    'user_agent' => env('AGENWEBSITE_SHIPPING_UA', 'WordPress/6.8.3; '.env('APP_URL', 'https://masfirmanpratama.com')),
    'plugin_version' => '2.3.11',
    'wordpress_version' => '6.8.3',
    'woocommerce_version' => '10.0',
    'site_url' => env('APP_URL', 'https://masfirmanpratama.com'),
    'timeout' => 30,

    // Origin pengiriman (Surabaya)
    'origin' => env('SHIPPING_ORIGIN', 'surabaya'),
    'origin_zipcode' => env('SHIPPING_ORIGIN_ZIPCODE', '60111'),

    // Kurir domestik yang diaktifkan (interseksi dgn service API)
    'couriers' => ['jne', 'jnt', 'sicepat', 'anteraja', 'pos'],

    // Berat & dimensi default produk (kg & cm) bila produk tak punya data
    'default_weight_kg' => 1,
    'default_dimensions_cm' => ['length' => 10, 'width' => 10, 'height' => 5],

    // Cache TTL (detik): master data couriers/services 24 jam; rate harga pendek
    'cache_master_ttl' => 86400,
    'cache_rate_ttl' => 1800,

    // Markup per service (extra_cost), key = service_id. Kosong = tanpa markup.
    'service_markup' => [],
];
```

**Step 2:** Verifikasi: `php artisan config:clear && php artisan tinker --execute="echo config('shipping.origin');"` → `surabaya`.

**Step 3:** Commit:
```bash
git add config/shipping.php && git commit -m "feat(shipping): add shipping config (agenwebsite api + origin surabaya)"
```

---

## PHASE A — API Client (Low-Level)

### Task A.1: AgenwebsiteClient — koneksi & license check
**Objective:** Service yang manggil API persis seperti plugin (UA WordPress + form-encoded).

**Files:**
- Create: `app/Services/Shipping/AgenwebsiteClient.php`
- Test: `tests/Feature/Shipping/AgenwebsiteClientTest.php`

**Step 1: Failing test** (`tests/Feature/Shipping/AgenwebsiteClientTest.php`):
```php
<?php
use App\Services\Shipping\AgenwebsiteClient;
use Illuminate\Support\Facades\Http;

it('sends WordPress user-agent and form body to license endpoint', function () {
    Http::fake([
        '*/license/activate' => Http::response([
            'data' => ['type' => 'exclusive', 'shipping_quota' => 'Unlimited'],
            'message' => 'Berhasil terkoneksi dengan Agenwebsite',
        ], 200),
    ]);

    $client = app(AgenwebsiteClient::class);
    $result = $client->activateLicense();

    expect($result['status'])->toBe('success');
    Http::assertSent(function ($request) {
        return str_contains($request->header('User-Agent')[0], 'WordPress/')
            && $request['product'] === 'agenwebsite-shipping'
            && $request->hasHeader('site-url');
    });
});
```

**Step 2:** Run: `php artisan test --filter=AgenwebsiteClientTest` → FAIL (class belum ada).

**Step 3: Implementasi** (`app/Services/Shipping/AgenwebsiteClient.php`):
```php
<?php

namespace App\Services\Shipping;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class AgenwebsiteClient
{
    public function __construct(private array $cfg)
    {
    }

    public static function fromConfig(): self
    {
        return new self(config('shipping'));
    }

    /** Base request meniru wp_remote_post: UA WordPress + header plugin + form body. */
    protected function http(): PendingRequest
    {
        return Http::asForm()
            ->withUserAgent($this->cfg['user_agent'])
            ->withHeaders([
                'plugin-version' => $this->cfg['plugin_version'],
                'wordpress-version' => $this->cfg['wordpress_version'],
                'woocommerce-version' => $this->cfg['woocommerce_version'],
                'php-version' => PHP_VERSION,
                'site-url' => $this->cfg['site_url'],
            ])
            ->timeout($this->cfg['timeout']);
    }

    protected function baseBody(array $extra = []): array
    {
        return array_merge([
            'license' => $this->cfg['license'],
            'product' => $this->cfg['product'],
        ], $extra);
    }

    /** POST ke endpoint, normalisasi hasil ke {status,message,result}. */
    public function post(string $path, array $body = [], array $query = []): array
    {
        if (($this->cfg['license'] ?? '') === '') {
            return ['status' => 'error', 'message' => 'Kode Lisensi belum diisi.', 'result' => null];
        }

        $url = rtrim($this->cfg['api_url'], '/').'/'.ltrim($path, '/');
        if ($query) {
            $url .= '?'.http_build_query($query);
        }

        try {
            $resp = $this->http()->post($url, $this->baseBody($body));
        } catch (\Throwable $e) {
            return ['status' => 'error', 'message' => 'Gagal terhubung dengan Agenwebsite', 'result' => null];
        }

        $json = $resp->json() ?? [];
        if ($resp->successful()) {
            return ['status' => 'success', 'message' => $json['message'] ?? 'OK', 'result' => $json['data'] ?? null];
        }

        return ['status' => 'error', 'message' => $json['message'] ?? 'Gagal terhubung dengan Agenwebsite', 'result' => null];
    }

    public function activateLicense(): array
    {
        return $this->post('license/activate');
    }
}
```

**Step 4:** Daftarkan binding di `app/Providers/AppServiceProvider.php` (method `register`):
```php
$this->app->bind(\App\Services\Shipping\AgenwebsiteClient::class, fn () => \App\Services\Shipping\AgenwebsiteClient::fromConfig());
```

**Step 5:** Run: `php artisan test --filter=AgenwebsiteClientTest` → PASS.

**Step 6:** Commit:
```bash
git add app/Services/Shipping/AgenwebsiteClient.php tests/Feature/Shipping/AgenwebsiteClientTest.php app/Providers/AppServiceProvider.php
git commit -m "feat(shipping): add AgenwebsiteClient (wp_remote_post replica)"
```

### Task A.2: Endpoint couriers & services (master data + cache)
**Objective:** Method `couriers()` & `services($category)` dengan cache 24 jam + fallback JSON.

**Files:**
- Modify: `app/Services/Shipping/AgenwebsiteClient.php`
- Create: `tests/Feature/Shipping/AgenwebsiteMasterDataTest.php`

**Step 1: Failing test** — fake `/shipping/couriers` & `/shipping/services?category=domestic`, assert hasil array + ke-cache (panggil 2x → Http::assertSentCount 1).

**Step 2:** Run → FAIL.

**Step 3:** Tambah method (gunakan `Cache::remember('shipping.couriers', config('shipping.cache_master_ttl'), ...)`). Endpoint: `services` pakai query `['category' => 'domestic']`. Pada error API → fallback baca `docs_dev/reference/agenwebsite-shipping/data/couriers.json` / `services.json` (copy file ke `storage/app/shipping/` saat task ini, jangan baca dari reference dir).

**Step 4:** Run → PASS.

**Step 5:** Commit: `feat(shipping): couriers & services master data with cache + fallback`.

### Task A.3: Endpoint price (rate ongkir)
**Objective:** Method `price(array $params)` → array baris tarif.

**Files:**
- Modify: `app/Services/Shipping/AgenwebsiteClient.php`
- Create: `tests/Feature/Shipping/AgenwebsitePriceTest.php`

**Step 1: Failing test** — fake `/shipping/price` balikin `{message:'Success', data:[{courier:'jne',service:'jne_reg',service_name:'REG',price:'17000',etd:'1-2 days',etd_from:1,etd_to:2}]}`. Assert `price()` balikin array dgn 1 baris, `price` ter-cast ke int 17000.

**Step 2:** Run → FAIL.

**Step 3:** Implementasi `price(array $params)` → `$this->post('shipping/price', $params)`; cast tiap `price` ke int. Cache pendek (`cache_rate_ttl`) keyed by hash params (origin+dest+weight+courier).

**Step 4:** Run → PASS.

**Step 5:** Commit: `feat(shipping): price endpoint with int cast + short cache`.

---

## PHASE B — Rate Service & Integrasi Checkout

### Task B.1: Tambah kolom weight & dimensi ke produk
**Objective:** Produk fisik (buku) punya berat + dimensi buat hitung ongkir.

**Files:**
- Create: `database/migrations/xxxx_add_shipping_fields_to_products.php`
- Modify: `app/Models/Product.php` (fillable + casts)
- Modify: `database/seeders/ProductSeeder.php` (isi weight buku, mis. 0.3–0.5 kg)
- Test: `tests/Feature/Shipping/ProductShippingFieldsTest.php`

**Step 1: Migration** — tambah kolom nullable: `weight_kg` (decimal 8,2, default null), `length_cm`/`width_cm`/`height_cm` (unsigned int, nullable), `is_shippable` (boolean default true; kelas/digital = false).

**Step 2:** Run `php artisan migrate` → cek kolom ada.

**Step 3:** Update `Product` fillable + casts (`weight_kg` => 'decimal:2', `is_shippable` => 'boolean'). Update seeder: buku isi weight wajar, kelas set `is_shippable=false`.

**Step 4: Test** — `Product::factory()` / seeder punya `weight_kg`, kelas `is_shippable=false`.

**Step 5:** Run `php artisan test --filter=ProductShippingFieldsTest` + `php artisan migrate:fresh --seed` → hijau.

**Step 6:** Commit: `feat(shipping): add weight/dimension fields to products`.

### Task B.2: ShippingRateService — weight & dimensi calculator
**Objective:** Hitung total berat (kg) + dimensi paket dari cart, replikasi logika plugin.

**Files:**
- Create: `app/Services/Shipping/ShippingRateService.php`
- Test: `tests/Feature/Shipping/ShippingWeightCalcTest.php`

**Step 1: Failing test** — kasih cart 2 item (qty beda), assert: total weight = Σ(weight×qty) min 1, skip item `is_shippable=false`, dimensi length=max, width=max, height=Σ(tinggi×qty), default 10/10/5 bila kosong.

**Step 2:** Run → FAIL.

**Step 3:** Implementasi method `calculateWeight(array $cartItems): float` & `calculateDimensions(array $cartItems): array`. Resolve produk dari slug (pakai Product model), skip non-shippable, fallback `config('shipping.default_weight_kg')`.

**Step 4:** Run → PASS.

**Step 5:** Commit: `feat(shipping): weight & dimension calculator in ShippingRateService`.

### Task B.3: ShippingRateService — getRates (origin→dest)
**Objective:** Gabungkan client + calculator → daftar rate siap tampil di checkout.

**Files:**
- Modify: `app/Services/Shipping/ShippingRateService.php`
- Test: `tests/Feature/Shipping/ShippingRatesTest.php`

**Step 1: Failing test** — fake `AgenwebsiteClient::price`, input (province, city, district, zipcode, cartItems), assert output array `[{courier, service, label, price (int), etd}]`, sudah +markup bila ada di config, hanya kurir aktif.

**Step 2:** Run → FAIL.

**Step 3:** Implementasi `getRates(array $destination, array $cartItems): array`:
- weight = calculateWeight, dimensi = calculateDimensions.
- params: origin (config), origin_zipcode, province (nama lengkap), city, district, zipcode, weight, courier (implode `|` dari config couriers), length/width/height.
- panggil `client->price($params)`.
- filter service aktif (config), map ke `{courier, service, label: "{service_name} ({etd})", price: price + markup, etd}`.
- bila API error/empty → return `[]` (checkout fallback ke flat config lama supaya tidak blank — lihat B.4).

**Step 4:** Run → PASS.

**Step 5:** Commit: `feat(shipping): getRates aggregator (origin surabaya -> destination)`.

### Task B.4: Endpoint AJAX rate untuk checkout (frontend)
**Objective:** Frontend checkout panggil endpoint ini saat user isi alamat → tampil pilihan kurir + ongkir.

**Files:**
- Create: `app/Http/Controllers/ShippingRateController.php`
- Modify: `routes/web.php` (route `POST /shipping/rates`, throttle + CSRF)
- Test: `tests/Feature/Shipping/ShippingRateEndpointTest.php`

**Step 1: Failing test** — POST `/shipping/rates` dgn payload (city, province, zipcode, cart_json) → 200 JSON `{rates:[...]}`. Validasi: field wajib, cart_json valid. Fake client.

**Step 2:** Run → FAIL.

**Step 3:** Implementasi controller `rates(Request $request)`: validate, parse cart_json, panggil `ShippingRateService::getRates`, return JSON. Route pakai middleware `throttle:30,1`. JANGAN expose error internal (jangan bocorkan license).

**Step 4:** Run → PASS.

**Step 5:** Commit: `feat(shipping): AJAX /shipping/rates endpoint for checkout`.

### Task B.5: Wire checkout blade — pilih kurir dinamis
**Objective:** Ganti dropdown shipping flat (config) jadi dinamis dari API saat alamat diisi.

**Files:**
- Modify: `resources/views/pages/checkout/index.blade.php` (Alpine: fetch rates saat city/province/zipcode terisi → render radio kurir)
- Modify: `app/Http/Controllers/CheckoutController.php` (`resolveShippingCost` + validasi)
- Test: `tests/Feature/CheckoutShippingIntegrationTest.php`

**Step 1: Failing test** — POST checkout dgn `shipping_method` = service_id dinamis (mis. `jne_reg`) → order tersimpan dengan ongkir = harga hasil rate (BUKAN flat config). Fake client. Pastikan server RE-VALIDATE harga ongkir server-side (jangan trust harga dari client — panggil ulang getRates, cocokkan service_id).

**Step 2:** Run → FAIL.

**Step 3a (blade):** Alpine `checkoutPage()` tambah state `shippingRates`, `loadingRates`. Watch perubahan city/province/zipcode → debounce → `fetch('/shipping/rates', {cart_json, ...})` → render radio. Pertahankan desain existing (glass card, primary color). Saat rates kosong → tampilkan pesan "Masukkan alamat lengkap untuk cek ongkir" atau fallback.

**Step 3b (controller):** `resolveShippingCost` di `CheckoutController::store` → kalau `shipping_method` adalah service_id dinamis, panggil `ShippingRateService::getRates` ulang server-side, ambil harga yg match service_id (anti-tamper, konsisten pola cart_total existing). Simpan `shipping_courier`, `shipping_service`, `shipping_etd` ke order.

**Step 4:** Run → PASS + `php artisan test` full hijau (283+).

**Step 5:** Commit: `feat(shipping): dynamic courier selection in checkout (server-validated)`.

### Task B.6: Simpan shipping meta ke order
**Objective:** Order menyimpan kurir/service/ongkir/etd terpilih.

**Files:**
- Create: `database/migrations/xxxx_add_shipping_to_orders.php`
- Modify: `app/Models/Order.php`
- Test: `tests/Feature/Shipping/OrderShippingMetaTest.php`

**Step 1: Migration** — kolom: `shipping_courier` (string nullable), `shipping_service` (string nullable), `shipping_cost` (int default 0; kalau sudah ada `total` saja, pisahkan subtotal vs ongkir), `shipping_etd` (string nullable). (Cek dulu schema `orders` existing — jangan duplikat kolom.)

**Step 2-5:** migrate, update model fillable, test order simpan meta, commit `feat(shipping): persist shipping meta on orders`.

### Task B.7: Admin Settings — panel pengaturan ongkir (DB-driven)
**Objective:** Klien bisa atur origin, kurir aktif, markup, license, toggle ongkir dari admin panel TANPA rilis kode (config file jadi DEFAULT/fallback).

**Files:**
- Create: `database/migrations/xxxx_create_shipping_settings_table.php` (atau pakai tabel `settings` existing kalau ada — CEK DULU: `search_files settings table/model`)
- Create: `app/Models/ShippingSetting.php` (atau helper `setting()` existing)
- Create: `app/Http/Controllers/Admin/ShippingSettingController.php`
- Create: `resources/views/admin/shipping/settings.blade.php`
- Modify: `routes/web.php` (route admin `GET/POST /admin/shipping/settings`, protected admin middleware)
- Modify: `config/shipping.php` reader → `ShippingRateService`/`AgenwebsiteClient` baca dari DB settings dulu, fallback ke config.
- Test: `tests/Feature/Shipping/AdminShippingSettingsTest.php`

**Step 1: CEK existing** — apakah store sudah punya tabel/model `settings` (lihat komentar `config/store.php`: "M2 di-replace ke tabel settings"). Kalau ada, pakai itu (key-value). Kalau belum, buat tabel `shipping_settings` key-value sederhana (`key` string unique, `value` text/json).

**Step 2: Failing test** — admin auth GET `/admin/shipping/settings` → 200 render form; POST update (origin=`surabaya`, couriers=[jne,jnt], markup, license) → tersimpan; non-admin → 403. `ShippingRateService` pakai nilai DB (origin berubah → params API ikut berubah).

**Step 3:** Run → FAIL.

**Step 4: Implementasi:**
- Setting yang bisa diatur admin: `origin` (kota asal), `origin_zipcode`, `couriers` (multi-select dari `/shipping/couriers`), `service_markup` (per service), `license` (password field — JANGAN tampilkan plaintext, tampilkan masked + status koneksi via `activateLicense()`), `shipping_enabled` (toggle global), `default_weight_kg`.
- Helper resolusi: `config('shipping')` jadi default; `ShippingSetting::get($key, $default)` override. Tambah method `resolved()` di service yang merge DB→config.
- Form admin: pertahankan desain admin panel existing (cek layout admin). Tampilkan status license (terkoneksi/expired + expire_date dari `activateLicense`).
- **Keamanan**: license disimpan encrypted (`Crypt::encryptString`) atau tetap di `.env` & admin cuma override non-secret. Pilih: license tetap `.env` (jangan pindah ke DB plaintext), admin settings atur origin/kurir/markup/toggle saja. JANGAN tampilkan license value.

**Step 5:** Run → PASS + full suite hijau.

**Step 6:** Commit: `feat(shipping): admin shipping settings panel (db-driven origin/couriers/markup)`.

---

## PHASE C — Fulfillment & AWB (Admin)

> Konteks: store sudah punya admin order management (approve/reject/ship) + status order. AWB generate bersifat ASINKRON — banyak kasus AWB belum langsung jadi → tunggu webhook callback (Phase C.3).

### Task C.1: AgenwebsiteClient — endpoint fulfillment
**Objective:** Tambah method `getRatesFulfillment`, `createShipment`, `requestPickup` ke client.

**Files:**
- Modify: `app/Services/Shipping/AgenwebsiteClient.php`
- Test: `tests/Feature/Shipping/AgenwebsiteFulfillmentTest.php`

**Step 1: Failing test** — fake `/shipment/create-order` balikin 3 skenario: (a) AWB langsung (`data.airwaybill`), (b) waiting (`data` tanpa airwaybill), (c) pending_payment (`data.payment_url`). Assert method balikin shape konsisten `{status, airwaybill?, payment_url?, reference_id?}`.

**Step 2:** Run → FAIL.

**Step 3:** Implementasi method-method (endpoint: `shipment/rates`, `shipment/create-order`, `shipment/request-pickup`, `shipment/eligibility`). Semua via `$this->post(...)`.

**Step 4:** Run → PASS.

**Step 5:** Commit: `feat(shipping): fulfillment endpoints in AgenwebsiteClient`.

### Task C.2: FulfillmentService — create shipment + state machine
**Objective:** Logika generate AWB dari order + transisi status.

**Files:**
- Create: `app/Services/Shipping/FulfillmentService.php`
- Create: `database/migrations/xxxx_add_fulfillment_to_orders.php`
- Modify: `app/Models/Order.php`
- Test: `tests/Feature/Shipping/FulfillmentServiceTest.php`

**Step 1: Migration** — kolom fulfillment di orders: `fulfillment_status` (string nullable; enum: `pending_payment|waiting_awb|shipped|delivered|failed`), `tracking_number` (string nullable), `tracking_status` (string nullable), `fulfillment_reference_id` (string nullable), `fulfillment_api_order_id` (string nullable), `label_url` (string nullable), `fulfillment_payload` (json nullable — snapshot create-order), `shipped_email_sent_at` (timestamp nullable). Normalisasi status ke lowercase (beda dgn plugin yg case-mixed).

**Step 2:** Run migrate → cek kolom.

**Step 3: Failing test** — `FulfillmentService::createShipment(Order)` 3 skenario: AWB langsung → status `shipped` + tracking_number tersimpan; waiting → `waiting_awb`; pending_payment → `pending_payment` + payment_url. Build payload shipper (dari config) + receiver (dari order address) + items (dari order items, weight kg, dimensi).

**Step 4:** Implementasi `buildShipmentData(Order)` + `createShipment(Order)`. Phone normalisasi ke `62xxx`. Min weight 1 kg.

**Step 5:** Run → PASS.

**Step 6:** Commit: `feat(shipping): FulfillmentService createShipment + order fulfillment columns`.

### Task C.3: Webhook AWB callback (asinkron)
**Objective:** Terima callback dari Agenwebsite → update status order (AWB jadi / failed / status_update).

**Files:**
- Create: `app/Http/Controllers/Webhooks/AwbCallbackController.php`
- Modify: `routes/web.php` (route `POST /webhooks/agenwebsite/awb` — TANPA CSRF, perlu di-except di `bootstrap/app.php` VerifyCsrfToken, TAPI dgn signature verification)
- Test: `tests/Feature/Shipping/AwbCallbackTest.php`

**Step 1: Failing test** — 3 tipe callback:
- success (`airwaybill` ada) → order `tracking_number` ter-set, status `shipped`, trigger email shipped (assert Mail::fake / Notification sent).
- failed (`status:'failed'`) → status `failed`, field fulfillment dibersihkan utk retry.
- status_update (`status:'status_update'`, `tracking_status:'DELIVERED'`) → status `delivered`.
- **Signature**: header `AW-Signature` = `hash('sha256', $license . $rawBody)`. Test tanpa/dengan signature salah → 401.

**Step 2:** Run → FAIL.

**Step 3:** Implementasi controller: verify signature pakai `hash_equals(hash('sha256', config('shipping.license').$request->getContent()), $request->header('AW-Signature'))`. Lookup order by `fulfillment_api_order_id` → `fulfillment_reference_id` → order_number. 3 handler method. JANGAN log isi license.
- Route harus di-except dari CSRF: di `bootstrap/app.php` → `$middleware->validateCsrfTokens(except: ['webhooks/agenwebsite/*'])`.

**Step 4:** Run → PASS.

**Step 5:** Commit: `feat(shipping): AWB callback webhook with signature verification`.

### Task C.4: Email notif "Pesanan Dikirim"
**Objective:** Customer dapat email saat AWB jadi (status shipped), idempoten.

**Files:**
- Create: `app/Mail/OrderShippedMail.php` + `resources/views/emails/order-shipped.blade.php`
- Modify: `FulfillmentService` / `AwbCallbackController` (trigger saat → shipped)
- Test: `tests/Feature/Shipping/OrderShippedMailTest.php`

**Step 1: Failing test** — saat order → shipped & `shipped_email_sent_at` null & ada tracking_number & ada email → Mail terkirim + `shipped_email_sent_at` ter-set. Panggil 2x → cuma 1 email (idempoten).

**Step 2-5:** implementasi Mailable (subject "Pesanan #{order} telah dikirim", tampil resi + kurir + link tracking), guard idempoten, test, commit `feat(shipping): customer order-shipped email (idempotent)`.

### Task C.5: Admin UI — tombol Generate Resi + status
**Objective:** Admin bisa generate AWB dari halaman order + lihat status fulfillment + label.

**Files:**
- Modify: admin order views (cek path existing, mis. `resources/views/admin/orders/show.blade.php`)
- Modify: `app/Http/Controllers/Admin/OrderController.php` (action generateShipment)
- Modify: `routes/web.php` (route admin protected)
- Test: `tests/Feature/Shipping/AdminGenerateShipmentTest.php`

**Step 1: Failing test** — admin auth POST generate-shipment untuk order → panggil FulfillmentService, render status. Guard: hanya admin, hanya order yg sudah dibayar.

**Step 2-5:** implementasi tombol + handler + tampil tracking_number/label_url/status, test, commit `feat(shipping): admin generate-resi UI + action`.

---

## PHASE D — Tracking (Customer)

### Task D.1: Endpoint tracking + tampilan di halaman track
**Objective:** Customer lacak paket di halaman `/track/{order_number}` (signed URL existing).

**Files:**
- Modify: `app/Services/Shipping/AgenwebsiteClient.php` (method `tracking($awb, $courier)` → `/shipping/tracking`)
- Modify: controller track existing + view track
- Test: `tests/Feature/Shipping/TrackingTest.php`

**Step 1: Failing test** — order dgn tracking_number → halaman track tampilkan riwayat status paket (fake client). Tanpa tracking_number → tampil "resi belum tersedia". Cache hasil tracking pendek (mis. 15 menit).

**Step 2-5:** implementasi method tracking + integrasi ke halaman track existing (pertahankan signed URL + desain), cache, test, commit `feat(shipping): customer package tracking on track page`.

---

## PHASE E — Verifikasi Akhir & Smoke Test Live

### Task E.1: Smoke test end-to-end terhadap API LIVE
**Objective:** Pastikan rate beneran muncul dgn license asli (bukan cuma fake).

**Steps (manual, JANGAN di test suite — pakai license live):**
1. `php artisan tinker` → `app(App\Services\Shipping\ShippingRateService::class)->getRates(['province'=>'DKI Jakarta','city'=>'Jakarta Selatan','district'=>'Kebayoran Baru','zipcode'=>'12110'], [['slug'=>'<buku-slug>','qty'=>1]])` → harus balik array rate dgn harga nyata.
2. Checkout via browser (tunnel) → isi alamat → kurir + ongkir muncul → submit → order tersimpan dgn ongkir benar.
3. Cek `storage/logs/laravel.log` bersih.
4. ⚠️ Konfirmasi license belum expire (2026-06-01).

### Task E.2: Full regression
1. `php artisan test` → semua hijau (283 + test shipping baru).
2. `./vendor/bin/pint` → clean.
3. `./vendor/bin/phpstan analyse` (kalau ada di project) → level 6 pass.
4. `php artisan optimize:clear`.
5. Pastikan `trustProxies` masih ada di `bootstrap/app.php`.

---

## CATATAN SCOPE (YAGNI)

**MASUK plan ini (MVP shipping fungsional):** rate real-time checkout (Phase A,B), fulfillment+AWB+callback+email (Phase C), tracking customer (Phase D).

**DITUNDA (fase lanjut, JANGAN kerjakan kecuali diminta):**
- Custom tariff per wilayah (tabel + admin CRUD) — §1.3 reference.
- Asuransi, COD fee, subsidi ongkir — §2 reference (formula sudah teraudit, tinggal implementasi saat dibutuhkan).
- Instant courier / GoSend (butuh maps + pinpoint) — §2.6.
- Wallet / top-up saldo fulfillment — §2.5 (server-side SaaS).
- Internasional.

**Urutan eksekusi disarankan:** Phase 0 → A → B (sampai sini = ongkir checkout LIVE, sudah berguna) → C → D → E. Bisa berhenti & rilis setelah Phase B kalau mau ongkir dulu, fulfillment nyusul.
