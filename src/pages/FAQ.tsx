import { useState } from 'react';

export default function FAQ() {
  const [openIndex, setOpenIndex] = useState<number | null>(0);

  const faqs = [
    {
      question: "Sipariş nasıl verilir?",
      answer: "Sitemizde beğendiğiniz ürünü seçtikten sonra 'Sepete Ekle' butonuna tıklayın. Sepetinize eklediğiniz ürünleri gözden geçirdikten sonra 'Siparişi Tamamla' butonuna tıklayarak sipariş işlemlerinizi tamamlayabilirsiniz."
    },
    {
      question: "Toptan sipariş için minimum miktar nedir?",
      answer: "Toptan siparişlerimizde minimum sipariş miktarı ürüne göre değişmektedir. Detaylı bilgi için 'Toptan Sipariş' sayfamızdan bizimle iletişime geçebilir veya +90 XXX XXX XX XX numaralı telefondan ulaşabilirsiniz."
    },
    {
      question: "Kargo ücreti ne kadardır?",
      answer: "500 TL ve üzeri alışverişlerinizde kargo ücretsizdir. 500 TL'nin altındaki siparişlerde kargo ücreti 49 TL'dir. Toptan siparişlerde kargo ücretsiz gönderilmektedir."
    },
    {
      question: "Siparişim ne zaman kargoya verilir?",
      answer: "Stokta bulunan ürünler için siparişiniz onaylandıktan sonra aynı gün veya en geç 1 iş günü içerisinde kargoya teslim edilir. Toptan siparişler için teslimat süresi 3-5 iş günüdür."
    },
    {
      question: "Hangi kargo firması ile gönderim yapılıyor?",
      answer: "Siparişlerimizi MNG Kargo, Yurtiçi Kargo ve Aras Kargo ile gönderiyoruz. Kargo firması seçimi sipariş adresine ve stok durumuna göre belirlenir."
    },
    {
      question: "İade ve değişim koşulları nelerdir?",
      answer: "Ürünlerinizi teslim aldıktan sonra 14 gün içerisinde kullanılmamış, etiketli ve faturası ile birlikte iade edebilirsiniz. İade işlemi için müşteri hizmetlerimizle iletişime geçmeniz gerekmektedir."
    },
    {
      question: "Ürünlerin bakım talimatları nelerdir?",
      answer: "Ürünlerimizin çoğu 30-40 derecede yıkanabilir. Detaylı bakım talimatları ürün etiketlerinde yer almaktadır. Uzun ömürlü kullanım için ürün etiketlerindeki bakım talimatlarına uymanızı öneririz."
    },
    {
      question: "Fatura bilgilerimi nasıl güncellerim?",
      answer: "Sipariş vermeden önce fatura bilgilerinizi eksiksiz girmeniz gerekmektedir. Sipariş sonrası fatura bilgisi değişikliği için sipariş onaylanmadan önce müşteri hizmetlerimizle iletişime geçiniz."
    },
    {
      question: "Ödeme seçenekleri nelerdir?",
      answer: "Kredi kartı (taksitli/tek çekim), banka havalesi/EFT ve kapıda ödeme seçenekleri mevcuttur. Toptan siparişlerde özel ödeme koşulları için bizimle iletişime geçebilirsiniz."
    },
    {
      question: "Ürünlerin kalite sertifikaları var mı?",
      answer: "Tüm ürünlerimiz kalite standartlarına uygun olarak üretilmektedir. Ürünlerimiz Oeko-Tex Standard 100 sertifikasına sahiptir ve insan sağlığına zararlı madde içermemektedir."
    }
  ];

  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4 max-w-4xl">
        {/* Header */}
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            Help Center
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            Sıkça Sorulan Sorular
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
          <p className="text-gray-600 mt-6 text-lg">
            Merak ettiğiniz soruların cevaplarını burada bulabilirsiniz
          </p>
        </div>

        {/* FAQ List */}
        <div className="space-y-4">
          {faqs.map((faq, index) => (
            <div
              key={index}
              className="bg-white rounded-xl border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-lg"
            >
              <button
                onClick={() => setOpenIndex(openIndex === index ? null : index)}
                className="w-full px-8 py-6 text-left flex items-center justify-between gap-4 hover:bg-gray-50 transition"
              >
                <span className="text-lg font-medium text-gray-900">
                  {faq.question}
                </span>
                <svg
                  className={`w-6 h-6 text-gray-500 transition-transform duration-300 flex-shrink-0 ${
                    openIndex === index ? 'rotate-180' : ''
                  }`}
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              
              {openIndex === index && (
                <div className="px-8 pb-6 pt-2">
                  <p className="text-gray-600 leading-relaxed">
                    {faq.answer}
                  </p>
                </div>
              )}
            </div>
          ))}
        </div>

        {/* Contact Box */}
        <div className="mt-12 bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-8 text-white text-center">
          <h3 className="text-2xl font-light mb-3">
            Sorunuz mu var?
          </h3>
          <p className="text-gray-300 mb-6">
            Aradığınız soruyu bulamadınız mı? Bize ulaşın, size yardımcı olalım.
          </p>
          <a
            href="/iletisim"
            className="inline-flex items-center gap-2 bg-white text-gray-900 px-8 py-3 rounded-xl font-medium hover:bg-gray-100 transition"
          >
            Bize Ulaşın
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
            </svg>
          </a>
        </div>
      </div>
    </div>
  );
}

