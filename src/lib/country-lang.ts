import type { SupportedLang } from '../hooks/useLang';

// Ülke kodu → Dil kodu eşlemesi
const COUNTRY_TO_LANG: Record<string, SupportedLang> = {
  // Türkçe
  TR: 'tr',

  // Almanca
  DE: 'de', AT: 'de', CH: 'de',

  // Rusça
  RU: 'ru', UA: 'ru', BY: 'ru', KZ: 'ru', AZ: 'ru', UZ: 'ru', GE: 'ru',

  // Arapça
  SA: 'ar', AE: 'ar', EG: 'ar', KW: 'ar', QA: 'ar', BH: 'ar',
  OM: 'ar', JO: 'ar', IQ: 'ar', SY: 'ar', LB: 'ar', MA: 'ar',

  // İngilizce (varsayılan diğerleri)
  US: 'en', GB: 'en', AU: 'en', CA: 'en', NZ: 'en',
};

export function countryToLang(countryCode: string): SupportedLang {
  return COUNTRY_TO_LANG[countryCode?.toUpperCase()] ?? 'en';
}
