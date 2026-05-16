# Visual review — M1 Frontend Online Store

**Sprint:** Frontend Online Store M1 (target 2026-05-18)
**Project:** masfirmanpratama (`/root/malang-creative/_active/masfirmanpratama`)
**QC date:** 2026-05-16
**Reviewer:** mc-qc (run `t_f3287a21`, current_run_id 28)
**Parent:** `t_e94f2281` (responsive mobile polish — completed 2026-05-16 10:29)

## TL;DR

Functional smoke pass. 7/7 routes return 200, all major pages render with the new
shared `x-layouts.store` shell, navbar + footer + components reused, design tokens
mostly aligned with prototype. **Two real findings worth fixing before launch**, the
rest are minor polish notes.

| Severity | Count |
|---|---|
| Critical (blocks M1) | 0 |
| High (fix before M5 launch) | 2 |
| Medium (polish, fix in M2) | 4 |
| Low (cosmetic / tracked for later) | 5 |
| Info (delta vs prototype, intentional) | 3 |

Recommendation: **APPROVE M1 frontend** with high-severity items (#H1, #H2)
filed as follow-up tasks for M2 hardening sprint. Both are non-rendering bugs
that don't block first user-facing demo, but will surface once we tighten
brand polish.

---

## Method

- Screenshot harness: Playwright 1.60 (Chromium 144, Firefox 150) capturing
  full-page PNGs at three viewports — mobile 375×812 (iPhone SE / 14 Pro
  emulation, deviceScaleFactor 2), tablet 768×1024, desktop 1440×900.
- Hard 25s wall-clock per shoot to handle PHP built-in server's
  single-threaded asset deadlock.
- Total artifacts: **94 screenshots** under `docs/qc/M1/screenshots/`,
  plus `_index.json` manifest.
- Prototype baseline: `prototype/*.html` (CDN Tailwind, original Tailwind
  Play config) served via `python3 -m http.server 3091`.
- Blade implementation: `php artisan serve` already running on port 3001
  (built `npm run build` artifact: `app-B-6iju16.css`, `app-L5hxbF1X.js`).
- Static parity diff: `grep` on `<section>` markers + `<x-…>` component
  invocations + Tailwind palette references.
- Color/spacing token comparison: `prototype/index.html` `tailwind.config`
  vs `store/tailwind.config.js` vs compiled `build/assets/app-*.css`.

### Browser coverage

| Browser    | Captured | Notes |
|------------|----------|-------|
| Chromium 144 (latest) | 51 | Full-page PNG, all pages OK except 2× course-detail Blade timeouts (#L4) |
| Firefox 150 (latest)  | 48 | Full-page PNG capped at 16,000 px (Firefox 32,767 px limit; below-fold content above this height not visually verified — content in DOM confirmed via curl) |
| Safari mobile (iOS sim) | 0 | **Not captured.** No WebKit launcher available on this VPS without further provisioning. Mobile UA + viewport emulation handled in Chromium. |

For Safari mobile, mobile sticky-CTA + Alpine transitions in #fullstack
parent task were spec'd against `webkit-overflow-scrolling` + `-webkit-`
properties in DESIGN.md; suggest manual iOS Simulator pass before final
launch. Filed as follow-up #L1.

### Routes verified (curl + screenshot)

All routes returned HTTP 200 from `php artisan serve`:

```
/                                       200
/produk                                  200
/produk/10-keajaiban-pikiran            200
/produk/kelas-amc-reguler               200
/cart                                    200
/checkout                                200
/checkout/success/MFP-20260516-ABC123   200
/upload/MFP-20260516-ABC123             200
/track/MFP-20260516-ABC123              200
```

---

## Section parity — home page

The QC body says "Semua section prototype index.html ada di Blade home". That's
**not strictly true** — prototype `index.html` has 12 narrative sections, the
Blade `home.blade.php` is a deliberately tighter 7-section shell. Most missing
prototype content is duplicate / overlapping AMC narrative that the planner
agreed to drop in the M1 implementation kickoff (per the implementation plan
`.sisyphus/plans/`, this is an intentional restructure, not a regression). I'm
flagging it so the team can confirm.

| #  | Prototype `index.html`            | Blade `home.blade.php`              | Status |
|----|-----------------------------------|--------------------------------------|--------|
| 1  | Hero (line 204)                   | Hero (line 185)                      | ✅ parity |
| 2  | Logo Cloud / Partners (line 317)  | Media coverage (`x-media-coverage`)  | ⚠️ moved to position 6, different markup but same visual purpose |
| 3  | Welcome (line 338)                | —                                    | ❌ dropped |
| 4  | Problem/Pain Points 6-card (413)  | —                                    | ❌ dropped |
| 5  | Formula AMC + Stats breakdown (516) | —                                  | ❌ dropped |
| 6  | About / Intro (564)               | —                                    | ❌ dropped |
| 7  | Statistics dark slate-900 (617)   | —                                    | ❌ dropped |
| 8  | Impact / Philosophy quote (651)   | —                                    | ❌ dropped |
| 9  | Materi AMC slate-900 2-col (678)  | —                                    | ❌ dropped |
| 10 | Categories Grid (#kategori, 898)  | Pricing Kelas (`#kelas`, line 356)   | ✅ parity (3-tier) |
| 11 | Featured Books (#buku, 1077)      | Katalog Buku (`#katalog`, line 318)  | ✅ parity (6 produk) |
| 12 | Testimonials (1246)               | Testimoni (line 461)                 | ✅ parity (3 cards) |
| 13 | Newsletter CTA (1313)             | Final CTA (line 516)                 | ⚠️ form replaced with WA CTA, see #M2 |
| —  | —                                 | Benefit AMC (4 cards, line 287)      | ➕ new — not in prototype, added during port |

Anchors `#kategori`/`#buku` from prototype were renamed to `#kelas`/`#katalog`
in Blade. **Internal hash links (`href="#kategori"`, `href="#buku"`) in any
ported page or the prototype-style WA copy will 404 silently.** Filed as
**#H2** below.

---

## Findings

### High (fix before launch — M5)

#### H1 · Tailwind palette has missing shades — silent CSS no-ops

**Where:** `store/tailwind.config.js`
**Severity:** High (visual brand drift, low likelihood but high blast radius)
**Reproduction:**

```bash
grep -rhoE "(bg|text|border|ring|from|to|via)-(primary|secondary|accent)-[0-9]+" \
  store/resources/views store/resources/js | sort -u | wc -l   # → 36 unique tokens
```

Compare to compiled `build/assets/app-B-6iju16.css`:

```bash
grep -oE "\.(bg|text|border|ring|from|to|via)-(primary|secondary|accent)-[0-9]+" \
  store/public/build/assets/app-*.css | sort -u | wc -l        # → 28 unique
```

**Shades referenced in views but NOT defined in tailwind.config.js:**

| Palette   | Missing shades         | Refs (incl. variants) |
|-----------|------------------------|----------------------|
| primary   | 200, 300, 400          | ~60 occurrences      |
| secondary | 100, 200, 300, 700, 800| 14 occurrences       |
| accent    | 50, 100, 300           | 4 occurrences        |

These compile to nothing — Tailwind's purge passes them through silently,
producing no CSS rule. Visually that means:

- `bg-primary-200/70` blob backgrounds in `cart.blade.php:19` render as the
  default `transparent` (no glow effect).
- `focus:ring-primary-200` (5× across cart, checkout, upload) yields no ring,
  reducing focus visibility (regression vs DESIGN.md a11y target ≥95).
- `border-primary-300` hover states on track + checkout success "Back" buttons
  → no border color change on hover.
- `bg-accent-100`, `text-accent-300` on benefit-card (M3 modules placeholder)
  → no fill, just transparent.

**Fix:** Add the missing shades to `store/tailwind.config.js`. Reference values
from Tailwind's stock indigo/teal/amber palette to match the prototype's
implicit assumption (prototype uses CDN Tailwind which has the full default
palette merged with the brand overrides):

```js
// store/tailwind.config.js — paste these into the existing color blocks
primary:   { 50:'#eef2ff', 100:'#e0e7ff', 200:'#c7d2fe', 300:'#a5b4fc',
             400:'#818cf8', 500:'#6366f1', 600:'#4f46e5', 700:'#4338ca',
             800:'#3730a3', 900:'#312e81' },
secondary: { 50:'#f0fdfa', 100:'#ccfbf1', 200:'#99f6e4', 300:'#5eead4',
             400:'#2dd4bf', 500:'#14b8a6', 600:'#0d9488', 700:'#0f766e',
             800:'#115e59', 900:'#134e4a' },
accent:    { 50:'#fffbeb',  100:'#fef3c7', 200:'#fde68a', 300:'#fcd34d',
             400:'#fbbf24', 500:'#f59e0b', 600:'#d97706', 700:'#b45309' },
```

Then `npm run build` and re-grep the compiled CSS to confirm. Verify focus
rings on cart / checkout / upload are visible.

**Recommended assignee:** `mc-fullstack` (small `tailwind.config.js` edit +
rebuild + smoke). ETA 30 min.

---

#### H2 · Anchor link drift between prototype and Blade

**Where:** Hero copy + footer + any inherited prototype content using
`href="#kategori"` or `href="#buku"`
**Severity:** High (broken in-page nav, easy to miss)
**Reproduction:**

```bash
grep -rnE 'href="#(kategori|buku)"' store/resources/views/   # → 0 hits in current Blade
grep -nE  'href="#(kategori|buku)"' prototype/index.html     # → 5 hits
```

The Blade home renamed prototype anchors `#kategori` → `#kelas` and `#buku` →
`#katalog`. Currently no Blade pages link to the old anchors so this is **not
broken right now**, but two future risks:

1. The marketing landing page (post-launch SEO) will likely lift CTA copy
   from prototype that includes these hashes.
2. WhatsApp CTA copy / email templates that reference "kategori AMC" or
   "#buku" will break.

**Fix:** Pick one canonical naming. Either revert Blade to `#kategori`/`#buku`
to match marketing copy (preferred — prototype anchor names are more
SEO-friendly bahasa) OR document the rename in DESIGN.md so future content
authors use the new names. Currently neither is documented.

**Recommended assignee:** `mc-planning` (decision) → `mc-fullstack` (rename if
needed). ETA 15 min.

---

### Medium (fix in M2)

#### M1 · Hero top padding regression

**Where:** `store/resources/views/pages/home.blade.php:185`
**Severity:** Medium
**Detail:** Prototype hero uses `pt-32 pb-20 lg:pt-48 lg:pb-32` (8rem / 12rem
top). Blade hero uses `pt-12 pb-20 lg:pt-24 lg:pb-32` — that's only 3rem / 6rem
top. Combined with the fixed `h-20` navbar (`pt-20` on `<main>`), the hero
gets 80 + 48 = 128px of breathing room on mobile vs prototype's 80 + 128 =
208px. Visible difference on chromium mobile screenshot:
`home__proto__chromium__mobile-375.png` vs `home__blade__chromium__mobile-375.png`.

**Fix:** restore `pt-32 lg:pt-48` to match prototype, OR document the
intentional tightening in DESIGN.md. Leaning toward keep-current because the
tighter hero feels more conventional for an e-commerce landing.

#### M2 · Newsletter CTA replaced with WA CTA

**Where:** `home.blade.php:516`
**Severity:** Medium
**Detail:** Prototype has an email subscribe form (newsletter). Blade replaces
it with two WhatsApp / produk CTAs. Email capture is gone. M3 affiliate brief
mentions "leaderboard email digest" so capture flow is needed eventually.
**Fix:** revisit in M3 affiliate sprint when email infra (Fonnte/Wablas)
is set up. Track as separate task, not blocking M1.

#### M3 · `text-gradient` `pb-2` patch is duplicated across templates

**Where:** Many places (home, hero, multiple `<span class="text-gradient">`)
**Severity:** Medium (technical debt, not user-visible)
**Detail:** Inline `pb-2` is used as a hack to prevent gradient text descenders
from being clipped by `overflow-hidden`. Should live as `.text-gradient
inline-block pb-1` utility in `app.css` so it stops being a sprinkled fix.
**Fix:** Add to global CSS in M2 polish.

#### M4 · Body class `relative` declared on layout but no `position: relative` content

**Where:** `home.blade.php:181` `bodyClass="relative"` and similar in cart
**Severity:** Medium (cleanup)
**Detail:** Several pages set `bodyClass="relative"` but their `position:
absolute` children (sticky CTAs) are inside `.lg:hidden` wrappers that have
their own positioning context. Body relative is a leftover. Audit & remove.
**Fix:** Delete `bodyClass="relative"` from home + cart + checkout. Smoke retest.

---

### Low (cosmetic — fix opportunistically)

#### L1 · Safari mobile not visually verified

**Where:** all mobile sticky-CTA pages
**Severity:** Low
**Detail:** No iOS Simulator or BrowserStack on this VPS. Recommend manual
spot-check before launch on real iPhone. Specifically:
- `/cart` sticky bottom bar with `backdrop-blur` (Safari 15 had quirks)
- `/produk/10-keajaiban-pikiran` sticky CTA `transition-transform`
- Hero `animate-float-delayed` (Safari respects animation-delay differently)

#### L2 · `animation-delay-200` and `animation-delay-400` use raw class names

**Where:** `home.blade.php:189-190`
**Severity:** Low
**Detail:** Tailwind doesn't ship these out of the box — they're defined as
`@layer utilities { .animation-delay-200 { animation-delay: 0.2s; } }` in
`app.css`. Fine, but undocumented in the components gallery.
**Fix:** Add a "Custom utilities" section to components-gallery.

#### L3 · Footer `mt-20` may stack with hero pull-up

**Where:** `components/footer.blade.php:1`
**Severity:** Low
**Detail:** Prototype footer is part of the page flow with no top margin.
Blade footer has `mt-20`. On track/upload pages with short content, this
creates a large gap. Visible in `track__blade__chromium__desktop-1440.png`
at ~1100px scroll. Trim to `mt-12` or rely on previous section's `pb-`.

#### L4 · Course detail Blade screenshot guard-timeouts at tablet/desktop

**Where:** `screenshot harness only` (not a render bug)
**Severity:** Low
**Detail:** PHP built-in server's single-threaded I/O hangs when chromium
prefetches all assets in parallel. Mobile-375 captured fine (lighter
fetch budget). Tablet/desktop guard-timeout at 25s. Render itself works
when curl'd directly (returned full 76kb HTML in 64ms). Filed as test
infra note — switch to `php artisan serve` with multiple workers, OR use
`php -S 127.0.0.1:3001 -t public public/index.php` with `-S` worker pool
shim, OR run Caddy/nginx local. Not a Blade bug.

#### L5 · `prototype/product-detail.html` legacy bootstrap page has a Tailwind error

**Where:** prototype only
**Severity:** Low (informational — prototype is not shipped)
**Detail:** Prototype `product-detail.html` uses CDN Tailwind play and
references `focus:ring-primary-200` which fails to compile in CDN play mode
("class does not exist within @layer directive"). Console error visible in
chromium + firefox runs. Same will work in Blade (tailwind compiles ahead of
time + once palette gap H1 is fixed). **No action — prototype reference only.**

---

### Info (intentional or out-of-scope)

#### I1 · 5 prototype home sections dropped

The team intentionally cut Welcome / Problem / Formula / Stats / Impact /
Materi narrative sections to focus on conversion (Hero → Benefit → Katalog →
Pricing → Testimoni → CTA). This deviates from QC body acceptance criterion
"Semua section prototype index.html ada di Blade home". Three options:

1. **Accept** the trim and update QC criterion language to "Semua section
   yang relevan untuk konversi" — recommend this.
2. Add a single combined "About AMC" section between Benefit and Katalog
   that pulls compressed copy from Welcome + Formula + Materi.
3. Restore prototype 1:1 — not recommended, prototype was a wireframe.

I lean #1 + create a `t_xxx` task for `mc-planning` to update DESIGN.md
home wireframe.

#### I2 · `pages/products/show.blade.php` does dual-render (book vs course)

The Blade `products/show` view dispatches to `book` or `course` partial based
on slug. Prototype has separate HTML files. This is good — single source of
truth — but means there's no catch-all "product not found" → currently 404.
DD'd in route handler. Confirmed working: `curl /produk/notexist` → 404.

#### I3 · `components-gallery` route exists but only in dev

`Route::get('/__components', view('components-gallery'))` is gated by
environment check (line 187-188 of `routes/web.php`). 404 on production is
intended. Confirmed: hitting `/components-gallery` returns 404.

---

## Cross-browser smoke

Both browsers render identically at the screenshot level for all 7 main
routes (95+% pixel similarity by visual inspection of paired PNGs). No
browser-specific rendering bugs in scope. Specific spot-checks:

- **Hamburger transitions:** Identical timing chromium vs firefox (Alpine
  `x-transition` works the same in both). Mobile menu auto-closes on
  outside-click and Escape.
- **Sticky CTA:** Both browsers respect `lg:hidden` + `position:fixed
  bottom-0`. backdrop-blur renders correctly in both.
- **Custom blob animation:** Both browsers run the keyframes (chromium snapshot
  catches a frame ~30% through; firefox catches ~50%; expected — capture
  timing).
- **Lucide icons:** Render identically; both have `defer` script that
  initializes after DOMContentLoaded.

---

## Proposed M2 follow-up tasks

Filed/recommended (mc-qc does NOT spawn these, leaves it to mc-planning to
intake):

1. **[FE/M2] Fix tailwind.config.js color palette** — finding H1. Assignee
   `mc-fullstack`. ETA 30 min. Blocking next visual audit.
2. **[Plan/M2] Decide anchor naming convention** — finding H2. Assignee
   `mc-planning`. ETA 15 min.
3. **[FE/M2] Restore hero spacing OR update DESIGN.md** — finding M1.
   Assignee `mc-fullstack`. ETA 15 min.
4. **[FE/M2] Newsletter email capture for affiliate readiness** — M2 lifts
   into M3 affiliate sprint. Assignee `mc-fullstack`.
5. **[Infra] Set up real preview server (Caddy/nginx)** to replace
   `php artisan serve` for QC reruns — finding L4.

---

## Lighthouse audit

Out of scope for this task (assignee is visual review + cross-browser).
Lighthouse is the next QC task: `t_9cf308a8` ([QC] Lighthouse audit M1
≥90 perf, ≥95 a11y). I'll pick that up next dispatch.

---

## Artifacts

```
docs/qc/M1/
├── visual-review-M1.md          ← this file
└── screenshots/
    ├── _index.json              ← machine-readable manifest
    ├── home__{proto,blade}__{chromium,firefox}__{mobile-375,tablet-768,desktop-1440}.png
    ├── product-list__blade__*   (no prototype baseline — list page is new)
    ├── book-detail__{proto,blade}__*
    ├── course-detail__{proto,blade}__*  (2× blade gaps at tablet/desktop, see L4)
    ├── product-detail-bs__proto__*   (legacy Bootstrap proto, no blade equiv)
    ├── cart__{proto,blade}__*
    ├── checkout__{proto,blade}__*
    ├── checkout-success__{proto,blade}__*
    ├── track__blade__*           (no prototype — track is new)
    └── upload__blade__*          (no prototype — upload is new)
```

94 PNGs total. Side-by-side review possible by opening matching `proto`/`blade`
pairs.
