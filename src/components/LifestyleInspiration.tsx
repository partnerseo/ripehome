import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { getFeaturedProducts } from '../lib/api';
import type { FeaturedProduct } from '../types/api';

const LifestyleInspiration = () => {
  const navigate = useNavigate();
  const [products, setProducts] = useState<FeaturedProduct[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getFeaturedProducts()
      .then(data => {
        // İlk 5 öne çıkan ürünü al
        setProducts((data || []).slice(0, 5));
      })
      .catch(error => {
        console.error('❌ Lifestyle products error:', error);
        setProducts([]);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  const handleProductClick = (product: FeaturedProduct) => {
    if (product.button_link) {
      window.location.href = product.button_link;
    }
  };

  if (loading) {
    return (
      <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-[#F8F6F3]">
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
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-[#F8F6F3]">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-8 md:mb-16">
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl text-neutral-800 mb-4 font-light">
            Yaşam Tarzı İlhamı
          </h2>
          <p className="font-sans text-neutral-600 text-lg">
            Ürünlerimizle hayatınıza dokunun
          </p>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
          {/* Büyük kart - İlk ürün */}
          {products[0] && (
            <div className="col-span-2 row-span-2">
              <div 
                onClick={() => handleProductClick(products[0])}
                className="group relative h-full overflow-hidden cursor-pointer rounded-2xl"
              >
                <img
                  src={products[0].image || '/pexels-cottonbro-4327012.jpg'}
                  alt={products[0].title}
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                <div className="absolute bottom-8 left-8 right-8">
                  <p className="font-serif text-2xl md:text-3xl text-white opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 line-clamp-2">
                    {products[0].title}
                  </p>
                  {products[0].category_label && (
                    <p className="font-sans text-sm text-white/80 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-200">
                      {products[0].category_label}
                    </p>
                  )}
                </div>
              </div>
            </div>
          )}

          {/* Küçük kartlar - 2. ve 3. ürünler */}
          {products.slice(1, 3).map((product, index) => (
            <div key={product.id || index} className="col-span-1">
              <div 
                onClick={() => handleProductClick(product)}
                className="group relative h-full overflow-hidden cursor-pointer rounded-2xl"
              >
                <img
                  src={product.image || '/pexels-cottonbro-4327012.jpg'}
                  alt={product.title}
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                <div className="absolute bottom-4 left-4 right-4">
                  <p className="font-serif text-lg md:text-xl text-white opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 line-clamp-2">
                    {product.title}
                  </p>
                  {product.category_label && (
                    <p className="font-sans text-xs text-white/80 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-200">
                      {product.category_label}
                    </p>
                  )}
                </div>
              </div>
            </div>
          ))}

          {/* Orta kartlar - 4. ve 5. ürünler */}
          {products.slice(3, 5).map((product, index) => (
            <div key={product.id || index} className="col-span-1">
              <div 
                onClick={() => handleProductClick(product)}
                className="group relative h-full overflow-hidden cursor-pointer rounded-2xl"
              >
                <img
                  src={product.image || '/pexels-cottonbro-4327012.jpg'}
                  alt={product.title}
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                <div className="absolute bottom-4 left-4 right-4">
                  <p className="font-serif text-lg md:text-xl text-white opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100 line-clamp-2">
                    {product.title}
                  </p>
                  {product.category_label && (
                    <p className="font-sans text-xs text-white/80 mt-1 opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-200">
                      {product.category_label}
                    </p>
                  )}
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default LifestyleInspiration;
