import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import Hero from '../components/Hero';
import SignatureCollection from '../components/SignatureCollection';
import CategoryGrid from '../components/CategoryGrid';
import LifestyleInspiration from '../components/LifestyleInspiration';
import BrandVideo from '../components/BrandVideo';
import Testimonials from '../components/Testimonials';
import SocialProof from '../components/SocialProof';
import BlogPreview from '../components/BlogPreview';
import Newsletter from '../components/Newsletter';
import { getCategories, getFeaturedSections, getSettings } from '../lib/api';
import type { Category, FeaturedSection, Settings } from '../types/api';

const Home = () => {
  const { t } = useTranslation();
  const [scrollY, setScrollY] = useState(0);
  const [loading, setLoading] = useState(true);
  const [categories, setCategories] = useState<Category[]>([]);
  const [featuredSections, setFeaturedSections] = useState<FeaturedSection[]>([]);
  const [settings, setSettings] = useState<Settings | null>(null);

  useEffect(() => {
    window.scrollTo(0, 0);
    const handleScroll = () => setScrollY(window.scrollY);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    Promise.all([getCategories(), getFeaturedSections(), getSettings()])
      .then(([cats, sections, siteSettings]) => {
        const validCategories = (cats || []).filter(c => (c.products_count || 0) > 0);
        setCategories(validCategories);
        setFeaturedSections(sections || []);
        setSettings(siteSettings);
      })
      .catch((error) => {
        console.error('API çağrısı başarısız:', error);
      })
      .finally(() => {
        setLoading(false);
      });
  }, []);

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center bg-white">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-neutral-800 mx-auto mb-4"></div>
          <p className="text-neutral-600 font-light">{t('common.loading')}</p>
        </div>
      </div>
    );
  }

  return (
    <>
      <Hero scrollY={scrollY} />
      <SignatureCollection />
      <CategoryGrid categories={categories} featuredSections={featuredSections} />
      {(settings?.video?.url || settings?.video?.file) && (
        <BrandVideo
          url={settings.video.url}
          file={settings.video.file}
          title={settings.video.title}
          subtitle={settings.video.subtitle}
        />
      )}
      <LifestyleInspiration />
      <Testimonials />
      <SocialProof />
      <BlogPreview />
      <Newsletter />
    </>
  );
};

export default Home;
