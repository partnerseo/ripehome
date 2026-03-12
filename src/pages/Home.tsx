import { useState, useEffect } from 'react';
import Hero from '../components/Hero';
import SignatureCollection from '../components/SignatureCollection';
import CategoryGrid from '../components/CategoryGrid';
import BrandPhilosophy from '../components/BrandPhilosophy';
import LifestyleInspiration from '../components/LifestyleInspiration';

import Testimonials from '../components/Testimonials';
import SocialProof from '../components/SocialProof';
import Newsletter from '../components/Newsletter';
import { getCategories, getFeaturedSections, getSettings } from '../lib/api';
import type { Category, FeaturedSection, Settings } from '../types/api';

const Home = () => {
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
          <p className="text-neutral-600 font-light">Yükleniyor...</p>
        </div>
      </div>
    );
  }

  return (
    <>
      <Hero scrollY={scrollY} />
      <SignatureCollection />
      <CategoryGrid categories={categories} featuredSections={featuredSections} />
      <BrandPhilosophy brand={settings?.brand} />
      <LifestyleInspiration />
      <Testimonials />
      <SocialProof />
      <Newsletter />
    </>
  );
};

export default Home;
