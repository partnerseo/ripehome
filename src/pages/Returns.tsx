export default function Returns() {
  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4 max-w-4xl">
        {/* Header */}
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            Return Policy
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            İade ve Değişim
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="bg-white rounded-2xl shadow-lg p-8 md:p-12 space-y-8">
          {/* İade Koşulları */}
          <div>
            <h2 className="text-2xl font-light text-gray-900 mb-4 flex items-center gap-3">
              <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              İade Koşulları
            </h2>
            <div className="space-y-3 text-gray-700">
              <p className="flex items-start gap-3">
                <span className="text-green-600 mt-1">✓</span>
                <span>Ürünü teslim aldıktan sonra <strong>14 gün içerisinde</strong> iade edebilirsiniz.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-green-600 mt-1">✓</span>
                <span>İade edilecek ürün <strong>kullanılmamış, yıkanmamış ve etiketli</strong> olmalıdır.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-green-600 mt-1">✓</span>
                <span>Ürün <strong>orijinal ambalajında</strong> ve faturası ile birlikte iade edilmelidir.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-green-600 mt-1">✓</span>
                <span>İade kargo ücreti <strong>müşteriye</strong> aittir.</span>
              </p>
            </div>
          </div>

          <hr className="border-gray-200" />

          {/* İade Süreci */}
          <div>
            <h2 className="text-2xl font-light text-gray-900 mb-4 flex items-center gap-3">
              <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
              İade Süreci
            </h2>
            <div className="space-y-4">
              <div className="flex gap-4">
                <div className="flex-shrink-0 w-8 h-8 bg-gray-900 text-white rounded-full flex items-center justify-center text-sm font-medium">
                  1
                </div>
                <div>
                  <h3 className="font-medium text-gray-900 mb-1">İade Talebinde Bulunun</h3>
                  <p className="text-gray-600 text-sm">
                    Müşteri hizmetlerimize e-posta veya telefon ile ulaşarak iade talebinizi iletin. 
                    Sipariş numaranızı ve iade sebebinizi belirtiniz.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <div className="flex-shrink-0 w-8 h-8 bg-gray-900 text-white rounded-full flex items-center justify-center text-sm font-medium">
                  2
                </div>
                <div>
                  <h3 className="font-medium text-gray-900 mb-1">İade Onayı Alın</h3>
                  <p className="text-gray-600 text-sm">
                    İade talebiniz değerlendirildikten sonra size bir iade kodu ve iade adresi gönderilecektir.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <div className="flex-shrink-0 w-8 h-8 bg-gray-900 text-white rounded-full flex items-center justify-center text-sm font-medium">
                  3
                </div>
                <div>
                  <h3 className="font-medium text-gray-900 mb-1">Ürünü Kargolayın</h3>
                  <p className="text-gray-600 text-sm">
                    Ürünü orijinal ambalajında, faturası ile birlikte belirtilen adrese kargo ile gönderin. 
                    İade kodu paket üzerine yazılmalıdır.
                  </p>
                </div>
              </div>

              <div className="flex gap-4">
                <div className="flex-shrink-0 w-8 h-8 bg-gray-900 text-white rounded-full flex items-center justify-center text-sm font-medium">
                  4
                </div>
                <div>
                  <h3 className="font-medium text-gray-900 mb-1">İade İşleminiz Tamamlanır</h3>
                  <p className="text-gray-600 text-sm">
                    Ürün tarafımıza ulaştıktan ve kontrolü yapıldıktan sonra 5-7 iş günü içerisinde 
                    ödemeniz iade edilir veya değişim yapılır.
                  </p>
                </div>
              </div>
            </div>
          </div>

          <hr className="border-gray-200" />

          {/* Değişim */}
          <div>
            <h2 className="text-2xl font-light text-gray-900 mb-4 flex items-center gap-3">
              <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
              </svg>
              Ürün Değişimi
            </h2>
            <p className="text-gray-700 mb-4">
              Aldığınız ürünü başka bir ürünle değiştirmek istiyorsanız:
            </p>
            <ul className="space-y-2 text-gray-700">
              <li className="flex items-start gap-3">
                <span className="text-gray-600 mt-1">•</span>
                <span>İade sürecini başlatın ve ürünü geri gönderin.</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-600 mt-1">•</span>
                <span>İstediğiniz yeni ürün için ayrı bir sipariş oluşturun.</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-600 mt-1">•</span>
                <span>İade işlemi tamamlandıktan sonra ödemeniz iade edilecektir.</span>
              </li>
            </ul>
            <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
              <p className="text-sm text-blue-900">
                <strong>Not:</strong> Aynı ürünün farklı bedeni veya rengi için değişim talep ediyorsanız, 
                müşteri hizmetlerimiz ile iletişime geçerek daha hızlı değişim yapabilirsiniz.
              </p>
            </div>
          </div>

          <hr className="border-gray-200" />

          {/* İade Edilemeyen Ürünler */}
          <div className="bg-red-50 border border-red-200 rounded-xl p-6">
            <h3 className="text-lg font-medium text-red-900 mb-3 flex items-center gap-2">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              İade Edilemeyen Ürünler
            </h3>
            <ul className="space-y-2 text-sm text-red-900">
              <li className="flex items-start gap-2">
                <span>✗</span>
                <span>Kullanılmış, yıkanmış veya kirlenmiş ürünler</span>
              </li>
              <li className="flex items-start gap-2">
                <span>✗</span>
                <span>Etiketi koparılmış ürünler</span>
              </li>
              <li className="flex items-start gap-2">
                <span>✗</span>
                <span>Özel sipariş üzerine hazırlanmış ürünler</span>
              </li>
              <li className="flex items-start gap-2">
                <span>✗</span>
                <span>İndirimli ürünler (kampanya şartlarına göre değişir)</span>
              </li>
            </ul>
          </div>

          {/* İletişim */}
          <div className="bg-gradient-to-r from-gray-900 to-gray-800 rounded-xl p-6 text-white text-center">
            <h3 className="text-xl font-light mb-2">İade Desteği</h3>
            <p className="text-gray-300 text-sm mb-4">
              İade sürecinizle ilgili sorularınız için bize ulaşın
            </p>
            <div className="flex flex-col sm:flex-row gap-3 justify-center">
              <a
                href="mailto:info@luxuryhometextiles.com"
                className="inline-flex items-center justify-center gap-2 bg-white text-gray-900 px-6 py-2 rounded-lg font-medium hover:bg-gray-100 transition text-sm"
              >
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                E-posta Gönder
              </a>
              <a
                href="tel:+90XXXXXXXXXX"
                className="inline-flex items-center justify-center gap-2 bg-white/10 border border-white/20 text-white px-6 py-2 rounded-lg font-medium hover:bg-white/20 transition text-sm"
              >
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Bizi Arayın
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

