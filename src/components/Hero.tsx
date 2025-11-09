import { useState, useEffect } from 'react';
import { ArrowRight } from 'lucide-react';
import { getHomeSliders } from '../lib/api';
import type { HomeSlider } from '../types/api';

interface HeroProps {
  scrollY: number;
}

const Hero = ({ scrollY }: HeroProps) => {
  const [isVisible, setIsVisible] = useState(false);
  const [sliders, setSliders] = useState<HomeSlider[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setIsVisible(true);
    
    async function fetchSliders() {
      try {
        const data = await getHomeSliders();
        setSliders(data);
      } catch (error) {
        console.error('Slider verisi yüklenemedi:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchSliders();
  }, []);

  const defaultSliders = [
    {
      id: 1,
      title: 'Pamuk Keten',
      subtitle: 'Nevresim Setleri',
      button_text: 'Ürünleri Keşfet',
      button_link: '/category/nevresim',
      image: '/pexels-cottonbro-4327012.jpg',
      order: 1
    },
    {
      id: 2,
      title: 'Muslin',
      subtitle: 'Yatak Örtüleri',
      button_text: 'Ürünleri Keşfet',
      button_link: '/category/yatak-ortuleri',
      image: '/pexels-shvetsa-5069401.jpg',
      order: 2
    }
  ];

  const displaySliders = !loading && sliders.length > 0 ? sliders : defaultSliders;
  const firstSlider = displaySliders[0];
  const secondSlider = displaySliders[1] || firstSlider;

  return (
    <section className="relative h-screen overflow-hidden">
      {/* Desktop Layout */}
      <div className="hidden md:grid md:grid-cols-2 h-full">
        <div className="relative group overflow-hidden">
          <div
            className="absolute inset-0 bg-gradient-to-br from-[#E5DDD1] to-[#C9B7A1] transition-transform duration-700"
            style={{ transform: `translateY(${scrollY * 0.5}px)` }}
          >
            <div 
              className="absolute inset-0 bg-cover bg-center opacity-90" 
              style={{ backgroundImage: `url(${firstSlider.image})` }}
            />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent" />
          <div className={`absolute bottom-16 left-12 right-12 z-10 transition-all duration-1000 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
            <h2 className="font-serif text-5xl lg:text-6xl text-white mb-4 font-light leading-tight">
              {firstSlider.title}
              {firstSlider.subtitle && <><br />{firstSlider.subtitle}</>}
            </h2>
            <button className="group/btn flex items-center gap-2 bg-white text-neutral-800 px-8 py-4 hover:bg-neutral-100 transition-all duration-300 hover:gap-4">
              <span className="font-sans text-sm tracking-wide">{firstSlider.button_text || 'Ürünleri Keşfet'}</span>
              <ArrowRight className="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
            </button>
          </div>
        </div>

        <div className="relative group overflow-hidden">
          <div
            className="absolute inset-0 bg-gradient-to-br from-[#D4C5B5] to-[#B8A593] transition-transform duration-700"
            style={{ transform: `translateY(${scrollY * 0.5}px)` }}
          >
            <div 
              className="absolute inset-0 bg-cover bg-center opacity-90" 
              style={{ backgroundImage: `url(${secondSlider.image})` }}
            />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent" />
          <div className={`absolute bottom-16 left-12 right-12 z-10 transition-all duration-1000 delay-200 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
            <h2 className="font-serif text-5xl lg:text-6xl text-white mb-4 font-light leading-tight">
              {secondSlider.title}
              {secondSlider.subtitle && <><br />{secondSlider.subtitle}</>}
            </h2>
            <button className="group/btn flex items-center gap-2 bg-white text-neutral-800 px-8 py-4 hover:bg-neutral-100 transition-all duration-300 hover:gap-4">
              <span className="font-sans text-sm tracking-wide">{secondSlider.button_text || 'Ürünleri Keşfet'}</span>
              <ArrowRight className="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
            </button>
          </div>
        </div>
      </div>

      {/* Mobile Layout - Single Hero with Slider */}
      <div className="md:hidden relative h-full">
        <div className="relative h-full overflow-hidden">
          <div
            className="absolute inset-0 bg-gradient-to-br from-[#E5DDD1] to-[#C9B7A1] transition-transform duration-700"
            style={{ transform: `translateY(${scrollY * 0.3}px)` }}
          >
            <div 
              className="absolute inset-0 bg-cover bg-center opacity-90" 
              style={{ backgroundImage: `url(${firstSlider.image})` }}
            />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent" />
          
          {/* Mobile Content */}
          <div className="absolute inset-0 flex flex-col justify-center items-center text-center px-6 z-10">
            <div className={`transition-all duration-1000 ${isVisible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'}`}>
              <h1 className="font-serif text-4xl sm:text-5xl text-white mb-4 font-light leading-tight">
                Luxury Home<br />Textiles
              </h1>
              <p className="font-sans text-white/90 text-lg mb-8 max-w-sm mx-auto leading-relaxed">
                Doğal liflerden üretilen premium ev tekstil ürünleri
              </p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <button className="group/btn flex items-center justify-center gap-2 bg-white text-neutral-800 px-6 py-3 hover:bg-neutral-100 transition-all duration-300 rounded-lg">
                  <span className="font-sans text-sm tracking-wide">Koleksiyonu Keşfet</span>
                  <ArrowRight className="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
                </button>
                <button className="group/btn flex items-center justify-center gap-2 border-2 border-white text-white px-6 py-3 hover:bg-white hover:text-neutral-800 transition-all duration-300 rounded-lg">
                  <span className="font-sans text-sm tracking-wide">Yeni Sezon</span>
                </button>
              </div>
            </div>
          </div>

          {/* Mobile Navigation Dots */}
          <div className="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex gap-2 z-20">
            <div className="w-2 h-2 bg-white rounded-full opacity-100"></div>
            <div className="w-2 h-2 bg-white/50 rounded-full"></div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Hero;
