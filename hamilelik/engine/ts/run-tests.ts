/**
 * Ortak vektör dosyasına karşı TypeScript motorunu doğrular.
 *
 *   node --experimental-strip-types run-tests.ts          → okunabilir rapor
 *   node --experimental-strip-types run-tests.ts --json   → PHP çıktısıyla karşılaştırmak için ham sonuç
 */
import { readFileSync } from 'node:fs';
import { dirname, join } from 'node:path';
import { fileURLToPath } from 'node:url';
import { calculate, type GestationalAgeResult, type Method } from './gestationalAge.ts';

const here = dirname(fileURLToPath(import.meta.url));
const data = JSON.parse(readFileSync(join(here, '../../ga-test-vectors.json'), 'utf8'));
const jsonMode = process.argv.includes('--json');

const results: Record<string, GestationalAgeResult> = {};
const failures: string[] = [];

for (const vector of data.vectors) {
  const actual = calculate(vector.method, vector.input_date, vector.today, vector.cycle_length);
  results[vector.name] = actual;

  for (const [key, expected] of Object.entries(vector.expected)) {
    const got = actual[key as keyof GestationalAgeResult];
    if (got !== expected) {
      failures.push(`${vector.name}\n    ${key}: beklenen ${JSON.stringify(expected)}, gelen ${JSON.stringify(got)}`);
    }
  }
}

// Vektör dosyasının kapsamadığı davranışlar — PHP tarafıyla aynı sırada ve aynı adlarla.
const behaviour: Record<string, GestationalAgeResult | { error: true }> = {};

behaviour['USG duzeltmesi GA yi kaydirir'] = calculate('lmp', '2026-01-05', '2026-06-22', 28, [
  { measured_on: '2026-03-01', ga_days_at_measure: 70 },
]);

behaviour['En son duzeltme gecerli'] = calculate('lmp', '2026-01-05', '2026-06-22', 28, [
  { measured_on: '2026-03-01', ga_days_at_measure: 70 },
  { measured_on: '2026-04-10', ga_days_at_measure: 100 },
]);

behaviour['Termin asiminda ilerleme kilitli'] = calculate('lmp', '2026-01-05', '2026-11-01');
behaviour['44 hafta sonrasi tamamlandi mi sorusu'] = calculate('lmp', '2025-01-05', '2025-11-20');

const throws = (fn: () => unknown, label: string): void => {
  try {
    fn();
    failures.push(`${label}\n    hata bekleniyordu, gelmedi`);
  } catch {
    behaviour[label] = { error: true };
  }
};

throws(() => calculate('lmp', '2026-06-23', '2026-06-22'), 'Gelecek tarihli SAT reddedilir');
throws(() => calculate('lmp', '2026-02-30', '2026-06-22'), 'Takvimde olmayan tarih reddedilir');
throws(() => calculate('lmp', '2026-01-05', '2026-06-22', 60), 'Gecersiz dongu uzunlugu reddedilir');
throws(() => calculate('bilinmeyen' as Method, '2026-01-05', '2026-06-22'), 'Bilinmeyen yontem reddedilir');

if (jsonMode) {
  console.log(JSON.stringify({ vectors: results, behaviour }, null, 4));
  process.exit(failures.length === 0 ? 0 : 1);
}

console.log(`TypeScript motoru — ${data.vectors.length + Object.keys(behaviour).length} kontrol\n`);

for (const vector of data.vectors) {
  const r = results[vector.name];
  console.log(`  ${vector.name.padEnd(52)} ${r.display.padEnd(8)} TDT ${r.due_date}  T${r.trimester}`);
}

console.log('');
for (const [label, r] of Object.entries(behaviour)) {
  const detail = 'error' in r ? 'hata firlatildi (beklenen)' : `${r.display} · ilerleme ${r.progress}`;
  console.log(`  ${label.padEnd(52)} ${detail}`);
}

if (failures.length > 0) {
  console.error(`\n${failures.length} BASARISIZ:\n\n${failures.join('\n\n')}`);
  process.exit(1);
}

console.log('\nTumu gecti.');
