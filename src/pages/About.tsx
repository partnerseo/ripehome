export default function About() {
  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            About Us
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            Hakkımızda
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto">
          <div className="bg-white rounded-2xl shadow-lg p-12">
            <p className="text-gray-700 text-lg leading-relaxed mb-6">
              <strong className="text-gray-900">Ripe Home</strong> olarak, Denizli'de kaliteli ve şık ev tekstili ürünleri sunuyoruz. 
              Yılların deneyimi ve müşteri memnuniyeti odaklı yaklaşımımızla sektörde öncü konumdayız.
            </p>
            <p className="text-gray-700 text-lg leading-relaxed mb-6">
              Toptan satış konusunda uzmanlaşmış ekibimiz, işletmelere özel çözümler sunmaktadır. 
              Geniş ürün yelpazemiz ve rekabetçi fiyatlarımızla sizlere hizmet vermekten mutluluk duyuyoruz.
            </p>
            <p className="text-gray-700 text-lg leading-relaxed mb-6">
              %100 doğal pamuk ve bambu liflerinden üretilmiş premium ürünlerimiz, 
              Oeko-Tex Standard 100 sertifikalıdır ve insan sağlığına zararlı kimyasal içermez.
            </p>
            <p className="text-gray-700 text-lg leading-relaxed">
              Kalite, güven ve müşteri memnuniyeti ilkelerimizle çalışmalarımızı sürdürüyoruz.
            </p>
            
            <div className="mt-8 pt-8 border-t border-gray-200">
              <h3 className="text-xl font-medium text-gray-900 mb-4">İletişim Bilgilerimiz</h3>
              <div className="space-y-2 text-gray-700">
                <p><strong>Adres:</strong> Sevindik Mah., 2291. Sok., No: 5A, Merkezefendi, Denizli</p>
                <p><strong>Telefon:</strong> +90 544 251 9716</p>
                <p><strong>E-posta:</strong> info@ripehome.com.tr</p>
                <p><strong>Web:</strong> www.ripehome.com.tr</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
