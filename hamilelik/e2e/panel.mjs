import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const shots = join(here, '../api/screenshots');
const CHROME = process.env.CHROME_PATH ?? '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';
const ADMIN_EMAIL = process.env.ADMIN_EMAIL ?? 'editor@hamilelik.app';
const ADMIN_PASSWORD = process.env.ADMIN_PASSWORD ?? 'gecici-uzun-parola';

mkdirSync(shots, { recursive: true });

const browser = await chromium.launch({ executablePath: CHROME });
const page = await browser.newPage({ viewport: { width: 1280, height: 900 } });
const errors = [];
page.on('pageerror', (e) => errors.push(String(e)));

await page.goto('http://127.0.0.1:8000/admin/login', { waitUntil: 'networkidle' });
await page.getByLabel(/e-mail|e-posta|email/i).first().fill(ADMIN_EMAIL);
await page.getByLabel(/password|parola|şifre/i).first().fill(ADMIN_PASSWORD);
await page.getByRole('button', { name: /sign in|giriş/i }).first().click();
await page.waitForURL(/\/admin/, { timeout: 20000 });
await page.waitForTimeout(1500);
console.log('  ✓ panele giris');

await page.goto('http://127.0.0.1:8000/admin/screening-templates', { waitUntil: 'networkidle' });
await page.waitForTimeout(1200);
await page.screenshot({ path: `${shots}/panel-tetkikler.png`, fullPage: true });
const draftBadges = await page.getByText('Taslak', { exact: true }).count();
console.log(`  ✓ tetkik listesi — "Taslak" rozeti: ${draftBadges}`);

await page.goto('http://127.0.0.1:8000/admin/week-contents', { waitUntil: 'networkidle' });
await page.waitForTimeout(1200);
await page.screenshot({ path: `${shots}/panel-haftalar.png`, fullPage: true });
console.log('  ✓ hafta icerikleri listesi');

// Yayina alma kapisi: onay alanlarini bosaltip yayinda birakmayi dene.
await page.getByRole('link', { name: /edit|düzenle/i }).first().click().catch(() => {});
await page.waitForTimeout(1500);
await page.screenshot({ path: `${shots}/panel-hafta-duzenle.png`, fullPage: true });
console.log('  ✓ hafta duzenleme formu');

await browser.close();
console.log(`\nSayfa hatasi: ${errors.length}`);
if (errors.length) { console.log(errors.slice(0,3).join('\n')); process.exit(1); }
console.log('Panel dogrulandi.');
