import { chromium } from 'playwright';
import { execSync } from 'node:child_process';
import { mkdirSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';

const here = dirname(fileURLToPath(import.meta.url));
const LOG = join(here, '../api/storage/logs/laravel.log');
const shots = join(here, '../app/screenshots');
const EMAIL = `anne${Date.now()}@example.com`;
const CHROME = process.env.CHROME_PATH ?? '/opt/pw-browsers/chromium-1194/chrome-linux/chrome';

mkdirSync(shots, { recursive: true });

const browser = await chromium.launch({ executablePath: CHROME });
const page = await browser.newPage({ viewport: { width: 420, height: 880 }, deviceScaleFactor: 2 });

const errors = [];
page.on('console', (m) => m.type() === 'error' && errors.push(m.text()));
page.on('pageerror', (e) => errors.push(String(e)));

const step = async (name) => {
  await page.waitForTimeout(700);
  await page.screenshot({ path: `${shots}/${name}.png` });
  console.log(`  ✓ ${name}`);
};

console.log('E2E: giris → kurulum → ana ekran\n');

await page.goto('http://127.0.0.1:8081/', { waitUntil: 'networkidle' });
await page.waitForSelector('text=Giriş yapın', { timeout: 20000 });
await step('01-giris');

await page.getByLabel('E-posta adresi').fill(EMAIL);
await page.getByRole('button', { name: 'Kod gönder' }).click();
await page.waitForSelector('text=Kodu girin', { timeout: 15000 });
await step('02-kod');

// Kodu sunucunun log'undan okuyoruz — istemciye hicbir zaman gonderilmiyor.
const log = execSync(`grep -oE "OTP ${EMAIL}: [0-9]{6}" ${LOG} | tail -1`).toString().trim();
const code = log.split(': ')[1];
if (!code) throw new Error('Kod log\'da bulunamadi');
console.log(`  · sunucu log'undan okunan kod: ${code}`);

await page.getByLabel('Giriş kodu').fill(code);
await page.getByRole('button', { name: 'Giriş yap' }).click();
await page.waitForSelector('text=Hangi tarihi biliyorsunuz?', { timeout: 20000 });
await step('03-kurulum');

// 24 hafta once bir SAT gir.
const lmp = new Date(Date.now() - 168 * 86400000);
await page.getByLabel('Gün').fill(String(lmp.getUTCDate()));
await page.getByLabel('Ay').fill(String(lmp.getUTCMonth() + 1));
await page.getByLabel('Yıl').fill(String(lmp.getUTCFullYear()));

await page.waitForSelector('text=Önizleme', { timeout: 10000 });
const preview = await page.locator('text=/^\\d+h \\d+g$/').first().textContent();
console.log(`  · istemci onizlemesi: ${preview}`);
await step('04-onizleme');

await page.getByRole('button', { name: 'Devam et' }).click();
await page.waitForSelector('text=Tahmini doğum', { timeout: 20000 });
await step('05-ana-ekran');

const week = await page.locator('text=/^hafta \\d+ gün$/').first().textContent();
const dueRow = await page.locator('text=Tahmini doğum').first().locator('..').textContent();
console.log(`  · ana ekran: ${week} · ${dueRow}`);

// Hafta detayina git.
await page.getByRole('button', { name: /hafta detayı/ }).click();
await page.waitForSelector('text=Şu an buradasınız', { timeout: 15000 });

// Icerik ayri bir sorgudan gelir; ekran acilir acilmaz hazir degil.
await page
  .getByText('Bebekte neler oluyor')
  .or(page.getByText('henüz hazır değil'))
  .first()
  .waitFor({ timeout: 15000 });

const reviewed = await page.getByText(/Tıbbi gözden geçirme:/).count();
const babySection = await page.getByText('Bebekte neler oluyor').count();
console.log(`  · hafta icerigi: ${babySection > 0 ? 'geldi' : 'YOK'} · onay satiri: ${reviewed > 0 ? 'var' : 'YOK'}`);
if (babySection === 0 || reviewed === 0) throw new Error('Yayindaki hafta icerigi ekranda gorunmedi');

await step('06-hafta-detayi');

// Takvim: onaylanmis tetkiklerden uretilen randevu pencereleri.
// expo-router onceki ekrani DOM'da tutar; gorunur olani hedeflemek gerekiyor.
await page.getByRole('button', { name: 'Ana ekrana dön' }).click();
const takvim = page.getByRole('button', { name: 'Takvim', exact: true });
await takvim.waitFor({ state: 'visible', timeout: 15000 });
await takvim.click();
await page.waitForSelector('text=Tetkikler ve randevular', { timeout: 15000 });
await page.getByText('Şeker yükleme testi').first().waitFor({ timeout: 15000 });
await step('07-takvim');

const rows = await page.getByText(/\d+–\d+\. hafta|\d+\. hafta$/).count();
console.log(`  · takvimde tetkik penceresi: ${rows}`);
if (rows === 0) throw new Error('Takvimde randevu penceresi gorunmedi');

// Hareket sayaci: 10 dokunusta oturum kapanir ve kuyruk sunucuya gider.
await page.getByRole('button', { name: 'Ana ekrana dön' }).click();
const kick = page.getByRole('button', { name: 'Hareket sayacı', exact: true });
await kick.waitFor({ state: 'visible', timeout: 15000 });
await kick.click();
await page.getByRole('button', { name: 'Saymaya başla' }).click();

const tapTarget = page.getByRole('button', { name: 'Hareket kaydet' });
await tapTarget.waitFor({ timeout: 10000 });
for (let i = 0; i < 10; i++) await tapTarget.click();

await page.getByText(/10 hareket .* tamamlandı/).waitFor({ timeout: 15000 });
console.log('  · hareket sayaci: 10/10 tamamlandi');
await step('08-hareket-sayaci');

await browser.close();

console.log(`\nKonsol hatasi: ${errors.length}`);
if (errors.length > 0) {
  console.log(errors.slice(0, 5).join('\n'));
  process.exit(1);
}
console.log('E2E gecti.');
