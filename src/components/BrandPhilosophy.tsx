import { ArrowRight } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

interface BrandProps {
  brand?: {
    title?: string;
    subtitle?: string;
    description?: string;
    image?: string;
    button_text?: string;
    button_link?: string;
  };
}

const BrandPhilosophy = ({ brand }: BrandProps) => {
  const navigate = useNavigate();

  const title = brand?.title || 'Doğallık, Kalite ve Şıklık\nEv Tekstilinde Buluşuyor';
  const subtitle = brand?.subtitle || 'Pamuk, keten ve muslinin zamansız dokusu evinizde.';
  const description = brand?.description || 'Her ürün, doğal liflerin benzersiz dokusunu ve zanaatkarlığın inceliğini bir araya getiriyor. Evinize sıcaklık katarken, doğaya saygı duyarak üretilen koleksiyonlarımızla lüksü yeniden tanımlıyoruz.';
  const buttonText = brand?.button_text || 'Marka Hikayemizi Keşfet';
  const buttonLink = brand?.button_link || '/hakkimizda';
  const bgImage = brand?.image || 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=1200&auto=format&fit=crop';

  const handleClick = () => {
    if (buttonLink.startsWith('http')) {
      window.open(buttonLink, '_blank');
    } else {
      navigate(buttonLink);
    }
  };

  return (
    <section className="relative h-auto md:min-h-[70vh] overflow-hidden">
      <div className="absolute inset-0">
        <img
          src={bgImage}
          alt="Brand Philosophy"
          className="w-full h-full object-cover"
          onError={(e) => {
            e.currentTarget.src = 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=1200&auto=format&fit=crop';
          }}
        />
        <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent" />
      </div>

      <div className="relative h-full flex items-center justify-start px-4 md:px-12 lg:px-24 py-12 md:py-16 lg:py-20">
        <div className="max-w-2xl w-full">
          <h2 className="font-serif text-3xl md:text-4xl lg:text-5xl xl:text-6xl text-white mb-4 md:mb-6 font-light leading-tight whitespace-pre-line">
            {title}
          </h2>
          <p className="font-sans text-white/90 text-base md:text-lg lg:text-xl mb-3 md:mb-4 leading-relaxed">
            {subtitle}
          </p>
          <p className="font-sans text-white/80 text-sm md:text-base mb-6 md:mb-8 leading-relaxed max-w-xl">
            {description}
          </p>
          <button
            onClick={handleClick}
            className="group flex items-center gap-3 bg-white text-neutral-800 px-6 md:px-8 py-3 md:py-4 hover:bg-neutral-100 transition-all duration-300 hover:gap-5 rounded-lg cursor-pointer"
          >
            <span className="font-sans text-sm tracking-wide">{buttonText}</span>
            <ArrowRight className="w-5 h-5 transition-transform group-hover:translate-x-1" />
          </button>
        </div>
      </div>
    </section>
  );
};

export default BrandPhilosophy;
