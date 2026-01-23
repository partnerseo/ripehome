export default function Terms() {
  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            Terms & Conditions
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            Kullanım Koşulları
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto space-y-8">
          <div className="bg-white rounded-2xl shadow-lg p-8">
            <p className="text-gray-600 mb-6">Son Güncellenme: 23 Kasım 2025</p>
            
            <h2 className="text-2xl font-light text-gray-900 mb-4">Genel Koşullar</h2>
            <p className="text-gray-700 leading-relaxed mb-6">
              Bu web sitesini kullanarak, aşağıdaki kullanım koşullarını kabul etmiş sayılırsınız. 
              Koşulları kabul etmiyorsanız, lütfen siteyi kullanmayınız.
            </p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">Sipariş ve Satış</h2>
            <p className="text-gray-700 leading-relaxed mb-4">
              Web sitemiz üzerinden verilen siparişler aşağıdaki koşullara tabidir:
            </p>
            <ul className="space-y-2 ml-6 text-gray-700">
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Tüm fiyatlar Türk Lirası (TL) üzerinden belirtilmiştir</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Fiyatlarda KDV dahildir</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Sipariş onayından sonra fiyat değişikliği yapılmaz</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Stok durumu değişebilir, stokta olmayan ürünler iptal edilir</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Hatalı fiyatlandırma durumunda sipariş iptal edilebilir</span>
              </li>
            </ul>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">Ödeme Koşulları</h2>
            <p className="text-gray-700 leading-relaxed mb-4">
              Ödemelerinizi aşağıdaki yöntemlerle yapabilirsiniz:
            </p>
            <ul className="space-y-2 ml-6 text-gray-700">
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Kredi Kartı (Visa, Mastercard, American Express)</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Banka Havalesi / EFT (Toptan siparişler için)</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Taksit seçenekleri banka ve kart türüne göre değişir</span>
              </li>
            </ul>
            <p className="text-gray-700 leading-relaxed mt-4">
              Ödeme bilgileriniz SSL şifreleme ile korunmaktadır ve güvenli ödeme sistemleri 
              aracılığıyla işlenmektedir.
            </p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">Teslimat ve Kargo</h2>
            <p className="text-gray-700 leading-relaxed mb-6">
              Teslimat koşulları hakkında detaylı bilgi için 
              <a href="/kargo-teslimat" className="text-gray-900 font-medium hover:underline mx-1">
                Kargo & Teslimat
              </a> 
              sayfamızı inceleyebilirsiniz. Kargo süreleri tahmini olup, 
              hava koşulları ve mücbir sebepler nedeniyle değişiklik gösterebilir.
            </p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">İptal ve İade</h2>
            <p className="text-gray-700 leading-relaxed mb-6">
              İptal ve iade koşulları hakkında detaylı bilgi için 
              <a href="/iade-degisim" className="text-gray-900 font-medium hover:underline mx-1">
                İade & Değişim
              </a> 
              sayfamızı inceleyebilirsiniz. Tüketici hakları kapsamında yasal 
              cayma hakkınız bulunmaktadır.
            </p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">Fikri Mülkiyet Hakları</h2>
            <p className="text-gray-700 leading-relaxed mb-6">
              Bu web sitesindeki tüm içerik (metin, görsel, logo, tasarım vb.) 
              Ripe Home'e aittir ve telif hakları ile korunmaktadır. 
              İzinsiz kullanım, kopyalama veya dağıtım yasaktır.
            </p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">Sorumluluk Reddi</h2>
            <p className="text-gray-700 leading-relaxed mb-4">
              Firmamız aşağıdaki durumlardan sorumlu değildir:
            </p>
            <ul className="space-y-2 ml-6 text-gray-700">
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Web sitesinin kesintisiz veya hatasız çalışacağı</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Ürün fotoğraflarındaki renk farklılıkları (ekran farklılıklarından kaynaklanabilir)</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Mücbir sebeplerden kaynaklanan gecikmeler</span>
              </li>
              <li className="flex items-start gap-3">
                <span className="text-gray-900 mt-1">•</span>
                <span>Üçüncü parti web sitelerine verilen bağlantılar</span>
              </li>
            </ul>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">Uyuşmazlık Çözümü</h2>
            <p className="text-gray-700 leading-relaxed mb-6">
              Bu sözleşmeden doğabilecek uyuşmazlıklarda Türkiye Cumhuriyeti yasaları 
              geçerlidir. Uyuşmazlıkların çözümünde Denizli Mahkemeleri ve İcra Daireleri 
              yetkilidir.
            </p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">Değişiklikler</h2>
            <p className="text-gray-700 leading-relaxed mb-6">
              Firmamız, kullanım koşullarında değişiklik yapma hakkını saklı tutar. 
              Değişiklikler web sitesinde yayınlandığı tarihte yürürlüğe girer.
            </p>

            <h2 className="text-2xl font-light text-gray-900 mb-4 mt-8">İletişim</h2>
            <p className="text-gray-700 leading-relaxed">
              Kullanım koşulları hakkında sorularınız için:
              <br />
              E-posta: <a href="mailto:info@ripehome.com.tr" className="text-gray-900 font-medium hover:underline">info@ripehome.com.tr</a>
              <br />
              Telefon: +90 534 573 06 69
              <br />
              Adres: Sevindik Mahallesi, 2291 Sokak, No: 7, Merkezefendi, Denizli, Türkiye
            </p>
          </div>
        </div>
      </div>
    </div>
  );
}

