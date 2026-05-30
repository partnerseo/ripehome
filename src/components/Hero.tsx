import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { ArrowRight } from 'lucide-react';
import { getHomeSliders } from '../lib/api';
import { useLangNavigate } from '../hooks/useLang';
import type { HomeSlider } from '../types/api';

interface HeroProps {
  scrollY: number;
}

const Hero = ({ scrollY }: HeroProps) => {
  const navigate = useLangNavigate();
  const { t } = useTranslation();
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
    subtitle: t('home.defaultSubtitle'),
    button_text: t('home.defaultButton'),
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
      {/* Desktop: 2 panel */}
      <div className="hidden md:grid md:grid-cols-2 h-full">
        <div className="relative overflow-hidden">
          <div className="absolute inset-0 bg-cover bg-center" style={{backgroundImage: bg1}} />
          <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent" />
          <div className="absolute bottom-28 left-12 right-6 z-10">
            <h2 className="font-serif text-5xl text-white font-light leading-tight">{firstSlider.title}<br />{firstSlider.subtitle}</h2>
          </div>
        </div>
        <div className="relative overflow-hidden">
          <div className="absolute inset-0 bg-cover bg-center" style={{backgroundImage: bg2}} />
          <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent" />
          <div className="absolute bottom-28 left-6 right-12 z-10">
            <h2 className="font-serif text-5xl text-white font-light leading-tight">{secondSlider.title}<br />{secondSlider.subtitle}</h2>
          </div>
        </div>
      </div>

      {/* Single centered button spanning both panels */}
      <div className="hidden md:flex absolute bottom-12 left-0 right-0 justify-center z-20">
        <button
          onClick={() => navigate(firstSlider.button_link)}
          className="flex items-center gap-3 bg-white text-gray-900 px-10 py-4 hover:bg-gray-100 transition-all duration-300 shadow-2xl text-sm tracking-widest uppercase font-medium"
        >
          <span>{firstSlider.button_text}</span>
          <ArrowRight className="w-4 h-4" />
        </button>
      </div>

      {/* Mobile: single panel */}
      <div className="md:hidden relative h-full">
        <div className="absolute inset-0 bg-cover bg-center" style={{backgroundImage: bg1}} />
        <div className="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent" />
        <div className="absolute inset-0 flex flex-col justify-end items-center text-center px-6 pb-16 z-10">
          <h1 className="font-serif text-4xl text-white mb-8 font-light leading-tight">{firstSlider.title}<br />{firstSlider.subtitle}</h1>
          <button
            onClick={() => navigate(firstSlider.button_link)}
            className="flex items-center justify-center gap-2 bg-white text-gray-800 px-8 py-4 hover:bg-gray-100 transition-all text-sm tracking-widest uppercase font-medium"
          >
            <span>{firstSlider.button_text}</span>
            <ArrowRight className="w-4 h-4" />
          </button>
        </div>
      </div>
    </section>
  );
};

export default Hero;
