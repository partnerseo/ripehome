import { useState, useEffect } from 'react';
import { Instagram, ExternalLink } from 'lucide-react';
import { getFeaturedProducts } from '../lib/api';
import type { FeaturedProduct } from '../types/api';

const SocialProof = () => {
  const [products, setProducts] = useState<FeaturedProduct[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getFeaturedProducts()
      .then(data => {
        // İlk 6 ürünü Instagram grid için al
        setProducts((data || []).slice(0, 6));
      })
      .catch(error => {
        console.error('❌ Instagram products error:', error);
        setProducts([]);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  const handleInstagramClick = () => {
    window.open('https://www.instagram.com/ripe_home/', '_blank');
  };

  if (loading) {
    return (
      <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-gradient-to-b from-gray-50 to-white">
        <div className="max-w-7xl mx-auto text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-gray-900 mx-auto"></div>
        </div>
      </section>
    );
  }

  if (!products || products.length === 0) {
    return null;
  }

  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-gradient-to-b from-gray-50 to-white">
      <div className="max-w-7xl mx-auto">
        {/* Başlık */}
        <div className="text-center mb-12 md:mb-16">
          <div className="inline-flex items-center gap-3 mb-6">
            <div className="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 via-pink-500 to-orange-500 flex items-center justify-center">
              <Instagram className="w-6 h-6 text-white" strokeWidth={2} />
            </div>
            <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl text-neutral-800 font-light">
              Bizi Instagram'da Keşfedin
            </h2>
          </div>
          <p className="text-gray-600 text-lg">
            Ürünlerimiz ve güncel paylaşımlarımız için takipte kalın
          </p>
        </div>

        {/* Instagram Grid */}
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
          {products.map((product, index) => (
            <button
              key={product.id || index}
              onClick={handleInstagramClick}
              className="group relative aspect-square overflow-hidden cursor-pointer bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500"
            >
              <img
                src={product.image || '/pexels-cottonbro-4327012.jpg'}
                alt={product.title}
                className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
              />
              
              {/* Hover Overlay */}
              <div className="absolute inset-0 bg-gradient-to-br from-purple-500/80 via-pink-500/80 to-orange-500/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center p-4">
                <Instagram className="w-10 h-10 text-white mb-3 transform group-hover:scale-110 transition-transform" />
                <p className="text-white text-sm font-medium text-center line-clamp-2">
                  {product.title}
                </p>
              </div>

              {/* Instagram Icon Badge */}
              <div className="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <Instagram className="w-4 h-4 text-pink-500" />
              </div>
            </button>
          ))}
        </div>

        {/* CTA Butonu */}
        <div className="text-center mt-12">
          <button
            onClick={handleInstagramClick}
            className="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-purple-500 via-pink-500 to-orange-500 text-white rounded-xl font-medium hover:shadow-2xl hover:scale-105 transition-all duration-300"
          >
            <Instagram className="w-5 h-5" strokeWidth={2} />
            <span>@ripe_home hesabını takip et</span>
            <ExternalLink className="w-4 h-4" />
          </button>
          <p className="text-gray-600 text-sm mt-4">
            Güncel ürünlerimiz ve kampanyalarımız için bizi Instagram'da takip edin
          </p>
        </div>
      </div>
    </section>
  );
};

export default SocialProof;
