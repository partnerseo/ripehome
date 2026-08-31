/**
 * Acil değerlendirme gerektirebilecek belirtiler.
 *
 * Bu liste bilinçli olarak uygulamanın içinde, panelde değil. İki sebebi var:
 *
 * 1. Her zaman erişilebilir olmalı. Panelden yönetilseydi bir kayıt yanlışlıkla
 *    taslağa düştüğünde liste boşalırdı — ve boş bir acil belirti listesi,
 *    olmamasından daha kötüdür.
 * 2. Çevrimdışı çalışmalı. Kanama başlayan biri bağlantı beklemez.
 *
 * Metin hekim gözden geçirmesinden geçer, ama sürüm yayınıyla birlikte:
 * değişiklik kod incelemesinden geçer, panelden anlık düzenlenmez.
 */

export interface RedFlag {
  code: string;
  title: string;
  detail: string;
}

export const RED_FLAGS: RedFlag[] = [
  {
    code: 'kanama',
    title: 'Vajinal kanama',
    detail: 'Miktarı az da olsa kanama değerlendirilmelidir.',
  },
  {
    code: 'siddetli_bas_agrisi',
    title: 'Şiddetli veya geçmeyen baş ağrısı',
    detail: 'Özellikle görme değişiklikleriyle birlikteyse.',
  },
  {
    code: 'gorme_bulaniklig',
    title: 'Görme bulanıklığı veya ışık çakmaları',
    detail: 'Preeklampsi belirtisi olabilir.',
  },
  {
    code: 'sag_ust_karin_agrisi',
    title: 'Sağ üst karın ağrısı',
    detail: 'Kaburgaların altında, sağ tarafta ağrı.',
  },
  {
    code: 'ani_odem',
    title: 'Ani el, yüz veya göz çevresi şişmesi',
    detail: 'Birkaç saat içinde başlayan belirgin şişlik.',
  },
  {
    code: 'hareket_azalmasi',
    title: 'Bebek hareketlerinde azalma',
    detail: '28. haftadan sonra hareketlerde belirgin azalma.',
  },
  {
    code: 'su_gelmesi',
    title: 'Su gelmesi',
    detail: 'Ani veya sızıntı şeklinde sıvı gelmesi.',
  },
  {
    code: 'ates',
    title: '38 °C üzeri ateş',
    detail: 'Düşmeyen yüksek ateş.',
  },
  {
    code: 'siddetli_karin_agrisi',
    title: 'Şiddetli, geçmeyen karın ağrısı',
    detail: '37. haftadan önce düzenli kasılmalar da dahil.',
  },
];

/** Günlükte işaretlenebilen, acil olmayan belirtiler. */
export const COMMON_SYMPTOMS: RedFlag[] = [
  { code: 'bulanti', title: 'Bulantı', detail: '' },
  { code: 'yorgunluk', title: 'Yorgunluk', detail: '' },
  { code: 'sirt_agrisi', title: 'Sırt ağrısı', detail: '' },
  { code: 'mide_yanmasi', title: 'Mide yanması', detail: '' },
  { code: 'uykusuzluk', title: 'Uykusuzluk', detail: '' },
  { code: 'bacak_krampi', title: 'Bacak krampı', detail: '' },
];

const URGENT_CODES = new Set(RED_FLAGS.map((flag) => flag.code));

export function isUrgent(code: string): boolean {
  return URGENT_CODES.has(code);
}

export function urgentAmong(codes: string[]): string[] {
  return codes.filter(isUrgent);
}
