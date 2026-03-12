import { useState, useEffect } from 'react';
import { ArrowLeft, Award, Share2, ChevronRight } from 'lucide-react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { getProduct, getProductsByCategory } from '../lib/api';
import { useCart } from '../context/CartContext';
import type { Product } from '../types/api';
import ImageSwiper from '../components/ImageSwiper';

const ProductDetail = () => {
  const navigate = useNavigate();
  const { slug } = useParams<{ slug: string }>();
  const { addToCart } = useCart();
  const [product, setProduct] = useState<Product | null>(null);
  const [relatedProducts, setRelatedProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const [shared, setShared] = useState(false);

  useEffect(() => {
    async function fetchProduct() {
      if (!slug) return;
      setLoading(true);
      try {
        const data = await getProduct(slug);
        setProduct(data);

        // Benzer ürünleri çek
        if (data?.category?.slug) {
          const res = await getProductsByCategory(data.category.slug, 1, 10);
          const filtered = (res.products || []).filter((p: Product) => p.slug !== slug);
          setRelatedProducts(filtered.slice(0, 4));
        }
      } catch (error) {
        console.error('Ürün yüklenemedi:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchProduct();
  }, [slug]);

  const handleShare = async () => {
    const url = window.location.href;
    if (navigator.share) {
      try {
        await navigator.share({ title: product?.name, url });
      } catch {}
    } else {
      await navigator.clipboard.writeText(url);
      setShared(true);
      setTimeout(() => setShared(false), 2000);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-12">
          <div className="animate-pulse grid lg:grid-cols-2 gap-12">
            <div>
              <div className="aspect-square bg-gray-200 rounded-2xl mb-4"></div>
              <div className="flex gap-2">
                {[...Array(4)].map((_, i) => (
                  <div key={i} className="w-20 h-20 bg-gray-200 rounded-xl"></div>
                ))}
              </div>
            </div>
            <div className="space-y-6">
              <div className="h-5 bg-gray-200 rounded w-48"></div>
              <div className="h-10 bg-gray-200 rounded w-3/4"></div>
              <div className="h-24 bg-gray-200 rounded"></div>
              <div className="h-40 bg-gray-200 rounded"></div>
            </div>
          </div>
        </div>
      </div>
    );
  }

  if (!product) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20 flex items-center justify-center">
        <div className="text-center">
          <div className="text-6xl mb-4">🔍</div>
          <h1 className="font-serif text-3xl text-neutral-800 mb-4">Ürün Bulunamadı</h1>
          <p className="text-neutral-600 mb-6 font-sans">Aradığınız ürün mevcut değil veya kaldırılmış olabilir.</p>
          <button
            onClick={() => navigate('/')}
            className="px-8 py-3 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300 rounded-lg font-sans"
          >
            Ana Sayfaya Dön
          </button>
        </div>
      </div>
    );
  }

  const productImages = product.images && product.images.length > 0
    ? product.images
    : ['/pexels-cottonbro-4327012.jpg'];

  return (
    <div className="min-h-screen bg-[#F8F6F3] pt-20">
      {/* Top Bar: Back + Breadcrumb */}
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-6">
        <div className="flex items-center justify-between">
          <button
            onClick={() => navigate(-1)}
            className="flex items-center gap-2 text-neutral-600 hover:text-neutral-800 transition-colors duration-300"
          >
            <ArrowLeft className="w-5 h-5" />
            <span className="font-sans text-sm">Geri Dön</span>
          </button>
          <nav className="hidden sm:flex items-center gap-1.5 text-sm text-neutral-500 font-sans">
            <Link to="/" className="hover:text-neutral-800 transition-colors">Ana Sayfa</Link>
            <ChevronRight className="w-3.5 h-3.5" />
            {product.category && (
              <>
                <Link
                  to={`/kategori/${product.category.slug}`}
                  className="hover:text-neutral-800 transition-colors"
                >
                  {product.category.name}
                </Link>
                <ChevronRight className="w-3.5 h-3.5" />
              </>
            )}
            <span className="text-neutral-800 font-medium truncate max-w-[200px]">{product.name}</span>
          </nav>
        </div>
      </div>

      {/* Main Content */}
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 pb-16">
        <div className="grid lg:grid-cols-2 gap-8 lg:gap-14">
          {/* Left: Images */}
          <div className="lg:sticky lg:top-28 lg:self-start">
            <ImageSwiper
              images={productImages}
              alt={product.name}
              showArrows={true}
              showDots={true}
              showThumbnails={true}
              enableZoom={true}
            />
          </div>

          {/* Right: Product Info */}
          <div className="space-y-6">
            {/* Badge + Title */}
            <div>
              {product.is_featured && (
                <span className="inline-flex items-center gap-1.5 px-3 py-1 bg-gradient-to-r from-[#D4AF37] to-[#C5A028] text-white text-xs font-sans rounded-full mb-3">
                  <Award className="w-3.5 h-3.5" />
                  Öne Çıkan Ürün
                </span>
              )}
              <h1 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light leading-tight">
                {product.name}
              </h1>
              {product.category && (
                <Link
                  to={`/kategori/${product.category.slug}`}
                  className="inline-block mt-2 text-[#8B7355] hover:text-[#6F5C46] font-sans text-sm transition-colors"
                >
                  {product.category.name}
                </Link>
              )}
            </div>

            {/* Share Button */}
            <div className="flex gap-3">
              <button
                onClick={handleShare}
                className="flex items-center gap-2 px-4 py-2 border border-neutral-200 rounded-lg text-neutral-600 hover:bg-white hover:text-neutral-800 transition-all font-sans text-sm"
              >
                <Share2 className="w-4 h-4" />
                {shared ? 'Kopyalandı!' : 'Paylaş'}
              </button>
            </div>

            {/* Description */}
            {product.description && (
              <div className="border-t border-neutral-200 pt-6">
                <h3 className="font-serif text-lg text-neutral-800 mb-3">Ürün Açıklaması</h3>
                <div
                  className="font-sans text-neutral-600 leading-relaxed prose prose-sm max-w-none prose-headings:font-serif prose-headings:text-neutral-800"
                  dangerouslySetInnerHTML={{ __html: product.description }}
                />
              </div>
            )}

            {/* Features */}
            {product.features && product.features.length > 0 && (
              <div className="border-t border-neutral-200 pt-6">
                <h3 className="font-serif text-lg text-neutral-800 mb-4">Özellikler</h3>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                  {product.features.map((feature, index) => (
                    <div
                      key={index}
                      className="flex items-start gap-3 bg-white p-4 rounded-xl border border-neutral-100 hover:border-[#E5DDD1] hover:shadow-sm transition-all duration-300"
                    >
                      <div className="w-8 h-8 rounded-lg bg-[#F8F6F3] flex items-center justify-center flex-shrink-0">
                        <svg className="w-4 h-4 text-[#8B7355]" fill="currentColor" viewBox="0 0 20 20">
                          <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"/>
                        </svg>
                      </div>
                      <div className="flex-1 min-w-0">
                        <h4 className="font-sans font-medium text-neutral-800 text-sm">{feature.title}</h4>
                        {feature.description && (
                          <p className="font-sans text-xs text-neutral-500 mt-0.5 leading-relaxed">{feature.description}</p>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Tags */}
            {product.tags && product.tags.length > 0 && (
              <div className="border-t border-neutral-200 pt-6">
                <h3 className="font-serif text-lg text-neutral-800 mb-3">Etiketler</h3>
                <div className="flex flex-wrap gap-2">
                  {product.tags.map((tag) => (
                    <span
                      key={tag.id}
                      className="px-3 py-1.5 bg-white text-sm font-sans rounded-full border-2 transition-colors hover:bg-neutral-50"
                      style={{ borderColor: tag.color, color: tag.color }}
                    >
                      {tag.name}
                    </span>
                  ))}
                </div>
              </div>
            )}

            {/* Product Details Table */}
            <div className="border-t border-neutral-200 pt-6">
              <div className="bg-white rounded-xl border border-neutral-100 divide-y divide-neutral-100">
                <div className="flex justify-between items-center px-5 py-3.5">
                  <span className="font-sans text-sm text-neutral-500">Ürün Kodu</span>
                  <span className="font-sans text-sm font-medium text-neutral-800">
                    {product.sku || String(product.id).padStart(5, '0')}
                  </span>
                </div>
                <div className="flex justify-between items-center px-5 py-3.5">
                  <span className="font-sans text-sm text-neutral-500">Minimum Sipariş</span>
                  <span className="font-sans text-sm font-medium text-neutral-800">
                    {product.min_order || 100} adet
                  </span>
                </div>
                <div className="flex justify-between items-center px-5 py-3.5">
                  <span className="font-sans text-sm text-neutral-500">Üretim Süresi</span>
                  <span className="font-sans text-sm font-medium text-neutral-800">
                    {product.production_time || '2-3 hafta'}
                  </span>
                </div>
                <div className="flex justify-between items-center px-5 py-3.5">
                  <span className="font-sans text-sm text-neutral-500">Kapasite</span>
                  <span className={`font-sans text-sm font-medium ${
                    (product.stock ?? 0) > 0 ? 'text-green-600' : 'text-amber-600'
                  }`}>
                    {(product.stock ?? 0) > 0 ? 'Stokta mevcut' : 'Üretimde'}
                  </span>
                </div>
              </div>
            </div>

            {/* CTA */}
            <div className="border-t border-neutral-200 pt-6">
              <button
                onClick={() => {
                  addToCart({
                    product_id: product.id,
                    product_name: product.name,
                    product_slug: product.slug,
                    product_image: product.images?.[0],
                    quantity: product.min_order || 100,
                  });
                  navigate('/toptan-siparis');
                }}
                className="w-full bg-[#8B7355] text-white py-4 rounded-xl font-sans font-medium hover:bg-[#6F5C46] transition-colors duration-300 text-lg text-center block shadow-lg shadow-[#8B7355]/20 cursor-pointer"
              >
                Toptan Fiyat Teklifi Al
              </button>
              <p className="text-center text-xs text-neutral-500 font-sans mt-3">
                24 saat içinde size özel fiyat teklifi göndereceğiz
              </p>
            </div>
          </div>
        </div>

        {/* Related Products */}
        {relatedProducts.length > 0 && (
          <div className="mt-20 border-t border-neutral-200 pt-12">
            <div className="flex items-center justify-between mb-8">
              <h2 className="font-serif text-2xl md:text-3xl text-neutral-800 font-light">
                Benzer Ürünler
              </h2>
              {product.category && (
                <Link
                  to={`/kategori/${product.category.slug}`}
                  className="flex items-center gap-1 text-[#8B7355] hover:text-[#6F5C46] font-sans text-sm transition-colors"
                >
                  Tümünü Gör
                  <ChevronRight className="w-4 h-4" />
                </Link>
              )}
            </div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
              {relatedProducts.map((rp) => (
                <Link
                  key={rp.id}
                  to={`/urun/${rp.slug}`}
                  className="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300"
                >
                  <div className="aspect-square overflow-hidden">
                    <img
                      src={rp.images?.[0] || '/pexels-cottonbro-4327012.jpg'}
                      alt={rp.name}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                      loading="lazy"
                    />
                  </div>
                  <div className="p-3 md:p-4">
                    <h3 className="font-sans text-sm md:text-base text-neutral-800 font-medium group-hover:text-[#8B7355] transition-colors duration-300 line-clamp-2">
                      {rp.name}
                    </h3>
                    {rp.features && rp.features.length > 0 && (
                      <p className="font-sans text-xs text-neutral-500 mt-1 line-clamp-1">
                        {rp.features.map(f => f.title).join(' · ')}
                      </p>
                    )}
                  </div>
                </Link>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default ProductDetail;
