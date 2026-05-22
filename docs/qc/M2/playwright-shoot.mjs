// QC M2 Admin visual review — Playwright screenshot harness
// Login as seeded admin, then capture full-page PNG of every admin route
// across 3 viewport (375 / 768 / 1440) on Chromium.
//
// Run from: /root/malang-creative/_active/masfirmanpratama
// node docs/qc/M2/playwright-shoot.mjs

import { chromium, devices } from 'playwright';
import fs from 'node:fs';
import path from 'node:path';

const BASE = process.env.BASE_URL || 'http://127.0.0.1:3001';
const EMAIL = process.env.ADMIN_EMAIL || 'admin@masfirmanpratama.com';
const PASSWORD = process.env.ADMIN_PASSWORD || 'admin123';
const OUT = process.env.OUT_DIR || 'docs/qc/M2/screenshots';

const VIEWPORTS = [
  { name: 'mobile-375',  width: 375,  height: 812 },
  { name: 'tablet-768',  width: 768,  height: 1024 },
  { name: 'desktop-1440', width: 1440, height: 900 },
];

// Routes to visit, after login. `extra` are interactions to perform before snap.
const ROUTES = [
  { id: 'login',                url: '/admin/login',                preLogin: true },
  { id: 'dashboard',            url: '/admin/dashboard' },
  { id: 'products-index',       url: '/admin/products' },
  { id: 'products-create',      url: '/admin/products/create' },
  { id: 'products-create-validation',
                                url: '/admin/products/create',
                                action: 'submitEmptyForm' },
  { id: 'orders-index',         url: '/admin/orders' },
  { id: 'orders-show',          url: 'FIRST_ORDER' },
  { id: 'settings',             url: '/admin/settings' },
  { id: 'wa-notifications',     url: '/admin/wa-notifications' },
  { id: 'installment-schemes',  url: '/admin/installment-schemes' },
  { id: 'installment-schemes-create',
                                url: '/admin/installment-schemes/create' },
];

fs.mkdirSync(OUT, { recursive: true });

async function login(page) {
  await page.goto(BASE + '/admin/login', { waitUntil: 'networkidle', timeout: 30_000 });
  await page.fill('input[name=email]', EMAIL);
  await page.fill('input[name=password]', PASSWORD);
  await Promise.all([
    page.waitForURL(/\/admin\/dashboard/, { timeout: 30_000 }),
    page.click('button[type=submit]'),
  ]);
}

async function findFirstOrderId(page) {
  await page.goto(BASE + '/admin/orders', { waitUntil: 'networkidle', timeout: 30_000 });
  // Try to find the first order link to /admin/orders/{id}
  const href = await page.evaluate(() => {
    const a = document.querySelector('a[href*="/admin/orders/"]:not([href$="/admin/orders"])');
    return a?.getAttribute('href') ?? null;
  });
  return href;
}

async function shoot(page, route, viewport, captured) {
  const url = route.url.startsWith('http') ? route.url : BASE + route.url;
  try {
    await page.goto(url, { waitUntil: 'networkidle', timeout: 30_000 });
  } catch (e) {
    captured.errors.push({ id: route.id, viewport: viewport.name, error: 'goto: ' + e.message });
    return;
  }

  if (route.action === 'submitEmptyForm') {
    try {
      // Click submit on first form, then wait for validation to appear
      await page.click('form button[type=submit]', { timeout: 5_000 });
      await page.waitForLoadState('networkidle', { timeout: 10_000 });
    } catch (e) {
      // fine, validation might be inline
    }
  }

  const file = path.join(OUT, `${route.id}__${viewport.name}.png`);
  try {
    await page.screenshot({ path: file, fullPage: true, timeout: 25_000 });
    captured.shots.push({ id: route.id, viewport: viewport.name, file: path.basename(file) });
  } catch (e) {
    captured.errors.push({ id: route.id, viewport: viewport.name, error: 'shot: ' + e.message });
  }
}

(async () => {
  const captured = { shots: [], errors: [], started: new Date().toISOString() };
  const browser = await chromium.launch({ headless: true });

  for (const viewport of VIEWPORTS) {
    const ctx = await browser.newContext({
      viewport: { width: viewport.width, height: viewport.height },
      deviceScaleFactor: 2,
      userAgent: viewport.name === 'mobile-375'
        ? 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148'
        : undefined,
    });
    const page = await ctx.newPage();

    // login route first (no auth needed)
    await shoot(page, ROUTES[0], viewport, captured);

    try {
      await login(page);
    } catch (e) {
      captured.errors.push({ id: 'login-flow', viewport: viewport.name, error: e.message });
      await ctx.close();
      continue;
    }

    // resolve FIRST_ORDER url after login
    const firstOrder = await findFirstOrderId(page);

    for (const route of ROUTES.slice(1)) {
      let r = route;
      if (route.url === 'FIRST_ORDER') {
        if (!firstOrder) {
          captured.errors.push({ id: route.id, viewport: viewport.name, error: 'no order found' });
          continue;
        }
        r = { ...route, url: firstOrder };
      }
      await shoot(page, r, viewport, captured);
    }

    await ctx.close();
  }

  captured.finished = new Date().toISOString();
  fs.writeFileSync(path.join(OUT, '_index.json'), JSON.stringify(captured, null, 2));
  console.log('shots:', captured.shots.length, 'errors:', captured.errors.length);
  if (captured.errors.length) console.log(JSON.stringify(captured.errors, null, 2));

  await browser.close();
})();
