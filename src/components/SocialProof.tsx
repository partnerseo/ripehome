import { Instagram } from 'lucide-react';

const instagramPosts = [
  '/pexels-cottonbro-4327012.jpg',
  '/pexels-shvetsa-5069401.jpg',
  '/pexels-tima-miroshnichenko-4794900.jpg',
  '/pexels-ivan-samkov-7901227.jpg',
  '/pexels-rachel-claire-4819835.jpg',
  '/pexels-lamiko-3754698.jpg',
];

const SocialProof = () => {
  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-[#F8F6F3]">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-8 md:mb-16">
          <div className="inline-flex items-center gap-3 mb-4">
            <Instagram className="w-8 h-8 text-[#8B7355]" strokeWidth={1.5} />
            <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl text-neutral-800 font-light">
              Bizi Instagram'da Keşfedin
            </h2>
          </div>
          <p className="font-sans text-neutral-600 text-lg">
            @luxuryhometextiles
          </p>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-6 gap-4">
          {instagramPosts.map((image, index) => (
            <div
              key={index}
              className="group relative aspect-square overflow-hidden cursor-pointer bg-white"
            >
              <img
                src={image}
                alt={`Instagram post ${index + 1}`}
                className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
              />
              <div className="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                <Instagram className="w-8 h-8 text-white" />
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default SocialProof;
