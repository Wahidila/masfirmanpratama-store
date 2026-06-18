# Plan — M4 Webhook Integration: Store → Affiliate (referral → komisi otomatis)

> Sprint: M4 · Started: 2026-06-18
> Branch usul: `feat/m4-webhook-referral`
> Goal: tutup glue inti antara `store/` dan `affiliate/` — order berbayar yang
> bawa referral code otomatis bikin `referral_order` + `commission` (cooling 7 hari)
> di sisi affiliate, via webhook HMAC-SHA256.

## Konteks: apa yang SUDAH ada (jangan bikin ulang)

### Affiliate side (receiver) — sudah ada
- Tabel `webhook_logs` (event_type, payload json, signature, status enum
  received/processed/failed/invalid_signature, source_ip) + model `WebhookLog`
- Tabel `referral_orders` (referral_code_id, affiliator_id, **store_order_id**,
  buyer_name, order_total, status pending/paid/refunded, ordered_at) + model
- Tabel `commissions` (affiliator_id, referral_order_id, amount, rate_applied,
  status cooling/available/withdrawn/cancelled, **available_at**) + model
- Tabel `commission_settings` (affiliator_type_id, product_type book/course/null,
  rate %, min_amount, **cooling_days** default 7, is_active)
- `ReferralController::track()` — set cookie `referral_code` (30 hari) lalu
  redirect ke `config('app.store_url')`. Sudah jalan + ada test.

### Store side (emitter) — sebagian ada
- Kolom `orders.ref_code` (migration `2026_05_19_092904_create_orders_table`)
- Event `PaymentVerified($order, $payment)` di-`event()` saat admin approve
  payment (`Admin/OrderController::approve`, recompute status → `paid`)
- Listener infra sudah ada pattern-nya (SendCustomerPaymentVerifiedNotification)
- CheckoutController + CourseCheckoutController nyimpen `ref_code` ke order
  (saat ini dari **form input** `ref_code`, belum dari cookie)

## Gap yang HARUS dibangun

### A. Store side — capture referral cookie → order.ref_code
Saat ini `ref_code` cuma keisi kalau ada di form input. Cookie `referral_code`
yang di-set affiliate **belum** kebaca store. Wiring:
1. Saat render checkout, baca `Cookie::get('referral_code')` → inject sbg
   hidden field default (form input tetap bisa override, tapi cookie jadi sumber utama)
2. Atau lebih bersih: di CheckoutController & CourseCheckoutController, kalau
   `ref_code` kosong, fallback ke cookie sebelum simpan order.
   **Decision usul: fallback di controller** (1 titik, ngga sentuh tiap blade).

### B. Store side — webhook EMITTER
1. `config/webhook.php` — `affiliate_url`, `secret` (dari `.env`
   `AFFILIATE_WEBHOOK_SECRET`, `AFFILIATE_WEBHOOK_URL`), `timeout`, `retry`
2. `app/Services/Webhook/AffiliateWebhookClient.php`:
   - `dispatch(string $event, array $payload)` — JSON encode, hitung
     `hash_hmac('sha256', $body, $secret)`, kirim header `X-Signature: sha256=...`
     + `X-Webhook-Event`, POST ke affiliate_url
   - retry 3x backoff (pakai Http::retry), log hasil
   - kalau ref_code kosong → skip (no-op, ngga usah kirim)
3. Listener baru `DispatchAffiliateOrderPaid` di event `PaymentVerified`:
   - guard: cuma kirim kalau `order.ref_code` ada
   - payload: `{ event, store_order_id, ref_code, buyer_name, order_total,
     product_type (book/course/mixed), ordered_at, idempotency_key }`
   - register di EventServiceProvider / via `Event::listen` (cek pattern store)
4. (Opsional M4 tail) event `order-refunded` saat payment di-reject/refund
   → dispatch event `order-refunded`. Bisa defer kalau scope mepet.

### C. Affiliate side — webhook RECEIVER
1. `config` HMAC secret (`.env` `STORE_WEBHOOK_SECRET`, **sama** dengan store)
2. Route `POST /webhooks/store` (no CSRF — exclude di bootstrap/app.php
   `$middleware->validateCsrfTokens(except: ['webhooks/*'])`)
