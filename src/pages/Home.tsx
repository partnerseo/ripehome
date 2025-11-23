import { useState, useEffect } from 'react';
import Hero from '../components/Hero';
import SignatureCollection from '../components/SignatureCollection';
import CategoryGrid from '../components/CategoryGrid';
import BrandPhilosophy from '../components/BrandPhilosophy';
import LifestyleInspiration from '../components/LifestyleInspiration';
import FeaturedProducts from '../components/FeaturedProducts';
import WhyChooseUs from '../components/WhyChooseUs';
import Testimonials from '../components/Testimonials';
import SocialProof from '../components/SocialProof';
import Newsletter from '../components/Newsletter';
import { getCategories, getProducts } from '../lib/api';
import type { Category, Product } from '../types/api';

const Home = () => {
  const [scrollY, setScrollY] = useState(0);
  const [loading, setLoading] = useState(true);
  const [categories, setCategories] = useState<Category[]>([]);
  const [featuredProducts, setFeaturedProducts] = useState<Product[]>([]);

  useEffect(() => {
    // Scroll to top on mount
    window.scrollTo(0, 0);
    
    const handleScroll = () => setScrollY(window.scrollY);
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    // Tüm API çağrılarını paralel yap - ÇOK DAHA HIZLI!
    Promise.all([
      getCategories(),
      getProducts(1)
    ])
      .then(([cats, prodsData]) => {
        console.log('🏠 Home data loaded:');
        console.log('  Categories:', cats?.length || 0);
        console.log('  Categories data:', cats);
        
        // Sadece ürünü olan kategorileri göster
        const validCategories = (cats || []).filter(c => (c.products_count || 0) > 0);
        console.log('  Valid categories (with products):', validCategories.length);
        
        setCategories(validCategories);
        
        // Sadece öne çıkan ürünleri filtrele
        const featured = prodsData.success && prodsData.data?.data 
          ? prodsData.data.data.filter((p: Product) => p.is_featured).slice(0, 8)
          : [];
        
        console.log('  Featured Products:', featured.length);
        setFeaturedProducts(featured);
      })
      .catch((error) => {
        console.error('❌ API çağrıları başarısız:', error);
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
      <CategoryGrid categories={categories} />
      <BrandPhilosophy />
      <LifestyleInspiration />
      <FeaturedProducts products={featuredProducts} />
      <WhyChooseUs />
      <Testimonials />
      <SocialProof />
      <Newsletter />
    </>
  );
};

export default Home;

