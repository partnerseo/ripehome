import { useNavigate } from 'react-router-dom';
import { ArrowRight, Award, Leaf, Users, Factory, Globe, Heart, Shield, Truck } from 'lucide-react';

const stats = [
  { value: '15+', label: 'Yıllık Deneyim' },
  { value: '500+', label: 'Toptan Müşteri' },
  { value: '50+', label: 'Ülkeye İhracat' },
  { value: '10.000+', label: 'Ürün Çeşidi' },
];

const values = [
  {
    icon: Leaf,
    title: 'Doğal Hammadde',
    description: 'Tüm ürünlerimizde %100 doğal pamuk, bambu ve keten lifleri kullanıyoruz. Doğaya saygılı üretim anlayışımızdan asla ödün vermiyoruz.',
  },
  {
    icon: Award,
    title: 'Sertifikalı Kalite',
    description: 'Oeko-Tex Standard 100 sertifikalı ürünlerimiz, insan sağlığına zararlı hiçbir kimyasal madde içermez.',
  },
  {
    icon: Shield,
    title: 'Güvenilir Tedarik',
    description: 'Zamanında teslimat ve tutarlı kalite ile iş ortaklarımıza her zaman güvenilir bir tedarikçi oluyoruz.',
  },
  {
    icon: Users,
    title: 'Müşteri Odaklı',
    description: 'Her müşterimize özel çözümler sunuyor, ihtiyaçlarına göre kişiselleştirilmiş hizmet veriyoruz.',
  },
  {
    icon: Factory,
    title: 'Kendi Üretimimiz',
    description: 'Denizli\'deki modern üretim tesislerimizde, son teknoloji makinelerle kaliteli üretim gerçekleştiriyoruz.',
  },
  {
    icon: Globe,
    title: 'Küresel Vizyon',
    description: '50\'den fazla ülkeye ihracat yaparak Türk ev tekstilini dünyaya tanıtıyoruz.',
  },
];

const timeline = [
  { year: '2009', title: 'Kuruluş', description: 'Denizli\'de küçük bir atölyede ev tekstili üretimine başladık.' },
  { year: '2013', title: 'İlk İhracat', description: 'Avrupa pazarına ilk ihracatımızı gerçekleştirdik.' },
  { year: '2016', title: 'Fabrika Yatırımı', description: 'Modern üretim tesisimizi kurarak kapasitemizi 5 katına çıkardık.' },
  { year: '2019', title: 'Sertifikasyon', description: 'Oeko-Tex Standard 100 ve ISO 9001 sertifikalarımızı aldık.' },
  { year: '2022', title: 'Marka Dönüşümü', description: 'Ripe Home markasıyla premium segmentte konumlandık.' },
  { year: '2024', title: 'Dijital Dönüşüm', description: 'Online toptan satış platformumuzu hayata geçirdik.' },
];

