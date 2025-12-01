export default function Shipping() {
  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            Delivery
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            Kargo & Teslimat
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto space-y-8">
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">Kargo Ücretleri</h2>
            <div className="space-y-4">
              <div className="flex items-start gap-4">
                <svg className="w-6 h-6 text-green-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                </svg>
                <div>
                  <p className="font-medium text-gray-900">500 TL ve üzeri alışverişlerde</p>
                  <p className="text-gray-600">Kargo tamamen ücretsiz</p>
                </div>
              </div>
              <div className="flex items-start gap-4">
                <svg className="w-6 h-6 text-gray-600 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <p className="font-medium text-gray-900">500 TL altı siparişlerde</p>
                  <p className="text-gray-600">Kargo ücreti 50 TL</p>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">Teslimat Süreleri</h2>
            <div className="space-y-4 text-gray-700">
              <p className="flex items-start gap-3">
                <span className="text-gray-900 font-medium min-w-[200px]">Stoklu Ürünler:</span>
                <span>Siparişiniz aynı gün kargoya verilir.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-gray-900 font-medium min-w-[200px]">Hafta Sonu Siparişler:</span>
                <span>Ertesi iş günü kargoya teslim edilir.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-gray-900 font-medium min-w-[200px]">Toptan Siparişler:</span>
                <span>3-5 iş günü içinde kargoya verilir.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-gray-900 font-medium min-w-[200px]">Kargo Teslimat:</span>
                <span>1-3 iş günü (Türkiye geneli)</span>
              </p>
            </div>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">Kargo Firmaları</h2>
            <p className="text-gray-700 leading-relaxed mb-4">
              Siparişleriniz Aras Kargo, Yurtiçi Kargo ve MNG Kargo ile gönderilmektedir. 
              Kargo firması seçimi otomatik olarak sistem tarafından yapılmaktadır.
            </p>
            <p className="text-gray-700 leading-relaxed">
              Kargo takip numaranız, siparişiniz kargoya verildikten sonra SMS ve e-posta 
              yoluyla tarafınıza iletilecektir.
            </p>
          </div>

          <div className="bg-white rounded-2xl shadow-lg p-8">
            <h2 className="text-2xl font-light text-gray-900 mb-6">Yurt Dışı Kargo</h2>
            <p className="text-gray-700 leading-relaxed mb-4">
              Toptan siparişler için yurt dışı kargo hizmeti sunuyoruz. Kargo ücreti ve 
              teslimat süresi hedef ülkeye göre değişiklik göstermektedir.
            </p>
            <p className="text-gray-700 leading-relaxed">
              Yurt dışı siparişler için lütfen bizimle iletişime geçiniz: 
              <a href="mailto:info@ripehome.com.tr" className="text-gray-900 font-medium hover:underline ml-1">
                info@ripehome.com.tr
              </a>
              <br />
              Telefon: <a href="tel:+905442519716" className="text-gray-900 font-medium hover:underline">+90 544 251 9716</a>
            </p>
          </div>

          <div className="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-8 border border-gray-200">
            <div className="flex items-start gap-4">
              <svg className="w-8 h-8 text-gray-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">Önemli Not</h3>
                <p className="text-gray-700 leading-relaxed">
                  Hava şartları, doğal afetler ve resmi tatiller kargo teslimat sürelerini 
                  etkileyebilir. Bu gibi durumlarda gecikmelerden firmamız sorumlu değildir.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
