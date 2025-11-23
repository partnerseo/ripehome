import { useState, useEffect } from 'react';
import { getFeaturedProducts } from '../lib/api';
import type { FeaturedProduct } from '../types/api';

export default function SignatureCollection() {
  const [products, setProducts] = useState<FeaturedProduct[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getFeaturedProducts()
      .then(data => {
        console.log('✅ Featured products loaded:', data);
        setProducts(data || []);
      })
      .catch(error => {
        console.error('❌ Error:', error);
        setProducts([]);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  // Tags parse - GÜVENLİ
  const parseTags = (tags: any): string[] => {
    if (!tags) return [];
    if (Array.isArray(tags)) return tags;
    if (typeof tags === 'string') {
      try {
        const parsed = JSON.parse(tags);
        return Array.isArray(parsed) ? parsed : [];
      } catch (e) {
        return [];
      }
    }
    return [];
  };

  if (loading) {
    return (
      <section className="py-24 bg-gradient-to-b from-white to-gray-50">
        <div className="container mx-auto px-4 text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
        </div>
      </section>
    );
  }

  if (!products || products.length === 0) {
    return null;
  }

  return (
    <section className="py-24 bg-gradient-to-b from-white to-gray-50">
      <div className="container mx-auto px-4">
        {/* Premium Başlık */}
        <div className="text-center mb-16">
          <div className="inline-block">
            <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
              Premium Koleksiyon
            </span>
            <h2 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
              Öne Çıkan Ürünlerimiz
            </h2>
            <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
          </div>
          <p className="text-gray-600 max-w-2xl mx-auto mt-6 text-lg">
            Özenle seçilmiş, en kaliteli ürünlerimizi keşfedin
          </p>
        </div>

        {/* Premium Kartlar */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          {products.map((item) => {
            const tags = parseTags(item.tags);
            
            return (
              <div 
                key={item.id}
                className="group bg-white rounded-2xl overflow-hidden hover:shadow-2xl transition-all duration-500 border border-gray-100"
              >
                {/* Image */}
                <div className="relative overflow-hidden aspect-[3/4]">
                  {item.image ? (
                    <img 
                      src={item.image} 
                      alt={item.title}
                      className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                    />
                  ) : (
                    <div className="w-full h-full bg-gray-100 flex items-center justify-center">
                      <span className="text-gray-400">Görsel Yok</span>
                    </div>
                  )}
                  
                  {/* Category Badge */}
                  {item.category_label && (
                    <div className="absolute top-4 left-4">
                      <span className="inline-block bg-white/95 backdrop-blur-sm text-gray-900 text-xs font-medium px-4 py-2 rounded-full">
                        {item.category_label}
                      </span>
                    </div>
                  )}
                </div>

                {/* Content */}
                <div className="p-8">
                  <h3 className="text-2xl font-light text-gray-900 mb-3 line-clamp-2 group-hover:text-gray-700 transition">
                    {item.title}
                  </h3>

                  {item.description && (
                    <p className="text-gray-600 text-sm leading-relaxed mb-6 line-clamp-3">
                      {item.description}
                    </p>
                  )}

                  {/* Tags */}
                  {tags.length > 0 && (
                    <div className="flex flex-wrap gap-2 mb-6">
                      {tags.map((tag, i) => (
                        <span 
                          key={i}
                          className="inline-block bg-gray-50 text-gray-700 text-xs font-medium px-3 py-1.5 rounded-full"
                        >
                          {tag}
                        </span>
                      ))}
                    </div>
                  )}

                  {/* Button */}
                  <a 
                    href={item.button_link || '#'}
                    className="group/btn relative inline-flex items-center justify-between w-full px-6 py-4 bg-gray-900 text-white rounded-xl font-medium overflow-hidden transition-all duration-300 hover:shadow-xl"
                  >
                    <span className="absolute inset-0 w-full h-full bg-gray-800 translate-y-full group-hover/btn:translate-y-0 transition-transform duration-300"></span>
                    <span className="relative z-10">
                      {item.button_text || 'Detayları Gör'}
                    </span>
                    <svg className="w-5 h-5 relative z-10 group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                    </svg>
                  </a>
                </div>
              </div>
            );
          })}
        </div>
      </div>
    </section>
  );
}
