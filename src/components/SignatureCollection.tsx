import { useState, useEffect } from 'react';
import { ArrowRight, Sparkles } from 'lucide-react';
import { getFeaturedProducts } from '../lib/api';
import type { FeaturedProduct } from '../types/api';

const SignatureCollection = () => {
  const [collections, setCollections] = useState<FeaturedProduct[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchFeaturedProducts() {
      try {
        const data = await getFeaturedProducts();
        setCollections(data);
      } catch (error) {
        console.error('Öne çıkan ürünler yüklenemedi:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchFeaturedProducts();
  }, []);
  if (loading) {
    return (
      <section className="py-16 md:py-32 px-4 md:px-12 lg:px-24 bg-gradient-to-b from-[#F8F6F3] to-white">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-12 md:mb-20">
            <div className="animate-pulse">
              <div className="h-16 bg-gray-200 rounded w-96 mx-auto mb-4"></div>
              <div className="h-8 bg-gray-200 rounded w-64 mx-auto"></div>
            </div>
          </div>
          <div className="grid md:grid-cols-3 gap-10">
            {[...Array(3)].map((_, i) => (
              <div key={i} className="animate-pulse bg-white" style={{ boxShadow: '0 4px 20px rgba(0,0,0,0.08)' }}>
                <div className="aspect-[4/5] bg-gray-200"></div>
                <div className="p-8">
                  <div className="h-4 bg-gray-200 rounded w-32 mb-4"></div>
                  <div className="h-8 bg-gray-200 rounded w-full mb-3"></div>
                  <div className="h-4 bg-gray-200 rounded w-full mb-4"></div>
                  <div className="flex gap-2 mb-6">
                    <div className="h-6 bg-gray-200 rounded-full w-20"></div>
                    <div className="h-6 bg-gray-200 rounded-full w-20"></div>
                  </div>
                  <div className="h-12 bg-gray-200 rounded"></div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="py-16 md:py-32 px-4 md:px-12 lg:px-24 bg-gradient-to-b from-[#F8F6F3] to-white">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-12 md:mb-20">
          <div className="inline-flex items-center gap-2 mb-6">
            <Sparkles className="w-6 h-6 text-[#D4AF37]" strokeWidth={1.5} />
            <span className="font-sans text-sm tracking-[0.3em] uppercase text-[#8B7355]">
              Signature
            </span>
            <Sparkles className="w-6 h-6 text-[#D4AF37]" strokeWidth={1.5} />
          </div>
          <h2 className="font-serif text-4xl md:text-5xl lg:text-7xl text-neutral-800 mb-6 font-light leading-tight">
            İmza Koleksiyonumuz
          </h2>
          <p className="font-sans text-neutral-600 text-xl max-w-2xl mx-auto leading-relaxed">
            Doğal dokuların zarafeti ile el işçiliğinin mükemmelliğini bir araya getiren,
            özenle seçilmiş koleksiyonlarımız
          </p>
        </div>

        <div className="grid md:grid-cols-3 gap-10">
          {collections.map((item) => (
            <a
              key={item.id}
              href={item.button_link}
              className="group relative bg-white overflow-hidden cursor-pointer transition-all duration-500 hover:-translate-y-2 block"
              style={{
                boxShadow: '0 4px 20px rgba(0,0,0,0.08)',
              }}
            >
              <div className="aspect-[4/5] overflow-hidden relative">
                <img
                  src={item.image || '/pexels-cottonbro-4327012.jpg'}
                  alt={item.title}
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
              </div>

              <div className="p-8">
                <div className="mb-4">
                  {item.category_label && (
                    <span className="font-sans text-xs tracking-widest uppercase text-[#8B7355] mb-2 block">
                      {item.category_label}
                    </span>
                  )}
                  <h3 className="font-serif text-3xl text-neutral-800 mb-3 font-light leading-tight">
                    {item.title}
                  </h3>
                  <p className="font-sans text-neutral-600 text-sm leading-relaxed mb-4">
                    {item.description}
                  </p>
                </div>

                {item.tags && (() => {
                  try {
                    const tagsArray = typeof item.tags === 'string' ? JSON.parse(item.tags) : item.tags;
                    return Array.isArray(tagsArray) && tagsArray.length > 0 && (
                      <div className="flex flex-wrap gap-2 mb-6">
                        {tagsArray.map((tag, idx) => (
                          <span
                            key={idx}
                            className="px-3 py-1 bg-[#F8F6F3] text-neutral-700 font-sans text-xs rounded-full"
                          >
                            {tag}
                          </span>
                        ))}
                      </div>
                    );
                  } catch {
                    return null;
                  }
                })()}

                <button className="group/btn w-full flex items-center justify-center gap-2 bg-neutral-800 text-white py-4 hover:bg-[#8B7355] transition-all duration-300">
                  <span className="font-sans text-sm tracking-wide">{item.button_text}</span>
                  <ArrowRight className="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
                </button>
              </div>

              <div className="absolute top-8 left-8 w-12 h-12 border-t-2 border-l-2 border-[#D4AF37] opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
              <div className="absolute bottom-8 right-8 w-12 h-12 border-b-2 border-r-2 border-[#D4AF37] opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
            </a>
          ))}
        </div>

        <div className="text-center mt-16">
          <a
            href="#"
            className="inline-flex items-center gap-3 font-sans text-neutral-800 hover:text-[#8B7355] transition-colors duration-300 group"
          >
            <span className="text-sm tracking-wide">Tüm Koleksiyonu Görüntüle</span>
            <ArrowRight className="w-5 h-5 transition-transform group-hover:translate-x-2" />
          </a>
        </div>
      </div>
    </section>
  );
};

export default SignatureCollection;
