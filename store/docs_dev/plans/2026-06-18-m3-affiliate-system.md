# Plan — M3 Affiliate System

> Branch: `feat/m3-affiliate-system`
> Sprint: M3 (Affiliate System) — deliverable per AGENTS.md tabel milestone
> Started: 2026-06-18
> Engineer: opencode (Sisyphus / kr/claude-opus-4.6)

## Scope (dari AGENTS.md "Sprint aktif — M3")

Affiliate System di domain/prefix yang sama (`/affiliate`), unified admin (`/admin`).
Affiliator daftar (3 tipe: alumni / non-alumni / peserta) → login → generate referral link →
lihat komisi → withdraw. Admin kelola affiliator + approve komisi + withdrawal.

**OUT OF SCOPE (defer ke M4):** webhook HMAC Store→Affiliate (sini single-DB, jadi attribution
internal saja), gamifikasi penuh (leaderboard real-time, reward auto-claim, level system).
Yang masuk: leaderboard read-only sederhana + event card statis.

## Arsitektur keputusan (locked)

| # | Decision | Rationale |
|---|----------|-----------|
| 1 | Single Laravel app, prefix `/affiliate` (BUKAN subdomain terpisah) | AGENTS.md: domain admin prefix `/admin`; affiliate sistem sama-DB. Webhook cross-DB defer M4. |
| 2 | Guard baru `affiliator` (provider `affiliators`), terpisah dari `web` + `admin` | Affiliator login beda dari customer & admin. Pola ikut guard `admin` existing. |
| 3 | Referral attribution lewat kolom `orders.ref_code` yang SUDAH ADA | Checkout udah nangkap ref_code. Tinggal: cookie capture di landing + map ref_code→referral_code→commission saat order lunas. |
| 4 | Komisi: percentage flat per setting (`commission_settings`), bisa override per tipe affiliator | Struktur belum locked klien → default flat %, extensible. |
| 5 | Referral cookie window 30 hari | Spec awal. Bump 90 hari = config, defer. |
| 6 | Palette: primary indigo / secondary teal / accent amber (ikut Store DESIGN.md) | Konsistensi brand. Landing affiliate distinctive via layout, bukan palette beda. |
| 7 | Admin affiliate panel = menu baru di unified admin nav (`config/admin-nav.php`) | UNIFIED admin (1 login). Proxied via guard `admin`. |
| 8 | Komisi credit saat order `status` = lunas/paid (event-driven, ikut pola WaNotification listener) | Hindari double-credit; idempotent per order. |

## Tabel DB (subset 14 → MVP 9 inti)

1. `affiliators` — id, name, email, password, phone, type(alumni/non_alumni/peserta), status(pending/active/suspended), bank_name, bank_account, bank_holder, email_verified_at, timestamps, soft deletes
2. `referral_codes` — id, affiliator_id, code(unique), clicks_count, created_at
3. `referral_clicks` — id, referral_code_id, ip_hash, user_agent, landing_url, created_at
4. `referral_orders` — id, referral_code_id, order_id, status(pending/credited/rejected), created_at (pivot order↔referral)
5. `commissions` — id, affiliator_id, referral_order_id, order_id, amount, rate, status(pending/approved/paid/rejected), approved_at, timestamps
6. `commission_settings` — id, scope(global/type:alumni/...), rate_percent, min_payout, created_at
7. `withdrawals` — id, affiliator_id, amount, status(requested/approved/paid/rejected), bank snapshot, requested_at, processed_at, note
8. `materials` — id, title, description, file_path, type, created_at (materi marketing download)
9. `affiliate_events` — id, title, description, starts_at, ends_at, reward_note, status (event card statis)

Defer: `withdrawal_methods`, `material_downloads`, `affiliate_event_participants`,
`affiliate_event_rewards`, `affiliator_types` (pakai enum kolom dulu).

## Batch (eksekusi opencode, urut, masing-masing < timeout)

- **B1 Foundation DB** — 9 migration + 9 model (relasi + casts + factory) + seeder demo + commission_settings default. Test: migrate + factory smoke.
- **B2 Auth affiliator** — guard config, RegisterController (3 tipe), LoginController, email verify, `auth:affiliator` middleware, FormRequest Indonesian. Test: register+login feature.
- **B3 Public landing** — `/affiliate` landing (benefit + CTA), register page, layout `affiliate.blade.php`. Test: route 200 + register flow.
- **B4 Dashboard affiliator** — referral link manager (generate + copy), earnings overview, withdraw form, leaderboard read-only, materials list, event cards. Test: dashboard render + withdraw request.
- **B5 Admin panel** — nav menu baru, AffiliatorController CRUD, CommissionController (approve/reject), WithdrawalController (approve/paid), MaterialController (upload), EventController. Test: admin CRUD feature.
- **B6 Referral tracking + gate** — middleware cookie capture di landing (`?ref=CODE`), ReferralService attribution saat order lunas → buat commission (idempotent), wire ke checkout/order-paid event. Full gate: `php artisan test` + `pint` + `phpstan` green.

## Verifikasi tiap batch
`php artisan migrate:fresh --seed` (test DB) + `php artisan test --filter=<batch>` + `./vendor/bin/pint` + `./vendor/bin/phpstan analyse`.

## Pitfalls (carry dari AGENTS.md decisions)
- Guard `admin` pakai provider `admins` — tiru pola persis untuk `affiliator`.
- Order schema: `status` unified (bukan split). Komisi credit saat status lunas.
- `ref_code` di orders bisa berupa JSON (CourseCheckout) atau string (CheckoutController) — ReferralService harus handle dua format.
- Validation message FULL Bahasa Indonesia (konvensi project).
- phpstan L6 + pint clean WAJIB sebelum commit tiap batch.
- Soft delete aware untuk affiliators.
