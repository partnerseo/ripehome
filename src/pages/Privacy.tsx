import { useTranslation } from 'react-i18next';

export default function Privacy() {
  const { t } = useTranslation();
  const collectedItems = t('privacy.collectedItems', { returnObjects: true }) as string[];
  const dataUsageItems = t('privacy.dataUsageItems', { returnObjects: true }) as string[];
  const rightsItems = t('privacy.rightsItems', { returnObjects: true }) as string[];

  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            {t('privacy.label')}
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            {t('privacy.title')}
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto space-y-8">
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <p className="text-gray-600 mb-6">{t('privacy.lastUpdated')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4">{t('privacy.dataProtection')}</h2>
            <p className="text-gray-700 leading-relaxed mb-6">{t('privacy.dataProtectionText')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('privacy.collectedInfo')}</h2>
            <p className="text-gray-700 leading-relaxed mb-4">{t('privacy.collectedInfoText')}</p>
            <ul className="space-y-2 ml-6 text-gray-700">
              {Array.isArray(collectedItems) && collectedItems.map((item, i) => (
                <li key={i} className="flex items-start gap-3">
                  <span className="text-gray-900 mt-1">•</span>
                  <span>{item}</span>
                </li>
              ))}
            </ul>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('privacy.dataUsage')}</h2>
            <p className="text-gray-700 leading-relaxed mb-4">{t('privacy.dataUsageText')}</p>
            <ul className="space-y-2 ml-6 text-gray-700">
              {Array.isArray(dataUsageItems) && dataUsageItems.map((item, i) => (
                <li key={i} className="flex items-start gap-3">
                  <span className="text-gray-900 mt-1">•</span>
                  <span>{item}</span>
                </li>
              ))}
            </ul>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('privacy.dataSecurity')}</h2>
            <p className="text-gray-700 leading-relaxed mb-6">{t('privacy.dataSecurityText')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('privacy.cookies')}</h2>
            <p className="text-gray-700 leading-relaxed mb-6">{t('privacy.cookiesText')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('privacy.rights')}</h2>
            <p className="text-gray-700 leading-relaxed mb-4">{t('privacy.rightsText')}</p>
            <ul className="space-y-2 ml-6 text-gray-700">
              {Array.isArray(rightsItems) && rightsItems.map((item, i) => (
                <li key={i} className="flex items-start gap-3">
                  <span className="text-gray-900 mt-1">•</span>
                  <span>{item}</span>
                </li>
              ))}
            </ul>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('privacy.contactTitle')}</h2>
            <p className="text-gray-700 leading-relaxed">
              {t('privacy.contactText')}
              <br />
              E-posta: <a href="mailto:info@ripehome.com.tr" className="text-gray-900 font-medium hover:underline">info@ripehome.com.tr</a>
              <br />
              {t('contact.phone')}: +90 534 573 06 69
              <br />
              {t('contact.address')}: Sevindik Mahallesi, 2291 Sokak, No: 7, Merkezefendi, Denizli
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}
