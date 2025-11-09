import { useState } from 'react';
import { ChevronLeft, ChevronRight, Star } from 'lucide-react';

const testimonials = [
  {
    name: 'Ayşe Demir',
    text: 'Pamuk nevresim takımı gerçekten harika! Hem kalitesi hem de dokusu mükemmel. Uyku kalitenizi artırıyor.',
    rating: 5,
    image: '/pexels-alex-tyson-919593032-19955727.jpg',
  },
  {
    name: 'Mehmet Yılmaz',
    text: 'Waffle bornoz aldım, kalitesi ve konforu beklediğimden çok daha iyi. Kesinlikle tavsiye ederim.',
    rating: 5,
    image: '/pexels-rodolphozanardo-1304110.jpg',
  },
  {
    name: 'Zeynep Kaya',
    text: 'Organik muslin ürünleri için aradığım tüm özellikleri buldum. Hem çevre dostu hem de lüks.',
    rating: 5,
    image: '/pexels-gabiguerino-2462996.jpg',
  },
];

const Testimonials = () => {
  const [currentIndex, setCurrentIndex] = useState(0);

  const next = () => {
    setCurrentIndex((prev) => (prev + 1) % testimonials.length);
  };

  const prev = () => {
    setCurrentIndex((prev) => (prev - 1 + testimonials.length) % testimonials.length);
  };

  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-white">
      <div className="max-w-5xl mx-auto">
        <div className="text-center mb-8 md:mb-16">
          <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl text-neutral-800 mb-4 font-light">
            Müşteri Yorumları
          </h2>
          <p className="font-sans text-neutral-600 text-lg">
            Sizin gibi memnun müşterilerimizden
          </p>
        </div>

        <div className="relative">
          <div className="bg-white rounded-lg shadow-xl p-12 max-w-3xl mx-auto">
            <div className="flex items-center mb-6">
              <img
                src={testimonials[currentIndex].image}
                alt={testimonials[currentIndex].name}
                className="w-16 h-16 rounded-full object-cover mr-4"
              />
              <div>
                <h3 className="font-serif text-2xl text-neutral-800">
                  {testimonials[currentIndex].name}
                </h3>
                <div className="flex gap-1 mt-1">
                  {[...Array(testimonials[currentIndex].rating)].map((_, i) => (
                    <Star key={i} className="w-4 h-4 fill-[#D4AF37] text-[#D4AF37]" />
                  ))}
                </div>
              </div>
            </div>
            <p className="font-sans text-neutral-700 text-lg leading-relaxed italic">
              "{testimonials[currentIndex].text}"
            </p>
          </div>

          <div className="flex justify-center gap-4 mt-8">
            <button
              onClick={prev}
              className="p-3 rounded-full bg-[#F8F6F3] hover:bg-[#E5DDD1] transition-colors duration-300"
            >
              <ChevronLeft className="w-6 h-6 text-neutral-800" />
            </button>
            <button
              onClick={next}
              className="p-3 rounded-full bg-[#F8F6F3] hover:bg-[#E5DDD1] transition-colors duration-300"
            >
              <ChevronRight className="w-6 h-6 text-neutral-800" />
            </button>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Testimonials;
