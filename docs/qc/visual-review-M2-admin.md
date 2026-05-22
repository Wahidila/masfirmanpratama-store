# Visual Review · M2 Admin

- **Task:** `t_bfc4f9c0` — Admin visual review
- **Reviewer:** mc-review-qc
- **Date:** 2026-05-22
- **Scope:** Admin foundation + Produk + Pesanan + Settings + Skema Cicilan + WA Notifikasi
- **Artifacts:** `docs/qc/M2/screenshots/` (33 PNG, 11 view × 3 viewport — 375/768/1440)
- **Method:** Static code analysis terhadap Blade/Tailwind config + screenshot inventory + design token audit. Vision-AI pass di-skip karena vision tool eksternal lagi error (5/5 attempt JSON parse fail dari upstream); analisa berbasis grep palette + struktur komponen masih cukup actionable.

## Verdict

⚠️ **REQUEST CHANGES** — 1 Critical (mobile nav), 2 High (palette inkonsistensi), 3 Medium, 2 Low.

Foundation solid (sidebar + navbar + komponen reusable rapi, tailwind config sudah declare design tokens dengan benar), tapi ada beberapa drift dari `DESIGN.md` yang harus dirapikan sebelum sign-off M2.

---

## Severity Breakdown

### 🔴 Critical (1) — must fix sebelum sign-off

#### C1. Mobile (<lg / <1024px) tidak punya nav links

**File:** `resources/views/layouts/admin.blade.php:23-36`, `resources/views/components/admin/sidebar.blade.php:25` (`hidden lg:flex`), `resources/views/components/admin/navbar.blade.php:7` (`hidden lg:flex`).

**Repro:**
1. Login admin di viewport mobile-375 atau tablet-768
2. Sidebar hilang (intentional — `hidden lg:flex`)
3. Mobile fallback header (`lg:hidden flex h-16 …`) cuma punya logo + tombol Logout
4. **Tidak ada akses ke Produk / Pesanan / WA Notifikasi / Skema Cicilan / Settings**

**Impact:** Klien Mas Firman / staff admin yang buka admin panel via HP otomatis stuck di /dashboard. Untuk audit harian (verifikasi bayar, input resi) ini blocker — flow utama M2 ngga reachable di mobile.

**Fix yang disarankan:**

Option A (cepat) — Hamburger drawer di mobile header:
```blade
{{-- resources/views/layouts/admin.blade.php --}}
<header class="lg:hidden …" x-data="{ open: false }">
    <button @click="open = true" class="…"><x-admin.icon name="menu" class="h-5 w-5" /></button>
    <a href="{{ route('admin.dashboard') }}">…</a>
    {{-- existing logout form --}}

    <div x-show="open" x-transition class="fixed inset-0 z-40 …">
        <div @click="open = false" class="absolute inset-0 bg-slate-900/60"></div>
        <aside class="relative ml-0 h-full w-64 bg-white shadow-xl">
            {{-- reuse $primaryNav loop dari x-admin.sidebar --}}
        </aside>
    </div>
</header>
```

Option B (refactor) — extract `$primaryNav` array ke `app/View/Composers/AdminNavComposer.php` atau `config/admin-nav.php`, share antara desktop sidebar + mobile drawer biar single source of truth.

**Recommendation:** Option A dulu untuk M2 sign-off, Option B masuk M3 backlog (bersamaan dengan Affiliate admin yang juga butuh nav).

---

### 🟠 High (2)

#### H1. Destructive color split — `red-*` vs `rose-*` inkonsistensi

**Pattern:** Semantic "destructive / required / error" dipakai dua palette berbeda di file yang berbeda.

