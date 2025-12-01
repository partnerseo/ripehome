import { useState, useEffect } from 'react';
import { Instagram, Facebook, Mail, Phone, MapPin, Twitter, Linkedin } from 'lucide-react';
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
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">Ripe Home</h3>
            <p className="font-sans text-neutral-600 leading-relaxed mb-4 text-sm">
              Denizli'den doğal liflerden üretilmiş premium ev tekstil ürünleriyle evinize doğallık ve şıklık katıyoruz.
            </p>
            <div className="flex gap-3">
              {settings?.social_media?.instagram && (
                <a 
                  href={settings.social_media.instagram} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="p-2 bg-[#F8F6F3] hover:bg-[#E5DDD1] transition-colors duration-300 rounded-full"
                >
                  <Instagram className="w-5 h-5 text-neutral-800" />
                </a>
              )}
              {settings?.social_media?.facebook && (
                <a 
                  href={settings.social_media.facebook} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="p-2 bg-[#F8F6F3] hover:bg-[#E5DDD1] transition-colors duration-300 rounded-full"
                >
                  <Facebook className="w-5 h-5 text-neutral-800" />
                </a>
              )}
              {settings?.social_media?.twitter && (
                <a 
                  href={settings.social_media.twitter} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="p-2 bg-[#F8F6F3] hover:bg-[#E5DDD1] transition-colors duration-300 rounded-full"
                >
                  <Twitter className="w-5 h-5 text-neutral-800" />
                </a>
              )}
              {settings?.social_media?.linkedin && (
                <a 
                  href={settings.social_media.linkedin} 
                  target="_blank" 
                  rel="noopener noreferrer"
                  className="p-2 bg-[#F8F6F3] hover:bg-[#E5DDD1] transition-colors duration-300 rounded-full"
                >
                  <Linkedin className="w-5 h-5 text-neutral-800" />
                </a>
              )}
            </div>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">İletişim</h3>
            <ul className="space-y-3 font-sans text-neutral-600 text-sm">
              {settings?.address && (
                <li className="flex items-start gap-2">
                  <MapPin className="w-4 h-4 mt-1 flex-shrink-0" />
                  <span>{settings.address}</span>
                </li>
              )}
              {settings?.phone && (
                <li className="flex items-center gap-2">
                  <Phone className="w-4 h-4 flex-shrink-0" />
                  <span>{settings.phone}</span>
                </li>
              )}
              {settings?.email && (
                <li className="flex items-center gap-2">
                  <Mail className="w-4 h-4 flex-shrink-0" />
                  <span>{settings.email}</span>
                </li>
              )}
            </ul>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">Müşteri Desteği</h3>
            <ul className="space-y-3 font-sans text-neutral-600 text-sm">
              <li><a href="/sss" className="hover:text-[#8B7355] transition-colors duration-300">Sıkça Sorulan Sorular</a></li>
              <li><a href="/kargo-teslimat" className="hover:text-[#8B7355] transition-colors duration-300">Kargo & Teslimat</a></li>
              <li><a href="/iade-degisim" className="hover:text-[#8B7355] transition-colors duration-300">İade & Değişim</a></li>
              <li><a href="/toptan-siparis" className="hover:text-[#8B7355] transition-colors duration-300">Toptan Sipariş</a></li>
              <li><a href="/iletisim" className="hover:text-[#8B7355] transition-colors duration-300">İletişim</a></li>
            </ul>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">Kurumsal</h3>
            <ul className="space-y-3 font-sans text-neutral-600 text-sm">
              <li><a href="/hakkimizda" className="hover:text-[#8B7355] transition-colors duration-300">Hakkımızda</a></li>
              <li><a href="/gizlilik-politikasi" className="hover:text-[#8B7355] transition-colors duration-300">Gizlilik Politikası</a></li>
              <li><a href="/kullanim-kosullari" className="hover:text-[#8B7355] transition-colors duration-300">Kullanım Koşulları</a></li>
            </ul>
          </div>

        </div>

        <div className="pt-8 border-t border-[#E5DDD1]">
          <div className="flex flex-col md:flex-row justify-between items-center gap-4">
            <p className="text-center md:text-left font-sans text-neutral-600 text-sm">
              {settings?.footer_text || '© 2025 Ripe Home. Tüm hakları saklıdır.'}
            </p>
            <div className="flex flex-wrap justify-center gap-4 text-sm text-neutral-600">
              <a href="/gizlilik-politikasi" className="hover:text-neutral-800 transition">Gizlilik Politikası</a>
              <span>•</span>
              <a href="/kullanim-kosullari" className="hover:text-neutral-800 transition">Kullanım Koşulları</a>
              <span>•</span>
              <a href="/iade-degisim" className="hover:text-neutral-800 transition">İade & Değişim</a>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
