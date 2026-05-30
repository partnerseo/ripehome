import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useLangPath } from '../hooks/useLang';

export default function Returns() {
  const { t } = useTranslation();
  const lp = useLangPath();
  const conditionItems = t('returns.conditionItems', { returnObjects: true }) as string[];
  const processSteps = t('returns.processSteps', { returnObjects: true }) as Array<{ title: string; text: string }>;
  const wholesaleReturnItems = t('returns.wholesaleReturnItems', { returnObjects: true }) as string[];

  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            {t('returns.label')}
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            {t('returns.title')}
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto space-y-8">
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">{t('returns.conditions')}</h2>
            <div className="space-y-4 text-gray-700 leading-relaxed">
              <p dangerouslySetInnerHTML={{ __html: t('returns.conditionsIntro') }} />
              <p className="font-medium text-gray-900 mt-6 mb-3">{t('returns.conditionsRequired')}</p>
              <ul className="space-y-2 ml-6">
                {Array.isArray(conditionItems) && conditionItems.map((item, i) => (
                  <li key={i} className="flex items-start gap-3">
                    <svg className="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{item}</span>
                  </li>
                ))}
              </ul>
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">{t('returns.process')}</h2>
            <div className="space-y-6">
              {Array.isArray(processSteps) && processSteps.map((step, i) => (
                <div key={i} className="flex gap-4">
                  <div className="flex-shrink-0 w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold">
                    {i + 1}
                  </div>
                  <div>
                    <h3 className="font-medium text-gray-900 mb-2">{step.title}</h3>
                    <p className="text-gray-700">{step.text}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">{t('returns.exchange')}</h2>
            <p className="text-gray-700 leading-relaxed mb-4">{t('returns.exchangeP1')}</p>
            <p className="text-gray-700 leading-relaxed">{t('returns.exchangeP2')}</p>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">{t('returns.wholesaleReturns')}</h2>
            <p className="text-gray-700 leading-relaxed mb-4">{t('returns.wholesaleReturnsIntro')}</p>
            <ul className="space-y-2 ml-6 text-gray-700">
              {Array.isArray(wholesaleReturnItems) && wholesaleReturnItems.map((item, i) => (
                <li key={i} className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-gray-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clipRule="evenodd" />
                  </svg>
                  <span>{item}</span>
                </li>
              ))}
            </ul>
          </div>

          <div className="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 border border-gray-200">
            <div className="text-center">
              <h3 className="text-xl font-medium text-gray-900 mb-4">{t('returns.needHelp')}</h3>
              <p className="text-gray-700 mb-6">{t('returns.happyToHelp')}</p>
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
    </div>
  );
}