| File | Pakai | Konteks |
|------|-------|---------|
| `components/admin/form-group.blade.php:20,32` | `red-500/600` | Required asterisk + error message |
| `components/admin/alert.blade.php:12` | `red-200/50/600/900` | Alert tone error |
| `admin/auth/login.blade.php:51,54,66,69` | `red-300/600` | Validation border + error text |
| `admin/products/edit.blade.php:19`, `index.blade.php:142,157,245`, `_form.blade.php:144`, `settings/_bank_accounts.blade.php:53` | `red-200/500/600` | Tombol hapus + checkbox |
| `admin/orders/show.blade.php:268,281,380,386,394,400,411,414` | `rose-400/500/600` | Required asterisk + reject button + validation border |
| `admin/installment-schemes/_form.blade.php:14,22,46,54,62,68,76,82,90`, `index.blade.php:131` | `rose-500/600/700` | Required asterisk + error text + delete button |
| `admin/wa-notifications/index.blade.php:32-34` | `rose-100/700/900` | Stat card "Failed" |

**Impact:** Inkonsistensi visual — dua hue merah berbeda di app yang sama. `tailwind.config.js` cuma declare `primary/secondary/accent/slate-850` jadi `red-*` & `rose-*` dua-duanya pakai default Tailwind (beda hue: red `#ef4444` warmer, rose `#f43f5e` cooler/pink).

**Fix:** Tentuin satu canonical destructive palette. Rekomendasi: `rose-*` (lebih lembut, kontras lebih bagus dengan indigo primary). Update:
1. `components/admin/form-group.blade.php` → `red-500` → `rose-500`, `red-600` → `rose-600`
2. `components/admin/alert.blade.php::tones.error` → semua `red-*` → `rose-*`
3. `admin/auth/login.blade.php` → ganti `red-300/600` → `rose-300/600`
4. `admin/products/*` + `settings/_bank_accounts.blade.php` → ganti `red-*` → `rose-*`
5. Atau (alternatif): tambah `danger: { …rose values… }` di `tailwind.config.js`, refactor semua call site → `text-danger-600` dst (DRY tapi PR lebih besar).

**Add ke Decisions di task_plan.md:** "Destructive semantic = `rose-*` (canonical). M2 cleanup."

#### H2. `text-orange-600` di home prototype bocor ke admin compile

**File:** `resources/views/pages/home.blade.php:118` (public store, tapi compile-nya barengan admin via Vite share build).

**Impact:** Tailwind safelist scan baca file ini → `orange-600` di-include di production bundle walau admin ngga pakai. Bukan visual bug tapi bundle bloat ringan + bukti drift dari `DESIGN.md` (accent canonical = Amber `#f59e0b`).

**Fix:** Ganti `text-orange-600` → `text-accent-600` di `home.blade.php:118`. Trivial, sekalian rapi-rapi M2 cleanup.

---

### 🟡 Medium (3)

#### M1. `bg-emerald-*` muncul di success-state — duplikat dengan `secondary-*`

**File:**
- `admin/orders/show.blade.php:343-349` — verified payment block pakai `bg-emerald-50 border-emerald-200 text-emerald-800`
- `admin/wa-notifications/index.blade.php:28-30` — stat "Sent" pakai `border-emerald-100 bg-emerald-50 text-emerald-700`

**Pattern:** `components/admin/alert.blade.php::tones.success` udah pakai `border-secondary-200 bg-secondary-50 text-secondary-900` (= teal). Ada inkonsistensi — sebagian success pakai teal (per design token), sebagian pakai emerald (default Tailwind).

**Fix:** Konsolidasi. Untuk verified payment & stat sent → ganti emerald → secondary. Atau accept emerald sebagai "success-reinforcement" tapi declare di `tailwind.config.js::colors.success` biar tracked.

#### M2. Code style: 5 file fail Pint

**File yang fail:**
1. `app/Http/Controllers/Admin/AuthController.php` — `concat_space, unary_operator_spaces`
2. `app/Http/Controllers/Admin/InstallmentSchemeController.php` — `fully_qualified_strict_types`
3. `app/Http/Controllers/Admin/ProductController.php` — `fully_qualified_strict_types`
4. `database/factories/OrderFactory.php` — `fully_qualified_strict_types`
5. `database/seeders/OrderSeeder.php` — `array_indentation, concat_space, unary_operator_spaces`

