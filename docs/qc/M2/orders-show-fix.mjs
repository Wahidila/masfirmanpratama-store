import { chromium } from 'playwright';
import path from 'node:path';

const BASE = 'http://127.0.0.1:3001';
const browser = await chromium.launch({ headless: true });
const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 }, deviceScaleFactor: 2 });
const page = await ctx.newPage();
await page.goto(BASE + '/admin/login', { waitUntil: 'load' });
await page.fill('input[name=email]', 'admin@masfirmanpratama.com');
await page.fill('input[name=password]', 'admin123');
await Promise.all([page.waitForURL(/\/admin\/dashboard/, { timeout: 30_000 }), page.click('button[type=submit]')]);
await page.goto(BASE + '/admin/orders', { waitUntil: 'load' });
const href = await page.evaluate(() => {
  const a = document.querySelector('a[href*="/admin/orders/"]:not([href$="/admin/orders"])');
  return a?.href ?? null;
});
console.log('href:', href);
if (href) {
  await page.goto(href, { waitUntil: 'load' });
  await page.waitForTimeout(800);
  await page.screenshot({ path: 'docs/qc/M2/screenshots/orders-show__desktop-1440.png', fullPage: true });
  console.log('saved');
}
await browser.close();
