import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const shots = join(here, '../api/screenshots');
const CHROME = process.env.CHROME_PATH ?? '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';
const BASE = process.env.PANEL_URL ?? 'http://127.0.0.1:8000';
const ADMIN_EMAIL = process.env.ADMIN_EMAIL ?? 'editor@hamilelik.app';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD ?? 'gecici-uzun-parola';

mkdirSync(shots, { recursive: true });

const browser = await chromium.launch({ executablePath: CHROME });
const page = await browser.newPage({ viewport: { width: 1280, height: 900 }, deviceScaleFactor: 2 });

const errors = [];
page.on('pageerror', (e) => errors.push(String(e)));
page.on('response', (r) => { if (r.status() >= 400) errors.push(`${r.status()} ${r.url()}`); });

const shot = async (name) => {
  await page.waitForTimeout(400);
  await page.screenshot({ path: `${shots}/${name}.png`, fullPage: true });
  console.log(`  ✓ ${name}`);
};

console.log('Panel: giris → genel bakis → onay kuyrugu → onay\n');

await page.goto(`${BASE}/admin/giris`, { waitUntil: 'networkidle' });
await shot('panel-01-giris');

await page.getByLabel('E-posta').fill(ADMIN_EMAIL);
await page.getByLabel('Parola').fill(ADMIN_PASSWORD);
await page.getByRole('button', { name: 'Giriş yap' }).click();
await page.waitForURL(`${BASE}/admin`, { timeout: 15000 });
await shot('panel-02-genel-bakis');

await page.getByRole('link', { name: 'Onay kuyruğuna git' }).click();
await page.waitForSelector('text=Onay bekleyen içerikler', { timeout: 10000 });
const queue = await page.getByRole('link', { name: 'Oku ve onayla' }).count();
console.log(`  · kuyrukta bekleyen: ${queue}`);
await shot('panel-03-onay-kuyrugu');

// Hekimin okuma ekrani: metin ve kaynaklar, altinda tek onay kutusu.
await page.getByRole('link', { name: 'Oku ve onayla' }).first().click();
await page.waitForSelector('text=Onaylanacak içerik', { timeout: 10000 });
await shot('panel-04-gozden-gecirme');

await page.getByRole('button', { name: 'Onayla ve yayına al' }).click();
await page.waitForSelector('text=onaylandı ve yayına alındı', { timeout: 10000 });
console.log('  ✓ onay islendi');
await shot('panel-05-onay-sonrasi');

await page.goto(`${BASE}/admin/tetkikler`, { waitUntil: 'networkidle' });
await shot('panel-06-tetkikler');

await page.goto(`${BASE}/admin/haftalar`, { waitUntil: 'networkidle' });
await shot('panel-07-haftalar');

await browser.close();
console.log(`\nSayfa/istek hatasi: ${errors.length}`);
if (errors.length) { console.log(errors.slice(0, 5).join('\n')); process.exit(1); }
console.log('Panel dogrulandi.');