**Fix:** `./vendor/bin/pint` (auto-fix). Dijalanin sekali, commit dengan message `chore(store): pint clean — unary spacing, FQCN imports`.

**Note:** `--diff` flag returns "0 files" (kemungkinan bug Pint terhadap path-list). `pint` tanpa argument bakal apply ke semua file fail.

#### M3. PHPStan ngga di-install

**Impact:** Type-check static analysis missing dari gauntlet. M2 tetap bisa lewat (PHPUnit 270 pass cukup), tapi M3 onwards akan miss type-error yang test ngga cover (mis. nullable handling).

**Fix (M3 backlog, bukan blocker):** `composer require --dev larastan/larastan`, set level 6, tambah ke gauntlet.

---

### 🟢 Low (2)

#### L1. Sidebar coming-soon section dead code

**File:** `components/admin/sidebar.blade.php:18` — `$comingSoon = []`. Block `@if (! empty($comingSoon))` di line 47-58 ngga akan pernah render di M2 (sudah semua ready).

**Fix:** Hapus block atau commented out. Trivial.

#### L2. Logo "F" inisial — konsistensi branding

**File:** `components/admin/sidebar.blade.php:30`, `layouts/admin.blade.php:25` — keduanya pakai `<span>F</span>`.

**Note:** Inisial "F" untuk Firman makes sense, tapi DESIGN.md ngga eksplisit mention. Future-proofing: extract ke `config/admin.php::logo_initial` atau `<x-admin.logo />` component biar gampang ganti kalau klien minta full mark.

---

## Acceptance Criteria Check

Dari task body `t_bfc4f9c0`:

| Criteria | Status | Note |
|---|---|---|
| Screenshot Playwright 3 viewport (375/768/1440) | ✅ | 33 file di `docs/qc/M2/screenshots/`, idx file ada |
| Palette consistency (Indigo/Teal/Slate, gak ngawur dari M1 prototype) | ⚠️ | Issue H1 (red/rose split), H2 (orange residue), M1 (emerald split) |
| Sidebar collapsible di mobile, link work, active state | ❌ | Issue C1 — mobile gak punya sidebar drawer sama sekali |
| Form validation message Indonesian, error styling consistent, success flash visible | ⚠️ | Indonesian ✅, styling inkonsisten karena H1 |
| Empty state produk/pesanan/settings | ⚠️ | Tidak diverifikasi via vision (tool error). Mc-fullstack: konfirmasi via re-screenshot post-fix |

---

## Rekomendasi Lanjutan

1. **Address C1, H1, H2 dulu** — itu blocker M2 sign-off. Estimated effort: 4-6 jam (mobile drawer + bulk find-replace red→rose + 1-line orange→accent).
2. **M2-M3 cleanup batch:** Pint auto-fix + emerald→secondary consolidation. Bisa standalone PR.
3. **L1, L2 boleh skip ke M3 backlog** — bukan blocker.
4. **Re-request review** setelah C1/H1/H2 fix → re-screenshot → ping `mc-review-qc` di thread ini lagi.

## Dependencies

Task ini block `t_c7e659c0` (M2 sign-off + Lighthouse). Selama C1 belum fix, sign-off di-tahan. Lighthouse re-audit (production-like nginx) bisa jalan paralel selama mc-fullstack address C1.

---

**Decision tracking:** add ke `docs_dev/task_plan.md::Decisions`:
> 2026-05-22 — `t_bfc4f9c0` REQUEST CHANGES (visual review M2). Critical: mobile admin gak punya nav drawer. High: red/rose destructive palette split. Canonical destructive = rose-*. Action: mc-fullstack address C1+H1+H2, re-screenshot, re-request.
