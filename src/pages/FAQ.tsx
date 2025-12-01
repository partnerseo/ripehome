import { useState } from 'react';

export default function FAQ() {
  const [openIndex, setOpenIndex] = useState<number | null>(0);

  const faqs = [
    {
      question: "Toptan sipariş için minimum miktar nedir?",
      answer: "Toptan siparişlerimizde minimum miktar ürün bazında değişiklik göstermektedir. Genellikle 50 adet ve üzeri siparişlerde toptan fiyatlandırma uygulanmaktadır. Detaylı bilgi için Toptan Sipariş sayfamızdan bizimle iletişime geçebilirsiniz."
    },
    {
      question: "Ürünlerinizin kumaş kompozisyonu nedir?",
      answer: "Ürünlerimiz %100 doğal pamuk ve bambu liflerinden üretilmektedir. Tüm ürünlerimiz Oeko-Tex Standard 100 sertifikalıdır ve insan sağlığına zararlı kimyasal içermez."
    },
    {
      question: "Kargo ücreti ne kadardır?",
      answer: "500 TL ve üzeri alışverişlerde kargo ücretsizdir. 500 TL altı siparişlerde kargo ücreti 50 TL'dir. Toptan siparişlerde kargo ücreti sipariş miktarına göre değişkenlik gösterebilir."
    },
    {
      question: "Siparişim ne zaman kargoya verilir?",
      answer: "Stokta bulunan ürünler için siparişiniz aynı gün kargoya verilir. Hafta sonu verilen siparişler ertesi iş günü kargoya teslim edilir. Toptan siparişlerde teslimat süresi 3-5 iş günüdür."
    },
    {
      question: "Ürün iadesi nasıl yapılır?",
      answer: "Ürünlerinizi teslim aldığınız tarihten itibaren 14 gün içinde iade edebilirsiniz. Ürünün kullanılmamış, yıkanmamış ve orijinal ambalajında olması gerekmektedir. İade işlemi için İletişim sayfamızdan bizimle iletişime geçebilirsiniz."
    },
    {
      question: "Toptan müşteri olarak özel fiyat alabilir miyim?",
      answer: "Evet, düzenli ve yüksek miktarlı alımlar yapan toptan müşterilerimize özel fiyatlandırma ve ödeme koşulları sunuyoruz. Detaylı bilgi için satış ekibimizle görüşebilirsiniz."
    },
    {
      question: "Yurt dışına kargo gönderiyor musunuz?",
      answer: "Evet, toptan siparişler için yurt dışı kargo hizmeti sunuyoruz. Kargo ücreti ve teslimat süresi hedef ülkeye göre değişiklik göstermektedir. Detaylı bilgi için bizimle iletişime geçebilirsiniz."
    },
    {
      question: "Ürünlerin bakım talimatları nelerdir?",
      answer: "Ürünlerimiz 40 derecede yıkanabilir. Çamaşır suyu kullanmayınız. Orta ısıda ütülenebilir. İlk yıkamada renk verme ihtimaline karşı beyaz ürünlerle birlikte yıkamayınız."
    },
    {
      question: "Fatura düzenliyor musunuz?",
      answer: "Evet, tüm siparişlerimizde e-fatura düzenlemekteyiz. Kurumsal müşterilerimiz için gerekli tüm belgelendirme işlemlerini yapıyoruz."
    },
    {
      question: "Özel tasarım veya logo baskısı yapıyor musunuz?",
      answer: "Evet, toptan siparişlerde özel tasarım, renk ve logo baskısı hizmeti sunuyoruz. Minimum sipariş miktarı için satış ekibimizle görüşebilirsiniz."
    }
  ];

  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            FAQ
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            Sıkça Sorulan Sorular
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto">
          <div className="space-y-4">
            {faqs.map((faq, index) => (
              <div
                key={index}
                className="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100"
              >
                <button
                  onClick={() => setOpenIndex(openIndex === index ? null : index)}
                  className="w-full px-8 py-6 text-left flex justify-between items-center hover:bg-gray-50 transition"
                >
                  <span className="text-lg font-medium text-gray-900 pr-4">
                    {faq.question}
                  </span>
                  <svg
                    className={`w-6 h-6 text-gray-500 transition-transform flex-shrink-0 ${
                      openIndex === index ? 'rotate-180' : ''
                    }`}
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      strokeLinecap="round"
                      strokeLinejoin="round"
                      strokeWidth={2}
                      d="M19 9l-7 7-7-7"
                    />
                  </svg>
                </button>
                {openIndex === index && (
                  <div className="px-8 pb-6">
                    <p className="text-gray-600 leading-relaxed">{faq.answer}</p>
                  </div>
                )}
              </div>
            ))}
          </div>

          <div className="mt-12 text-center bg-gray-50 rounded-2xl p-8">
            <h3 className="text-xl font-medium text-gray-900 mb-4">
              Sorunuz yanıtlanmadı mı?
            </h3>
            <p className="text-gray-600 mb-6">
              Müşteri hizmetlerimiz size yardımcı olmaktan mutluluk duyar.
            </p>
            <a
              href="/iletisim"
              className="inline-flex items-center gap-2 bg-gray-900 text-white px-8 py-4 rounded-xl font-medium hover:bg-gray-800 transition"
            >
              Bize Ulaşın
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </a>
          </div>
        </div>
      </div>
    </div>
  );
}
