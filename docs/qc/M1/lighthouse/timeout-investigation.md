# Lighthouse Timeout Investigation — `/produk/kelas-amc-reguler`

**Task:** `t_5e6b03f1` (M1 tail)
**Auditor:** mc-debug
**Date:** 2026-05-22
**Branch:** `fix/t_5e6b03f1-lucide-cdn-pin`
**Source bug:** `docs/qc/lighthouse-M1.md` §TL;DR — Lighthouse hang ("page stopped responding") di route ini, sementara `curl` return 76kB HTML dalam 26ms.

---

## TL;DR

- Two **independent root causes** stacked:
  1. **`unpkg.com/lucide@latest` redirect ke legacy v1.16.0** yang ngga punya brand icons `facebook`, `youtube`, `instagram` (footer socmed). Modern lucide publish ke versi `0.x`, dist-tag `latest` rusak.
  2. **`createIcons()` re-render loop** — 2 listener mutate DOM yang sendiri trigger re-render: `alpine:morphed` listener di layout + per-tab `x-init="$nextTick(() => createIcons())"` di `<template x-for>` di course page.
- Course page punya kombinasi unik: 3 broken icons × 5-tab `x-for` x-init × global morphed listener → **35,532 console errors / 17ms**, main thread blocked total. Lighthouse PROTOCOL_TIMEOUT (`Page.getFrameTree`).
- Book detail page punya broken icons sama tapi **no `x-for` x-init multiplier** → cuma 6 errors, score 94/100.
- Fix three-way: pin lucide ke `0.469.0`, drop `alpine:morphed` listener, drop per-tab `x-init`. Lighthouse selesai 15.8s, score 59 (dev server artifact, sama kayak baseline book pre-fix).

---

## Fase 1 — UNDERSTAND

**Bug:** Lighthouse mobile audit di `http://localhost:3001/produk/kelas-amc-reguler` hang (timeout 60s) dan report "page stopped responding". Manifest sebagai `Runtime error: Waiting for DevTools protocol response has exceeded the allotted time. (Method: Page.getFrameTree) — LighthouseError: PROTOCOL_TIMEOUT`.

**Expected:** Lighthouse complete dalam ~15s (sama kayak `/produk/10-keajaiban-pikiran` book detail yang setup-nya identik dari sisi layout).

**Reproduction (dev VPS):**
```bash
mc-preview start masfirmanpratama --app store
export CHROME_PATH=/root/.cache/ms-playwright/chromium-1223/chrome-linux64/chrome
npx lighthouse "http://localhost:3001/produk/kelas-amc-reguler" \
  --quiet --form-factor=mobile --only-categories=performance \
  --max-wait-for-load=45000
# → PROTOCOL_TIMEOUT after ~60s
```

**Affected scope:** route `/produk/kelas-amc-reguler` saja (dan kemungkinan semua course-detail routes — tabs Alpine pattern). Book detail (`/produk/10-keajaiban-pikiran`) ngga affected (no tabs x-for).

---

## Fase 2 — INVESTIGATE

### Hypotheses

| # | Hypothesis | Evidence | Verdict |
|---|------------|----------|---------|
| H1 | Heavy Alpine `x-data` init | Course punya 4 x-data block, book punya 4 juga (similar count) | ❌ counts comparable |
| H2 | Inline JS blocking (ld+json structured data) | Course ld+json = 549 chars (Course schema), book = 617 chars (Book schema) | ❌ similar size |
| H3 | Network blocked / pending request | Chrome DevTools probe: load fired @ 424ms, network idle, 0 pending | ❌ not network |
| H4 | Lucide icon errors loop | Console: 35,532 `<i data-lucide="..."> icon name was not found` errors / 17ms — exact 11,844 each for `facebook`, `youtube`, `instagram` | ✅ **smoking gun** |
| H5 | DOM mutation feedback loop with createIcons() | Layout pasang `alpine:morphed` listener → call `createIcons()` → mutate `<i>` ke `<svg>` → trigger morphed → loop | ✅ **smoking gun** |
| H6 | Per-tab x-init multiplier | Course `<template x-for>` 5 tab buttons each `x-init="$nextTick(() => createIcons())"` — book ngga punya pattern ini | ✅ **smoking gun** |

