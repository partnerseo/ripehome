import { useState, useEffect } from 'react';
import { Award, Leaf, Truck, Sparkles } from 'lucide-react';
import { getFeaturedSections } from '../lib/api';
import type { FeaturedSection } from '../types/api';

const WhyChooseUs = () => {
  const [featuredSections, setFeaturedSections] = useState<FeaturedSection[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchFeaturedSections() {
      try {
        const data = await getFeaturedSections();
        setFeaturedSections(data);
      } catch (error) {
        console.error('Featured sections yüklenemedi:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchFeaturedSections();
  }, []);

  // Fallback data if API fails
  const defaultFeatures = [
    {
      icon: '🏆',
      title: 'Premium Kalite',
      description: 'En kaliteli doğal malzemelerden üretilmiş ürünler',
    },
    {
      icon: '🌿',
      title: 'Doğal Malzemeler',
      description: 'Pamuk, keten ve kenevir gibi organik lifler',
    },
    {
      icon: '🚚',
      title: 'Hızlı Teslimat',
      description: 'Türkiye geneline özenli ve hızlı kargo',
    },
    {
      icon: '✨',
      title: 'Çevre Dostu',
      description: 'Sürdürülebilir üretim ve bilinçli tüketim',
    },
  ];

  const getIconComponent = (iconName: string | undefined) => {
    if (!iconName) return Award;
    
    const iconMap: { [key: string]: any } = {
      'award': Award,
      'leaf': Leaf,
      'truck': Truck,
      'sparkles': Sparkles,
    };
    
    return iconMap[iconName.toLowerCase()] || Award;
  };

  if (loading) {
    return (
      <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-[#F8F6F3]">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-8 md:mb-16">
            <div className="animate-pulse">
              <div className="h-12 bg-gray-200 rounded w-96 mx-auto mb-4"></div>
              <div className="h-6 bg-gray-200 rounded w-64 mx-auto"></div>
            </div>
          </div>
          <div className="grid md:grid-cols-4 gap-12">
            {[...Array(4)].map((_, i) => (
              <div key={i} className="text-center animate-pulse">
                <div className="w-20 h-20 bg-gray-200 rounded-full mx-auto mb-6"></div>
                <div className="h-6 bg-gray-200 rounded mb-3"></div>
                <div className="h-4 bg-gray-200 rounded"></div>
              </div>
            ))}
          </div>
        </div>
      </section>
    );
  }

  const displayFeatures = featuredSections.length > 0 
    ? featuredSections 
    : defaultFeatures;

  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-[#F8F6F3]">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-8 md:mb-16">
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl text-neutral-800 mb-4 font-light">
            Neden Bizi Seçmelisiniz?
          </h2>
          <p className="font-sans text-neutral-600 text-lg">
            Fark yaratan değerlerimiz
          </p>
        </div>

        <div className="grid md:grid-cols-4 gap-12">
          {displayFeatures.map((feature, index) => {
            // API'den gelen FeaturedSection için
            if ('id' in feature) {
              const Icon = getIconComponent(feature.icon);
              return (
                <div key={feature.id} className="text-center group">
                  <div className="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-white shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                    {feature.icon && !['award', 'leaf', 'truck', 'sparkles'].includes(feature.icon.toLowerCase()) ? (
                      <span className="text-4xl">{feature.icon}</span>
                    ) : (
                      <Icon className="w-10 h-10 text-[#8B7355] group-hover:scale-110 transition-transform duration-300" strokeWidth={1.5} />
                    )}
                  </div>
                  <h3 className="font-serif text-2xl text-neutral-800 mb-3">
                    {feature.title}
                  </h3>
                  <p className="font-sans text-neutral-600 leading-relaxed">
                    {feature.description}
                  </p>
                </div>
              );
            }
            
            // Fallback default features için
            return (
              <div key={index} className="text-center group">
                <div className="inline-flex items-center justify-center w-20 h-20 mb-6 rounded-full bg-white shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                  <span className="text-4xl">{feature.icon}</span>
                </div>
                <h3 className="font-serif text-2xl text-neutral-800 mb-3">
                  {feature.title}
                </h3>
                <p className="font-sans text-neutral-600 leading-relaxed">
                  {feature.description}
                </p>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
};

export default WhyChooseUs;