3. `app/Http/Controllers/Webhooks/StoreWebhookController.php`:
   - **verify HMAC** `hash_equals()` — kalau gagal: simpan `webhook_logs`
     status `invalid_signature`, return 401
   - **idempotency**: cek `webhook_logs` / `referral_orders.store_order_id`
     udah pernah diproses? skip (return 200, status processed) — anti double
   - log ke `webhook_logs` status `received` dulu
   - handle `order-paid`:
     a. resolve `ReferralCode` dari `ref_code` (kalau ngga ketemu → log failed,
        return 200 biar store ngga retry selamanya)
     b. `referral_orders` create/update → status `paid`
     c. hitung komisi: ambil `commission_settings` match
        (affiliator_type + product_type, fallback global), `amount =
        order_total * rate/100`, guard `min_amount`
     d. `commissions` create status `cooling`, `available_at = now() +
        cooling_days`
     e. update `webhook_logs` status `processed`
   - handle `order-refunded`: referral_order → `refunded`, commission terkait
     → `cancelled` (kalau belum withdrawn)
4. Komisi cooling→available: command `commissions:release` (scheduler harian)
   yang flip `cooling`→`available` saat `available_at <= now()`. Cek apakah
   udah ada (M3 mungkin udah scaffold) sebelum bikin baru.

## Keamanan (wajib)
- HMAC-SHA256 `hash_equals` (timing-safe), secret via `.env` ngga di-commit
- Idempotency key (store_order_id + event) anti double-credit komisi
- Receiver return 200 untuk error bisnis (ref ngga ketemu) biar store ngga
  retry storm; return 401 cuma buat signature invalid
- Rate think: webhook endpoint public → throttle middleware
- `.env.example` kedua app di-update dgn key baru (tanpa value asli)

## Test (acceptance)
Store:
- [ ] cookie `referral_code` ke-capture jadi `order.ref_code` (feature test)
- [ ] PaymentVerified dgn ref_code → AffiliateWebhookClient ke-call (Http::fake)
- [ ] tanpa ref_code → no dispatch
- [ ] signature header bener (assert hash_hmac)

Affiliate:
- [ ] valid signature + ref valid → referral_order paid + commission cooling +
      available_at = +cooling_days
- [ ] invalid signature → 401 + webhook_log invalid_signature, no commission
- [ ] duplicate store_order_id → idempotent, no double commission
- [ ] ref_code ngga ketemu → 200 + log failed, no commission
- [ ] order-refunded → referral_order refunded + commission cancelled
- [ ] commissions:release flip cooling→available saat jatuh tempo

## Phase
- [ ] P0: branch `feat/m4-webhook-referral` + baca ulang scaffold M3 receiver-side
- [ ] P1: Store config + AffiliateWebhookClient + listener + cookie capture
- [ ] P2: Store tests (Http::fake) hijau + pint
- [ ] P3: Affiliate config + route + StoreWebhookController + commission calc
- [ ] P4: Affiliate idempotency + refund + release command
- [ ] P5: Affiliate tests hijau + pint
- [ ] P6: integration smoke (store dispatch → affiliate receive, dua app lokal)
- [ ] P7: update .env.example both + AGENTS.md decisions log + self-review

## Open decisions (perlu konfirmasi)
1. Komisi structure final: rate flat per affiliator_type, atau per product_type
   (book vs course beda %)? commission_settings sudah support dua-duanya —
   tinggal seed nilai. **Butuh angka dari klien.**
2. Cooling window: 7 hari (default migration) tetap, atau ubah?
3. product_type "mixed" (cart isi book + course) — komisi dihitung per-item
   atau flat per-order? Usul M4: **per-order pakai rate global** dulu,
   per-item defer.
4. Refund handling: auto-cancel commission, atau perlu admin approve dulu?

## Catatan eksekusi (alur agent)
- Implementasi code = Engineer via **OpenCode CLI** (bukan delegate_task subagent)
- Tester (profil terpisah) review sebelum merge
- Tiap agent lapor ke topic Telegram masing-masing
