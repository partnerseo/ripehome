import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { Instagram, Facebook, Mail, Phone, MapPin, Twitter, Linkedin, FileText } from 'lucide-react';
import { getSettings } from '../lib/api';
import type { Settings } from '../types/api';

const Footer = () => {
  const [settings, setSettings] = useState<Settings | null>(null);

  useEffect(() => {
    async function fetchSettings() {
      try {
        const data = await getSettings();
        setSettings(data);
      } catch (error) {
        console.error('Site ayarları yüklenemedi:', error);
      }
    }
    fetchSettings();
  }, []);

  return (
    <footer className="bg-white border-t border-[#E5DDD1]">
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-12 md:py-16">
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-12 mb-12">
          <div>
            <img 
              src="/ripehomelogo.jpg" 
              alt="Ripe Home Logo" 
              className="h-14 md:h-16 lg:h-20 w-auto object-contain mb-6"
            />
            <p className="font-sans text-neutral-600 leading-relaxed mb-4 text-sm">
              Denizli'den doğal liflerden üretilmiş premium ev tekstil ürünleriyle evinize doğallık ve şıklık katıyoruz.
            </p>
            <div className="flex gap-3">
              <a 
                href={settings?.social_media?.instagram || 'https://www.instagram.com/ripe_home/'} 
                target="_blank" 
                rel="noopener noreferrer"
                className="p-2 bg-[#F8F6F3] hover:bg-gradient-to-br hover:from-purple-500 hover:via-pink-500 hover:to-orange-500 hover:text-white transition-all duration-300 rounded-full"
                title="Instagram'da bizi takip edin"
              >
                <Instagram className="w-5 h-5" />
              </a>
              
              {settings?.social_media?.facebook && (
                <a 
                  href={settings.social_media.facebook} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="p-2 bg-[#F8F6F3] hover:bg-blue-600 hover:text-white transition-all duration-300 rounded-full"
                  title="Facebook'ta bizi takip edin"
                >
                  <Facebook className="w-5 h-5" />
                </a>
              )}
              
              {settings?.social_media?.twitter && (
                <a 
                  href={settings.social_media.twitter} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="p-2 bg-[#F8F6F3] hover:bg-sky-500 hover:text-white transition-all duration-300 rounded-full"
                  title="Twitter'da bizi takip edin"
                >
                  <Twitter className="w-5 h-5" />
                </a>
              )}
              
              {settings?.social_media?.linkedin && (
                <a 
                  href={settings.social_media.linkedin} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="p-2 bg-[#F8F6F3] hover:bg-blue-700 hover:text-white transition-all duration-300 rounded-full"
                  title="LinkedIn'de bizi takip edin"
                >
                  <Linkedin className="w-5 h-5" />
                </a>
              )}
            </div>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">İletişim</h3>
            <ul className="space-y-4 font-sans text-neutral-600 text-sm">
              <li className="flex items-start gap-3">
                <MapPin className="w-5 h-5 mt-0.5 flex-shrink-0 text-[#8B7355]" />
                <a 
                  href="https://maps.google.com/?q=Sevindik+Mah.+2291+Sok.+No:5A+Merkezefendi+Denizli"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="hover:text-[#8B7355] transition-colors leading-relaxed"
                >
                  Sevindik Mah., 2291. Sok., No: 5A<br />
                  Merkezefendi, Denizli
                </a>
              </li>
              
              <li className="flex items-center gap-3">
                <Phone className="w-5 h-5 flex-shrink-0 text-[#8B7355]" />
                <a 
                  href={`tel:${settings?.phone || '+905442519716'}`}
                  className="hover:text-[#8B7355] transition-colors font-medium"
                >
                  {settings?.phone || '+90 544 251 9716'}
                </a>
              </li>
              
              <li className="flex items-center gap-3">
                <Mail className="w-5 h-5 flex-shrink-0 text-[#8B7355]" />
                <a 
                  href={`mailto:${settings?.email || 'info@ripehome.com.tr'}`}
                  className="hover:text-[#8B7355] transition-colors font-medium"
                >
                  {settings?.email || 'info@ripehome.com.tr'}
                </a>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">Müşteri Desteği</h3>
            <ul className="space-y-3 font-sans text-neutral-600 text-sm">
              <li>
                <Link to="/sss" className="hover:text-[#8B7355] transition-colors duration-300 inline-flex items-center gap-1 group">
                  <span>Sıkça Sorulan Sorular</span>
                  <svg className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </li>
              <li>
                <Link to="/kargo-teslimat" className="hover:text-[#8B7355] transition-colors duration-300 inline-flex items-center gap-1 group">
                  <span>Kargo & Teslimat</span>
                  <svg className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </li>
              <li>
                <Link to="/iade-degisim" className="hover:text-[#8B7355] transition-colors duration-300 inline-flex items-center gap-1 group">
                  <span>İade & Değişim</span>
                  <svg className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </li>
              <li>
                <Link to="/toptan-siparis" className="hover:text-[#8B7355] transition-colors duration-300 inline-flex items-center gap-1 group">
                  <span>Toptan Sipariş</span>
                  <svg className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </li>
              <li>
                <Link to="/iletisim" className="hover:text-[#8B7355] transition-colors duration-300 inline-flex items-center gap-1 group">
                  <span>İletişim</span>
                  <svg className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">Kurumsal</h3>
            <ul className="space-y-3 font-sans text-neutral-600 text-sm">
              <li>
                <Link to="/hakkimizda" className="hover:text-[#8B7355] transition-colors duration-300 inline-flex items-center gap-1 group">
                  <span>Hakkımızda</span>
                  <svg className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </li>
              <li>
                <Link to="/gizlilik-politikasi" className="hover:text-[#8B7355] transition-colors duration-300 inline-flex items-center gap-1 group">
                  <span>Gizlilik Politikası</span>
                  <svg className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </li>
              <li>
                <Link to="/kullanim-kosullari" className="hover:text-[#8B7355] transition-colors duration-300 inline-flex items-center gap-1 group">
                  <span>Kullanım Koşulları</span>
                  <svg className="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                  </svg>
                </Link>
              </li>
            </ul>
          </div>

        </div>

        <div className="pt-8 border-t border-[#E5DDD1]">
          <div className="flex flex-col md:flex-row justify-between items-center gap-4">
            <div className="flex flex-col md:flex-row items-center gap-4">
              <p className="text-center md:text-left font-sans text-neutral-600 text-sm">
                {settings?.footer_text || '© 2025 Ripe Home. Tüm hakları saklıdır.'}
              </p>
              <a 
                href="/yikamatalimati.pdf" 
                target="_blank" 
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#8B7355] to-[#A0826D] text-white text-sm font-medium rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-300 group"
              >
                <FileText className="w-4 h-4" />
                <span>Yıkama Talimatı (PDF)</span>
                <svg className="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                </svg>
              </a>
            </div>
            <div className="flex flex-wrap justify-center gap-4 text-sm text-neutral-600">
              <Link to="/gizlilik-politikasi" className="hover:text-[#8B7355] transition">
                Gizlilik Politikası
              </Link>
              <span>•</span>
              <Link to="/kullanim-kosullari" className="hover:text-[#8B7355] transition">
                Kullanım Koşulları
              </Link>
              <span>•</span>
              <Link to="/iade-degisim" className="hover:text-[#8B7355] transition">
                İade & Değişim
              </Link>
              <span>•</span>
              <Link to="/iletisim" className="hover:text-[#8B7355] transition">
                İletişim
              </Link>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
