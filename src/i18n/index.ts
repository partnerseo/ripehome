import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';
import LanguageDetector from 'i18next-browser-languagedetector';

import tr from './locales/tr.json';
import en from './locales/en.json';
import ar from './locales/ar.json';
import ru from './locales/ru.json';
import de from './locales/de.json';

export const languages = [
  { code: 'tr', name: 'Türkçe', flag: '🇹🇷', dir: 'ltr' },
  { code: 'en', name: 'English', flag: '🇬🇧', dir: 'ltr' },
  { code: 'ar', name: 'العربية', flag: '🇸🇦', dir: 'rtl' },
  { code: 'ru', name: 'Русский', flag: '🇷🇺', dir: 'ltr' },
  { code: 'de', name: 'Deutsch', flag: '🇩🇪', dir: 'ltr' },
] as const;

export type LanguageCode = (typeof languages)[number]['code'];

i18n
  .use(LanguageDetector)
  .use(initReactI18next)
  .init({
    resources: {
      tr: { translation: tr },
      en: { translation: en },
      ar: { translation: ar },
      ru: { translation: ru },
      de: { translation: de },
    },
    fallbackLng: 'tr',
    detection: {
      order: ['localStorage', 'navigator'],
      lookupLocalStorage: 'ripehome-lang',
      caches: ['localStorage'],
    },
    interpolation: {
      escapeValue: false,
    },
  });

export default i18n;
