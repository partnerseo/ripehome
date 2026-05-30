// Merkezi rota / slug yönetimi (ripehome).
// HER dil kendi lokalize slug'ını alır (tr/en/ar/ru/de). Eski TR slug'lar
// nginx 301 ile yeni slug'a yönlenir. Tüm linkler useLangPath/useLangNavigate
// üzerinden geçtiği için çağrı yerleri değişmeden lokalize olur.

const LANGS = ['tr', 'en', 'ar', 'ru', 'de'] as const;
export type Lang = (typeof LANGS)[number];
export const SUPPORTED_LANGS: string[] = [...LANGS];

type SlugSet = { tr: string; en: string; ar: string; ru: string; de: string };

// route key → her dilde slug. ar/ru temiz ASCII transliterasyon, de Almanca.
export const ROUTE_SLUGS = {
  about:        { tr: 'hakkimizda',           en: 'about',             ar: 'hawlana',          ru: 'o-nas',                 de: 'ueber-uns' },
  contact:      { tr: 'iletisim',             en: 'contact',           ar: 'tawasul',          ru: 'kontakty',              de: 'kontakt' },
  products:     { tr: 'urunler',              en: 'products',          ar: 'muntajat',         ru: 'produktsiya',           de: 'produkte' },
  category:     { tr: 'kategori',             en: 'category',          ar: 'fia',              ru: 'kategoriya',            de: 'kategorie' },
  product:      { tr: 'urun',                 en: 'product',           ar: 'muntaj',           ru: 'tovar',                 de: 'produkt' },
  wholesale:    { tr: 'toptan-siparis',       en: 'wholesale',         ar: 'talab-jumla',      ru: 'optovyy-zakaz',         de: 'grosshandel' },
  faq:          { tr: 'sss',                  en: 'faq',               ar: 'asila',            ru: 'voprosy',               de: 'haeufige-fragen' },
  shipping:     { tr: 'kargo-teslimat',       en: 'shipping-delivery', ar: 'tawsil',           ru: 'dostavka',              de: 'versand' },
  returns:      { tr: 'iade-degisim',         en: 'returns-exchange',  ar: 'istibdal',         ru: 'vozvrat-obmen',         de: 'ruckgabe' },
  privacy:      { tr: 'gizlilik-politikasi',  en: 'privacy-policy',    ar: 'khososiya',        ru: 'konfidentsialnost',     de: 'datenschutz' },
  terms:        { tr: 'kullanim-kosullari',   en: 'terms-of-service',  ar: 'shurut-istikhdam', ru: 'usloviya-ispolzovaniya',de: 'nutzungsbedingungen' },
  blog:         { tr: 'blog',                 en: 'blog',              ar: 'mudawana',         ru: 'blog',                  de: 'blog' },
  member:       { tr: 'uye-paneli',           en: 'my-account',        ar: 'hisabi',           ru: 'moy-akkaunt',           de: 'mein-konto' },
} as const;

export type RouteKey = keyof typeof ROUTE_SLUGS;

function pick(set: SlugSet, lang: string): string {
  return (set as Record<string, string>)[lang] ?? set.tr;
}

function allSlugs(set: SlugSet): string[] {
  return Array.from(new Set([set.tr, set.en, set.ar, set.ru, set.de]));
}

/** route key → ilgili dildeki slug */
export function slugFor(key: RouteKey, lang: string): string {
  return pick(ROUTE_SLUGS[key], lang || 'tr');
}

// TR slug → route key (eski TR path'leri çözmek için)
const TR_TO_KEY: Record<string, RouteKey> = Object.fromEntries(
  (Object.keys(ROUTE_SLUGS) as RouteKey[]).map((k) => [ROUTE_SLUGS[k].tr, k])
) as Record<string, RouteKey>;

/**
 * Çağrı yerleri TR path verir (lp('/hakkimizda'), lp('/kategori/foo')).
 * İlk segmenti dile göre lokalize eder, geri kalan (dinamik slug) korunur.
 * lang prefix'i EKLEMEZ — useLang ekler.
 */
export function localizeTrPath(trPath: string, lang: string): string {
  if (!trPath || trPath === '/') return '/';
  if (/^https?:\/\//.test(trPath)) return trPath;
  const clean = trPath.replace(/^\/+/, '');
  const [seg0, ...rest] = clean.split('/');
  const key = TR_TO_KEY[seg0];
  if (!key) return '/' + clean; // bilinmeyen → olduğu gibi
  const localized = slugFor(key, lang);
  return '/' + [localized, ...rest].join('/');
}

// Herhangi bir dildeki slug → route key (PageTitle vb. için)
const SLUG_TO_KEY: Record<string, RouteKey> = (() => {
  const m: Record<string, RouteKey> = {};
  for (const k of Object.keys(ROUTE_SLUGS) as RouteKey[]) {
    for (const s of allSlugs(ROUTE_SLUGS[k])) m[s] = k;
  }
  return m;
})();

export function keyForSlug(seg: string): RouteKey | undefined {
  return SLUG_TO_KEY[seg];
}

/** App.tsx route tablosu: bir key için tüm dil slug varyantları (eşsiz). */
export function patternsFor(key: RouteKey, sub?: string): string[] {
  return allSlugs(ROUTE_SLUGS[key]).map((s) => (sub ? `${s}/${sub}` : s));
}

/** Dil değiştirirken mevcut yolu yeni dilin slug'larıyla yeniden kur. */
export function switchLangPath(pathname: string, newLang: string): string {
  const parts = pathname.split('/').filter(Boolean); // [lang, seg0, ...rest]
  const rest = parts.slice(1);
  if (rest.length === 0) return `/${newLang}`;
  const seg0 = rest[0];
  for (const key of Object.keys(ROUTE_SLUGS) as RouteKey[]) {
    if (!allSlugs(ROUTE_SLUGS[key]).includes(seg0)) continue;
    const localized = slugFor(key, newLang);
    return '/' + [newLang, localized, ...rest.slice(1)].join('/');
  }
  return `/${newLang}/${rest.join('/')}`;
}
