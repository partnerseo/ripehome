import { useState, useEffect } from 'react';
import { ArrowLeft, Filter, Grid3X3, List, ChevronDown, RefreshCw, ShoppingBag, Plus, Check } from 'lucide-react';
import { useParams, useSearchParams, useLocation } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useLangNavigate, useLang } from '../hooks/useLang';
import { slugFor } from '../lib/routes';
import { applyHead, setPrerenderReady } from '../lib/seo';
import { getProductsByCategory } from '../lib/api';

const IS_PRERENDER =
  typeof navigator !== 'undefined' && /PrerenderBot/i.test(navigator.userAgent);
import { useCart } from '../context/CartContext';
import type { Category, Product } from '../types/api';
import PriceDisplay from '../components/PriceDisplay';

const CategoryPage = () => {
  const { t } = useTranslation();
  const navigate = useLangNavigate();
  const lang = useLang();
  const location = useLocation();
  const { slug } = useParams<{ slug: string }>();
  const { cart, addToCart, cartCount } = useCart();
  const [searchParams, setSearchParams] = useSearchParams();
  const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
  const [sortBy, setSortBy] = useState('popular');
  const [showFilters, setShowFilters] = useState(false);
  const [category, setCategory] = useState<Category | null>(null);
  const [products, setProducts] = useState<Product[]>([]);
  const [meta, setMeta] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(false);

  const page = parseInt(searchParams.get('page') || '1', 10);
  const scrollKey = `category-scroll-${slug}`;

  const setPage = (p: number | ((prev: number) => number)) => {
    const next = typeof p === 'function' ? p(page) : p;
    sessionStorage.removeItem(scrollKey); // sayfa değişince scroll sıfırla
    setSearchParams(next === 1 ? {} : { page: String(next) }, { replace: false });
  };

  // Slug değişince sıfırla
  useEffect(() => {
    setCategory(null);
    setProducts([]);
    setMeta(null);
    setError(false);
  }, [slug]);

  // Data fetching
  useEffect(() => {
    if (!slug) return;

    setLoading(true);
    setError(false);

    getProductsByCategory(slug, page, 24)
      .then(response => {
        if (page === 1 || !category) {
          setCategory(response.category);
        }
        setProducts(response.products || []);
        setMeta(response.meta);
      })
      .catch(() => {
        setError(true);
      })
      .finally(() => {
        setLoading(false);
      });
  }, [slug, page]);

  // SEO: kategori yüklenince title/desc/canonical + non-canonical slug temizliği
  useEffect(() => {
    if (!category) return;
    const cslug = (category as { slug?: string }).slug || slug || '';
    const canonical = `/${lang}/${slugFor('category', lang)}/${cslug}`;
    applyHead({
      pathname: location.pathname,
      lang,
      title: (category as { meta_title?: string }).meta_title || category.name,
      description: ((category as { meta_description?: string; description?: string }).meta_description
        || (category as { description?: string }).description || '')
        .replace(/<[^>]*>/g, '').slice(0, 160) || undefined,
      canonicalPath: canonical,
    });
    setPrerenderReady(true);
    if (slug && cslug && slug !== cslug && !IS_PRERENDER) {
      navigate(`/${slugFor('category', lang)}/${cslug}`, { replace: true });
    }
  }, [category, lang]);

  // Ürünler yüklenince kaydedilen scroll pozisyonuna dön
  useEffect(() => {
    if (loading || products.length === 0) return;
    const saved = sessionStorage.getItem(scrollKey);
    if (saved) {
      sessionStorage.removeItem(scrollKey);
      requestAnimationFrame(() => {
        window.scrollTo({ top: parseInt(saved, 10), behavior: 'instant' as ScrollBehavior });
      });
    }
  }, [loading, products]);

  // Sayfa değişince yukarı scroll (scroll restore yoksa)
  useEffect(() => {
    if (!sessionStorage.getItem(scrollKey)) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }, [page]);

  const sortOptions = [
    { value: 'popular', label: t('category.sortPopular') },
    { value: 'newest', label: t('category.sortNewest') },
    { value: 'rating', label: t('category.sortRating') },
    { value: 'name', label: t('category.sortName') }
  ];

  // İlk yükleme skeleton
  if (loading && !category) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20">
        <div className="relative h-64 md:h-80 bg-gradient-to-br from-gray-200 to-gray-300 animate-pulse">
          <div className="absolute inset-0 flex items-center justify-center">
            <div className="text-center space-y-4">
              <div className="h-12 w-64 bg-gray-300 rounded mx-auto"></div>
              <div className="h-6 w-96 bg-gray-300 rounded mx-auto"></div>
            </div>
          </div>
        </div>
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-12">
          <div className="animate-pulse">
            <div className="h-8 bg-gray-200 rounded w-64 mb-8"></div>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {[...Array(6)].map((_, i) => (
                <div key={i} className="bg-white rounded-lg overflow-hidden">
                  <div className="aspect-square bg-gray-200"></div>
                  <div className="p-4 space-y-3">
                    <div className="h-4 bg-gray-200 rounded"></div>
                    <div className="h-4 bg-gray-200 rounded w-3/4"></div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    );
  }

  // Hata durumu
  if (error) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20 flex items-center justify-center">
        <div className="text-center px-4">
          <h1 className="font-serif text-2xl text-neutral-800 mb-3">{t('common.loadError', 'Yükleme hatası')}</h1>
          <p className="font-sans text-neutral-500 mb-6 text-sm">{t('common.tryAgain', 'Lütfen tekrar deneyin.')}</p>
          <div className="flex gap-3 justify-center">
            <button
              onClick={() => { setError(false); setLoading(true); setPage(1); }}
              className="inline-flex items-center gap-2 px-6 py-3 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300 rounded-lg text-sm"
            >
              <RefreshCw className="w-4 h-4" />
              {t('common.retry', 'Yeniden Dene')}
            </button>
            <button
              onClick={() => navigate('/')}
              className="px-6 py-3 border border-[#8B7355] text-[#8B7355] hover:bg-[#F8F6F3] transition-colors duration-300 rounded-lg text-sm"
            >
              {t('common.goHome')}
            </button>
          </div>
        </div>
      </div>
    );
  }

  // Kategori bulunamadı
  if (!category) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20 flex items-center justify-center">
        <div className="text-center">
          <h1 className="font-serif text-3xl text-neutral-800 mb-4">{t('category.notFound')}</h1>
          <button
            onClick={() => navigate('/')}
            className="px-6 py-3 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300"
          >
            {t('common.goHome')}
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#F8F6F3] pt-20">
      {/* Hero Section */}
      <div className="relative h-64 md:h-80 overflow-hidden bg-gradient-to-br from-[#E5DDD1] to-[#C9B7A1]">
        {category.image && (
          <img
            src={category.image}
            alt={category.name}
            className="w-full h-full object-cover"
            onError={(e) => { e.currentTarget.style.display = 'none'; }}
          />
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
        <div className="absolute inset-0 flex items-center justify-center text-center px-6">
          <div>
            <h1 className="font-serif text-4xl md:text-5xl text-white mb-3 font-light">
              {category.name}
            </h1>
            {meta && meta.total > 0 && (
              <p className="font-sans text-white/80 text-sm">
                {t('nav.productCount', { count: meta.total })}
              </p>
            )}
          </div>
        </div>
      </div>

      {/* Category Description */}
      {category.description && (
        <div className="bg-white border-b border-neutral-100">
          <div className="max-w-4xl mx-auto px-4 md:px-12 lg:px-24 py-6">
            <p className="font-sans text-neutral-600 leading-relaxed text-sm md:text-base text-center">
              {category.description}
            </p>
          </div>
        </div>
      )}

      {/* Back Button & Breadcrumb */}
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-6">
        <div className="flex items-center justify-between mb-4">
          <button
            onClick={() => navigate(-1)}
            className="flex items-center gap-2 text-neutral-600 hover:text-neutral-800 transition-colors duration-300"
          >
            <ArrowLeft className="w-5 h-5" />
            <span className="font-sans text-sm">{t('common.back')}</span>
          </button>
          <div className="text-sm text-neutral-500 font-sans">
            <span>{t('common.homePage')}</span> / <span className="text-neutral-800">{category.name}</span>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 pb-16">
        {/* Filters & Sort Bar */}
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 p-4 bg-white rounded-lg shadow-sm">
          <div className="flex items-center gap-4">
            <button
              onClick={() => setShowFilters(!showFilters)}
              className="flex items-center gap-2 px-4 py-2 border border-[#E5DDD1] rounded-lg hover:bg-[#F8F6F3] transition-colors duration-300"
            >
              <Filter className="w-4 h-4" />
              <span className="font-sans text-sm">{t('common.filter')}</span>
            </button>
            <div className="font-sans text-sm text-neutral-600">
              {meta ? (
                <span><span className="font-medium">{meta.total}</span> {t('common.productsFound')}</span>
              ) : (
                <span>{products.length} {t('common.productsFound')}</span>
              )}
            </div>
          </div>

          <div className="flex items-center gap-4">
            {/* Sort Dropdown */}
            <div className="relative">
              <select
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
                className="appearance-none bg-white border border-[#E5DDD1] rounded-lg px-4 py-2 pr-8 font-sans text-sm focus:outline-none focus:border-[#C9B7A1]"
              >
                {sortOptions.map((option) => (
                  <option key={option.value} value={option.value}>
                    {option.label}
                  </option>
                ))}
              </select>
              <ChevronDown className="absolute right-2 top-1/2 transform -translate-y-1/2 w-4 h-4 text-neutral-500 pointer-events-none" />
            </div>

            {/* View Mode Toggle */}
            <div className="flex border border-[#E5DDD1] rounded-lg overflow-hidden">
              <button
                onClick={() => setViewMode('grid')}
                className={`p-2 transition-colors duration-300 ${
                  viewMode === 'grid' ? 'bg-[#8B7355] text-white' : 'text-neutral-600 hover:bg-[#F8F6F3]'
                }`}
              >
                <Grid3X3 className="w-4 h-4" />
              </button>
              <button
                onClick={() => setViewMode('list')}
                className={`p-2 transition-colors duration-300 ${
                  viewMode === 'list' ? 'bg-[#8B7355] text-white' : 'text-neutral-600 hover:bg-[#F8F6F3]'
                }`}
              >
                <List className="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>

        {/* Products */}
        <div className="flex-1">
          {products.length === 0 && !loading ? (
            <div className="bg-white rounded-lg shadow-sm p-12 text-center">
              <div className="max-w-md mx-auto">
                <div className="text-6xl mb-4">📦</div>
                <h3 className="font-serif text-2xl text-neutral-800 mb-2">
                  {t('category.noProducts')}
                </h3>
                <p className="font-sans text-neutral-600 mb-6">
                  {t('category.noProductsText')}
                </p>
                <button
                  onClick={() => navigate('/')}
                  className="px-6 py-3 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300 rounded-lg"
                >
                  {t('common.goHome')}
                </button>
              </div>
            </div>
          ) : viewMode === 'grid' ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {products.map((product) => (
                <div
                  key={product.id}
                  onClick={() => { sessionStorage.setItem(scrollKey, String(window.scrollY)); navigate(`/urun/${product.slug}`); }}
                  className="group bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
                >
                  <div className="aspect-[3/4] overflow-hidden">
                    <img
                      src={product.images?.[0] || '/ripehomelogo.jpg'}
                      alt={product.name}
                      className="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500"
                    />
                  </div>
                  <div className="p-4">
                    <h3 className="font-sans text-neutral-800 font-medium mb-3 group-hover:text-[#8B7355] transition-colors duration-300">
                      {product.name}
                    </h3>

                    {/* Features */}
                    {product.features && Array.isArray(product.features) && product.features.length > 0 && (
                      <div className="mb-3 space-y-1.5">
                        {product.features.slice(0, 3).map((feature, index) => (
                          <div key={index} className="flex items-start gap-2 text-sm">
                            <span className="text-[#8B7355] mt-0.5 flex-shrink-0">✓</span>
                            <div className="flex-1 min-w-0">
                              <p className="font-medium text-neutral-700 text-xs leading-tight">
                                {feature.title || String(feature)}
                              </p>
                              {feature.description && (
                                <p className="text-neutral-500 text-xs leading-tight mt-0.5">
                                  {feature.description}
                                </p>
                              )}
                            </div>
                          </div>
                        ))}
                      </div>
                    )}

                    {/* Tags */}
                    {product.tags && product.tags.length > 0 && (
                      <div className="flex flex-wrap gap-1 mb-3">
                        {product.tags.map((tag) => (
                          <span
                            key={tag.id}
                            className="px-2 py-1 border border-[#E5DDD1] text-neutral-600 font-sans text-xs rounded"
                            style={{ borderColor: tag.color }}
                          >
                            {tag.name}
                          </span>
                        ))}
                      </div>
                    )}

                    {/* Price + Sepete Ekle */}
                    <div className="mt-auto pt-2 border-t border-neutral-100 flex items-center justify-between gap-2">
                      <PriceDisplay price={product.price} size="sm" />
                      <button
                        onClick={e => {
                          e.stopPropagation();
                          addToCart({
                            product_id: product.id,
                            product_name: product.name,
                            product_slug: product.slug,
                            product_image: product.images?.[0],
                            quantity: product.min_order || 50,
                          });
                        }}
                        className={`flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 ${
                          cart.find(i => i.product_id === product.id)
                            ? 'bg-green-100 text-green-700'
                            : 'bg-[#8B7355] text-white hover:bg-[#6F5C46]'
                        }`}
                        title="Sepete Ekle"
                      >
                        {cart.find(i => i.product_id === product.id)
                          ? <><Check className="w-3.5 h-3.5" /> Eklendi</>
                          : <><Plus className="w-3.5 h-3.5" /> Ekle</>
                        }
                      </button>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="space-y-4">
              {products.map((product) => (
                <div
                  key={product.id}
                  onClick={() => { sessionStorage.setItem(scrollKey, String(window.scrollY)); navigate(`/urun/${product.slug}`); }}
                  className="group bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
                >
                  <div className="flex gap-4 p-4">
                    <div className="w-32 h-32 flex-shrink-0 overflow-hidden rounded-lg">
                      <img
                        src={product.images?.[0] || '/ripehomelogo.jpg'}
                        alt={product.name}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                      />
                    </div>
                    <div className="flex-1 space-y-2">
                      <h3 className="font-sans text-lg text-neutral-800 font-medium group-hover:text-[#8B7355] transition-colors duration-300">
                        {product.name}
                      </h3>
                      {product.description && (
                        <p className="font-sans text-sm text-neutral-600 line-clamp-2">
                          {product.description.replace(/<[^>]*>/g, '')}
                        </p>
                      )}
                      <div className="flex flex-wrap gap-2">
                        {product.features?.slice(0, 3).map((feature, index) => (
                          <span
                            key={index}
                            className="px-3 py-1 bg-[#F8F6F3] text-neutral-600 font-sans text-sm rounded-full"
                          >
                            {feature.title || String(feature)}
                          </span>
                        ))}
                      </div>
                      {product.tags && product.tags.length > 0 && (
                        <div className="flex flex-wrap gap-2">
                          {product.tags.map((tag) => (
                            <span
                              key={tag.id}
                              className="px-3 py-1 border text-neutral-600 font-sans text-sm rounded-full"
                              style={{ borderColor: tag.color, color: tag.color }}
                            >
                              {tag.name}
                            </span>
                          ))}
                        </div>
                      )}
                      <div className="flex items-center justify-between pt-2 border-t border-neutral-100">
                        <PriceDisplay price={product.price} size="sm" />
                        <button
                          onClick={e => {
                            e.stopPropagation();
                            addToCart({
                              product_id: product.id,
                              product_name: product.name,
                              product_slug: product.slug,
                              product_image: product.images?.[0],
                              quantity: product.min_order || 50,
                            });
                          }}
                          className={`flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${
                            cart.find(i => i.product_id === product.id)
                              ? 'bg-green-100 text-green-700'
                              : 'bg-[#8B7355] text-white hover:bg-[#6F5C46]'
                          }`}
                        >
                          {cart.find(i => i.product_id === product.id)
                            ? <><Check className="w-4 h-4" /> Eklendi</>
                            : <><Plus className="w-4 h-4" /> Sepete Ekle</>
                          }
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          )}

          {/* Loading Overlay (sayfa değişimi) */}
          {loading && page > 1 && (
            <div className="flex justify-center items-center py-12">
              <div className="flex items-center gap-3">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-[#8B7355]"></div>
                <span className="text-neutral-600 font-sans">{t('common.loading')}</span>
              </div>
            </div>
          )}

          {/* Pagination */}
          {meta && meta.last_page > 1 && (
            <div className="mt-12 flex justify-center items-center gap-2">
              <button
                onClick={() => setPage(p => Math.max(1, p - 1))}
                disabled={page === 1 || loading}
                className="px-4 py-2 border border-[#E5DDD1] rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#F8F6F3] transition-colors duration-300 font-sans text-sm"
              >
                ← {t('common.previous')}
              </button>

              <div className="flex gap-1">
                {[...Array(meta.last_page)].map((_, i) => {
                  const pageNum = i + 1;
                  if (
                    pageNum === 1 ||
                    pageNum === 2 ||
                    pageNum === meta.last_page ||
                    pageNum === meta.last_page - 1 ||
                    (pageNum >= page - 1 && pageNum <= page + 1)
                  ) {
                    return (
                      <button
                        key={pageNum}
                        onClick={() => setPage(pageNum)}
                        disabled={loading}
                        className={`w-10 h-10 rounded-lg font-sans text-sm transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed ${
                          page === pageNum
                            ? 'bg-[#8B7355] text-white shadow-md'
                            : 'border border-[#E5DDD1] hover:bg-[#F8F6F3]'
                        }`}
                      >
                        {pageNum}
                      </button>
                    );
                  } else if (pageNum === page - 2 || pageNum === page + 2) {
                    return <span key={pageNum} className="px-2 text-neutral-400">...</span>;
                  }
                  return null;
                })}
              </div>

              <button
                onClick={() => setPage(p => Math.min(meta.last_page, p + 1))}
                disabled={page === meta.last_page || loading}
                className="px-4 py-2 border border-[#E5DDD1] rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#F8F6F3] transition-colors duration-300 font-sans text-sm"
              >
                {t('common.next')} →
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Sabit Sepet Barı — sepette ürün varsa */}
      {cartCount > 0 && (
        <div className="fixed bottom-0 left-0 right-0 z-40 p-4 flex justify-center pointer-events-none">
          <div className="pointer-events-auto w-full max-w-lg bg-gray-900 text-white rounded-2xl shadow-2xl flex items-center justify-between px-5 py-3.5 gap-4">
            <div className="flex items-center gap-3">
              <div className="relative">
                <ShoppingBag className="w-5 h-5" />
                <span className="absolute -top-2 -right-2 bg-[#8B7355] text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                  {cartCount}
                </span>
              </div>
              <span className="text-sm font-medium">
                {cartCount} ürün seçildi
              </span>
            </div>
            <button
              onClick={() => navigate('/toptan-siparis')}
              className="bg-[#8B7355] hover:bg-[#6F5C46] text-white px-5 py-2 rounded-xl text-sm font-semibold transition-colors flex items-center gap-2"
            >
              Sipariş Ver →
            </button>
          </div>
        </div>
      )}
    </div>
  );
};

export default CategoryPage;
