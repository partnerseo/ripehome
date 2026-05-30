import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useLangNavigate } from '../hooks/useLang';
import { ArrowRight, Leaf, Users, Factory, Globe, Heart, Shield, Truck } from 'lucide-react';
import { getSettings } from '../lib/api';

const IMG = (name: string) => `https://ripehome.com.tr/storage/product-images/${name}.jpg`;
const DEFAULT_HERO = IMG('l9rU0WmDr0WnEYcH3ubwCCeddz71JPyEIkv2Ypg4');

export default function About() {
  const navigate = useLangNavigate();
  const { t } = useTranslation();
  const [heroImage, setHeroImage] = useState<string>(DEFAULT_HERO);

  useEffect(() => {
    getSettings().then(s => {
      if (s?.about_hero_image) setHeroImage(s.about_hero_image);
    });
  }, []);

  const stats = [
    { value: '15+', label: t('about.stats.experience') },
    { value: '500+', label: t('about.stats.wholesale') },
    { value: '50+', label: t('about.stats.export') },
    { value: '10.000+', label: t('about.stats.variety') },
  ];

  const values = [
    { icon: Leaf,    title: t('about.values.naturalRaw'),      description: t('about.values.naturalRawDesc') },
    { icon: Shield,  title: t('about.values.reliableSupply'),  description: t('about.values.reliableSupplyDesc') },
    { icon: Users,   title: t('about.values.customerFocused'), description: t('about.values.customerFocusedDesc') },
    { icon: Factory, title: t('about.values.ownProduction'),   description: t('about.values.ownProductionDesc') },
    { icon: Globe,   title: t('about.values.globalVision'),    description: t('about.values.globalVisionDesc') },
  ];

  const timeline = [
    { year: '2009', title: t('about.timeline.founding'),             description: t('about.timeline.foundingDesc') },
    { year: '2013', title: t('about.timeline.firstExport'),          description: t('about.timeline.firstExportDesc') },
    { year: '2016', title: t('about.timeline.factoryInvestment'),    description: t('about.timeline.factoryInvestmentDesc') },
    { year: '2019', title: t('about.timeline.certification'),        description: t('about.timeline.certificationDesc') },
    { year: '2022', title: t('about.timeline.brandTransformation'),  description: t('about.timeline.brandTransformationDesc') },
    { year: '2024', title: t('about.timeline.digitalTransformation'),description: t('about.timeline.digitalTransformationDesc') },
  ];

  const wholesaleItems = [
    { icon: Truck,   text: t('about.wholesaleItems.delivery') },
    { icon: Users,   text: t('about.wholesaleItems.pricing') },
    { icon: Factory, text: t('about.wholesaleItems.production') },
    { icon: Shield,  text: t('about.wholesaleItems.certified') },
  ];

  return (
    <div className="min-h-screen bg-[#F8F6F3]">

      {/* ── HERO ── */}
      <section className="relative pt-20">
        <div className="relative h-[480px] md:h-[580px] overflow-hidden">
          <img
            src={heroImage}
            alt="Ripe Home"
            className="w-full h-full object-cover object-center"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-black/75 via-black/35 to-black/10" />
          <div className="absolute inset-0 flex items-end justify-center pb-16 px-4">
            <div className="text-center">
              <p className="font-sans text-white/70 text-xs tracking-[0.35em] uppercase mb-3">
                {t('about.fromDenizli')}
              </p>
              <h1
                className="font-serif text-4xl md:text-6xl text-white font-light leading-tight mb-4"
                dangerouslySetInnerHTML={{ __html: t('about.heroTitle').replace('\n', '<br />') }}
              />
              <p className="font-sans text-white/80 text-base md:text-lg max-w-xl mx-auto leading-relaxed">
                {t('about.heroSubtitle')}
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* ── STATS ── */}
      <section className="relative -mt-10 z-10 px-4">
        <div className="max-w-4xl mx-auto">
          <div className="bg-white rounded-2xl shadow-xl grid grid-cols-2 md:grid-cols-4 divide-x divide-neutral-100">
            {stats.map((s, i) => (
              <div key={i} className="text-center py-7 px-4">
                <div className="font-serif text-3xl text-[#8B7355] font-light">{s.value}</div>
                <div className="font-sans text-xs text-neutral-500 mt-1 leading-snug">{s.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* ── HİKAYEMİZ ── */}
      <section className="py-24">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">
          <div className="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {/* Metin */}
            <div>
              <p className="font-sans text-[#8B7355] text-xs tracking-[0.25em] uppercase mb-3">
                {t('about.ourStory')}
              </p>
              <h2
                className="font-serif text-3xl md:text-4xl text-neutral-800 font-light leading-tight mb-6"
                dangerouslySetInnerHTML={{ __html: t('about.storyTitle').replace('\n', '<br />') }}
              />
              <div className="space-y-4 font-sans text-neutral-600 leading-relaxed text-sm md:text-base">
                <p dangerouslySetInnerHTML={{ __html: t('about.storyP1') }} />
                <p>{t('about.storyP2')}</p>
                <p>{t('about.storyP3')}</p>
              </div>
            </div>

            {/* 3'lü resim kolajı */}
            <div className="grid grid-cols-2 gap-3 h-[480px]">
              <div className="row-span-2 rounded-2xl overflow-hidden shadow-md">
                <img
                  src={IMG('Ipc6avFDsz1o2vEN2vD1QgKhaUufMJ6D22hiCLYV')}
                  alt="Kimono"
                  className="w-full h-full object-cover"
                />
              </div>
              <div className="rounded-2xl overflow-hidden shadow-md">
                <img
                  src={IMG('fbQLZ5lA50MX3ejtamNYAOOCPeZXEq5M2Y5B0oNp')}
                  alt="Kimono"
                  className="w-full h-full object-cover object-top"
                />
              </div>
              <div className="rounded-2xl overflow-hidden shadow-md">
                <img
                  src={IMG('1ZZqkcAJrW5YI1oBS0LGAgO46A6HiBF4E48vQIzO')}
                  alt="Kimono"
                  className="w-full h-full object-cover object-top"
                />
              </div>
            </div>

          </div>
        </div>
      </section>

      {/* ── MİSYON & VİZYON ── */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">
          <div className="grid md:grid-cols-2 gap-6">
            <div className="bg-gradient-to-br from-[#F8F6F3] to-white rounded-2xl p-8 border border-neutral-100">
              <div className="w-11 h-11 bg-[#8B7355]/10 rounded-xl flex items-center justify-center mb-5">
                <Heart className="w-5 h-5 text-[#8B7355]" />
              </div>
              <h3 className="font-serif text-xl text-neutral-800 mb-3">{t('about.mission')}</h3>
              <p className="font-sans text-neutral-600 leading-relaxed text-sm">{t('about.missionText')}</p>
            </div>
            <div className="bg-gradient-to-br from-[#F8F6F3] to-white rounded-2xl p-8 border border-neutral-100">
              <div className="w-11 h-11 bg-[#8B7355]/10 rounded-xl flex items-center justify-center mb-5">
                <Globe className="w-5 h-5 text-[#8B7355]" />
              </div>
              <h3 className="font-serif text-xl text-neutral-800 mb-3">{t('about.vision')}</h3>
              <p className="font-sans text-neutral-600 leading-relaxed text-sm">{t('about.visionText')}</p>
            </div>
          </div>
        </div>
      </section>

      {/* ── DEĞERLERİMİZ ── */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">
          <div className="text-center mb-12">
            <p className="font-sans text-[#8B7355] text-xs tracking-[0.25em] uppercase mb-3">{t('about.whyUs')}</p>
            <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">{t('about.valuesTitle')}</h2>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            {values.map((item, i) => (
              <div key={i} className="bg-white rounded-2xl p-6 border border-neutral-100 hover:shadow-md hover:border-[#E5DDD1] transition-all duration-300 group">
                <div className="w-10 h-10 bg-[#F8F6F3] rounded-xl flex items-center justify-center mb-4 group-hover:bg-[#8B7355]/10 transition-colors">
                  <item.icon className="w-5 h-5 text-[#8B7355]" />
                </div>
                <h3 className="font-serif text-lg text-neutral-800 mb-2">{item.title}</h3>
                <p className="font-sans text-neutral-500 text-sm leading-relaxed">{item.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>


      {/* ── TOPTAN HİZMETLER ── */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="rounded-2xl overflow-hidden shadow-lg">
              <img
                src={IMG('GMbMYx46yTQPTX7DPmH4vOyVguPLbW1ouzUJWTuh')}
                alt="Ripe Home Kimono"
                className="w-full h-[440px] object-cover object-top"
              />
            </div>
            <div>
              <p className="font-sans text-[#8B7355] text-xs tracking-[0.25em] uppercase mb-3">{t('about.wholesaleServices')}</p>
              <h2
                className="font-serif text-3xl md:text-4xl text-neutral-800 font-light leading-tight mb-6"
                dangerouslySetInnerHTML={{ __html: t('about.wholesaleTitle').replace('\n', '<br />') }}
              />
              <div className="space-y-4">
                {wholesaleItems.map((item, i) => (
                  <div key={i} className="flex items-start gap-4">
                    <div className="w-9 h-9 bg-[#8B7355]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                      <item.icon className="w-4 h-4 text-[#8B7355]" />
                    </div>
                    <p className="font-sans text-neutral-600 leading-relaxed text-sm">{item.text}</p>
                  </div>
                ))}
              </div>
              <button
                onClick={() => navigate('/toptan-siparis')}
                className="mt-8 inline-flex items-center gap-2 bg-[#8B7355] text-white px-7 py-3.5 rounded-xl font-sans font-medium hover:bg-[#6F5C46] transition-colors text-sm"
              >
                {t('about.placeWholesaleOrder')}
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* ── İLETİŞİM BİLGİLERİ ── */}
      <section className="py-14 bg-white">
        <div className="max-w-4xl mx-auto px-4 md:px-12">
          <div className="grid md:grid-cols-3 gap-6 text-center">
            {[
              {
                icon: <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>,
                icon2: <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>,
                title: t('about.ourAddress'),
                content: <p className="font-sans text-sm text-neutral-500 leading-relaxed">Sevindik Mah., 2291 Sok. No:7<br />Merkezefendi, Denizli</p>,
              },
              {
                icon: <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>,
                title: t('about.email'),
                content: <a href="mailto:info@ripehome.com.tr" className="font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors">info@ripehome.com.tr</a>,
              },
              {
                icon: <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>,
                title: t('about.phone'),
                content: <a href="tel:+905345730669" className="font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors">+90 534 573 06 69</a>,
              },
            ].map((item, i) => (
              <div key={i}>
                <div className="w-11 h-11 bg-[#F8F6F3] rounded-xl flex items-center justify-center mx-auto mb-3">
                  <svg className="w-5 h-5 text-[#8B7355]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {item.icon}
                    {item.icon2}
                  </svg>
                </div>
                <h4 className="font-sans font-medium text-neutral-800 mb-1.5 text-sm">{item.title}</h4>
                {item.content}
              </div>
            ))}
          </div>
        </div>
      </section>

    </div>
  );
}
