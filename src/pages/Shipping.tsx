import { useNavigate } from 'react-router-dom';
import { Truck, Clock, Package, Globe, MapPin, Phone, Mail, ArrowRight, CheckCircle, AlertCircle } from 'lucide-react';

const deliverySteps = [
  { icon: Package, title: 'Sipariş Onayı', description: 'Siparişiniz alındıktan sonra onay bilgisi gönderilir.' },
  { icon: CheckCircle, title: 'Hazırlık', description: 'Ürünleriniz özenle paketlenir ve kalite kontrolden geçirilir.' },
  { icon: Truck, title: 'Kargoya Teslim', description: 'Paketiniz anlaşmalı kargo firmasına teslim edilir.' },
  { icon: MapPin, title: 'Teslimat', description: 'Kargo takip numaranızla siparişinizi anlık takip edebilirsiniz.' },
];

const shippingZones = [
  { region: 'Marmara & Ege', duration: '1–2 iş günü', icon: '🏙️' },
  { region: 'İç Anadolu & Akdeniz', duration: '2–3 iş günü', icon: '🏔️' },
  { region: 'Karadeniz & Doğu', duration: '3–4 iş günü', icon: '🌲' },
  { region: 'Yurt Dışı (Avrupa)', duration: '5–10 iş günü', icon: '🌍' },
];

