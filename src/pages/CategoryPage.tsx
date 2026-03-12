import { useState, useEffect } from 'react';
import { ArrowLeft, Filter, Grid3X3, List, ChevronDown, Star } from 'lucide-react';
import { useNavigate, useParams } from 'react-router-dom';
import { getCategory, getProductsByCategory } from '../lib/api';
import type { Category, Product } from '../types/api';

const CategoryPage = () => {
  const navigate = useNavigate();
  const { slug } = useParams<{ slug: string }>();
  const [viewMode, setViewMode] = useState<'grid' | 'list'>('grid');
  const [sortBy, setSortBy] = useState('popular');
  const [showFilters, setShowFilters] = useState(false);
  const [category, setCategory] = useState<Category | null>(null);
  const [products, setProducts] = useState<Product[]>([]);
  const [meta, setMeta] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [page, setPage] = useState(1);

  // Slug değişince page'i sıfırla
  useEffect(() => {
    setPage(1);
  }, [slug]);

  // Data fetching
  useEffect(() => {
    if (!slug) {
      console.log('❌ No slug provided!');
      return;
    }

    console.log(`🔍 CategoryPage - Loading page ${page} for ${slug}`);
    setLoading(true);

    getProductsByCategory(slug, page, 100)
      .then(response => {
        console.log('✅ API Response:', response);
        console.log('📦 Products loaded:', response.products?.length || 0);
        console.log('📊 Total products:', response.meta?.total || 0);
        console.log(`📄 Page ${page}/${response.meta?.last_page || 1}`);
        
        // Kategoriyi sadece ilk yüklemede veya yoksa set et
        if (page === 1 || !category) {
          setCategory(response.category);
        }
        
        setProducts(response.products || []);
        setMeta(response.meta);
      })
      .catch(error => {
        console.error('❌ Error:', error);
      })
      .finally(() => {
        setLoading(false);
        console.log('✔️ Loading completed');
      });
  }, [slug, page]); // category dependency YOK!

  // Sayfa değişince yukarı scroll
  useEffect(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }, [page]);

  const fallbackProducts = [
    {
      id: 1,
      name: 'Premium Pamuk Nevresim Takımı',
      image: '/pexels-cottonbro-4327012.jpg',
      rating: 4.8,
      reviewCount: 127,
      features: ['%100 Pamuk', 'Hipoalerjenik', 'Nefes Alabilir'],
      sizes: ['Tek Kişilik', 'Çift Kişilik', 'King Size']
    },
    {
      id: 2,
      name: 'Organik Keten Nevresim Seti',
      image: '/pexels-shvetsa-5069401.jpg',
      rating: 4.9,
      reviewCount: 89,
      features: ['%100 Keten', 'Organik', 'Antibakteriyel'],
      sizes: ['Çift Kişilik', 'King Size']
    },
    {
      id: 3,
      name: 'Muslin Nevresim Takımı',
      image: '/pexels-olly-3765034.jpg',
      rating: 4.7,
      reviewCount: 156,
      features: ['Muslin Dokuma', 'Yumuşak', 'Çabuk Kurur'],
      sizes: ['Tek Kişilik', 'Çift Kişilik']
    },
    {
      id: 4,
      name: 'Bambu Nevresim Seti',
      image: '/pexels-lamiko-3754698.jpg',
      rating: 4.6,
      reviewCount: 203,
      features: ['Bambu Lifi', 'Çevre Dostu', 'Termostat'],
      sizes: ['Çift Kişilik', 'King Size']
    },
    {
      id: 5,
      name: 'Premium Saten Nevresim',
      image: '/pexels-heyho-7746572.jpg',
      rating: 4.9,
      reviewCount: 78,
      features: ['Saten Dokuma', 'İpeksi Doku', 'Lüks'],
      sizes: ['Çift Kişilik', 'King Size']
    },
    {
      id: 6,
      name: 'Çocuk Nevresim Takımı',
      image: '/pexels-rdne-6724448.jpg',
      rating: 4.8,
      reviewCount: 94,
      features: ['Çocuk Dostu', 'Renkli', 'Güvenli Boyalar'],
      sizes: ['Tek Kişilik']
    }
  ];

  const filters = {
    material: ['Pamuk', 'Keten', 'Muslin', 'Bambu', 'Saten'],
    size: ['Tek Kişilik', 'Çift Kişilik', 'King Size'],
    features: ['Hipoalerjenik', 'Organik', 'Antibakteriyel', 'Çevre Dostu']
  };

  const sortOptions = [
    { value: 'popular', label: 'En Popüler' },
    { value: 'newest', label: 'En Yeni' },
    { value: 'rating', label: 'En Yüksek Puan' },
    { value: 'name', label: 'A-Z' }
  ];

  if (loading && page === 1 && !category) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20">
        {/* Skeleton Header */}
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

  if (!category) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20 flex items-center justify-center">
        <div className="text-center">
          <h1 className="font-serif text-3xl text-neutral-800 mb-4">Kategori Bulunamadı</h1>
          <button
            onClick={() => navigate('/')}
            className="px-6 py-3 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300"
          >
            Ana Sayfaya Dön
          </button>
        </div>
      </div>
    );
  }

  const displayProducts = products.length > 0 ? products : [];
  
  console.log('🎨 Rendering CategoryPage');
  console.log('📦 Products from API:', products.length);
  console.log('📊 Total in DB:', meta?.total || 0);
  console.log('🎭 Displaying products:', displayProducts.length);
  console.log('📂 Category:', category?.name);

  return (
    <div className="min-h-screen bg-[#F8F6F3] pt-20">
      {/* Hero Section */}
      <div className="relative h-64 md:h-80 overflow-hidden bg-gradient-to-br from-gray-200 to-gray-300">
        {category.image ? (
          <img
            src={category.image}
            alt={category.name}
            className="w-full h-full object-cover"
            onError={(e) => {
              console.error('❌ Kategori resmi yüklenemedi:', category.image);
              e.currentTarget.style.display = 'none';
            }}
            onLoad={() => {
              console.log('✅ Kategori resmi yüklendi:', category.image);
            }}
          />
        ) : (
          <div className="w-full h-full flex items-center justify-center bg-gradient-to-br from-[#E5DDD1] to-[#C9B7A1]">
            <div className="text-white text-6xl opacity-20">📦</div>
          </div>
        )}
        <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent" />
        <div className="absolute inset-0 flex items-center justify-center text-center px-6">
          <div>
            <h1 className="font-serif text-4xl md:text-5xl text-white mb-4 font-light">
              {category.name}
            </h1>
            <p className="font-sans text-white/90 text-lg max-w-2xl mx-auto">
              {category.description || `Özenle seçilmiş ${category.name.toLowerCase()} ürünleri`}
            </p>
            {meta && meta.total > 0 && (
              <p className="font-sans text-white/80 text-sm mt-3">
                {meta.total} ürün
              </p>
            )}
          </div>
        </div>
      </div>

      {/* Back Button & Breadcrumb */}
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-6">
        <div className="flex items-center justify-between mb-4">
          <button
            onClick={() => navigate(-1)}
            className="flex items-center gap-2 text-neutral-600 hover:text-neutral-800 transition-colors duration-300"
          >
            <ArrowLeft className="w-5 h-5" />
            <span className="font-sans text-sm">Geri Dön</span>
          </button>
          <div className="text-sm text-neutral-500 font-sans">
            <span>Ana Sayfa</span> / <span className="text-neutral-800">{category.name}</span>
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
              <span className="font-sans text-sm">Filtrele</span>
            </button>
            <div className="font-sans text-sm text-neutral-600">
              {meta ? (
                <>
                  <span className="font-medium">{meta.total}</span> ürün bulundu
                  {meta.total > displayProducts.length && (
                    <span className="text-amber-600 ml-2">
                      (İlk {displayProducts.length} gösteriliyor)
                    </span>
                  )}
                </>
              ) : (
                <span>{displayProducts.length} ürün bulundu</span>
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

        <div className="flex gap-8">
          {/* Filters Sidebar */}
          {showFilters && (
            <div className="w-64 space-y-6">
              <div className="bg-white p-6 rounded-lg shadow-sm">
                <h3 className="font-serif text-lg text-neutral-800 mb-4">Malzeme</h3>
                <div className="space-y-2">
                  {filters.material.map((material) => (
                    <label key={material} className="flex items-center gap-2 cursor-pointer">
                      <input type="checkbox" className="rounded border-[#E5DDD1]" />
                      <span className="font-sans text-sm text-neutral-700">{material}</span>
                    </label>
                  ))}
                </div>
              </div>

              <div className="bg-white p-6 rounded-lg shadow-sm">
                <h3 className="font-serif text-lg text-neutral-800 mb-4">Boyut</h3>
                <div className="space-y-2">
                  {filters.size.map((size) => (
                    <label key={size} className="flex items-center gap-2 cursor-pointer">
                      <input type="checkbox" className="rounded border-[#E5DDD1]" />
                      <span className="font-sans text-sm text-neutral-700">{size}</span>
                    </label>
                  ))}
                </div>
              </div>

              <div className="bg-white p-6 rounded-lg shadow-sm">
                <h3 className="font-serif text-lg text-neutral-800 mb-4">Özellikler</h3>
                <div className="space-y-2">
                  {filters.features.map((feature) => (
                    <label key={feature} className="flex items-center gap-2 cursor-pointer">
                      <input type="checkbox" className="rounded border-[#E5DDD1]" />
                      <span className="font-sans text-sm text-neutral-700">{feature}</span>
                    </label>
                  ))}
                </div>
              </div>
            </div>
          )}

          {/* Products Grid/List */}
          <div className="flex-1">
            {displayProducts.length === 0 ? (
              <div className="bg-white rounded-lg shadow-sm p-12 text-center">
                <div className="max-w-md mx-auto">
                  <div className="text-6xl mb-4">📦</div>
                  <h3 className="font-serif text-2xl text-neutral-800 mb-2">
                    Ürün Bulunamadı
                  </h3>
                  <p className="font-sans text-neutral-600 mb-6">
                    Bu kategoride henüz ürün bulunmamaktadır.
                  </p>
                  <button
                    onClick={() => navigate('/')}
                    className="px-6 py-3 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300 rounded-lg"
                  >
                    Ana Sayfaya Dön
                  </button>
                </div>
              </div>
            ) : viewMode === 'grid' ? (
              <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {displayProducts.map((product) => (
                  <div
                    key={product.id}
                    onClick={() => navigate(`/urun/${product.slug}`)}
                    className="group bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
                  >
                    <div className="aspect-square overflow-hidden">
                      <img
                        src={product.images?.[0] || '/pexels-cottonbro-4327012.jpg'}
                        alt={product.name}
                        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                      />
                    </div>
                    <div className="p-4">
                      <h3 className="font-sans text-neutral-800 font-medium mb-3 group-hover:text-[#8B7355] transition-colors duration-300">
                        {product.name}
                      </h3>
                      
                      {/* Features - Detaylı Liste */}
                      {product.features && Array.isArray(product.features) && product.features.length > 0 && (
                        <div className="mb-3 space-y-1.5">
                          {product.features.slice(0, 3).map((feature, index) => (
                            <div key={index} className="flex items-start gap-2 text-sm">
                              <span className="text-[#8B7355] mt-0.5 flex-shrink-0">✓</span>
                              <div className="flex-1 min-w-0">
                                <p className="font-medium text-neutral-700 text-xs leading-tight">
                                  {feature.title || feature}
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
                        <div className="flex flex-wrap gap-1">
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
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <div className="space-y-4">
                {displayProducts.map((product) => (
                  <div
                    key={product.id}
                    onClick={() => navigate(`/urun/${product.slug}`)}
                    className="group bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
                  >
                    <div className="flex gap-4 p-4">
                      <div className="w-32 h-32 flex-shrink-0 overflow-hidden rounded-lg">
                        <img
                          src={product.images?.[0] || '/pexels-cottonbro-4327012.jpg'}
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
                              {feature.title || feature}
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
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}
            
            {/* Loading Overlay */}
            {loading && page > 1 && (
              <div className="flex justify-center items-center py-12">
                <div className="flex items-center gap-3">
                  <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-[#8B7355]"></div>
                  <span className="text-neutral-600 font-sans">Yükleniyor...</span>
                </div>
              </div>
            )}
            
            {/* Pagination */}
            {meta && meta.last_page > 1 && (
              <div className="mt-12 flex justify-center items-center gap-2">
                {/* Previous Button */}
                <button
                  onClick={() => setPage(p => Math.max(1, p - 1))}
                  disabled={page === 1 || loading}
                  className="px-4 py-2 border border-[#E5DDD1] rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#F8F6F3] transition-colors duration-300 font-sans text-sm"
                >
                  ← Önceki
                </button>

                {/* Page Numbers */}
                <div className="flex gap-1">
                  {[...Array(meta.last_page)].map((_, i) => {
                    const pageNum = i + 1;
                    
                    // Sadece ilk 2, son 2 ve current civarını göster
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
                    } else if (
                      pageNum === page - 2 ||
                      pageNum === page + 2
                    ) {
                      return <span key={pageNum} className="px-2 text-neutral-400">...</span>;
                    }
                    return null;
                  })}
                </div>

                {/* Next Button */}
                <button
                  onClick={() => setPage(p => Math.min(meta.last_page, p + 1))}
                  disabled={page === meta.last_page || loading}
                  className="px-4 py-2 border border-[#E5DDD1] rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#F8F6F3] transition-colors duration-300 font-sans text-sm"
                >
                  Sonraki →
                </button>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default CategoryPage;
