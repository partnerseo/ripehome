import { ArrowUpRight, Tag } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import type { Category, FeaturedSection } from '../types/api';

interface CategoryGridProps {
  categories: Category[];
  featuredSections?: FeaturedSection[];
}

const gradients = [
  'from-[#F8F6F3] to-[#E5DDD1]',
  'from-[#E5DDD1] to-[#D4C5B5]',
  'from-[#D4C5B5] to-[#C9B7A1]',
  'from-[#C9B7A1] to-[#B8A48E]',
];

const CategoryGrid = ({ categories, featuredSections = [] }: CategoryGridProps) => {
  const navigate = useNavigate();

  const handleCategoryClick = (slug: string) => {
    navigate(`/kategori/${slug}`);
  };

  if (!categories || categories.length === 0) {
    return (
      <section className="pt-8 pb-16 md:pt-12 md:pb-24 px-4 md:px-12 lg:px-24 bg-white">
        <div className="max-w-7xl mx-auto text-center">
          <div className="text-xl text-neutral-500">Kategori bulunamadı</div>
        </div>
      </section>
    );
  }

  return (
    <section className="pt-8 pb-16 md:pt-12 md:pb-24 px-4 md:px-12 lg:px-24 bg-white">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-12 md:mb-20">
          <div className="inline-flex items-center gap-2 mb-6">
            <div className="w-12 h-px bg-[#C9B7A1]" />
            <Tag className="w-5 h-5 text-[#8B7355]" strokeWidth={1.5} />
            <div className="w-12 h-px bg-[#C9B7A1]" />
          </div>
          <h2 className="font-serif text-4xl md:text-5xl lg:text-7xl text-neutral-800 mb-6 font-light">
            Koleksiyonlarımız
          </h2>
          <p className="font-sans text-neutral-600 text-xl max-w-2xl mx-auto leading-relaxed">
            İhtiyacınıza özel, özenle hazırlanmış ürün koleksiyonlarımızı keşfedin
          </p>
        </div>

        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 md:gap-6 mb-12">
          {categories.map((category) => (
            <div
              key={category.id}
              onClick={() => handleCategoryClick(category.slug)}
              className="group relative cursor-pointer"
            >
              <div className="relative aspect-square overflow-hidden rounded-2xl mb-4 bg-[#F8F6F3]">
                <img
                  src={category.image || '/pexels-cottonbro-4327012.jpg'}
                  alt={category.name}
                  className="w-full h-full object-cover transition-all duration-700 group-hover:scale-110 group-hover:brightness-75"
                />

                <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />

                <div className="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                  <div className="bg-white text-neutral-800 rounded-full p-3 transform translate-y-4 group-hover:translate-y-0 transition-transform duration-500">
                    <ArrowUpRight className="w-6 h-6" strokeWidth={1.5} />
                  </div>
                </div>

                <div className="absolute top-0 left-0 w-full h-full border-2 border-[#D4AF37] rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"
                     style={{ transform: 'scale(1.05)' }} />
              </div>

              <div className="text-center">
                <h3 className="font-sans text-neutral-800 font-medium mb-1 tracking-wide group-hover:text-[#8B7355] transition-colors duration-300 text-sm md:text-base">
                  {category.name}
                </h3>
                <p className="font-sans text-neutral-500 text-xs md:text-sm">
                  {category.products_count ? `${category.products_count} Ürün` : 'Ürünler'}
                </p>
              </div>
            </div>
          ))}
        </div>

        {/* Öne Çıkan Bölümler - Admin panelden yönetilir */}
        {featuredSections.length > 0 && (
          <div className={`grid md:grid-cols-${Math.min(featuredSections.length, 3)} gap-6 mt-16`}>
            {featuredSections.map((section, index) => (
              <button
                key={section.id}
                onClick={() => {
                  if (section.link) {
                    if (section.link.startsWith('http')) {
                      window.open(section.link, '_blank');
                    } else {
                      navigate(section.link);
                    }
                  }
                }}
                className={`bg-gradient-to-br ${gradients[index % gradients.length]} p-8 rounded-2xl text-center hover:shadow-xl hover:scale-105 transition-all duration-300 cursor-pointer`}
              >
                {section.image ? (
                  <div className="w-12 h-12 rounded-full overflow-hidden mx-auto mb-4">
                    <img src={section.image} alt={section.title} className="w-full h-full object-cover" />
                  </div>
                ) : (
                  <div className="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg className="w-6 h-6 text-[#8B7355]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                  </div>
                )}
                <h3 className="font-serif text-2xl text-neutral-800 mb-2">{section.title}</h3>
                {section.description && (
                  <p className="font-sans text-neutral-600 text-sm mb-4">{section.description}</p>
                )}
                {section.link && (
                  <span className="font-sans text-sm text-[#8B7355] hover:underline inline-flex items-center gap-1">
                    {section.button_text || 'Keşfet'}
                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                    </svg>
                  </span>
                )}
              </button>
            ))}
          </div>
        )}
      </div>
    </section>
  );
};

export default CategoryGrid;