export default function Shipping() {
  const navigate = useNavigate();

  return (
    <div className="min-h-screen bg-[#F8F6F3]">
      {/* Hero */}
      <section className="relative pt-20">
        <div className="bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900 py-16 md:py-24">
          <div className="max-w-5xl mx-auto px-4 md:px-12 text-center">
            <div className="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
              <Truck className="w-8 h-8 text-white" />
            </div>
            <h1 className="font-serif text-4xl md:text-5xl lg:text-6xl text-white font-light mb-4">
              Kargo & Teslimat
            </h1>
            <p className="font-sans text-white/70 text-lg max-w-2xl mx-auto">
              Siparişleriniz özenle paketlenir ve en hızlı şekilde kapınıza ulaştırılır.
            </p>
          </div>
        </div>
      </section>

      {/* Toptan Kargo Bilgisi */}
      <section className="relative -mt-10 z-10">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="bg-white rounded-2xl shadow-xl p-8 md:p-10 border border-neutral-100">
            <div className="grid md:grid-cols-2 gap-8">
              <div className="flex items-start gap-4">
                <div className="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                  <Truck className="w-6 h-6 text-green-600" />
                </div>
                <div>
                  <h3 className="font-sans font-medium text-neutral-800 text-lg">Toptan Siparişlerde</h3>
                  <p className="font-sans text-green-600 font-medium mt-1">Kargo tamamen ücretsiz</p>
                  <p className="font-sans text-sm text-neutral-500 mt-1">Minimum sipariş tutarını karşılayan tüm toptan siparişlerde geçerlidir.</p>
                </div>
              </div>
              <div className="flex items-start gap-4">
                <div className="w-12 h-12 bg-[#8B7355]/10 rounded-xl flex items-center justify-center flex-shrink-0">
                  <Clock className="w-6 h-6 text-[#8B7355]" />
                </div>
                <div>
                  <h3 className="font-sans font-medium text-neutral-800 text-lg">Hızlı Hazırlık</h3>
                  <p className="font-sans text-[#8B7355] font-medium mt-1">1–3 iş günü içinde kargoda</p>
                  <p className="font-sans text-sm text-neutral-500 mt-1">Stoklu ürünlerde aynı gün kargoya teslim edilir.</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Sipariş Süreci */}
      <section className="py-20 md:py-28">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="text-center mb-14">
            <p className="font-sans text-[#8B7355] text-sm tracking-[0.2em] uppercase mb-4">Adım Adım</p>
            <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">Sipariş Süreci</h2>
          </div>
          <div className="grid md:grid-cols-4 gap-6">
            {deliverySteps.map((step, i) => (
              <div key={i} className="relative text-center group">
                {/* Bağlantı çizgisi */}
                {i < deliverySteps.length - 1 && (
                  <div className="hidden md:block absolute top-8 left-[60%] right-[-40%] h-px bg-[#E5DDD1]" />
                )}
                <div className="relative z-10 w-16 h-16 bg-white border-2 border-[#E5DDD1] rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:border-[#8B7355] group-hover:shadow-md transition-all duration-300">
                  <step.icon className="w-7 h-7 text-[#8B7355]" />
                </div>
                <div className="font-sans text-xs text-[#8B7355] font-medium mb-1">{i + 1}. Adım</div>
                <h3 className="font-sans font-medium text-neutral-800 mb-2">{step.title}</h3>
                <p className="font-sans text-sm text-neutral-500 leading-relaxed">{step.description}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Teslimat Süreleri */}
      <section className="py-16 bg-white">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="text-center mb-14">
            <p className="font-sans text-[#8B7355] text-sm tracking-[0.2em] uppercase mb-4">Bölgelere Göre</p>
            <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">Teslimat Süreleri</h2>
          </div>
          <div className="grid md:grid-cols-2 gap-4">
            {shippingZones.map((zone, i) => (
              <div
                key={i}
                className="flex items-center gap-5 bg-[#F8F6F3] rounded-xl p-5 border border-neutral-100 hover:shadow-md hover:border-[#E5DDD1] transition-all duration-300"
              >
                <div className="text-3xl">{zone.icon}</div>
                <div className="flex-1">
                  <h4 className="font-sans font-medium text-neutral-800">{zone.region}</h4>
                  <p className="font-sans text-sm text-neutral-500 mt-0.5">Tahmini teslimat süresi</p>
                </div>
                <div className="font-sans font-medium text-[#8B7355] text-right">{zone.duration}</div>
              </div>
            ))}
          </div>
          <p className="font-sans text-xs text-neutral-400 text-center mt-6">
            * Teslimat süreleri tahmini olup, kargo firmasının iş yoğunluğuna göre değişiklik gösterebilir.
          </p>
        </div>
      </section>

      {/* Detaylı Bilgiler */}
      <section className="py-20 md:py-28">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="grid md:grid-cols-2 gap-8">
            {/* Kargo Firmaları */}
            <div className="bg-white rounded-2xl p-7 border border-neutral-100">
              <div className="w-11 h-11 bg-[#F8F6F3] rounded-xl flex items-center justify-center mb-5">
                <Truck className="w-5 h-5 text-[#8B7355]" />
              </div>
              <h3 className="font-serif text-xl text-neutral-800 mb-3">Kargo Firmaları</h3>
              <p className="font-sans text-neutral-500 text-sm leading-relaxed mb-4">
                Siparişleriniz Aras Kargo, Yurtiçi Kargo ve MNG Kargo aracılığıyla gönderilmektedir.
                Firma seçimi lokasyonunuza göre otomatik olarak yapılır.
              </p>
              <div className="space-y-2">
                {['Kargo takip numarası SMS ve e-posta ile iletilir', 'Tüm gönderiler sigortalıdır', 'Hasarlı teslimatlarda ücretsiz yenisi gönderilir'].map((item, i) => (
                  <div key={i} className="flex items-start gap-2">
                    <CheckCircle className="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                    <span className="font-sans text-sm text-neutral-600">{item}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Toptan Sipariş */}
            <div className="bg-white rounded-2xl p-7 border border-neutral-100">
              <div className="w-11 h-11 bg-[#F8F6F3] rounded-xl flex items-center justify-center mb-5">
                <Package className="w-5 h-5 text-[#8B7355]" />
              </div>
              <h3 className="font-serif text-xl text-neutral-800 mb-3">Toptan Sipariş Teslimatı</h3>
              <p className="font-sans text-neutral-500 text-sm leading-relaxed mb-4">
                Toptan siparişleriniz profesyonelce paletlenir ve güvenli şekilde taşınır.
                Büyük hacimli gönderiler için özel nakliye düzenlenir.
              </p>
              <div className="space-y-2">
                {['Stoklu ürünlerde 1–3 iş günü hazırlık', 'Üretimli siparişlerde 2–3 hafta', 'Paletli ve streçli ambalajlama', 'Kapıda teslimat veya depo teslim seçeneği'].map((item, i) => (
                  <div key={i} className="flex items-start gap-2">
                    <CheckCircle className="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                    <span className="font-sans text-sm text-neutral-600">{item}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Yurt Dışı */}
            <div className="bg-white rounded-2xl p-7 border border-neutral-100">
              <div className="w-11 h-11 bg-[#F8F6F3] rounded-xl flex items-center justify-center mb-5">
                <Globe className="w-5 h-5 text-[#8B7355]" />
              </div>
              <h3 className="font-serif text-xl text-neutral-800 mb-3">Yurt Dışı Gönderim</h3>
              <p className="font-sans text-neutral-500 text-sm leading-relaxed mb-4">
                50'den fazla ülkeye ihracat deneyimimizle, uluslararası gönderimlerinizi sorunsuz yönetiyoruz.
              </p>
              <div className="space-y-2">
                {['FOB, CIF ve DDP teslim seçenekleri', 'Gümrük evraklarının hazırlanması', 'Konteyner ve parsiyel yükleme', 'İhracat faturası ve menşe belgesi'].map((item, i) => (
                  <div key={i} className="flex items-start gap-2">
                    <CheckCircle className="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                    <span className="font-sans text-sm text-neutral-600">{item}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Paketleme */}
            <div className="bg-white rounded-2xl p-7 border border-neutral-100">
              <div className="w-11 h-11 bg-[#F8F6F3] rounded-xl flex items-center justify-center mb-5">
                <Package className="w-5 h-5 text-[#8B7355]" />
              </div>
              <h3 className="font-serif text-xl text-neutral-800 mb-3">Ambalaj ve Paketleme</h3>
              <p className="font-sans text-neutral-500 text-sm leading-relaxed mb-4">
                Ürünleriniz taşıma sırasında zarar görmemesi için özel ambalajlanır. İsteğe göre markalı paketleme de yapılır.
              </p>
              <div className="space-y-2">
                {['Tekli poşet + kolektif kutu ambalaj', 'İsteğe bağlı markalı etiketleme', 'Barkod ve ürün bilgi kartı ekleme', 'Nem ve toz geçirmez paketleme'].map((item, i) => (
                  <div key={i} className="flex items-start gap-2">
                    <CheckCircle className="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" />
                    <span className="font-sans text-sm text-neutral-600">{item}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Önemli Bilgiler */}
      <section className="py-16 bg-white">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="text-center mb-10">
            <h2 className="font-serif text-2xl md:text-3xl text-neutral-800 font-light">Sıkça Sorulan Sorular</h2>
          </div>
          <div className="space-y-4">
            {[
              {
                q: 'Siparişimi nasıl takip edebilirim?',
                a: 'Siparişiniz kargoya verildikten sonra, kargo takip numarası SMS ve e-posta ile tarafınıza iletilir. Bu numara ile kargo firmasının web sitesinden anlık takip yapabilirsiniz.',
              },
              {
                q: 'Teslimat adresimi değiştirebilir miyim?',
                a: 'Siparişiniz kargoya teslim edilmeden önce adres değişikliği yapabilirsiniz. Bunun için müşteri hizmetlerimizle iletişime geçmeniz yeterlidir.',
              },
              {
                q: 'Hasarlı ürün teslim alırsam ne yapmalıyım?',
                a: 'Kargo teslimatı sırasında hasarlı paket teslim alırsanız, tutanak tutturarak ürünü teslim alın ve 24 saat içinde bizimle iletişime geçin. Hasarlı ürününüz ücretsiz olarak yenisiyle değiştirilir.',
              },
              {
                q: 'Toptan siparişlerde minimum tutar nedir?',
                a: 'Toptan sipariş için minimum tutar ürün kategorisine göre değişiklik gösterir. Detaylı bilgi için toptan sipariş sayfamızı ziyaret edebilir veya bizimle iletişime geçebilirsiniz.',
              },
            ].map((faq, i) => (
              <div key={i} className="bg-[#F8F6F3] rounded-xl p-6 border border-neutral-100">
                <h4 className="font-sans font-medium text-neutral-800 mb-2">{faq.q}</h4>
                <p className="font-sans text-sm text-neutral-500 leading-relaxed">{faq.a}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Uyarı */}
      <section className="py-8">
        <div className="max-w-5xl mx-auto px-4 md:px-12">
          <div className="flex items-start gap-4 bg-amber-50 border border-amber-200 rounded-xl p-5">
            <AlertCircle className="w-5 h-5 text-amber-600 mt-0.5 flex-shrink-0" />
            <div>
              <h4 className="font-sans font-medium text-amber-800 mb-1">Önemli Bilgilendirme</h4>
              <p className="font-sans text-sm text-amber-700 leading-relaxed">
                Hava koşulları, doğal afetler, resmi tatiller ve kargo firmasının iş yoğunluğu teslimat sürelerini etkileyebilir.
                Bu tür mücbir sebeplerden kaynaklanan gecikmelerde sorumluluk kargo firmasına aittir.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* İletişim CTA */}
      <section className="py-16 bg-gradient-to-br from-neutral-900 via-neutral-800 to-neutral-900">
        <div className="max-w-4xl mx-auto px-4 text-center">
          <h2 className="font-serif text-3xl md:text-4xl text-white font-light mb-4">
            Kargo Hakkında Sorunuz mu Var?
          </h2>
          <p className="font-sans text-white/70 text-lg mb-8 max-w-2xl mx-auto">
            Teslimat süreçleri veya özel kargo talepleri hakkında detaylı bilgi almak için bizimle iletişime geçin.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <button
              onClick={() => navigate('/iletisim')}
              className="inline-flex items-center justify-center gap-2 bg-white text-neutral-800 px-8 py-4 rounded-xl font-sans font-medium hover:bg-neutral-100 transition-colors"
            >
              <Mail className="w-4 h-4" />
              İletişime Geç
            </button>
            <a
              href="tel:+905345730669"
              className="inline-flex items-center justify-center gap-2 border-2 border-white/30 text-white px-8 py-4 rounded-xl font-sans font-medium hover:bg-white/10 transition-colors"
            >
              <Phone className="w-4 h-4" />
              +90 534 573 06 69
            </a>
          </div>
        </div>
      </section>
    </div>
  );
}
