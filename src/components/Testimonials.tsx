import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { ChevronLeft, ChevronRight, Star } from 'lucide-react';
import { getTestimonials } from '../lib/api';

interface Testimonial {
  id: number;
  customer_name: string;
  company: string;
  position: string;
  rating: number;
  comment: string;
  avatar: string | null;
}

const Testimonials = () => {
  const { t, i18n } = useTranslation();
  const [items, setItems] = useState<Testimonial[]>([]);
  const [currentIndex, setCurrentIndex] = useState(0);

  useEffect(() => {
    getTestimonials().then((data) => {
      if (Array.isArray(data) && data.length > 0) setItems(data);
    });
  }, [i18n.language]);

  if (items.length === 0) return null;

  const current = items[currentIndex];

  const next = () => setCurrentIndex((prev) => (prev + 1) % items.length);
  const prev = () => setCurrentIndex((prev) => (prev - 1 + items.length) % items.length);

  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-white">
      <div className="max-w-5xl mx-auto">
        <div className="text-center mb-8 md:mb-16">
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl text-neutral-800 mb-4 font-light">
            {t('home.testimonials')}
          </h2>
          <p className="font-sans text-neutral-600 text-lg">
            {t('home.testimonialsSubtitle')}
          </p>
        </div>

        <div className="relative">
          <div className="bg-white rounded-lg shadow-xl p-12 max-w-3xl mx-auto">
            <div className="flex items-center mb-6">
              {current.avatar ? (
                <img
                  src={`https://ripehome.com.tr/storage/${current.avatar}`}
                  alt={current.customer_name}
                  className="w-16 h-16 rounded-full object-cover mr-4"
                />
              ) : (
                <div className="w-16 h-16 rounded-full bg-[#F8F6F3] flex items-center justify-center mr-4 text-2xl font-serif text-[#8B7355]">
                  {current.customer_name?.[0] ?? '?'}
                </div>
              )}
              <div>
                <h3 className="font-serif text-2xl text-neutral-800">
                  {current.customer_name}
                </h3>
                {(current.company || current.position) && (
                  <p className="font-sans text-sm text-neutral-500 mt-0.5">
                    {[current.position, current.company].filter(Boolean).join(' — ')}
                  </p>
                )}
                <div className="flex gap-1 mt-1">
                  {[...Array(current.rating)].map((_, i) => (
                    <Star key={i} className="w-4 h-4 fill-[#D4AF37] text-[#D4AF37]" />
                  ))}
                </div>
              </div>
            </div>
            <p className="font-sans text-neutral-700 text-lg leading-relaxed italic">
              "{current.comment}"
            </p>
          </div>

          {items.length > 1 && (
            <div className="flex justify-center gap-4 mt-8">
              <button
                onClick={prev}
                className="p-3 rounded-full bg-[#F8F6F3] hover:bg-[#E5DDD1] transition-colors duration-300"
              >
                <ChevronLeft className="w-6 h-6 text-neutral-800" />
              </button>
              <button
                onClick={next}
                className="p-3 rounded-full bg-[#F8F6F3] hover:bg-[#E5DDD1] transition-colors duration-300"
              >
                <ChevronRight className="w-6 h-6 text-neutral-800" />
              </button>
            </div>
          )}

          {items.length > 1 && (
            <div className="flex justify-center gap-2 mt-4">
              {items.map((_, i) => (
                <button
                  key={i}
                  onClick={() => setCurrentIndex(i)}
                  className={`w-2 h-2 rounded-full transition-colors ${i === currentIndex ? 'bg-[#8B7355]' : 'bg-[#E5DDD1]'}`}
                />
              ))}
            </div>
          )}
        </div>
      </div>
    </section>
  );
};

export default Testimonials;
