import { useTranslation } from 'react-i18next';
import { useLangNavigate } from '../hooks/useLang';
import { Truck, Clock, Package, MapPin, Phone, Mail, CheckCircle, AlertCircle } from 'lucide-react';

export default function Shipping() {
  const navigate = useLangNavigate();
  const { t } = useTranslation();

  const deliverySteps = [
    { icon: Package, title: t('shipping.steps.orderConfirmation'), description: t('shipping.steps.orderConfirmationDesc') },
    { icon: CheckCircle, title: t('shipping.steps.preparation'), description: t('shipping.steps.preparationDesc') },
    { icon: Truck, title: t('shipping.steps.shipped'), description: t('shipping.steps.shippedDesc') },
    { icon: MapPin, title: t('shipping.steps.delivery'), description: t('shipping.steps.deliveryDesc') },
  ];

  const shippingZones = [
    { region: t('shipping.zones.marmaraEge'), duration: t('shipping.zones.marmaraEgeTime'), icon: '🏙️' },
    { region: t('shipping.zones.icAnadolu'), duration: t('shipping.zones.icAnadoluTime'), icon: '🏔️' },
    { region: t('shipping.zones.karadeniz'), duration: t('shipping.zones.karadenizTime'), icon: '🌲' },
    { region: t('shipping.zones.international'), duration: t('shipping.zones.internationalTime'), icon: '🌍' },
  ];

  const faqs = t('shipping.faqs', { returnObjects: true }) as Array<{ q: string; a: string }>;

  return (
    <div className="min-h-screen bg-[#F8F6F3]">
      {/* Hero */}
      <section className="relative pt-20">
        <div className="bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 py-16 md:py-24">
          <div className="max-w-5xl mx-auto px-4 md:px-12 text-center">
            <div className="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <Truck className="w-8 h-8 text-white" />
            </div>
            <h1 className="font-serif text-4xl md:text-5xl lg:text-6xl text-white font-light mb-4">{t('shipping.title')}</h1>
            <p className="font-sans text-white/70 text-lg max-w-2xl mx-auto">{t('shipping.subtitle')}</p>
          </div>
        </div>
      </section>

      {/* Toptan Kargo Bilgisi */}
      <section className="relative -mt-10 z-10">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="bg-white rounded-2xl shadow-xl p-8 md:p-10 border border-neutral-100">
            <div className="grid md:grid-cols-2 gap-8">
              <div className="flex items-start gap-4">
                <div className="w-12 h-12 bg-[#8B7355]/10 rounded-xl flex items-center justify-center flex-shrink-0">
                  <Truck className="w-6 h-6 text-[#8B7355]" />
                </div>
                <div>
                  <h3 className="font-sans font-medium text-neutral-800 text-lg">{t('shipping.freeShipping')}</h3>
                  <p className="font-sans text-sm text-neutral-500 mt-1">{t('shipping.freeShippingNote')}</p>
                </div>
              </div>
              <div className="flex items-start gap-4">
                <div className="w-12 h-12 bg-[#8B7355]/10 rounded-xl flex items-center justify-center flex-shrink-0">
                  <Clock className="w-6 h-6 text-[#8B7355]" />
                </div>
                <div>
                  <h3 className="font-sans font-medium text-neutral-800 text-lg">{t('shipping.fastPreparation')}</h3>
                  <p className="font-sans text-[#8B7355] font-medium mt-1">{t('shipping.fastPreparationTime')}</p>
                  <p className="font-sans text-sm text-neutral-500 mt-1">{t('shipping.fastPreparationNote')}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Sipariş Süreci */}
      <section className="py-20 md:py-28">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="text-center mb-14">
            <p className="font-sans text-[#8B7355] text-sm tracking-[0.2em] uppercase mb-4">{t('shipping.stepByStep')}</p>
            <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">{t('shipping.orderProcess')}</h2>
          </div>
          <div className="grid md:grid-cols-4 gap-6">
            {deliverySteps.map((step, i) => (
              <div key={i} className="relative text-center group">
                {i < deliverySteps.length - 1 && (
                  <div className="hidden md:block absolute top-8 left-[60%] right-[-40%] h-px bg-[#E5DDD1]" />
                )}
                <div className="relative z-10 w-16 h-16 bg-white border-2 border-[#E5DDD1] rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:border-[#8B7355] group-hover:shadow-md transition-all duration-300">
                  <step.icon className="w-7 h-7 text-[#8B7355]" />
                </div>
                <div className="font-sans text-xs text-[#8B7355] font-medium mb-1">{t('common.stepLabel', { n: i + 1 })}</div>
                <h3 className="font-sans font-medium text-neutral-800 mb-2">{step.title}</h3>
                <p className="font-sans text-sm text-neutral-500 leading-relaxed">{step.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Teslimat Süreleri */}
      <section className="py-16 bg-white">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="text-center mb-14">
            <p className="font-sans text-[#8B7355] text-sm tracking-[0.2em] uppercase mb-4">{t('shipping.byRegion')}</p>
            <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">{t('shipping.deliveryTimes')}</h2>
          </div>
          <div className="grid md:grid-cols-2 gap-4">
            {shippingZones.map((zone, i) => (
              <div key={i} className="flex items-center gap-5 bg-[#F8F6F3] rounded-xl p-5 border border-neutral-100 hover:shadow-md hover:border-[#E5DDD1] transition-all duration-300">
                <div className="text-3xl">{zone.icon}</div>
                <div className="flex-1">
                  <h4 className="font-sans font-medium text-neutral-800">{zone.region}</h4>
                  <p className="font-sans text-sm text-neutral-500 mt-0.5">{t('shipping.estimatedDelivery')}</p>
                </div>
                <div className="font-sans font-medium text-[#8B7355] text-right">{zone.duration}</div>
              </div>
            ))}
          </div>
          <p className="font-sans text-xs text-neutral-400 text-center mt-6">{t('shipping.deliveryDisclaimer')}</p>
        </div>
      </section>

      {/* Sıkça Sorulan Sorular */}
      <section className="py-16 bg-white">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="text-center mb-10">
            <h2 className="font-serif text-2xl md:text-3xl text-neutral-800 font-light">{t('shipping.faqTitle')}</h2>
          </div>
          <div className="space-y-4">
            {Array.isArray(faqs) && faqs.map((faq, i) => (
              <div key={i} className="bg-[#F8F6F3] rounded-xl p-6 border border-neutral-100">
                <h4 className="font-sans font-medium text-neutral-800 mb-2">{faq.q}</h4>
                <p className="font-sans text-sm text-neutral-500 leading-relaxed">{faq.a}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Uyarı */}
      <section className="py-8">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="flex items-start gap-4 bg-amber-50 border border-amber-200 rounded-xl p-5">
            <AlertCircle className="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" />
            <div>
              <h4 className="font-sans font-medium text-amber-800 mb-1">{t('shipping.importantNotice')}</h4>
              <p className="font-sans text-sm text-amber-700 leading-relaxed">{t('shipping.importantNoticeText')}</p>
            </div>
          </div>
        </div>
      </section>

      {/* İletişim CTA */}
      <section className="py-16 bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="font-serif text-3xl md:text-4xl text-white font-light mb-4">{t('shipping.shippingQuestion')}</h2>
          <p className="font-sans text-white/70 text-lg mb-8 max-w-2xl mx-auto">{t('shipping.shippingQuestionText')}</p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button
              onClick={() => navigate('/iletisim')}
              className="inline-flex items-center justify-center gap-2 bg-white text-neutral-800 px-8 py-4 rounded-xl font-sans font-medium hover:bg-neutral-100 transition-colors"
            >
              <Mail className="w-4 h-4" />
              {t('common.getInTouch')}
            </button>
            <a
              href="tel:+905345730669"
              className="inline-flex items-center justify-center gap-2 border-2 border-white/30 text-white px-8 py-4 rounded-xl font-sans font-medium hover:bg-white/10 transition-colors"
            >
              <Phone className="w-4 h-4" />
              +90 534 573 06 69
            </a>
          </div>
        </div>
      </section>
    </div>
  );
}
