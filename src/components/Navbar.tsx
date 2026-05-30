import { useState, useEffect } from 'react';
import { Menu, X, ShoppingBag, User, LogOut } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { getCategories } from '../lib/api';
import { useCart } from '../context/CartContext';
import { useAuth } from '../context/AuthContext';
import { useLang, useLangNavigate } from '../hooks/useLang';
import LanguageSwitcher from './LanguageSwitcher';
import type { Category } from '../types/api';

const Navbar = () => {
  const navigate = useLangNavigate();
  const lang = useLang();
  const { t } = useTranslation();
  const { cartCount } = useCart();
  const { member, isAuthenticated, isLoading: authLoading, openAuthModal } = useAuth();
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
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        isScrolled
          ? 'bg-white shadow-lg py-2'
          : 'bg-white shadow-md py-3'
      }`}
    >
      <div className="max-w-7xl mx-auto px-4 md:px-8 lg:px-12">
        <div className="relative flex items-center justify-between">
          {/* Logo + Made in Turkey (desktop) */}
          <div className="flex items-center gap-3">
            <button onClick={() => navigate('/')} className="flex items-center">
              <img
                src="/ripehomelogo.jpg"
                alt="Ripe Home Logo"
                className="h-10 md:h-12 lg:h-14 w-auto object-contain transition-all duration-300 hover:opacity-80"
              />
            </button>
            <div className="flex flex-col items-center gap-px pl-3 border-l-2 border-[#C9B7A1]">
              <span className="font-sans text-[7px] tracking-[0.25em] uppercase text-neutral-400 leading-none">MADE IN</span>
              <div className="flex items-center gap-1 mt-[2px]">
                <svg width="10" height="10" viewBox="0 0 20 20" className="flex-shrink-0">
                  <circle cx="10" cy="10" r="9" fill="#E30A17"/>
                  <circle cx="8.5" cy="10" r="5.5" fill="white"/>
                  <circle cx="10" cy="10" r="4" fill="#E30A17"/>
                  <polygon points="14,10 15.5,8 15.5,12" fill="white"/>
                </svg>
                <span className="font-serif text-[13px] tracking-[0.12em] text-neutral-800 leading-none font-medium">TURKEY</span>
              </div>
            </div>
          </div>

          {/* Desktop Nav */}
          <nav className="hidden xl:flex items-center gap-6">
            <button
              onClick={() => navigate('/')}
              className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide relative group whitespace-nowrap"
            >
              {t('nav.home')}
              <span className="absolute -bottom-1 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
            </button>

            <button
              onClick={() => navigate('/hakkimizda')}
              className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide relative group whitespace-nowrap"
            >
              {t('nav.about')}
              <span className="absolute -bottom-1 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
            </button>

            {/* Ürünlerimiz Dropdown */}
            <div
              className="relative"
              onMouseEnter={() => setIsProductsOpen(true)}
              onMouseLeave={() => setIsProductsOpen(false)}
            >
              <button className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide flex items-center gap-1.5 group whitespace-nowrap">
                {t('nav.products')}
                <svg
                  className={`w-3.5 h-3.5 transition-transform duration-300 ${isProductsOpen ? 'rotate-180' : ''}`}
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              {/* Dropdown Menu */}
              {isProductsOpen && categories.length > 0 && (
                <div className="absolute top-full left-1/2 -translate-x-1/2 pt-4">
                  <div className="bg-white rounded-2xl shadow-2xl border border-gray-100 w-72 py-3 max-h-[32rem] overflow-y-auto">
                    <div className="px-5 pb-2 border-b border-gray-100">
                      <p className="text-xs uppercase tracking-wider text-gray-500 font-medium">
                        {t('nav.categories')}
                      </p>
                    </div>
                    {categories.map(cat => (
                      <button
                        key={cat.id}
                        onClick={() => {
                          navigate(`/kategori/${cat.slug}`);
                          setIsProductsOpen(false);
                        }}
                        className="block w-full text-left px-5 py-3 hover:bg-gray-50 transition-colors group/item"
                      >
                        <div className="flex items-center justify-between">
                          <div>
                            <div className="font-medium text-gray-900 text-sm">
                              {cat.name}
                            </div>
                            {cat.products_count > 0 && (
                              <div className="text-xs text-gray-500 mt-0.5">
                                {t('nav.productCount', { count: cat.products_count })}
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
              onClick={() => navigate('/blog')}
              className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide relative group whitespace-nowrap"
            >
              {t('nav.blog')}
              <span className="absolute -bottom-1 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
            </button>

            <button
              onClick={() => navigate('/sss')}
              className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide relative group whitespace-nowrap"
            >
              {t('faq.label')}
              <span className="absolute -bottom-1 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
            </button>

            <button
              onClick={() => navigate('/iletisim')}
              className="text-sm font-medium text-gray-700 hover:text-gray-900 tracking-wide relative group whitespace-nowrap"
            >
              {t('nav.contact')}
              <span className="absolute -bottom-1 left-0 w-0 h-[2px] bg-gray-900 transition-all duration-300 group-hover:w-full"></span>
            </button>

            {/* Ayırıcı */}
            <div className="h-5 w-px bg-gray-200"></div>

            {/* Language Switcher */}
            <LanguageSwitcher />

            {/* Üye Giriş / Profil */}
            {authLoading ? (
              /* Auth yüklenirken iskelet — "Giriş" gösterme */
              <div className="w-16 h-6 bg-gray-100 rounded-full animate-pulse" />
            ) : isAuthenticated ? (
              <button
                onClick={() => navigate('/uye-paneli')}
                className="flex items-center gap-1.5 text-sm font-medium text-gray-700 hover:text-gray-900 transition whitespace-nowrap"
              >
                <div className="w-7 h-7 bg-gray-900 rounded-full flex items-center justify-center flex-shrink-0">
                  <span className="text-white text-[11px] font-bold">{member?.ad?.[0]?.toUpperCase()}</span>
                </div>
                <span>{member?.ad}</span>
              </button>
            ) : (
              <button
                onClick={openAuthModal}
                className="flex items-center gap-1.5 text-sm font-medium text-gray-700 hover:text-gray-900 transition whitespace-nowrap"
              >
                <User className="w-4 h-4" />
                <span>{t('nav.login')}</span>
              </button>
            )}

            {/* Toptan Sipariş */}
            <button
              onClick={() => navigate('/toptan-siparis')}
              className={`group relative inline-flex items-center gap-2 px-4 py-2.5 text-white rounded-xl text-sm font-medium transition-all duration-300 whitespace-nowrap flex-shrink-0 ${
                cartCount > 0
                  ? 'bg-[#8B7355] hover:bg-[#6F5C46] shadow-lg shadow-[#8B7355]/40 animate-pulse-slow'
                  : 'bg-gray-900 hover:bg-gray-700'
              }`}
            >
              <span className="relative">
                <ShoppingBag className="w-4 h-4" />
                {cartCount > 0 && (
                  <span className="absolute -top-2 -right-2 bg-white text-[#8B7355] text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                    {cartCount}
                  </span>
                )}
              </span>
              <span>{cartCount > 0 ? `${cartCount} ürün — Sipariş Ver` : t('nav.wholesale')}</span>
            </button>
          </nav>

          {/* Mobile: Language + Menu Button */}
          <div className="xl:hidden flex items-center gap-2">
            <LanguageSwitcher />
            <button
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
            >
              {isMobileMenuOpen ? (
                <X className="w-6 h-6 text-neutral-800" />
              ) : (
                <Menu className="w-6 h-6 text-neutral-800" />
              )}
            </button>
          </div>
        </div>

        {/* Mobile Menu */}
        {isMobileMenuOpen && (
          <div className="xl:hidden border-t border-gray-100 py-4 bg-white mt-4">
            <nav className="flex flex-col space-y-2">
              <button
                onClick={() => {
                  navigate('/');
                  setIsMobileMenuOpen(false);
                }}
                className="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
              >
                {t('nav.home')}
              </button>

              <button
                onClick={() => {
                  navigate('/hakkimizda');
                  setIsMobileMenuOpen(false);
                }}
                className="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
              >
                {t('nav.about')}
              </button>

              <div>
                <button
                  onClick={() => setIsProductsOpen(!isProductsOpen)}
                  className="w-full text-left px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg flex justify-between items-center transition"
                >
                  {t('nav.products')}
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
                          navigate(`/kategori/${cat.slug}`);
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
                  navigate('/blog');
                  setIsMobileMenuOpen(false);
                }}
                className="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
              >
                {t('nav.blog')}
              </button>

              <button
                onClick={() => {
                  navigate('/sss');
                  setIsMobileMenuOpen(false);
                }}
                className="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
              >
                {t('faq.label')}
              </button>

              <button
                onClick={() => {
                  navigate('/iletisim');
                  setIsMobileMenuOpen(false);
                }}
                className="px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition text-left"
              >
                {t('nav.contact')}
              </button>

              <button
                onClick={() => {
                  navigate('/toptan-siparis');
                  setIsMobileMenuOpen(false);
                }}
                className="mx-4 mt-4 text-center bg-gradient-to-r from-gray-900 to-gray-800 text-white px-6 py-4 rounded-xl font-medium hover:shadow-xl transition-all inline-flex items-center justify-center gap-2"
              >
                <span className="relative">
                  <ShoppingBag className="w-5 h-5" />
                  {cartCount > 0 && (
                    <span className="absolute -top-2 -right-2 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                      {cartCount}
                    </span>
                  )}
                </span>
                {t('nav.wholesale')}
              </button>
            </nav>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
