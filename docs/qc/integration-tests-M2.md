# Backend Integration Tests · M2

- **Task:** `t_cc364ff9` — Backend integration tests (PHPUnit/Pest)
- **Reviewer:** mc-review-qc
- **Date:** 2026-05-22
- **Test runner:** PHPUnit (Laravel default), SQLite `:file:` (`database/database.sqlite`)
- **Branch reviewed:** `main` post-`8368c02` (WA notification stub merge)

## Verdict

✅ **APPROVE** — coverage memenuhi spec, semua test pass.

```
Tests:    270 passed (1019 assertions)
Duration: 6.62s
```

---

## Coverage vs Spec

Task body minta 6 area test bundle. Status per area:

| Spec | File implementasi | Test count | Status |
|------|-------------------|------------|--------|
| **ProductCrudTest** — index/create/edit/delete/restore + image upload validation | `tests/Feature/Admin/ProductCrudTest.php` (22) + `ProductSoftDeleteTest.php` (13) | 35 | ✅ |
| **OrderFlowTest** — checkout → upload → verify → resi (E2E) | Distributed: `CheckoutStoreTest.php` (16), `UploadStoreDbTest.php` (13), `OrderPaymentVerifyTest.php` (16), `OrderShipTest.php` (13) | 58 | ✅ |
| **PaymentVerificationTest** — approve full / partial cicilan / reject + status transition | `tests/Feature/Admin/OrderPaymentVerifyTest.php` (16) | 16 | ✅ |
| **CicilanTest** — DP 30% schedule, partial→awaiting_payment, final→verified | `CheckoutStoreTest::test_cicilan_*` (3 tests dengan n=3 DP 30% pattern) + `OrderPaymentVerifyTest::test_approve_partial_payment_marks_order_partial_paid` | 4+ | ✅ |
| **SignedUrlTest** — /upload + /track 403/200/expired | `tests/Feature/SignedUrlGuardTest.php` (11) | 11 | ✅ |
| **WaNotificationStubTest** — event dispatch → row di wa_notifications | `tests/Feature/WaNotificationStubTest.php` (15) | 15 | ✅ |

**Total:** 270 tests / 1019 assertions, 6.62 detik. Ngga ada flaky atau skipped.

---

## Spec Deviation (worth flagging)

### Status enum: `awaiting_payment` ≠ `pending`

Task spec (line 4): "payment partial → status awaiting_payment, payment final → verified"

Aktual schema: `database/migrations/2026_05_19_092904_create_orders_table.php` enum status **tidak punya** `awaiting_payment`. Yang ada: `pending`, `partial_paid`, `paid`, dst (cek migration full untuk list lengkap).

Source-of-truth komentar di `CheckoutStoreTest.php:17`:
> "Schema source-of-truth: orders.status enum (no 'awaiting_payment' — pakai 'pending')."

Dan komentar di `CheckoutController.php:30`:
> "'awaiting_payment' yang ngga ada di enum, default ke schema"

**Decision:** Test mengikuti schema (correct call). Spec di task body kemungkinan lag dari decision schema yang muncul kemudian. Saran: update spec di `docs_dev/task_plan.md` agar match schema (`awaiting_payment` → `pending`/`partial_paid` mapping). Bukan blocker.

---

## Per-Controller Coverage Cek

| Controller | LoC | Test files yang exercise route | Hits |
|------------|-----|--------------------------------|------|
| `AuthController` | 64 | AuthTest, semua admin tests via `actingAs` | `route('admin.login')` 22× |
| `DashboardController` | 31 | AuthTest (smoke) | covered |
| `InstallmentSchemeController` | 152 | InstallmentSchemeCrudTest (25 tests) | `route('admin.installment-schemes')` 27× |
| `OrderController` | 278 | OrderIndexTest (11) + OrderShowTest (12) + OrderShipTest (13) + OrderPaymentVerifyTest (16) | 52 tests |
| `ProductController` | 309 | ProductCrudTest (22) + ProductSoftDeleteTest (13) | 35 tests |
| `SettingsController` | 107 | SettingsTest (16) | 16 tests |
| `WaNotificationController` | 80 | WaNotificationStubTest (3 admin index tests) | 4× route call |

**Rough coverage estimate:** 70%+ untuk admin controllers (target task spec). Tidak diukur via Xdebug/PCOV (PCOV/Xdebug tidak ke-install — `phpunit.xml` punya `<coverage><include>` block tapi driver missing). Jika target hard 70%, tambah `pcov-clobber` atau `xdebug` di M3 backlog.

---

## Test Quality Notes

**Strong patterns yang ke-detect:**
- Schema-as-source-of-truth komentar di test header (e.g. `CheckoutStoreTest:17`) — mencegah drift
- Edge case representative: rounding di cicilan, idempotency reapprove (`test_cannot_reapprove_already_processed_payment`), tampering protection (`test_server_recalculates_price_ignoring_client_tampering`, `test_server_rejects_tampered_cart_total_diverging_more_than_1pct`)
- Signed URL: 4 attack scenario (no sig / expired / tampered / valid) × 2 method (GET/POST) = 8 baseline + extra TTL/redirect tests
- File upload: required, size limit (2MB), MIME whitelist, randomized filename, double-upload prevention

**Minor improvement (non-blocking, M3 backlog):**
- `OrderFlowTest` standalone gak ada — E2E flow tersebar di 4 file. Optional: `tests/Feature/Admin/OrderFlowE2ETest.php` yang chain checkout→upload→verify→ship dalam single test untuk regression catcher.
- Coverage tool (Xdebug/PCOV) belum jalan — angka 70% tidak terukur, hanya inferred. M3: install PCOV, set CI gate `--min=70`.
- PHPStan / Larastan ngga ada (lihat `visual-review-M2-admin.md::M3`). Tipe-related bug bakal lolos sampai runtime.

---

## Acceptance Criteria Check

Dari task body:

| Criteria | Status |
|---|---|
| 6 test bundle (Product/Order/Payment/Cicilan/SignedUrl/Wa) | ✅ all 6 covered |
| Coverage 70% lines untuk Admin + Services | ⚠️ likely ≥70% based on test density, tapi tidak terukur (no PCOV) |
| `php artisan test` green | ✅ 270/270 pass, 6.62s |

---

## Cross-Reference dengan Visual Review

Task ini standalone PASS, tapi M2 sign-off (`t_c7e659c0`) tetap **tertahan** karena `t_bfc4f9c0` (visual review) di-block dengan 1 Critical (mobile nav drawer absent) + 2 High. Setelah mc-fullstack address itu, baru `t_c7e659c0` bisa dibuka.

---

**Decision tracking:** add ke `docs_dev/task_plan.md::Decisions`:
> 2026-05-22 — `t_cc364ff9` APPROVE (backend integration tests M2). 270 pass / 1019 assertions / 6.62s. Coverage spec lengkap. Backlog M3: install PCOV untuk hard 70% gate, optional OrderFlowE2ETest standalone.
