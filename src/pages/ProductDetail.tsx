import { useState, useEffect } from 'react';
import { ArrowLeft, Star, Info, Award, Leaf } from 'lucide-react';
import { useNavigate, useParams } from 'react-router-dom';
import { getProduct } from '../lib/api';
import type { Product } from '../types/api';

const ProductDetail = () => {
  const navigate = useNavigate();
  const { slug } = useParams<{ slug: string }>();
  const [selectedImage, setSelectedImage] = useState(0);
  const [product, setProduct] = useState<Product | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function fetchProduct() {
      if (!slug) return;
      
      try {
        const data = await getProduct(slug);
        setProduct(data);
      } catch (error) {
        console.error('Ürün yüklenemedi:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchProduct();
  }, [slug]);

  const fallbackProduct = {
    id: 1,
    name: 'Premium Pamuk Nevresim Takımı',
    description: 'Doğal pamuk liflerinden üretilen bu premium nevresim takımı, yumuşak dokusu ve nefes alabilir yapısı ile konforlu bir uyku deneyimi sunar.',
    longDescription: 'Bu özel nevresim takımı, %100 organik pamuktan üretilmiştir. Hipoalerjenik özelliği sayesinde hassas ciltler için uygundur. Çevre dostu boyalarla renklendirilmiş olup, yıkamada renk atmaz ve uzun yıllar kullanılabilir.',
    images: [
      '/pexels-cottonbro-4327012.jpg',
      '/pexels-shvetsa-5069401.jpg',
      '/pexels-olly-3765034.jpg',
      '/pexels-lamiko-3754698.jpg'
    ],
    features: [
      '%100 Organik Pamuk',
      'Hipoalerjenik',
      'Nefes Alabilir',
      'Çevre Dostu Boyalar',
      'Kolay Bakım'
    ],
    specifications: {
      'Malzeme': '%100 Organik Pamuk',
      'Boyutlar': 'Tek Kişilik, Çift Kişilik, King Size',
      'Bakım': 'Makinede yıkanabilir (30°C)',
      'Menşei': 'Türkiye',
      'Sertifika': 'OEKO-TEX Standard 100'
    },
    careInstructions: [
      'Makinede 30°C\'de yıkayın',
      'Benzer renklerle yıkayın',
      'Çamaşır suyu kullanmayın',
      'Orta ısıda ütüleyebilirsiniz',
      'Gölgede kurutun'
    ],
    rating: 4.8,
    reviewCount: 127,
    category: 'Nevresim Setleri'
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-12">
          <div className="animate-pulse grid lg:grid-cols-2 gap-12">
            <div>
              <div className="aspect-square bg-gray-200 rounded-2xl mb-4"></div>
              <div className="grid grid-cols-4 gap-3">
                {[...Array(4)].map((_, i) => (
                  <div key={i} className="aspect-square bg-gray-200 rounded-lg"></div>
                ))}
              </div>
            </div>
            <div className="space-y-6">
              <div className="h-12 bg-gray-200 rounded"></div>
              <div className="h-6 bg-gray-200 rounded"></div>
              <div className="h-32 bg-gray-200 rounded"></div>
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
          <h1 className="font-serif text-3xl text-neutral-800 mb-4">Ürün Bulunamadı</h1>
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

  const displayProduct = product || fallbackProduct;
  const productImages = displayProduct.images && displayProduct.images.length > 0 
    ? displayProduct.images 
    : ['/pexels-cottonbro-4327012.jpg'];

  return (
    <div className="min-h-screen bg-[#F8F6F3] pt-20">
      {/* Back Button */}
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-6">
        <button
          onClick={() => navigate(-1)}
          className="flex items-center gap-2 text-neutral-600 hover:text-neutral-800 transition-colors duration-300"
        >
          <ArrowLeft className="w-5 h-5" />
          <span className="font-sans text-sm">Geri Dön</span>
        </button>
      </div>

      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 pb-16">
        <div className="grid lg:grid-cols-2 gap-12">
          {/* Product Images */}
          <div className="space-y-4">
            {/* Main Image */}
            <div className="aspect-square overflow-hidden rounded-2xl bg-white">
              <img
                src={productImages[selectedImage]}
                alt={displayProduct.name}
                className="w-full h-full object-cover"
              />
            </div>
            
            {/* Thumbnail Images */}
            <div className="grid grid-cols-4 gap-3">
              {productImages.map((image, index) => (
                <button
                  key={index}
                  onClick={() => setSelectedImage(index)}
                  className={`aspect-square overflow-hidden rounded-lg transition-all duration-300 ${
                    selectedImage === index 
                      ? 'ring-2 ring-[#8B7355] ring-offset-2' 
                      : 'hover:opacity-80'
                  }`}
                >
                  <img
                    src={image}
                    alt={`${displayProduct.name} ${index + 1}`}
                    className="w-full h-full object-cover"
                  />
                </button>
              ))}
            </div>
          </div>

          {/* Product Info */}
          <div className="space-y-6">
            {/* Breadcrumb */}
            <div className="text-sm text-neutral-500 font-sans">
              <span>Ana Sayfa</span> 
              {displayProduct.category && <> / <span>{displayProduct.category.name}</span></>} 
              / <span className="text-neutral-800">{displayProduct.name}</span>
            </div>

            {/* Title */}
            <div>
              <h1 className="font-serif text-3xl md:text-4xl text-neutral-800 mb-3 font-light">
                {displayProduct.name}
              </h1>
              {displayProduct.is_featured && (
                <span className="inline-block px-3 py-1 bg-[#D4AF37] text-white text-xs font-sans rounded-full">
                  Öne Çıkan Ürün
                </span>
              )}
            </div>

            {/* Description */}
            <div className="space-y-4">
              <div 
                className="font-sans text-neutral-700 leading-relaxed prose prose-sm max-w-none"
                dangerouslySetInnerHTML={{ __html: displayProduct.description || '' }}
              />
            </div>

            {/* Features */}
            {displayProduct.features && displayProduct.features.length > 0 && (
              <div>
                <h3 className="font-serif text-xl text-neutral-800 mb-3 font-light">Özellikler</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                  {displayProduct.features.map((feature, index) => (
                    <div key={index} className="flex items-start gap-3 bg-white p-4 rounded-lg border border-neutral-200">
                      <svg className="w-5 h-5 text-[#8B7355] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fillRule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clipRule="evenodd"/>
                      </svg>
                      <div className="flex-1">
                        <h4 className="font-sans font-semibold text-neutral-800">{feature.title}</h4>
                        {feature.description && (
                          <p className="font-sans text-sm text-neutral-600 mt-1">{feature.description}</p>
                        )}
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {/* Tags */}
            {displayProduct.tags && displayProduct.tags.length > 0 && (
              <div>
                <h3 className="font-serif text-xl text-neutral-800 mb-3">Etiketler</h3>
                <div className="flex flex-wrap gap-2">
                  {displayProduct.tags.map((tag) => (
                    <span
                      key={tag.id}
                      className="px-3 py-1 bg-white text-neutral-700 font-sans text-sm rounded-full border-2"
                      style={{ borderColor: tag.color, color: tag.color }}
                    >
                      {tag.name}
                    </span>
                  ))}
                </div>
              </div>
            )}

            {/* Quality Assurance */}
            <div>
              <h3 className="font-serif text-xl text-neutral-800 mb-4 flex items-center gap-2">
                <Award className="w-5 h-5 text-[#8B7355]" />
                Kalite Güvencesi
              </h3>
              <div className="bg-white rounded-lg p-4 space-y-3">
                <div className="flex items-center gap-3">
                  <div className="w-2 h-2 bg-green-500 rounded-full" />
                  <span className="font-sans text-sm text-neutral-700">Kaliteli malzemeler</span>
                </div>
                <div className="flex items-center gap-3">
                  <div className="w-2 h-2 bg-green-500 rounded-full" />
                  <span className="font-sans text-sm text-neutral-700">Zararlı kimyasal içermez</span>
                </div>
                <div className="flex items-center gap-3">
                  <div className="w-2 h-2 bg-green-500 rounded-full" />
                  <span className="font-sans text-sm text-neutral-700">Sürdürülebilir üretim</span>
                </div>
                <div className="flex items-center gap-3">
                  <div className="w-2 h-2 bg-green-500 rounded-full" />
                  <span className="font-sans text-sm text-neutral-700">Türkiye'de üretilmiştir</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ProductDetail;
