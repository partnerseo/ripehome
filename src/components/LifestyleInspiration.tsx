const inspirations = [
  { title: 'Sabah Huzuru', image: '/pexels-lamiko-3754698.jpg', size: 'large' },
  { title: 'Doğallığın Dokusu', image: '/pexels-heyho-7746572.jpg', size: 'small' },
  { title: 'Minimal Şıklık', image: '/pexels-olly-3765034.jpg', size: 'small' },
  { title: 'Zamansız Konfor', image: '/pexels-rdne-6724448.jpg', size: 'medium' },
  { title: 'Saf Zarafet', image: '/pexels-rachel-claire-4819835.jpg', size: 'medium' },
];

const LifestyleInspiration = () => {
  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-[#F8F6F3]">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-8 md:mb-16">
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl text-neutral-800 mb-4 font-light">
            Yaşam Tarzı İlhamı
          </h2>
          <p className="font-sans text-neutral-600 text-lg">
            Günlük anların güzelliği
          </p>
        </div>

        <div className="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
          <div className="col-span-2 row-span-2">
            <div className="group relative h-full overflow-hidden cursor-pointer">
              <img
                src={inspirations[0].image}
                alt={inspirations[0].title}
                className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
              />
              <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
              <div className="absolute bottom-8 left-8">
                <p className="font-serif text-3xl text-white opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                  {inspirations[0].title}
                </p>
              </div>
            </div>
          </div>

          {inspirations.slice(1, 3).map((item, index) => (
            <div key={index} className="col-span-1">
              <div className="group relative h-full overflow-hidden cursor-pointer">
                <img
                  src={item.image}
                  alt={item.title}
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                <div className="absolute bottom-4 left-4">
                  <p className="font-serif text-xl text-white opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                    {item.title}
                  </p>
                </div>
              </div>
            </div>
          ))}

          {inspirations.slice(3).map((item, index) => (
            <div key={index} className="col-span-1">
              <div className="group relative h-full overflow-hidden cursor-pointer">
                <img
                  src={item.image}
                  alt={item.title}
                  className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
                <div className="absolute bottom-4 left-4">
                  <p className="font-serif text-xl text-white opacity-0 group-hover:opacity-100 transition-opacity duration-500 delay-100">
                    {item.title}
                  </p>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default LifestyleInspiration;
