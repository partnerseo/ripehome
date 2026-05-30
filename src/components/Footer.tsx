import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { Instagram, Mail, Phone, MapPin, FileText } from 'lucide-react';
import { useLangPath } from '../hooks/useLang';

const Footer = () => {
  const { t } = useTranslation();
  const lp = useLangPath();

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
              {t('footer.description')}
            </p>
            <div className="flex gap-3">
              <a
                href="https://www.instagram.com/ripe_home/"
                target="_blank"
                rel="noopener noreferrer"
                className="p-2 bg-[#F8F6F3] hover:bg-gradient-to-br hover:from-purple-500 hover:via-pink-500 hover:to-orange-500 hover:text-white transition-all duration-300 rounded-full"
                title={t('footer.followInstagram')}
              >
                <Instagram className="w-5 h-5" />
              </a>
            </div>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">{t('footer.contact')}</h3>
            <ul className="space-y-4 font-sans text-neutral-600 text-sm">
              <li className="flex items-start gap-3">
                <MapPin className="w-5 h-5 mt-0.5 flex-shrink-0 text-[#8B7355]" />
                <a
                  href="https://maps.google.com/?q=Sevindik+Mahallesi+2291+Sokak+No:7+Merkezefendi+Denizli"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="hover:text-[#8B7355] transition-colors leading-relaxed"
                >
                  Sevindik Mah., 2291. Sok., No:7<br />
                  Merkezefendi, Denizli
                </a>
              </li>

              <li className="flex items-center gap-3">
                <Phone className="w-5 h-5 flex-shrink-0 text-[#8B7355]" />
                <a
                  href="tel:+905345730669"
                  className="hover:text-[#8B7355] transition-colors font-medium"
                >
                  +90 534 573 06 69
                </a>
              </li>

              <li className="flex items-center gap-3">
                <Mail className="w-5 h-5 flex-shrink-0 text-[#8B7355]" />
                <a
                  href="mailto:info@ripehome.com.tr"
                  className="hover:text-[#8B7355] transition-colors font-medium"
                >
                  info@ripehome.com.tr
                </a>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">{t('footer.customerSupport')}</h3>
            <ul className="space-y-3 font-sans text-neutral-600 text-sm">
              <li>
                <Link to={lp('/sss')} className="hover:text-[#8B7355] transition-colors duration-300">
                  {t('footer.faq')}
                </Link>
              </li>
              <li>
                <Link to={lp('/kargo-teslimat')} className="hover:text-[#8B7355] transition-colors duration-300">
                  {t('footer.shippingDelivery')}
                </Link>
              </li>
              <li>
                <Link to={lp('/toptan-siparis')} className="hover:text-[#8B7355] transition-colors duration-300">
                  {t('footer.wholesale')}
                </Link>
              </li>
              <li>
                <Link to={lp('/iletisim')} className="hover:text-[#8B7355] transition-colors duration-300">
                  {t('footer.contactLink')}
                </Link>
              </li>
              <li>
                <Link to={lp('/blog')} className="hover:text-[#8B7355] transition-colors duration-300">
                  Blog
                </Link>
              </li>
            </ul>
          </div>

          <div>
            <h3 className="font-serif text-2xl text-neutral-800 mb-6">{t('footer.corporate')}</h3>
            <ul className="space-y-3 font-sans text-neutral-600 text-sm">
              <li>
                <Link to={lp('/hakkimizda')} className="hover:text-[#8B7355] transition-colors duration-300">
                  {t('footer.aboutUs')}
                </Link>
              </li>
              <li>
                <Link to={lp('/gizlilik-politikasi')} className="hover:text-[#8B7355] transition-colors duration-300">
                  {t('footer.privacyPolicy')}
                </Link>
              </li>
              <li>
                <Link to={lp('/kullanim-kosullari')} className="hover:text-[#8B7355] transition-colors duration-300">
                  {t('footer.termsOfUse')}
                </Link>
              </li>
            </ul>
          </div>
        </div>

        <div className="pt-8 border-t border-[#E5DDD1]">
          <div className="flex flex-col md:flex-row justify-between items-center gap-4">
            <div className="flex flex-col md:flex-row items-center gap-4">
              <p className="text-center md:text-left font-sans text-neutral-600 text-sm">
                {t('footer.copyright')}
              </p>
              <a
                href="/yikamatalimati.pdf"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#8B7355] to-[#A0826D] text-white text-sm font-medium rounded-lg hover:shadow-lg hover:scale-105 transition-all duration-300"
              >
                <FileText className="w-4 h-4" />
                <span>{t('footer.washingInstructions')}</span>
              </a>
            </div>
            <div className="flex flex-wrap justify-center gap-4 text-sm text-neutral-600">
              <Link to={lp('/gizlilik-politikasi')} className="hover:text-[#8B7355] transition">
                {t('footer.privacyPolicy')}
              </Link>
              <span>•</span>
              <Link to={lp('/kullanim-kosullari')} className="hover:text-[#8B7355] transition">
                {t('footer.termsOfUse')}
              </Link>
              <span>•</span>
              <Link to={lp('/iletisim')} className="hover:text-[#8B7355] transition">
                {t('footer.contactLink')}
              </Link>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
};

export default Footer;
