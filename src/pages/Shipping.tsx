export default function Shipping() {
  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4 max-w-4xl">
        {/* Header */}
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            Delivery Info
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            Kargo ve Teslimat
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="bg-white rounded-2xl shadow-lg p-8 md:p-12 space-y-8">
          {/* Kargo Ücretleri */}
          <div>
            <h2 className="text-2xl font-light text-gray-900 mb-4 flex items-center gap-3">
              <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Kargo Ücretleri
            </h2>
            <div className="space-y-3 text-gray-700">
              <p className="flex items-start gap-3">
                <span className="text-green-600 mt-1">✓</span>
                <span><strong>500 TL ve üzeri</strong> alışverişlerinizde kargo <strong>ücretsizdir</strong>.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-gray-600 mt-1">•</span>
                <span>500 TL altı siparişlerde kargo ücreti <strong>49 TL</strong>'dir.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-green-600 mt-1">✓</span>
                <span>Toptan siparişlerde kargo <strong>her zaman ücretsiz</strong>dir.</span>
              </p>
            </div>
          </div>

          <hr className="border-gray-200" />

          {/* Teslimat Süreleri */}
          <div>
            <h2 className="text-2xl font-light text-gray-900 mb-4 flex items-center gap-3">
              <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              Teslimat Süreleri
            </h2>
            <div className="space-y-3 text-gray-700">
              <p className="flex items-start gap-3">
                <span className="text-blue-600 mt-1">⚡</span>
                <span><strong>Stokta bulunan ürünler:</strong> Siparişiniz onaylandıktan sonra aynı gün veya en geç 1 iş günü içinde kargoya verilir.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-gray-600 mt-1">•</span>
                <span><strong>Kargo süresi:</strong> Kargoya teslimden sonra 1-3 iş günü içinde elinizde olur.</span>
              </p>
              <p className="flex items-start gap-3">
                <span className="text-gray-600 mt-1">•</span>
                <span><strong>Toptan siparişler:</strong> 3-5 iş günü içinde hazırlanır ve kargoya verilir.</span>
              </p>
            </div>
          </div>

          <hr className="border-gray-200" />

          {/* Kargo Firmaları */}
          <div>
            <h2 className="text-2xl font-light text-gray-900 mb-4 flex items-center gap-3">
              <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              Kargo Firmaları
            </h2>
            <p className="text-gray-700 mb-4">
              Siparişlerinizi aşağıdaki güvenilir kargo firmaları ile gönderiyoruz:
            </p>
            <div className="grid md:grid-cols-3 gap-4">
              <div className="border border-gray-200 rounded-lg p-4 text-center">
                <p className="font-medium text-gray-900">MNG Kargo</p>
              </div>
              <div className="border border-gray-200 rounded-lg p-4 text-center">
                <p className="font-medium text-gray-900">Yurtiçi Kargo</p>
              </div>
              <div className="border border-gray-200 rounded-lg p-4 text-center">
                <p className="font-medium text-gray-900">Aras Kargo</p>
              </div>
            </div>
            <p className="text-sm text-gray-600 mt-4">
              * Kargo firması seçimi sipariş adresine ve stok durumuna göre otomatik belirlenir.
            </p>
          </div>

          <hr className="border-gray-200" />

          {/* Kargo Takibi */}
          <div>
            <h2 className="text-2xl font-light text-gray-900 mb-4 flex items-center gap-3">
              <svg className="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
              Kargo Takibi
            </h2>
            <p className="text-gray-700">
              Siparişiniz kargoya verildikten sonra kayıtlı e-posta adresinize kargo takip numarası gönderilir. 
              Bu numara ile kargo firmasının web sitesinden veya mobil uygulamasından kargonuzun durumunu takip edebilirsiniz.
            </p>
          </div>

          <hr className="border-gray-200" />

          {/* Dikkat Edilmesi Gerekenler */}
          <div className="bg-amber-50 border border-amber-200 rounded-xl p-6">
            <h3 className="text-lg font-medium text-amber-900 mb-3 flex items-center gap-2">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
              </svg>
              Önemli Bilgiler
            </h3>
            <ul className="space-y-2 text-sm text-amber-900">
              <li className="flex items-start gap-2">
                <span>•</span>
                <span>Lütfen teslimat adresinizi eksiksiz ve doğru girdiğinizden emin olun.</span>
              </li>
              <li className="flex items-start gap-2">
                <span>•</span>
                <span>Teslim alırken ürünleri kargo görevlisi önünde kontrol ediniz.</span>
              </li>
              <li className="flex items-start gap-2">
                <span>•</span>
                <span>Hasarlı paketleri teslim almayınız ve durumu müşteri hizmetlerimize bildiriniz.</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  );
}

