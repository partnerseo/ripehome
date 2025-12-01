export default function Returns() {
  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            Return Policy
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            İade & Değişim
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto space-y-8">
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">İade Koşulları</h2>
            <div className="space-y-4 text-gray-700 leading-relaxed">
              <p>
                Ürünlerinizi teslim aldığınız tarihten itibaren <strong className="text-gray-900">14 gün içinde</strong> iade edebilirsiniz.
              </p>
              <p className="font-medium text-gray-900 mt-6 mb-3">İade için gerekli koşullar:</p>
              <ul className="space-y-2 ml-6">
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Ürün kullanılmamış ve yıkanmamış olmalıdır</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Orijinal ambalajında ve etiketleriyle birlikte olmalıdır</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Fatura fotokopisi ile birlikte gönderilmelidir</span>
                </li>
                <li className="flex items-start gap-3">
                  <svg className="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                  <span>Hijyen kuralları gereği tek kullanımlık ve kişisel ürünler iade edilemez</span>
                </li>
              </ul>
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">İade Süreci</h2>
            <div className="space-y-6">
              <div className="flex gap-4">
                <div className="flex-shrink-0 w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold">
                  1
                </div>
                <div>
                  <h3 className="font-medium text-gray-900 mb-2">İade Talebi</h3>
                  <p className="text-gray-700">
                    İletişim sayfamızdan veya info@ripehome.com.tr adresinden 
                    iade talebinizi iletin. Sipariş numaranızı ve iade sebebini belirtin.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <div className="flex-shrink-0 w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold">
                  2
                </div>
                <div>
                  <h3 className="font-medium text-gray-900 mb-2">Onay & Kargo</h3>
                  <p className="text-gray-700">
                    İade talebiniz onaylandıktan sonra, size iade kargo kodu gönderilecektir. 
                    Ürünü anlaşmalı kargo firması ile ücretsiz gönderebilirsiniz.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <div className="flex-shrink-0 w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold">
                  3
                </div>
                <div>
                  <h3 className="font-medium text-gray-900 mb-2">İnceleme</h3>
                  <p className="text-gray-700">
                    İade edilen ürün tarafımıza ulaştıktan sonra 2-3 iş günü içinde 
                    incelenir ve onaylanır.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <div className="flex-shrink-0 w-12 h-12 bg-gray-900 text-white rounded-full flex items-center justify-center font-bold">
                  4
                </div>
                <div>
                  <h3 className="font-medium text-gray-900 mb-2">İade Ödemesi</h3>
                  <p className="text-gray-700">
                    İade onayından sonra 5-7 iş günü içinde ödemeniz kredi kartınıza 
                    veya banka hesabınıza iade edilir.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">Ürün Değişimi</h2>
            <p className="text-gray-700 leading-relaxed mb-4">
              Aldığınız üründe renk, beden veya model değişikliği yapmak isterseniz, 
              önce iade işlemi başlatıp ardından yeni sipariş vermeniz gerekmektedir.
            </p>
            <p className="text-gray-700 leading-relaxed">
              Hatalı veya hasarlı ürün teslimatı durumunda, kargo ücreti tarafımızca 
              karşılanarak yeni ürün gönderimi yapılır.
            </p>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">Toptan Sipariş İadeleri</h2>
            <p className="text-gray-700 leading-relaxed mb-4">
              Toptan siparişlerde iade koşulları farklılık gösterebilir. Toptan siparişlerde:
            </p>
            <ul className="space-y-2 ml-6 text-gray-700">
              <li className="flex items-start gap-3">
                <svg className="w-5 h-5 text-gray-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clipRule="evenodd" />
                </svg>
                <span>Üretim hatası veya hasar durumunda tam iade yapılır</span>
              </li>
              <li className="flex items-start gap-3">
                <svg className="w-5 h-5 text-gray-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clipRule="evenodd" />
                </svg>
                <span>Müşteri kaynaklı iadeler için özel şartlar geçerlidir</span>
              </li>
              <li className="flex items-start gap-3">
                <svg className="w-5 h-5 text-gray-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clipRule="evenodd" />
                </svg>
                <span>Detaylı bilgi için satış ekibimizle görüşünüz</span>
              </li>
            </ul>
          </div>

          <div className="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 border border-gray-200">
            <div className="text-center">
              <h3 className="text-xl font-medium text-gray-900 mb-4">
                İade konusunda yardıma mı ihtiyacınız var?
              </h3>
              <p className="text-gray-700 mb-6">
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
    </div>
  );
}
