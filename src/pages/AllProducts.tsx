import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { useLangNavigate, useLangPath } from '../hooks/useLang';
import { Link } from 'react-router-dom';
import { getCategories, getProductsByCategory } from '../lib/api';
import type { Category, Product } from '../types/api';

interface CategoryGroup {
  category: Category;
  products: Product[];
}

const ProductCard = ({ product, onClick }: { product: Product; onClick: () => void }) => (
  <div
    onClick={onClick}
    className="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 cursor-pointer"
  >
    <div className="aspect-[3/4] overflow-hidden">
      <img
        src={product.images?.[0] || '/pexels-cottonbro-4327012.jpg'}
        alt={product.name}
        className="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500"
        loading="lazy"
      />
    </div>
    <div className="p-3 md:p-4">
      <h3 className="font-sans text-sm md:text-base text-neutral-800 font-medium group-hover:text-[#8B7355] transition-colors line-clamp-2">
        {product.name}
      </h3>
    </div>
  </div>
);

const AllProducts = () => {
  const { t, i18n } = useTranslation();
  const navigate = useLangNavigate();
  const lp = useLangPath();
  const [groups, setGroups] = useState<CategoryGroup[]>([]);
  const [loading, setLoading] = useState(true);
  const [totalCount, setTotalCount] = useState(0);

  useEffect(() => {
    setLoading(true);
    getCategories()
      .then(async (cats) => {
        const validCats = (cats || []).filter(c => (c.products_count || 0) > 0);
        const results = await Promise.all(
          validCats.map(cat =>
            getProductsByCategory(cat.slug, 1, 200)
              .then(data => ({ category: cat, products: data.products || data.data || [] }))
              .catch(() => ({ category: cat, products: [] }))
          )
        );
        const filled = results.filter(g => g.products.length > 0);
        setGroups(filled);
        setTotalCount(filled.reduce((s, g) => s + g.products.length, 0));
      })
      .catch(() => setGroups([]))
      .finally(() => setLoading(false));
  }, [i18n.language]);

  return (
    <div className="min-h-screen bg-[#F8F6F3] pt-20">
      {/* Header */}
      <div className="bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 py-14 md:py-20">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 text-center">
          <h1 className="font-serif text-4xl md:text-5xl text-white font-light mb-3">
            {t('nav.products')}
          </h1>
          {totalCount > 0 && (
            <p className="font-sans text-white/60 text-sm">
              {t('nav.productCount', { count: totalCount })}
            </p>
          )}
        </div>
      </div>

      {/* Breadcrumb */}
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-5">
        <nav className="flex items-center gap-1.5 text-sm text-neutral-500 font-sans">
          <Link to={lp('/')} className="hover:text-neutral-800 transition-colors">{t('common.homePage')}</Link>
          <span>/</span>
          <span className="text-neutral-800 font-medium">{t('nav.products')}</span>
        </nav>
      </div>

      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 pb-16 space-y-14">
        {loading ? (
          <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            {[...Array(12)].map((_, i) => (
              <div key={i} className="animate-pulse bg-white rounded-xl overflow-hidden">
                <div className="aspect-[3/4] bg-gray-200" />
                <div className="p-3 space-y-2">
                  <div className="h-4 bg-gray-200 rounded w-3/4" />
                  <div className="h-3 bg-gray-200 rounded w-1/2" />
                </div>
              </div>
            ))}
          </div>
        ) : groups.length === 0 ? (
          <div className="text-center py-20">
            <p className="font-sans text-neutral-500">{t('category.noProducts')}</p>
          </div>
        ) : (
          groups.map(({ category, products }) => (
            <div key={category.id}>
              {/* Kategori başlığı */}
              <div className="flex items-center justify-between mb-6">
                <div>
                  <h2 className="font-serif text-2xl md:text-3xl text-neutral-800 font-light">
                    {category.name}
                  </h2>
                  <p className="font-sans text-sm text-neutral-400 mt-0.5">
                    {t('nav.productCount', { count: products.length })}
                  </p>
                </div>
                <Link
                  to={lp(`/kategori/${category.slug}`)}
                  className="font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors flex items-center gap-1"
                >
                  {t('common.seeAll')} →
                </Link>
              </div>

              <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-5">
                {products.slice(0, 10).map(product => (
                  <ProductCard
                    key={product.id}
                    product={product}
                    onClick={() => navigate(`/urun/${product.slug}`)}
                  />
                ))}
              </div>

              {products.length > 10 && (
                <div className="text-center mt-6">
                  <Link
                    to={lp(`/kategori/${category.slug}`)}
                    className="inline-flex items-center gap-2 px-6 py-2.5 border border-[#8B7355] text-[#8B7355] rounded-xl font-sans text-sm hover:bg-[#8B7355] hover:text-white transition-all duration-200"
                  >
                    {category.name} — {t('common.seeAll')} ({products.length})
                  </Link>
                </div>
              )}

              <div className="border-b border-neutral-200 mt-10" />
            </div>
          ))
        )}
      </div>
    </div>
  );
};

export default AllProducts;