export default function About() {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-[#F8F6F3]">
      {/* Hero */}
      <section className="relative pt-20">
        <div className="relative h-[400px] md:h-[500px] overflow-hidden">
          <img
            src="https://images.unsplash.com/photo-1558618666-fcd25c85f82e?w=1600&auto=format&fit=crop"
            alt="Ripe Home Üretim"
            className="w-full h-full object-cover"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-black/20" />
          <div className="absolute inset-0 flex items-center justify-center">
            <div className="text-center px-4">
              <p className="font-sans text-white/80 text-sm tracking-[0.3em] uppercase mb-4">Denizli'den Dünyaya</p>
              <h1 className="font-serif text-4xl md:text-6xl lg:text-7xl text-white font-light leading-tight mb-6">
                Doğallık ve Kalite<br />Bir Arada
              </h1>
              <p className="font-sans text-white/90 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed">
                15 yılı aşkın deneyimimizle, doğal liflerden üretilen premium ev tekstili ürünlerini dünyaya sunuyoruz.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Bar */}
      <section className="relative -mt-16 z-10">
        <div className="max-w-5xl mx-auto px-4">
          <div className="bg-white rounded-2xl shadow-xl grid grid-cols-2 md:grid-cols-4 divide-x divide-neutral-100">
            {stats.map((stat, i) => (
              <div key={i} className="text-center py-8 px-4">
                <div className="font-serif text-3xl md:text-4xl text-[#8B7355] font-light">{stat.value}</div>
                <div className="font-sans text-sm text-neutral-500 mt-1">{stat.label}</div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Hikayemiz */}
      <section className="py-20 md:py-28">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">
          <div className="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            <div>
              <p className="font-sans text-[#8B7355] text-sm tracking-[0.2em] uppercase mb-4">Hikayemiz</p>
              <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light leading-tight mb-6">
                Denizli'nin Bereketli Topraklarından<br />Evinize Uzanan Yolculuk
              </h2>
              <div className="space-y-4 font-sans text-neutral-600 leading-relaxed">
                <p>
                  <strong className="text-neutral-800">Ripe Home</strong>, Türkiye'nin tekstil başkenti Denizli'de doğdu.
                  Kuruluşumuzdan bu yana, doğal liflerin eşsiz dokusunu modern tasarımlarla buluşturarak
                  evinize konfor ve şıklık katıyoruz.
                </p>
                <p>
                  Pamuk, keten, bambu ve muslin gibi doğal hammaddelerden ürettiğimiz nevresim takımları,
                  bornozlar, havlular ve yatak örtüleri; hem estetiği hem de işlevselliği bir arada sunuyor.
                  Her ürünümüz, ustalarımızın elinden geçerek sizlere ulaşıyor.
                </p>
                <p>
                  Toptan satış alanında edindiğimiz güçlü itibar, kaliteden ödün vermeden rekabetçi fiyatlar
                  sunma becerimizden kaynaklanıyor. Otel zincirleri, perakende mağazalar ve butik markalar
                  başta olmak üzere yüzlerce iş ortağımıza hizmet veriyoruz.
                </p>
              </div>
            </div>
            <div className="relative">
              <div className="rounded-2xl overflow-hidden shadow-lg">
                <img
                  src="https://images.unsplash.com/photo-1620874131372-1b0b01401bfb?w=800&auto=format&fit=crop"
                  alt="Doğal kumaşlar"
                  className="w-full h-[400px] md:h-[500px] object-cover"
                />
              </div>
              <div className="absolute -bottom-6 -left-6 bg-[#8B7355] text-white rounded-xl p-6 shadow-lg hidden md:block">
                <div className="font-serif text-3xl font-light">2009</div>
                <div className="font-sans text-sm text-white/80">yılından beri</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Misyon & Vizyon */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">
          <div className="grid md:grid-cols-2 gap-8">
            <div className="bg-gradient-to-br from-[#F8F6F3] to-white rounded-2xl p-8 md:p-10 border border-neutral-100">
              <div className="w-12 h-12 bg-[#8B7355]/10 rounded-xl flex items-center justify-center mb-5">
                <Heart className="w-6 h-6 text-[#8B7355]" />
              </div>
              <h3 className="font-serif text-2xl text-neutral-800 mb-4">Misyonumuz</h3>
              <p className="font-sans text-neutral-600 leading-relaxed">
                Doğal ve sürdürülebilir hammaddelerden ürettiğimiz yüksek kaliteli ev tekstili ürünleriyle,
                yaşam alanlarını daha konforlu ve estetik hale getirmek. İş ortaklarımıza güvenilir tedarik,
                rekabetçi fiyat ve kusursuz hizmet sunarak uzun vadeli değer yaratmak.
              </p>
            </div>
            <div className="bg-gradient-to-br from-[#F8F6F3] to-white rounded-2xl p-8 md:p-10 border border-neutral-100">
              <div className="w-12 h-12 bg-[#8B7355]/10 rounded-xl flex items-center justify-center mb-5">
                <Globe className="w-6 h-6 text-[#8B7355]" />
              </div>
              <h3 className="font-serif text-2xl text-neutral-800 mb-4">Vizyonumuz</h3>
              <p className="font-sans text-neutral-600 leading-relaxed">
                Türk ev tekstili sektöründe uluslararası arenada söz sahibi bir marka olmak.
                Sürdürülebilir üretim pratikleri ve yenilikçi tasarım anlayışımızla,
                dünya genelinde tercih edilen bir premium ev tekstili markası haline gelmek.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Değerlerimiz */}
      <section className="py-20 md:py-28">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">
          <div className="text-center mb-14">
            <p className="font-sans text-[#8B7355] text-sm tracking-[0.2em] uppercase mb-4">Neden Biz?</p>
            <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">
              Değerlerimiz ve Farkımız
            </h2>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {values.map((item, i) => (
              <div
                key={i}
                className="bg-white rounded-2xl p-7 border border-neutral-100 hover:shadow-lg hover:border-[#E5DDD1] transition-all duration-300 group"
              >
                <div className="w-12 h-12 bg-[#F8F6F3] rounded-xl flex items-center justify-center mb-5 group-hover:bg-[#8B7355]/10 transition-colors duration-300">
                  <item.icon className="w-6 h-6 text-[#8B7355]" />
                </div>
                <h3 className="font-serif text-xl text-neutral-800 mb-3">{item.title}</h3>
                <p className="font-sans text-neutral-500 text-sm leading-relaxed">{item.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Yolculuğumuz - Timeline */}
      <section className="py-16 bg-white">
        <div className="max-w-4xl mx-auto px-4 md:px-12">
          <div className="text-center mb-14">
            <p className="font-sans text-[#8B7355] text-sm tracking-[0.2em] uppercase mb-4">Kilometre Taşları</p>
            <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">
              Yolculuğumuz
            </h2>
          </div>
          <div className="relative">
            {/* Timeline line */}
            <div className="absolute left-6 md:left-1/2 top-0 bottom-0 w-px bg-[#E5DDD1] md:-translate-x-px" />

            <div className="space-y-8">
              {timeline.map((item, i) => (
                <div key={i} className={`relative flex items-start gap-6 md:gap-0 ${i % 2 === 0 ? 'md:flex-row' : 'md:flex-row-reverse'}`}>
                  {/* Content */}
                  <div className={`flex-1 ${i % 2 === 0 ? 'md:pr-12 md:text-right' : 'md:pl-12'}`}>
                    <div className="bg-[#F8F6F3] rounded-xl p-5 border border-neutral-100 hover:shadow-md transition-all duration-300">
                      <span className="font-serif text-2xl text-[#8B7355]">{item.year}</span>
                      <h4 className="font-sans font-medium text-neutral-800 mt-1">{item.title}</h4>
                      <p className="font-sans text-sm text-neutral-500 mt-1 leading-relaxed">{item.description}</p>
                    </div>
                  </div>

                  {/* Dot */}
                  <div className="absolute left-6 md:left-1/2 w-3 h-3 bg-[#8B7355] rounded-full -translate-x-1.5 mt-7 ring-4 ring-[#F8F6F3] z-10" />

                  {/* Spacer for other side */}
                  <div className="hidden md:block flex-1" />
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Toptan Hizmet */}
      <section className="py-20 md:py-28">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="order-2 lg:order-1">
              <div className="rounded-2xl overflow-hidden shadow-lg">
                <img
                  src="https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&auto=format&fit=crop"
                  alt="Premium ev tekstili"
                  className="w-full h-[400px] object-cover"
                />
              </div>
            </div>
            <div className="order-1 lg:order-2">
              <p className="font-sans text-[#8B7355] text-sm tracking-[0.2em] uppercase mb-4">Toptan Hizmetlerimiz</p>
              <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light leading-tight mb-6">
                İşletmenize Özel<br />Çözümler Sunuyoruz
              </h2>
              <div className="space-y-4">
                {[
                  { icon: Truck, text: 'Türkiye genelinde ve 50+ ülkeye hızlı teslimat' },
                  { icon: Users, text: 'Özel fiyat teklifi ve kişiselleştirilmiş hizmet' },
                  { icon: Factory, text: 'Fason üretim ve özel etiket (private label) imkanı' },
                  { icon: Shield, text: 'Sertifikalı ürünler ve kalite garantisi' },
                ].map((item, i) => (
                  <div key={i} className="flex items-start gap-4">
                    <div className="w-10 h-10 bg-[#8B7355]/10 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                      <item.icon className="w-5 h-5 text-[#8B7355]" />
                    </div>
                    <p className="font-sans text-neutral-600 leading-relaxed">{item.text}</p>
                  </div>
                ))}
              </div>
              <button
                onClick={() => navigate('/toptan-siparis')}
                className="mt-8 inline-flex items-center gap-2 bg-[#8B7355] text-white px-8 py-4 rounded-xl font-sans font-medium hover:bg-[#6F5C46] transition-colors shadow-lg shadow-[#8B7355]/20"
              >
                Toptan Sipariş Ver
                <ArrowRight className="w-4 h-4" />
              </button>
            </div>
          </div>
        </div>
      </section>

      {/* İletişim CTA */}
      <section className="bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 py-16">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="font-serif text-3xl md:text-4xl text-white font-light mb-4">
            Birlikte Çalışalım
          </h2>
          <p className="font-sans text-white/70 text-lg mb-8 max-w-2xl mx-auto">
            İşletmenize özel fiyat teklifi almak veya ürünlerimiz hakkında bilgi edinmek için
            bizimle iletişime geçin.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button
              onClick={() => navigate('/iletisim')}
              className="inline-flex items-center justify-center gap-2 bg-white text-neutral-800 px-8 py-4 rounded-xl font-sans font-medium hover:bg-neutral-100 transition-colors"
            >
              İletişime Geç
              <ArrowRight className="w-4 h-4" />
            </button>
            <a
              href="tel:+905345730669"
              className="inline-flex items-center justify-center gap-2 border-2 border-white/30 text-white px-8 py-4 rounded-xl font-sans font-medium hover:bg-white/10 transition-colors"
            >
              +90 534 573 06 69
            </a>
          </div>
        </div>
      </section>

      {/* İletişim Bilgileri */}
      <section className="py-16 bg-white">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="grid md:grid-cols-3 gap-8 text-center">
            <div>
              <div className="w-12 h-12 bg-[#F8F6F3] rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg className="w-5 h-5 text-[#8B7355]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </div>
              <h4 className="font-sans font-medium text-neutral-800 mb-2">Adresimiz</h4>
              <p className="font-sans text-sm text-neutral-500 leading-relaxed">
                Sevindik Mahallesi, 2291 Sokak,<br />No: 7, Merkezefendi, Denizli
              </p>
            </div>
            <div>
              <div className="w-12 h-12 bg-[#F8F6F3] rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg className="w-5 h-5 text-[#8B7355]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
              </div>
              <h4 className="font-sans font-medium text-neutral-800 mb-2">E-posta</h4>
              <a href="mailto:info@ripehome.com.tr" className="font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors">
                info@ripehome.com.tr
              </a>
            </div>
            <div>
              <div className="w-12 h-12 bg-[#F8F6F3] rounded-xl flex items-center justify-center mx-auto mb-4">
                <svg className="w-5 h-5 text-[#8B7355]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
              </div>
              <h4 className="font-sans font-medium text-neutral-800 mb-2">Telefon</h4>
              <a href="tel:+905345730669" className="font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors">
                +90 534 573 06 69
              </a>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
