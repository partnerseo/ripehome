// Bağımsız SEO head yöneticisi (ripehome) — title, description, canonical,
// hreflang (5 dil + x-default), Open Graph/Twitter, prerender-status-code (404).
// routes.ts slug map'inden beslenir; tüm tag'ler data-seo ile idempotent.
import i18n from '../i18n';
import { switchLangPath, keyForSlug, type RouteKey } from './routes';

const SUPPORTED = ['tr', 'en', 'ar', 'ru', 'de'];
// Panelden (Ayarlar → Site SEO) gelir; SeoManager setSiteSeo ile doldurur.
const SITE = {
  name: 'Ripe Home',
  homeTitle: 'Ripe Home',
  description: 'Ripe Home — premium ev tekstili, bornoz ve kimono üretimi.',
};
export function setSiteSeo(s: { title?: string; description?: string }): void {
  if (s.title && s.title.trim()) { SITE.name = s.title.trim(); SITE.homeTitle = s.title.trim(); }
  if (s.description && s.description.trim()) SITE.description = s.description.trim();
}

const KEY_I18N: Partial<Record<RouteKey, string>> = {
  about: 'nav.about',
  contact: 'nav.contact',
  products: 'nav.products',
  wholesale: 'nav.wholesale',
  blog: 'blog.label',
  faq: 'faq.title',
  shipping: 'shipping.title',
  returns: 'returns.title',
  privacy: 'privacy.title',
  terms: 'terms.title',
  member: 'member.title',
};
const KEY_FALLBACK: Record<string, string> = {
  about: 'About', contact: 'Contact', products: 'Products', wholesale: 'Wholesale',
  blog: 'Blog', faq: 'FAQ', shipping: 'Shipping', returns: 'Returns',
  privacy: 'Privacy Policy', terms: 'Terms', member: 'My Account',
  category: 'Category', product: 'Product',
};

function tr(key?: string, fb?: string): string {
  if (!key) return fb || '';
  const v = i18n.t(key);
  return v && v !== key ? v : (fb || key);
}

function origin(): string {
  return typeof window !== 'undefined' ? window.location.origin : 'https://ripehome.com.tr';
}

function upsert(sel: string, make: () => HTMLElement): HTMLElement {
  let el = document.head.querySelector<HTMLElement>(sel);
  if (!el) { el = make(); document.head.appendChild(el); }
  return el;
}

function setMeta(attr: 'name' | 'property', key: string, content: string) {
  // Mevcut (index.html statik) tag'i ÜZERİNE yaz — duplicate <meta> olmasın.
  const el = upsert(`meta[${attr}="${key}"]`, () => {
    const m = document.createElement('meta');
    m.setAttribute(attr, key);
    return m;
  });
  el.setAttribute('data-seo', '1');
  el.setAttribute('content', content);
}

export interface SeoInput {
  pathname: string;
  lang: string;
  title?: string;
  description?: string;
  canonicalPath?: string;
  image?: string;
  notFound?: boolean;
}

export function applyHead(input: SeoInput): void {
  if (typeof document === 'undefined') return;
  const o = origin();
  const seg = input.pathname.split('/').filter(Boolean)[1] || '';
  const rkey = keyForSlug(seg);

  const label = input.title
    || (rkey ? tr(KEY_I18N[rkey], KEY_FALLBACK[rkey]) : '')
    || (input.notFound ? '404' : '');
  const title = label ? `${label} | ${SITE.name}` : SITE.homeTitle;
  const desc = input.description
    || tr('home.defaultSubtitle', SITE.description);

  document.title = title;
  setMeta('name', 'description', desc);

  const canonPath = input.canonicalPath || input.pathname;
  const canonUrl = o + canonPath;

  const link = upsert('link[rel="canonical"][data-seo]', () => {
    const l = document.createElement('link');
    l.setAttribute('rel', 'canonical');
    l.setAttribute('data-seo', '1');
    return l;
  }) as HTMLLinkElement;
  link.setAttribute('href', canonUrl);

  document.head.querySelectorAll('link[rel="alternate"][data-seo]').forEach((n) => n.remove());
  for (const L of SUPPORTED) {
    const a = document.createElement('link');
    a.setAttribute('rel', 'alternate');
    a.setAttribute('hreflang', L);
    a.setAttribute('href', o + switchLangPath(canonPath, L));
    a.setAttribute('data-seo', '1');
    document.head.appendChild(a);
  }
  const xd = document.createElement('link');
  xd.setAttribute('rel', 'alternate');
  xd.setAttribute('hreflang', 'x-default');
  xd.setAttribute('href', o + switchLangPath(canonPath, 'tr'));
  xd.setAttribute('data-seo', '1');
  document.head.appendChild(xd);

  setMeta('property', 'og:type', 'website');
  setMeta('property', 'og:site_name', SITE.name);
  setMeta('property', 'og:title', title);
  setMeta('property', 'og:description', desc);
  setMeta('property', 'og:url', canonUrl);
  setMeta('property', 'og:locale', input.lang);
  if (input.image) setMeta('property', 'og:image', input.image);
  setMeta('name', 'twitter:card', input.image ? 'summary_large_image' : 'summary');
  setMeta('name', 'twitter:title', title);
  setMeta('name', 'twitter:description', desc);
  if (input.image) setMeta('name', 'twitter:image', input.image);

  document.documentElement.setAttribute('lang', input.lang);

  const existing404 = document.head.querySelector('meta[name="prerender-status-code"]');
  if (input.notFound) {
    if (!existing404) {
      const m = document.createElement('meta');
      m.setAttribute('name', 'prerender-status-code');
      m.setAttribute('content', '404');
      m.setAttribute('data-seo-status', '1');
      document.head.appendChild(m);
    }
  } else if (existing404 && existing404.getAttribute('data-seo-status')) {
    existing404.remove();
  }
}

// Prerender hazır sinyali (bot doğru title/meta için bekler).
if (typeof window !== 'undefined' && (window as any).prerenderReady === undefined) {
  (window as any).prerenderReady = false;
}
export function setPrerenderReady(v: boolean): void {
  if (typeof window !== 'undefined') (window as any).prerenderReady = v;
}
