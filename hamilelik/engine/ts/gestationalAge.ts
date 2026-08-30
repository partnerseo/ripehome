/**
 * Gebelik yaşı motoru — engine/php/GestationalAge.php ikizi.
 *
 * İstemci çevrimdışıyken hesabı kendisi yapar, sunucu bildirimleri planlarken
 * aynı sonucu üretmek zorundadır. İki uygulama ga-test-vectors.json ile
 * doğrulanır; buradaki her davranış değişikliği PHP tarafına da taşınmalıdır.
 *
 * Tüm hesap takvim GÜNÜ üzerinden yapılır. Tarihler "YYYY-MM-DD" biçiminde,
 * kullanıcının yerel takvim günü olarak verilir. Ayrıştırma UTC gece yarısına
 * sabitlendiği için cihazın saat dilimi ve yaz saati geçişleri sonucu etkilemez.
 */

export type Method = 'lmp' | 'due_date' | 'conception' | 'ivf_d5' | 'ivf_d3';

export interface Redating {
  measured_on: string;
  ga_days_at_measure: number;
}

export interface GestationalAgeResult {
  lmp_date: string;
  due_date: string;
  ga_days: number;
  week: number;
  day: number;
  display: string;
  trimester: 1 | 2 | 3;
  days_left: number;
  progress: number;
  is_overdue: boolean;
  needs_completion_prompt: boolean;
}

/** Tam term: 40 hafta. */
export const TOTAL_DAYS = 280;

/** 1. trimesterin bitişi (14h0g bu günde başlar). */
const TRIMESTER_2_START = 98;

/** 2. trimesterin bitişi (28h0g bu günde başlar). */
const TRIMESTER_3_START = 196;

/** Bu eşiği aşınca kullanıcıya "gebelik tamamlandı mı?" sorulur (44 hafta). */
const COMPLETION_PROMPT_DAYS = 308;

const MS_PER_DAY = 86_400_000;
const DATE_PATTERN = /^\d{4}-\d{2}-\d{2}$/;

const parse = (date: string): number => {
  if (!DATE_PATTERN.test(date)) {
    throw new Error(`Tarih YYYY-MM-DD biçiminde olmalı: ${date}`);
  }

  const [year, month, day] = date.split('-').map(Number);
  const stamp = Date.UTC(year, month - 1, day);

  // Date.UTC taşan günleri sessizce kaydırır (2026-02-30 → 2 Mart); geri
  // biçimlendirip karşılaştırarak bunu yakalıyoruz.
  if (format(stamp) !== date) {
    throw new Error(`Geçersiz tarih: ${date}`);
  }

  return stamp;
};

const format = (stamp: number): string => new Date(stamp).toISOString().slice(0, 10);

const addDays = (stamp: number, days: number): number => stamp + days * MS_PER_DAY;

/**
 * İki takvim günü arasındaki fark. Her iki tarih de UTC gece yarısına
 * sabitlendiği için yaz saati geçişleri sonucu kaydırmaz.
 */
const diffDays = (from: number, to: number): number => Math.round((to - from) / MS_PER_DAY);

const trimester = (gaDays: number): 1 | 2 | 3 => {
  if (gaDays < TRIMESTER_2_START) return 1;
  if (gaDays < TRIMESTER_3_START) return 2;
  return 3;
};

/** Termin aşımında %100'de kilitlenir; ilerleme halkası geri sarmaz. */
const progress = (gaDays: number): number =>
  Math.round(Math.max(0, Math.min(1, gaDays / TOTAL_DAYS)) * 10_000) / 10_000;

/**
 * Giriş yönteminden etkin SAT'ı (son adet tarihi) türetir.
 *
 * Uzun döngü geç ovülasyon demektir: gebelik daha gençtir, TDT ileri gider.
 * Düzeltmeyi TDT'ye değil etkin SAT'a uyguluyoruz ki GA ve TDT aynı değerden türesin.
 */
export function effectiveLmp(method: Method, inputDate: string, cycleLength = 28): string {
  const stamp = parse(inputDate);

  if (cycleLength < 20 || cycleLength > 45) {
    throw new Error(`Döngü uzunluğu 20-45 gün aralığında olmalı: ${cycleLength}`);
  }

  switch (method) {
    case 'lmp':
      return format(addDays(stamp, cycleLength - 28));
    case 'due_date':
      return format(addDays(stamp, -TOTAL_DAYS));
    case 'conception':
      return format(addDays(stamp, -14));
    case 'ivf_d5':
      return format(addDays(stamp, -19));
    case 'ivf_d3':
      return format(addDays(stamp, -17));
    default: {
      const unreachable: never = method;
      throw new Error(`Bilinmeyen giriş yöntemi: ${String(unreachable)}`);
    }
  }
}

/**
 * En son USG düzeltmesinden etkin SAT'ı türetir. Orijinal SAT korunur;
 * düzeltme kaldırılınca hesap eski hâline döner.
 */
export function applyRedating(redatings: Redating[]): string {
  const sorted = [...redatings].sort((a, b) => a.measured_on.localeCompare(b.measured_on));
  const latest = sorted[sorted.length - 1];

  return format(addDays(parse(latest.measured_on), -latest.ga_days_at_measure));
}

/** Etkin SAT ve bugünden türetilen tüm alanlar. */
export function fromLmp(lmp: string, today: string): GestationalAgeResult {
  const lmpStamp = parse(lmp);
  const todayStamp = parse(today);
  const dueStamp = addDays(lmpStamp, TOTAL_DAYS);
  const gaDays = diffDays(lmpStamp, todayStamp);

  if (gaDays < 0) {
    throw new Error(`Son adet tarihi gelecekte olamaz: ${lmp}`);
  }

  return {
    lmp_date: format(lmpStamp),
    due_date: format(dueStamp),
    ga_days: gaDays,
    week: Math.floor(gaDays / 7),
    day: gaDays % 7,
    display: `${Math.floor(gaDays / 7)}h ${gaDays % 7}g`,
    trimester: trimester(gaDays),
    days_left: diffDays(todayStamp, dueStamp),
    progress: progress(gaDays),
    is_overdue: gaDays > TOTAL_DAYS,
    needs_completion_prompt: gaDays > COMPLETION_PROMPT_DAYS,
  };
}

/** Bir gebeliğin bugünkü durumunu hesaplar. */
export function calculate(
  method: Method,
  inputDate: string,
  today: string,
  cycleLength = 28,
  redatings: Redating[] = [],
): GestationalAgeResult {
  const lmp = redatings.length > 0 ? applyRedating(redatings) : effectiveLmp(method, inputDate, cycleLength);

  return fromLmp(lmp, today);
}