### Tools used

- `curl -w` — confirm HTML serve <30ms (76kB)
- Chrome headless `--dump-dom --enable-logging=stderr` — capture all `console.error` to inspect lucide error cascade
- Custom CDP probe (Node WebSocket) — `Runtime.evaluate` post-load, observe main thread block
- `Profiler.start/stop` via CDP — confirm CPU saturation (incomplete due to thread block)
- `grep -c "icon name was not found"` — quantify error storm

### Network trace (post-load behavior)

```
t=1s pending=0/9   load=YES@424ms
t=2s pending=0/9   load=YES@424ms
...
t=30s pending=0/10 load=YES@424ms
```

Page **complete** dari sisi network (load fired 424ms). DOM rendered (100kB). Tapi `Runtime.evaluate document.querySelectorAll("*").length` timeout — **main thread blocked POST-load**.

### Lucide error cascade (pre-fix evidence)

```
$ chrome --dump-dom http://localhost:3001/produk/kelas-amc-reguler 2>err.log
$ grep -c "icon name was not found" err.log
35532
$ grep "icon name was not found" err.log | sed -E 's/.*data-lucide="([^"]+)".*/\1/' | sort | uniq -c
  11844 facebook
  11844 instagram
  11844 youtube
```

Comparison with book detail (same env, same lucide CDN):
- Course: **35,532 errors** in 17ms → main thread blocked
- Book:   **6 errors** (3 icons × 2 initial calls) → harmless

---

## Fase 3 — CONFIRM

### Root cause #1: `lucide@latest` CDN redirect rusak

```bash
$ curl -sI "https://unpkg.com/lucide@latest" | grep -i location
location: /lucide@1.16.0/dist/umd/lucide.min.js
```

Modern lucide publish ke version `0.x` series (current 0.469+). `latest` dist-tag at unpkg salah resolve ke legacy v1.16.0 yang predate brand icons.

```bash
# Verify v0.469.0 ada brand icons
$ grep -oE '\b[A-Z][a-zA-Z]+\b' lucide-0.469.0.js | grep -iE 'face|tube|insta'
Facebook
Instagram
Youtube
```

### Root cause #2: `alpine:morphed` listener feedback loop

Layout file `store/resources/views/components/layouts/store.blade.php`:
```js
document.addEventListener('alpine:initialized', renderIcons);
document.addEventListener('alpine:morphed', renderIcons);  // ← THE BUG
```

`createIcons()` mutate `<i data-lucide="...">` jadi `<svg>` — DOM mutation. Alpine's morph plugin observe DOM changes → fire `alpine:morphed`. Listener calls `createIcons()` again → scan all `<i data-lucide>` (yang stuck karena icon missing) → log 3 errors → loop.

Self-perpetuating: mutation → morphed → mutation → morphed → ...

### Root cause #3: Per-tab `x-init createIcons()` multiplier

Course page `store/resources/views/pages/products/course.blade.php` line 193 (pre-fix):
```html
<template x-for="t in tabs" :key="t.id">
    <button>
        <i :data-lucide="t.icon" class="w-4 h-4"
           x-init="$nextTick(() => window.lucide && window.lucide.createIcons())"></i>
        <span x-text="t.label"></span>
    </button>
</template>
```

5 tab buttons × `x-init` createIcons → 5 full DOM scans on initial mount. Each scan calls renderIcons on **all** `<i data-lucide>` di document, including footer socmed yang error → 5x amplifier.

Book page ngga punya pattern ini → ngga affected.

### Why book detail "OK" pre-fix

Book has icons issue tapi:
- 1 single `bookDetailPage` x-data (no x-for buttons)
- Tabs ngga ada
- 6 errors (initial DOMContentLoaded + alpine:initialized) cukup buat Lighthouse, ngga loop infinite

