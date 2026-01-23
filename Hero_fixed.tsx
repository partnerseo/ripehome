import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { ArrowRight } from 'lucide-react';
import { getHomeSliders } from '../lib/api';
import type { HomeSlider } from '../types/api';

interface HeroProps {
  scrollY: number;
}

const Hero = ({ scrollY }: HeroProps) => {
  const navigate = useNavigate();
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
        console.error('Error:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchSliders();
  }, []);

  if (loading) {
    return (
      <section className="relative h-screen overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
        <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-gray-900"></div>
      </section>
    );
  }

  const defaultSliders = [{
    id: 1,
    title: 'Ripe Home',
    subtitle: 'Premium Ev Tekstili',
    button_text: 'Ürünleri Keşfet',
    button_link: '/products',
    image: '/pexels-cottonbro-4327012.jpg',
  }];

  const displaySliders = sliders.length > 0 ? sliders : defaultSliders;
  const firstSlider = displaySliders[0];
  const secondSlider = displaySliders[1] || firstSlider;

  return (
    <section className="relative h-screen overflow-hidden">
      <div className="hidden md:grid md:grid-cols-2 h-full">
        <div className="relative group overflow-hidden">
          <div className="absolute inset-0 bg-gradient-to-br from-[#E5DDD1] to-[#C9B7A1]">
            <div className="absolute inset-0 bg-cover bg-center opacity-90" style={{backgroundImage: 'url(' + firstSlider.image + ')'}} />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent" />
          <div className={'absolute bottom-16 left-12 right-12 z-10 transition-all duration-1000 ' + (isVisible ? 'opacity-100' : 'opacity-0')}>
            <h2 className="font-serif text-5xl text-white mb-4 font-light">{firstSlider.title}<br />{firstSlider.subtitle}</h2>
            <button onClick={() => navigate(firstSlider.button_link || '/products')} className="flex items-center gap-2 bg-white text-neutral-800 px-8 py-4 hover:bg-neutral-100">
              <span>{firstSlider.button_text}</span><ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </div>

        <div className="relative group overflow-hidden">
          <div className="absolute inset-0 bg-gradient-to-br from-[#D4C5B5] to-[#B8A593]">
            <div className="absolute inset-0 bg-cover bg-center opacity-90" style={{backgroundImage: 'url(' + secondSlider.image + ')'}} />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent" />
          <div className={'absolute bottom-16 left-12 right-12 z-10 transition-all duration-1000 ' + (isVisible ? 'opacity-100' : 'opacity-0')}>
            <h2 className="font-serif text-5xl text-white mb-4 font-light">{secondSlider.title}<br />{secondSlider.subtitle}</h2>
            <button onClick={() => navigate(secondSlider.button_link || '/products')} className="flex items-center gap-2 bg-white text-neutral-800 px-8 py-4 hover:bg-neutral-100">
              <span>{secondSlider.button_text}</span><ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Hero;
