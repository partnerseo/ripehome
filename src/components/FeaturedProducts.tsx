import { useNavigate } from 'react-router-dom';
import type { Product } from '../types/api';

interface FeaturedProductsProps {
  products: Product[];
}

const FeaturedProducts = ({ products }: FeaturedProductsProps) => {
  const navigate = useNavigate();
  
  if (products.length === 0) {
    return (
      <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-white">
        <div className="max-w-7xl mx-auto text-center">
          <div className="text-xl text-neutral-500">Öne çıkan ürün bulunamadı</div>
        </div>
      </section>
    );
  }
  
  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-white">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-8 md:mb-16">
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl text-neutral-800 mb-4 font-light">
            Öne Çıkan Ürünler
          </h2>
          <p className="font-sans text-neutral-600 text-lg">
            En çok tercih edilen koleksiyonlarımız
          </p>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
          {products.map((product) => (
            <div 
              key={product.id} 
              onClick={() => navigate(`/product/${product.slug}`)}
              className="group cursor-pointer"
            >
              <div className="relative aspect-square mb-4 overflow-hidden bg-[#F8F6F3] rounded-lg">
                <img
                  src={product.images?.[0] || '/pexels-cottonbro-4327012.jpg'}
                  alt={product.name}
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300" />
              </div>
              <h3 className="font-sans text-neutral-800 mb-2 font-light text-sm md:text-base">
                {product.name}
              </h3>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default FeaturedProducts;
