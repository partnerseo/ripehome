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

  const miniPosts = [
    '/pexels-cottonbro-4327012.jpg',
    '/pexels-shvetsa-5069401.jpg',
    '/pexels-tima-miroshnichenko-4794900.jpg',
  ];

  return (
    <footer className="bg-white border-t border-[#E5DDD1]">
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-12 md:py-16">
        <div className="grid md:grid-cols-4 gap-12 mb-12">
          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">Hakkımızda</h3>
            <p className="font-sans text-neutral-600 leading-relaxed mb-4 text-sm">
              Doğal liflerden üretilmiş premium ev tekstil ürünleriyle evinize doğallık ve şıklık katıyoruz.
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
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">Destek</h3>
            <ul className="space-y-3 font-sans text-neutral-600 text-sm">
              <li><a href="/sss" className="hover:text-[#8B7355] transition-colors duration-300">Sıkça Sorulan Sorular</a></li>
              <li><a href="/kargo" className="hover:text-[#8B7355] transition-colors duration-300">Kargo Bilgileri</a></li>
              <li><a href="/iade-ve-degisim" className="hover:text-[#8B7355] transition-colors duration-300">İade ve Değişim</a></li>
              <li><a href="/hakkimizda" className="hover:text-[#8B7355] transition-colors duration-300">Hakkımızda</a></li>
              <li><a href="/iletisim" className="hover:text-[#8B7355] transition-colors duration-300">İletişim</a></li>
            </ul>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">Instagram</h3>
            <div className="grid grid-cols-3 gap-2 mb-4">
              {miniPosts.map((image, index) => (
                <div key={index} className="aspect-square overflow-hidden group cursor-pointer">
                  <img
                    src={image}
                    alt={`Instagram ${index + 1}`}
                    className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                  />
                </div>
              ))}
            </div>
            <a href="#" className="font-sans text-sm text-[#8B7355] hover:underline">
              @luxuryhometextiles
            </a>
          </div>
        </div>

        <div className="pt-8 border-t border-[#E5DDD1]">
          <p className="text-center font-sans text-neutral-600 text-sm">
            {settings?.footer_text || '© 2024 Luxury Home Textiles. Tüm hakları saklıdır.'}
          </p>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
