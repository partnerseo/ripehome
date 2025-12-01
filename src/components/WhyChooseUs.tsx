import { useNavigate } from 'react-router-dom';
import { Award, Leaf, Truck, Shield, Star, Package } from 'lucide-react';

const WhyChooseUs = () => {
  const navigate = useNavigate();

  const features = [
    {
      icon: 'star',
      title: 'Premium Kalite',
      description: '%100 doğal pamuk ve bambu liflerinden üretilmiş Oeko-Tex sertifikalı ürünler',
      link: '/hakkimizda',
      color: 'from-amber-500 to-yellow-600'
    },
    {
      icon: 'leaf',
      title: 'Doğal & Ekolojik',
      description: 'Zararlı kimyasal içermeyen, çevre dostu ve sürdürülebilir üretim',
      link: '/hakkimizda',
      color: 'from-green-500 to-emerald-600'
    },
    {
      icon: 'truck',
      title: 'Hızlı Kargo',
      description: 'Türkiye geneline hızlı teslimat, 500 TL üzeri ücretsiz kargo',
      link: '/kargo-teslimat',
      color: 'from-blue-500 to-indigo-600'
    },
    {
      icon: 'package',
      title: 'Toptan Avantajları',
      description: 'İşletmelere özel fiyatlar ve toplu sipariş kolaylığı',
      link: '/toptan-siparis',
      color: 'from-purple-500 to-pink-600'
    },
  ];

  const getIconComponent = (iconName: string) => {
    const iconMap: { [key: string]: any } = {
      'award': Award,
      'leaf': Leaf,
      'truck': Truck,
      'shield': Shield,
      'star': Star,
      'package': Package,
    };
    return iconMap[iconName.toLowerCase()] || Award;
  };

  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-gradient-to-b from-white to-gray-50">
      <div className="max-w-7xl mx-auto">
        {/* Başlık */}
        <div className="text-center mb-12 md:mb-20">
          <div className="inline-block">
            <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
              Avantajlarımız
            </span>
            <h2 className="text-4xl md:text-5xl lg:text-6xl font-light tracking-tight text-gray-900 mt-4 mb-6">
              Neden Ripe Home?
            </h2>
            <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
          </div>
          <p className="text-gray-600 max-w-2xl mx-auto mt-6 text-lg">
            Kalite, doğallık ve güvenin adresi
          </p>
        </div>

        {/* Özellik Kartları */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          {features.map((feature, index) => {
            const Icon = getIconComponent(feature.icon);
            return (
              <button
                key={index}
                onClick={() => navigate(feature.link)}
                className="group relative bg-white rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 cursor-pointer border border-gray-100 hover:border-gray-200 hover:-translate-y-2"
              >
                {/* Gradient Background on Hover */}
                <div className={`absolute inset-0 rounded-2xl bg-gradient-to-br ${feature.color} opacity-0 group-hover:opacity-5 transition-opacity duration-500`}></div>
                
                {/* İkon */}
                <div className="relative mb-6">
                  <div className={`inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br ${feature.color} text-white shadow-lg group-hover:scale-110 group-hover:rotate-6 transition-all duration-500`}>
                    <Icon className="w-8 h-8" strokeWidth={2} />
                  </div>
                </div>

                {/* İçerik */}
                <div className="relative text-center">
                  <h3 className="font-serif text-xl md:text-2xl text-gray-900 mb-3 group-hover:text-gray-700 transition-colors">
                    {feature.title}
                  </h3>
                  <p className="font-sans text-gray-600 text-sm leading-relaxed mb-4">
                    {feature.description}
                  </p>
                  
                  {/* Detay Butonu */}
                  <div className="inline-flex items-center gap-2 text-sm font-medium text-gray-900 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <span>Detaylar</span>
                    <svg className="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                    </svg>
                  </div>
                </div>

                {/* Köşe İşareti */}
                <div className="absolute top-4 right-4 w-8 h-8 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                  <svg className="w-full h-full text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 7l5 5m0 0l-5 5m5-5H6" />
                  </svg>
                </div>
              </button>
            );
          })}
        </div>

        {/* Alt Bilgi */}
        <div className="mt-16 text-center">
          <p className="text-gray-600 text-lg mb-6">
            Sorularınız mı var? Size yardımcı olmaktan mutluluk duyarız.
          </p>
          <button
            onClick={() => navigate('/iletisim')}
            className="inline-flex items-center gap-3 px-8 py-4 bg-gray-900 text-white rounded-xl font-medium hover:bg-gray-800 hover:shadow-xl transition-all duration-300"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>Bize Ulaşın</span>
            <svg className="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </div>
      </div>
    </section>
  );
};

export default WhyChooseUs;
