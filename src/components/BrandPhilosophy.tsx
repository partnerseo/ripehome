import { ArrowRight } from 'lucide-react';

const BrandPhilosophy = () => {
  return (
    <section className="relative h-[70vh] overflow-hidden">
      <div className="absolute inset-0">
        <img
          src="/pexels-75707588-9148737.jpg"
          alt="Brand Philosophy"
          className="w-full h-full object-cover"
        />
        <div className="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-transparent" />
      </div>

      <div className="relative h-full flex items-center px-4 md:px-12 lg:px-24">
        <div className="max-w-2xl">
          <h2 className="font-serif text-3xl md:text-5xl lg:text-7xl text-white mb-6 font-light leading-tight">
            Doğallık, Kalite ve Şıklık<br />Ev Tekstilinde Buluşuyor
          </h2>
          <p className="font-sans text-white/90 text-lg md:text-xl mb-4 leading-relaxed">
            Pamuk, keten ve muslinin zamansız dokusu evinizde.
          </p>
          <p className="font-sans text-white/80 text-base mb-8 leading-relaxed max-w-xl">
            Her ürün, doğal liflerin benzersiz dokusunu ve zanaatkarlığın inceliğini
            bir araya getiriyor. Evinize sıcaklık katarken, doğaya saygı duyarak
            üretilen koleksiyonlarımızla lüksü yeniden tanımlıyoruz.
          </p>
          <button className="group flex items-center gap-3 bg-white text-neutral-800 px-8 py-4 hover:bg-neutral-100 transition-all duration-300 hover:gap-5 mb-8">
            <span className="font-sans text-sm tracking-wide">Marka Hikayemizi Keşfet</span>
            <ArrowRight className="w-5 h-5 transition-transform group-hover:translate-x-1" />
          </button>
        </div>
      </div>
    </section>
  );
};

export default BrandPhilosophy;
