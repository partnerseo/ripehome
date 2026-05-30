import { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useLocation, useParams } from 'react-router-dom';
import { useLangNavigate } from '../hooks/useLang';
import { applyHead } from '../lib/seo';

const IS_PRERENDER =
  typeof navigator !== 'undefined' && /PrerenderBot/i.test(navigator.userAgent);

export default function NotFound() {
  const navigate = useLangNavigate();
  const { t } = useTranslation();
  const { pathname } = useLocation();
  const { lang } = useParams<{ lang: string }>();
  const [countdown, setCountdown] = useState(5);

  useEffect(() => {
    applyHead({ pathname, lang: lang || 'tr', notFound: true });
  }, [pathname, lang]);

  useEffect(() => {
    if (IS_PRERENDER) return;
    const timer = setInterval(() => {
      setCountdown(prev => prev - 1);
    }, 1000);

    const redirect = setTimeout(() => {
      navigate('/', { replace: true });
    }, 5000);

    return () => {
      clearInterval(timer);
      clearTimeout(redirect);
    };
  }, [navigate]);

  return (
    <div className="min-h-screen bg-[#F8F6F3] pt-20 flex items-center justify-center">
      <div className="text-center px-4">
        <h1 className="font-serif text-8xl md:text-9xl text-[#8B7355] font-light mb-4">404</h1>
        <h2 className="font-serif text-2xl md:text-3xl text-neutral-800 mb-4">{t('notFound.title')}</h2>
        <p className="font-sans text-neutral-500 mb-8 max-w-md mx-auto">
          {t('notFound.text')}
          <br />
          <span className="text-sm">{t('notFound.redirect', { count: countdown })}</span>
        </p>
        <button
          onClick={() => navigate('/', { replace: true })}
          className="px-8 py-3 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300 rounded-xl font-sans font-medium"
        >
          {t('common.goHome')}
        </button>
      </div>
    </div>
  );
}
