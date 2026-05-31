# M-Shipping Reference — Hasil Audit Plugin `agenwebsite-shipping` (WooCommerce)

> **Tujuan dokumen:** referensi reimplementasi logika ongkir/shipping di store Laravel 11 (`store/`).
> **Sumber:** plugin WooCommerce `agenwebsite-shipping` v2.3.11 (di-extract ke `docs_dev/reference/agenwebsite-shipping/`, READ-ONLY, jangan di-deploy).
> **Catatan stack:** store kita Laravel 11 (Blade + custom checkout), BUKAN WordPress. Plugin ini cuma jadi referensi LOGIKA, bukan dipasang.
> **Status:** Audit selesai (3 domain). API live SaaS TIDAK bisa dipanggil dari luar WP (lihat §0). Belum ada implementasi.

---

## §0. TEMUAN KRITIS — Provider API & Kelayakan

Plugin ini adalah **thin client** ke SaaS proprietary `https://api-v2.agenwebsite.com/v2`. Semua kalkulasi tarif riil terjadi di server agenwebsite; plugin cuma nyiapin payload (origin, dest, weight, dimensi, kurir), POST, lalu filter + markup hasilnya.

**Smoke test API (2026-05-31) — TERVERIFIKASI LIVE ✅:**

Awalnya curl mentah balik **HTTP 500 di semua endpoint**. **Root cause ketemu: User-Agent.** `wp_remote_post` WordPress mengirim UA `WordPress/<ver>; <site-url>`, dan API agenwebsite **menolak request tanpa WordPress UA** (balik 500). Setelah meniru UA itu + body form-encoded → **semua endpoint balik HTTP 200**.

**Resep request yang BERHASIL (replikasi `wp_remote_post`):**
- Method `POST`, `Content-Type: application/x-www-form-urlencoded` (body = `http_build_query`, BUKAN JSON).
- **`User-Agent: WordPress/6.8.3; https://masfirmanpratama.com`** ← KUNCI. Tanpa ini → 500.
- Header tambahan: `plugin-version: 2.3.11`, `wordpress-version: 6.8.3`, `woocommerce-version: 10.0`, `php-version: 8.2.31`, `site-url: <url>`.
- Body wajib: `license`, `product=agenwebsite-shipping`.

**Hasil verifikasi live (HTTP 200 semua):**
- `/license/activate` → `{data:{account_email, expire_date:"2026-06-01", type:"exclusive", shipping_quota:"Unlimited"}}`. ⚠️ **License EXPIRE 2026-06-01 (besok!)** — perlu diperpanjang.
- `/shipping/couriers` → `[{id, title, logo_url, category}]` (jne, jnt, jtc, sicepat, anteraja, lion, pos, spx, gosend-instant, jne-international...).
- `/shipping/services?category=domestic` → `[{courier_id, name, courier, category, is_premium, enable, extra_cost}]` (mis. `spx_spxstd`, `anteraja_reg`, `jne_reg`...).
- `/shipping/price` → `[{courier, service, service_name, price (string), etd, etd_from, etd_to}]`. Contoh `jne_reg` = `"17000"`, etd `"1-2 days"`.
- **Unit berat = KG (terverifikasi)**: weight=1 → 17.000, weight=1000 → 17.000.000 (linear ×1000). Kirim kg aktual (buku ~0.3–1 kg), min 1.

**Implikasi arsitektur (REVISI):** API agenwebsite **BISA dipanggil langsung dari Laravel** — TIDAK perlu stand-up WordPress. Cukup `Http::withUserAgent('WordPress/6.8.3; <site-url>')->asForm()->withHeaders([...])->post(...)`. Provider ini agregator semua kurir Indonesia (real-time rate + AWB + tracking) → jadi sumber tarif utama M-shipping. Plugin tetap jadi referensi LOGIKA (weight/dimensi calc, custom tariff, insurance, COD, subsidi, fulfillment state machine, AWB callback HMAC) di §1–§3.

License key disimpan di `store/.env` → `AGENWEBSITE_SHIPPING_LICENSE` + `AGENWEBSITE_SHIPPING_API_URL` (gitignored). Account: `contentsaya1@gmail.com`, tipe exclusive, quota unlimited, **expire 2026-06-01**.

---

## §1. RATE / ONGKIR ENGINE

### 1.1 Alur perhitungan end-to-end
Entry: `calculate_shipping($package)` (dipanggil saat cart/checkout).
1. Ambil tujuan: `country`, `state` (kode provinsi 2-huruf, mis. `JB`), `city`, `zipcode`.
2. Parse city/district: field city berisi `"Kota, Kecamatan"` → `explode(', ')`. **Konvensi: kecamatan digabung ke kota dgn separator `", "`.** (Di Laravel: sebaiknya kolom `city` & `district` terpisah eksplisit.)
3. Tentukan kurir aktif dari settings (`courier[]`), + instant bila aktif & bukan excluded day.
4. Hitung berat total (§1.4) + dimensi paket (§1.5).
5. **Custom tariff dulu (PRO)**: bila match wilayah → di-merge ke depan daftar rate.
6. **API rate domestik**: country=ID & ((city&district) ATAU zipcode) → `POST /shipping/price`.
7. API internasional (opsional) bila country≠ID.
8. Bila hasil kosong → tidak ada metode shipping tampil.
9. Subsidi ongkir (PRO) dihitung → disimpan ke meta/session (lihat §2.2 — sebagian besar display-only).
10. Daftarkan tiap rate: `id=agenwebsite_shipping_<service_id>`, `label="<name> (<etd>)"`, `cost=harga`.