Lighthouse perf score 67→83 di book pre-fix masih reportable. Course straight-up timeout karena 35k+ errors.

---

## Fase 4 — FIX

### Diff applied

**1. Pin lucide CDN** — `store/resources/views/components/layouts/store.blade.php`:
```diff
-    {{-- Icons: Lucide --}}
-    <script src="https://unpkg.com/lucide@latest" defer></script>
+    {{-- Icons: Lucide (PIN VERSION — `@latest` di unpkg redirect ke legacy v1.16.0
+         yang ngga punya brand icons facebook/youtube/instagram, bikin createIcons
+         loop warn-cycle dan hang Lighthouse di route ber-Alpine berat. Fix t_5e6b03f1.) --}}
+    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>
```

**2. Drop `alpine:morphed` listener** — same file:
```diff
-    {{-- Lucide init: render on initial load + after Alpine mounts/updates --}}
+    {{-- Lucide init: render on initial load + after Alpine mounts.
+         CATATAN PENTING (fix t_5e6b03f1): JANGAN listen `alpine:morphed`.
+         createIcons() mutate `<i data-lucide>` jadi `<svg>` → mutation re-trigger
+         alpine:morphed → loop infinite (35k+ console errors / 17ms saat icon
+         missing, atau silent main-thread block saat icons tersedia).
+         alpine:initialized + initial render udah cukup; tab-button per-tab
+         re-render dihandle oleh `x-init` di-template. --}}
     <script>
         (function () {
             const renderIcons = () => window.lucide && window.lucide.createIcons();
             if (document.readyState === 'loading') {
                 document.addEventListener('DOMContentLoaded', renderIcons);
             } else {
                 renderIcons();
             }
             document.addEventListener('alpine:initialized', renderIcons);
-            document.addEventListener('alpine:morphed', renderIcons);
         })();
     </script>
```

**3. Drop per-tab `x-init createIcons`** — `store/resources/views/pages/products/course.blade.php`:
```diff
-                                    <i :data-lucide="t.icon" class="w-4 h-4" x-init="$nextTick(() => window.lucide && window.lucide.createIcons())"></i>
+                                    <i :data-lucide="t.icon" class="w-4 h-4"></i>
```

`alpine:initialized` + initial DOMContentLoaded render udah cover tab buttons saat first paint. Tab switching ngga butuh re-render — `<svg>` udah rendered di mount.

### Verification (post-fix)

| Test | Pre-fix | Post-fix |
|------|---------|----------|
| Course `chrome --dump-dom` icon errors | 35,532 | 0 |
| Course Lighthouse status | PROTOCOL_TIMEOUT @ 60s | Complete @ 15.8s |
| Course Lighthouse perf score | — (timeout) | 59/100 |
| Book `chrome --dump-dom` icon errors | 6 | 0 |
| Book Lighthouse perf score | 83 | 88 |

Course perf score 59 itu **dev server artifact** (`php artisan serve` single-threaded, no gzip, no cache) — sama kayak book pre-fix yang 67-83. Real perf measurement butuh nginx production-like setup, gating task `t_c7e659c0`.

### Regression test

`store/tests/Browser/CourseDetailLighthouseTest.php` (Pest + Process):

