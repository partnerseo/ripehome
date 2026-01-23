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
        console.log('HERO DEBUG - Sliders:', data);
        console.log('HERO DEBUG - First image:', data[0]?.image);
        setSliders(data);
      } catch (error) {
        console.error('HERO ERROR:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchSliders();
  }, []);

  if (loading) {
    return (
      <section className="relative h-screen bg-gray-100 flex items-center justify-center">
        <div className="animate-spin rounded-full h-16 w-16 border-b-2 border-gray-900"></div>
      </section>
    );
  }

  const defaultSliders = [{
    id: 1,
    title: 'Ripe Home',
    subtitle: 'Premium Ev Tekstili',
    button_text: 'Keşfet',
    button_link: '/products',
    image: '/pexels-cottonbro-4327012.jpg',
  }];

  const displaySliders = sliders.length > 0 ? sliders : defaultSliders;
  const firstSlider = displaySliders[0];
  const secondSlider = displaySliders[1] || firstSlider;

  const bg1 = 'url(' + firstSlider.image + ')';
  const bg2 = 'url(' + secondSlider.image + ')';

  return (
    <section className="relative h-screen overflow-hidden">
      <div className="hidden md:grid md:grid-cols-2 h-full">
        <div className="relative group overflow-hidden">
          <div className="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300">
            <div className="absolute inset-0 bg-cover bg-center" style={{backgroundImage: bg1}} />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
          <div className="absolute bottom-16 left-12 right-12 z-10">
            <h2 className="font-serif text-5xl text-white mb-4 font-light leading-tight">{firstSlider.title}<br />{firstSlider.subtitle}</h2>
            <button onClick={() => navigate(firstSlider.button_link)} className="flex items-center gap-2 bg-white text-gray-800 px-8 py-4 hover:bg-gray-100 transition-all">
              <span className="text-sm tracking-wide">{firstSlider.button_text}</span>
              <ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </div>

        <div className="relative group overflow-hidden">
          <div className="absolute inset-0 bg-gradient-to-br from-gray-300 to-gray-400">
            <div className="absolute inset-0 bg-cover bg-center" style={{backgroundImage: bg2}} />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
          <div className="absolute bottom-16 left-12 right-12 z-10">
            <h2 className="font-serif text-5xl text-white mb-4 font-light leading-tight">{secondSlider.title}<br />{secondSlider.subtitle}</h2>
            <button onClick={() => navigate(secondSlider.button_link)} className="flex items-center gap-2 bg-white text-gray-800 px-8 py-4 hover:bg-gray-100 transition-all">
              <span className="text-sm tracking-wide">{secondSlider.button_text}</span>
              <ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>

      <div className="md:hidden relative h-full">
        <div className="relative h-full overflow-hidden">
          <div className="absolute inset-0 bg-gradient-to-br from-gray-200 to-gray-300">
            <div className="absolute inset-0 bg-cover bg-center" style={{backgroundImage: bg1}} />
          </div>
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" />
          <div className="absolute inset-0 flex flex-col justify-center items-center text-center px-6 z-10">
            <h1 className="font-serif text-4xl sm:text-5xl text-white mb-4 font-light leading-tight">{firstSlider.title}<br />{firstSlider.subtitle}</h1>
            <p className="text-white/90 text-lg mb-8 max-w-sm mx-auto">Doğal liflerden üretilen premium ev tekstil ürünleri</p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <button onClick={() => navigate(firstSlider.button_link)} className="flex items-center justify-center gap-2 bg-white text-gray-800 px-6 py-3 hover:bg-gray-100 transition-all rounded-lg">
                <span className="text-sm tracking-wide">{firstSlider.button_text}</span>
                <ArrowRight className="w-4 h-4" />
              </button>
              <button onClick={() => navigate(secondSlider.button_link)} className="flex items-center justify-center gap-2 border-2 border-white text-white px-6 py-3 hover:bg-white hover:text-gray-800 transition-all rounded-lg">
  <span className="text-sm tracking-wide">{secondSlider.button_text}</span>
</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Hero;
