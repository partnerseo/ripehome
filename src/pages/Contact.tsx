export default function Contact() {
  return (
    <div className="min-h-screen pt-32 pb-16">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <span className="text-sm uppercase tracking-[0.3em] text-gray-500 font-medium">
            Get in Touch
          </span>
          <h1 className="text-5xl font-light tracking-tight text-gray-900 mt-4 mb-6">
            İletişim
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-gray-900 to-transparent mx-auto"></div>
        </div>

        <div className="max-w-4xl mx-auto">
          <div className="bg-white rounded-2xl shadow-lg p-12">
            <div className="grid md:grid-cols-2 gap-12">
              <div>
                <h2 className="text-2xl font-light text-gray-900 mb-6">Bize Ulaşın</h2>
                <div className="space-y-4">
                  <div className="flex items-start gap-4">
                    <svg className="w-6 h-6 text-gray-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <div>
                      <p className="font-medium text-gray-900">Telefon</p>
                      <p className="text-gray-600">+90 534 573 06 69</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-4">
                    <svg className="w-6 h-6 text-gray-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <div>
                      <p className="font-medium text-gray-900">E-posta</p>
                      <p className="text-gray-600">info@ripehome.com.tr</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-4">
                    <svg className="w-6 h-6 text-gray-600 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <div>
                      <p className="font-medium text-gray-900">Adres</p>
                      <p className="text-gray-600">Sevindik Mahallesi, 2291 Sokak, No: 7<br />Merkezefendi, Denizli, Türkiye</p>
                    </div>
                  </div>
                </div>
              </div>

              <div>
                <h2 className="text-2xl font-light text-gray-900 mb-6">Mesaj Gönderin</h2>
                <form className="space-y-4">
                  <input
                    type="text"
                    placeholder="Adınız"
                    className="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                  />
                  <input
                    type="email"
                    placeholder="E-posta"
                    className="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                  />
                  <textarea
                    placeholder="Mesajınız"
                    rows={4}
                    className="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                  ></textarea>
                  <button
                    type="submit"
                    className="w-full bg-gray-900 text-white px-6 py-3 rounded-lg font-medium hover:bg-gray-800 transition"
                  >
                    Gönder
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