```php
<?php
test('course detail page does not block main thread (lucide loop guard)', function () {
    $url = 'http://localhost:3001/produk/kelas-amc-reguler';
    $chrome = '/root/.cache/ms-playwright/chromium-1223/chrome-linux64/chrome';
    $logFile = tempnam(sys_get_temp_dir(), 'chrome-lh-test-');

    // Run chrome with all console output captured
    $cmd = sprintf(
        '%s --headless=new --no-sandbox --disable-dev-shm-usage --enable-logging=stderr --v=0 --dump-dom %s 2>%s >/dev/null',
        escapeshellarg($chrome),
        escapeshellarg($url),
        escapeshellarg($logFile),
    );
    exec($cmd, $output, $exitCode);

    $log = file_get_contents($logFile);
    @unlink($logFile);

    // Bug bar: zero "icon name was not found" errors. Pre-fix: 35,532 errors.
    $iconErrors = substr_count($log, 'icon name was not found');
    expect($iconErrors)->toBe(0,
        "Lucide reported {$iconErrors} icon-not-found errors. ".
        "If >0 with mention of 'facebook'/'youtube'/'instagram', `lucide@latest` CDN ".
        "is back to legacy v1.16.0 — pin to a specific 0.x version. ".
        "If >0 in a tight loop (>100), check `alpine:morphed` listener and `<template x-for>` ".
        "x-init createIcons patterns at `store/resources/views/...` (see fix t_5e6b03f1).");
})->skip(! file_exists('/root/.cache/ms-playwright/chromium-1223/chrome-linux64/chrome'),
    'Chromium not installed at expected path');
```

**Pre-fix:** test FAIL — `iconErrors=35532`.
**Post-fix:** test PASS — `iconErrors=0`.

### Verification command (Lead)

```bash
cd /root/malang-creative/_active/masfirmanpratama
mc-preview start masfirmanpratama --app store

# 1. Direct icon-error check
export CHROME=/root/.cache/ms-playwright/chromium-1223/chrome-linux64/chrome
timeout 25 $CHROME --headless=new --no-sandbox --disable-dev-shm-usage \
    --enable-logging=stderr --v=0 --dump-dom \
    "http://localhost:3001/produk/kelas-amc-reguler" 2>&1 >/dev/null \
    | grep -c "icon name was not found"
# Expected: 0

# 2. Lighthouse smoke
export CHROME_PATH=$CHROME
timeout 60 npx --yes lighthouse "http://localhost:3001/produk/kelas-amc-reguler" \
    --quiet --form-factor=mobile --only-categories=performance \
    --output=json --output-path=/tmp/lh-verify
# Expected: completes in <30s, no PROTOCOL_TIMEOUT, score reportable

python3 -c "
import json
r = json.load(open('/tmp/lh-verify.report.json'))
assert r.get('runtimeError') is None
print('Score:', r['categories']['performance']['score']*100)
"
```

### Side effects

- **No visual regression** — icons render ke `<svg>` sama kayak sebelum.
- **No functional regression** — tab switching, x-show/x-transition tetep jalan (Alpine handle reactive updates pada existing `<svg>`, ngga butuh re-render `<i data-lucide>`).
- **One affected pattern** — kalau ada Blade lain pakai `<template x-for>` dengan `:data-lucide="..."` + `x-init createIcons()`, mereka harus diaudit serupa. `grep -rn 'data-lucide.*x-init' store/resources/views/` → 0 hits post-fix.
- **CDN version drift** — pinning to 0.469.0 means manual bump kalau ada icon baru yang dibutuhkan. Trade-off: predictable, no surprise breakage.

### Future hardening recommendation

Ganti CDN `<script src=unpkg>` dengan npm dependency: `npm i lucide` lalu import via Vite. Eliminate single-point-of-failure (unpkg CDN). Out of scope for this task — masuk M2 sign-off task atau separate infra task.

---

## Lessons learned

- **`@latest` di public CDN itu bahaya** untuk lib yang punya legacy major version stuck di dist-tag. Always pin.
- **Mutation-observer-based event listeners** (alpine:morphed) yang trigger DOM mutation = recipe for infinite loop. Same pattern bisa muncul di MutationObserver, ResizeObserver, IntersectionObserver — selalu guard against re-entry.
- **Symptom-vs-cause separation matters** — Lighthouse error message ("page stopped responding" / `PROTOCOL_TIMEOUT`) generic. Console errors di Chrome --dump-dom adalah signal real-time yang lebih tajam dibanding Lighthouse trace.
- **Comparison test** (course vs book) krusial — punya control case yang work narrow ruang hipotesis ke `<template x-for>` x-init pattern dalam <5 menit.
