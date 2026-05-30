import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useLangPath } from '../hooks/useLang';

export default function Terms() {
  const { t } = useTranslation();
  const lp = useLangPath();
  const orderSalesItems = t('terms.orderSalesItems', { returnObjects: true }) as string[];
  const paymentItems = t('terms.paymentItems', { returnObjects: true }) as string[];
  const disclaimerItems = t('terms.disclaimerItems', { returnObjects: true }) as string[];

  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            {t('terms.label')}
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            {t('terms.title')}
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto space-y-8">
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <p className="text-gray-600 mb-6">{t('terms.lastUpdated')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4">{t('terms.general')}</h2>
            <p className="text-gray-700 leading-relaxed mb-6">{t('terms.generalText')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('terms.orderSales')}</h2>
            <p className="text-gray-700 leading-relaxed mb-4">{t('terms.orderSalesText')}</p>
            <ul className="space-y-2 ml-6 text-gray-700">
              {Array.isArray(orderSalesItems) && orderSalesItems.map((item, i) => (
                <li key={i} className="flex items-start gap-3">
                  <span className="text-gray-900 mt-1">•</span>
                  <span>{item}</span>
                </li>
              ))}
            </ul>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('terms.payment')}</h2>
            <p className="text-gray-700 leading-relaxed mb-4">{t('terms.paymentText')}</p>
            <ul className="space-y-2 ml-6 text-gray-700">
              {Array.isArray(paymentItems) && paymentItems.map((item, i) => (
                <li key={i} className="flex items-start gap-3">
                  <span className="text-gray-900 mt-1">•</span>
                  <span>{item}</span>
                </li>
              ))}
            </ul>
            <p className="text-gray-700 leading-relaxed mt-4">{t('terms.paymentSecurity')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('terms.deliveryShipping')}</h2>
            <p className="text-gray-700 leading-relaxed mb-6">
              {t('terms.deliveryShippingText')}{' '}
              <Link to={lp('/kargo-teslimat')} className="text-gray-900 font-medium hover:underline mx-1">
                {t('terms.deliveryShippingLink')}
              </Link>{' '}
              {t('terms.deliveryShippingAfter')}
            </p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('terms.intellectualProperty')}</h2>
            <p className="text-gray-700 leading-relaxed mb-6">{t('terms.intellectualPropertyText')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('terms.disclaimer')}</h2>
            <p className="text-gray-700 leading-relaxed mb-4">{t('terms.disclaimerText')}</p>
            <ul className="space-y-2 ml-6 text-gray-700">
              {Array.isArray(disclaimerItems) && disclaimerItems.map((item, i) => (
                <li key={i} className="flex items-start gap-3">
                  <span className="text-gray-900 mt-1">•</span>
                  <span>{item}</span>
                </li>
              ))}
            </ul>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('terms.disputes')}</h2>
            <p className="text-gray-700 leading-relaxed mb-6">{t('terms.disputesText')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('terms.changes')}</h2>
            <p className="text-gray-700 leading-relaxed mb-6">{t('terms.changesText')}</p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">{t('terms.contactTitle')}</h2>
            <p className="text-gray-700 leading-relaxed">
              {t('terms.contactText')}
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