**Transformasi service (inti):**
- Kirim kurir pipe-delimited (`jne|jnt|sicepat`) ke API → terima array tarif mentah.
- Loop service yang dikonfigurasi lokal (`services[]`); hanya `enable=1` & kurirnya terpilih.
- Match baris API `line.service == courier_id` → ambil `price` + `etd`.
- **`cost = api_price + extra_cost`** (extra_cost = markup admin per-service).
- Output: `{courier, service_id, label, price, etd}`.
> Daftar service final = INTERSEKSI (service aktif di settings) ∩ (service dari API). API tentukan harga & ketersediaan; settings tentukan tampil/markup/label.

### 1.2 API rate
- Base: `AW_SHIPPING_API_URL = https://api-v2.agenwebsite.com/v2`. Public widget: `https://api-public.agenwebsite.com`.
- Endpoint: `price`, `price-international`, `services` (`?category=domestic|instant|international`), `couriers`, `license` (`/license/activate`), `search_data` (`/shipping/data`, autocomplete kota/kecamatan), `tracking` (`/shipping/tracking`).
- Body selalu disisipi `license` + `product='agenwebsite-shipping'`. Header versi: `plugin-version`, `wordpress-version`, `woocommerce-version`, `php-version`, `site-url`. Timeout default 300s.

**Payload `/shipping/price` (rekonstruksi dari kode):**
```json
{
  "license": "XXXX", "product": "agenwebsite-shipping",
  "province": "Jawa Barat", "city": "Bandung", "district": "Coblong", "zipcode": "40132",
  "weight": 1500, "courier": "jne|jnt|sicepat", "method": "",
  "origin": "jakarta", "origin_zipcode": "10110",
  "length": 20, "width": 15, "height": 10
}
```
**Response (dinormalisasi `process_result`):** baca `body.message` + `body.data`. 200 → `{status:'success', result: body.data}`.
```json
{ "message": "OK", "data": [
  { "service": "jne_reg", "price": 32000, "etd": "2-3 hari" },
  { "service": "jne_yes", "price": 48000, "etd": "1 hari" }
]}
```
Field per baris yang dipakai: **`service`** (=courier_id lowercase), **`price`**, **`etd`**.
> ⚠️ Shape ini REKONSTRUKSI dari pemakaian kode, BELUM terverifikasi live (API 500). Verifikasi via log API asli saat integrasi.

**Caching:** couriers/services (master) di-cache transient 24 jam + fallback JSON lokal. Tarif harga TIDAK di-cache. **Error:** gagal/non-200 → status error → output kosong → tidak ada rate.

### 1.3 Custom tariff per wilayah
Tabel `{prefix}awshipping_custom_tariffs`. Kolom:
- `id`, `name`, `province` (nama lengkap; kosong=semua), `city` (kosong=semua), `district` (JSON array; kosong=semua), `products` (JSON array product_id; kosong=semua), `shipping_cost` (JSON `[{name,cost}]`), `weight_multiplier` (0/1; bila 1 → `cost × ceil(weight_kg)`), `status` (1/0), timestamps.

**Matching & precedence** (`get_tariff_by_destination`): WHERE status=1 AND (province match OR kosong) AND (city match OR kosong) AND (district LIKE `%"<district>"%` OR kosong). ORDER BY spesifisitas: (1) ada products DESC, (2) ada province DESC, (3) ada city DESC, (4) ada district DESC, (5) id DESC. Lalu loop: aturan tanpa products langsung match; dengan products harus ada irisan (`array_intersect`) dgn cart. Ambil **1 tariff pertama** yang match.
**Precedence vs API:** custom tariff **DIGABUNG** (array_merge) dgn API rate, BUKAN override. Custom muncul lebih dulu. Hanya untuk license non-free.

### 1.4 Perhitungan berat
`aw_shipping_calculate_weight`: iterasi cart, skip virtual/downloadable. Berat = `get_weight()` (kosong→`default_weight`), `× qty`, akumulasi. Konversi unit: bila `g` → /1000 (dinormalisasi ke **kg**). Hasil 2 desimal, min dipaksa 1.
> ⚠️ Inkonsistensi: path rate kirim **kg**, tapi event tracking kirim **gram**. Verifikasi unit yang diterima endpoint saat integrasi. **Volumetrik:** TIDAK ada di plugin — dimensi dikirim mentah, volumetrik dihitung server.

### 1.5 Dimensi
`aw_shipping_calculate_dimensions`: konversi ke cm. `length`=max panjang antar item, `width`=max lebar, `height`=Σ(tinggi×qty) (ditumpuk). Default kosong: L=10,W=10,H=5 cm.

### 1.6 Settings kunci (`aw_shipping_settings`)
`license`, `origin` (slug kota, 45 didukung di `data/origins.json`), `zipcode` (origin), `courier[]` (free=max 1, PRO=unlimited), `services[]` (lihat tabel), `default_weight`, `enable`, `instant_enable`+`instant_excluded_days`, `international_enable`, `position_latitude/longitude` (instant), `cod_fee`, `awb.shipper`, dll.

**Service config (`services[]` & `data/services.json`):** `courier_id` (mis. `jne_reg`), `name` (`JNE REG`), `description`, `courier` (`jne`), `enable` (`1`/`0`), `extra_cost` (markup), `is_premium`.
**Couriers (`data/couriers.json`):** 8 default — jne, pos, tiki, sicepat, jnt, anteraja, paxel, spx.
