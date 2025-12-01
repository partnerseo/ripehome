import { useState, useEffect } from 'react';
import { Menu, X } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { getCategories } from '../lib/api';
import type { Category } from '../types/api';

const Navbar = () => {
  const navigate = useNavigate();
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);
  const [isProductsOpen, setIsProductsOpen] = useState(false);
  const [categories, setCategories] = useState<Category[]>([]);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  useEffect(() => {
    getCategories().then(cats => {
      const validCategories = (cats || []).filter(c => (c.products_count || 0) > 0);
      setCategories(validCategories);
    });
  }, []);

  return (
    <nav
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${
        isScrolled
          ? 'bg-white/95 backdrop-blur-md shadow-lg py-4'
          : 'bg-white/90 backdrop-blur-sm shadow-md py-6'
      }`}
    >
      <div className="max-w-7xl mx-auto px-6 md:px-12 lg:px-24">
        <div className="flex items-center justify-between">
          {/* Logo */}
            <button onClick={() => navigate('/')} className="flex items-center">
              <img 
                src="/ripehomelogo.jpg" 
                alt="Ripe Home Logo" 
                className="h-12 md:h-16 lg:h-20 w-auto object-contain transition-all duration-300 hover:opacity-80"
              />
            </button>

          {/* Desktop Nav */}
          <nav className="hidden lg:flex items-center space-x-10">
            <button 
              onClick={() => navigate('/')}
              className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide relative group"
            >
              Anasayfa
              <span className="absolute -bottom-2 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
            </button>

            <button 
              onClick={() => navigate('/hakkimizda')}
              className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide relative group"
            >
              Hakkımızda
              <span className="absolute -bottom-2 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
            </button>

            {/* Ürünlerimiz Dropdown */}
            <div 
              className="relative"
              onMouseEnter={() => setIsProductsOpen(true)}
              onMouseLeave={() => setIsProductsOpen(false)}
            >
              <button className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide flex items-center gap-2 group">
                Ürünlerimiz
                <svg 
                  className={`w-4 h-4 transition-transform duration-300 ${isProductsOpen ? 'rotate-180' : ''}`}
                  fill="none" 
                  stroke="currentColor" 
                  viewBox="0 0 24 24"
                >
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
                <span className="absolute -bottom-2 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
              </button>

              {/* Dropdown Menu */}
              {isProductsOpen && categories.length > 0 && (
                <div className="absolute top-full left-1/2 -translate-x-1/2 pt-4">
                  <div className="bg-white rounded-2xl shadow-2xl border border-gray-100 w-80 py-4 max-h-[32rem] overflow-y-auto">
                    <div className="px-6 pb-3 border-b border-gray-100">
                      <p className="text-xs uppercase tracking-wider text-gray-500 font-medium">
                        Kategorilerimiz
                      </p>
                    </div>
                    {categories.map(cat => (
                      <button
                        key={cat.id}
                        onClick={() => {
                          navigate(`/category/${cat.slug}`);
                          setIsProductsOpen(false);
                        }}
                        className="block w-full text-left px-6 py-4 hover:bg-gray-50 transition-colors group/item"
                      >
                        <div className="flex items-center justify-between">
                          <div>
                            <div className="font-medium text-gray-900 group-hover/item:text-gray-900">
                              {cat.name}
                            </div>
                            {cat.products_count > 0 && (
                              <div className="text-xs text-gray-500 mt-1">
                                {cat.products_count} ürün
                              </div>
                            )}
                          </div>
                          <svg 
                            className="w-4 h-4 text-gray-400 group-hover/item:text-gray-900 group-hover/item:translate-x-1 transition-all" 
                            fill="none" 
                            stroke="currentColor" 
                            viewBox="0 0 24 24"
                          >
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                          </svg>
                        </div>
                      </button>
                    ))}
                  </div>
                </div>
              )}
            </div>

            <button 
              onClick={() => navigate('/iletisim')}
              className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide relative group"
            >
              İletişim
              <span className="absolute -bottom-2 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
            </button>

            {/* Toptan Sipariş Button */}
            <button 
              onClick={() => navigate('/toptan-siparis')}
              className="group relative inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white rounded-xl font-medium overflow-hidden transition-all duration-300 hover:shadow-2xl hover:scale-105"
            >
              <span className="absolute inset-0 w-full h-full bg-gradient-to-r from-gray-800 via-gray-700 to-gray-800 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
              <svg className="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              <span className="relative z-10 tracking-wide">Toptan Sipariş</span>
              <svg className="w-4 h-4 relative z-10 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </nav>

          {/* Mobile Menu Button */}
          <button
            onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            className="lg:hidden"
          >
            {isMobileMenuOpen ? (
              <X className="w-6 h-6 text-neutral-800" />
            ) : (
              <Menu className="w-6 h-6 text-neutral-800" />
            )}
          </button>
        </div>

        {/* Mobile Menu */}
        {isMobileMenuOpen && (
          <div className="lg:hidden border-t border-gray-100 py-6 bg-white mt-6">
            <nav className="flex flex-col space-y-2">
              <button 
                onClick={() => {
                  navigate('/');
                  setIsMobileMenuOpen(false);
                }}
                className="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
              >
                Anasayfa
              </button>
              
              <button 
                onClick={() => {
                  navigate('/hakkimizda');
                  setIsMobileMenuOpen(false);
                }}
                className="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
              >
                Hakkımızda
              </button>
              
              <div>
                <button
                  onClick={() => setIsProductsOpen(!isProductsOpen)}
                  className="w-full text-left px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg flex justify-between items-center transition"
                >
                  Ürünlerimiz
                  <svg 
                    className={`w-4 h-4 transition ${isProductsOpen ? 'rotate-180' : ''}`} 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                
                {isProductsOpen && (
                  <div className="pl-6 pr-4 py-2 space-y-1">
                    {categories.map(cat => (
                      <button
                        key={cat.id}
                        onClick={() => {
                          navigate(`/category/${cat.slug}`);
                          setIsMobileMenuOpen(false);
                          setIsProductsOpen(false);
                        }}
                        className="block w-full text-left py-2 text-sm text-gray-600 hover:text-gray-900"
                      >
                        {cat.name}
                      </button>
                    ))}
                  </div>
                )}
              </div>

              <button 
                onClick={() => {
                  navigate('/iletisim');
                  setIsMobileMenuOpen(false);
                }}
                className="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
              >
                İletişim
              </button>
              
              <button 
                onClick={() => {
                  navigate('/toptan-siparis');
                  setIsMobileMenuOpen(false);
                }}
                className="mx-4 mt-4 text-center bg-gradient-to-r from-gray-900 to-gray-800 text-white px-6 py-4 rounded-xl font-medium hover:shadow-xl transition-all inline-flex items-center justify-center gap-2"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Toptan Sipariş
              </button>
            </nav>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
