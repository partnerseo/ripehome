import { useParams, useNavigate, type NavigateOptions } from 'react-router-dom';
import { localizeTrPath, SUPPORTED_LANGS as RT_LANGS } from '../lib/routes';

export const SUPPORTED_LANGS = ['tr', 'en', 'ar', 'ru', 'de'] as const;
export type SupportedLang = (typeof SUPPORTED_LANGS)[number];

export function useLang(): string {
  const { lang } = useParams<{ lang: string }>();
  return lang || 'tr';
}

// Zaten dil prefix'li / mutlak URL mü?
function isAbsoluteOrLangPrefixed(p: string): boolean {
  return p.startsWith('http') || RT_LANGS.some((l) => p.startsWith(`/${l}/`) || p === `/${l}`);
}

/** navigate('/hakkimizda') → navigate('/en/about') (aktif dile göre lokalize) */
export function useLangNavigate() {
  const lang = useLang();
  const navigate = useNavigate();
  return (path: string | number, options?: NavigateOptions) => {
    if (typeof path === 'number') { navigate(path); return; }
    if (isAbsoluteOrLangPrefixed(path)) { navigate(path, options); return; }
    const local = localizeTrPath(path, lang);
    const prefixed = local === '/' ? `/${lang}` : `/${lang}${local}`;
    navigate(prefixed, options);
  };
}

/** TR path'i aktif dilin slug'ına çevirip lang-prefix ekler. */
export function useLangPath() {
  const lang = useLang();
  return (path: string) => {
    if (isAbsoluteOrLangPrefixed(path)) return path;
    const local = localizeTrPath(path, lang);
    return local === '/' ? `/${lang}` : `/${lang}${local}`;
  };
}
