import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useLangPath } from '../hooks/useLang';
import { getFaq } from '../lib/api';

export default function FAQ() {
  const [openIndex, setOpenIndex] = useState<number | null>(0);
  const { t, i18n } = useTranslation();
  const lp = useLangPath();

  // Panelden (Setting) gelen SSS; boşsa i18n içeriğine düş (hiç bozulmaz)
  const i18nFaqs = t('faq.questions', { returnObjects: true }) as Array<{ q: string; a: string }>;
  const [faqs, setFaqs] = useState<Array<{ q: string; a: string }>>(
    Array.isArray(i18nFaqs) ? i18nFaqs : []
  );
  useEffect(() => {
    let alive = true;
    getFaq().then((list) => {
      if (alive && Array.isArray(list) && list.length > 0) setFaqs(list);
    });
    return () => { alive = false; };
  }, [i18n.language]);

  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            {t('faq.label')}
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            {t('faq.title')}
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto">
          <div className="space-y-4">
            {Array.isArray(faqs) && faqs.map((faq, index) => (
              <div
                key={index}
                className="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100"
              >
                <button
                  onClick={() => setOpenIndex(openIndex === index ? null : index)}
                  className="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 transition"
                >
                  <span className="text-lg font-medium text-gray-900 pr-4">
                    {faq.q}
                  </span>
                  <svg
                    className={`w-6 h-6 text-gray-500 transition-transform flex-shrink-0 ${
                      openIndex === index ? 'rotate-180' : ''
                    }`}
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M19 9l-7 7-7-7"
                    />
                  </svg>
                </button>
                {openIndex === index && (
                  <div className="px-8 pb-6">
                    <p className="text-gray-600 leading-relaxed">{faq.a}</p>
                  </div>
                )}
              </div>
            ))}
          </div>

          <div className="mt-12 text-center bg-gray-50 rounded-2xl p-8">
            <h3 className="text-xl font-medium text-gray-900 mb-4">
              {t('faq.notAnswered')}
            </h3>
            <p className="text-gray-600 mb-6">
              {t('faq.happyToHelp')}
            </p>
            <Link
              to={lp('/iletisim')}
              className="inline-flex items-center gap-2 bg-gray-900 text-white px-8 py-4 rounded-xl font-medium hover:bg-gray-800 transition"
            >
              {t('common.contactUs')}
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
