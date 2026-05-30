import { useState } from 'react';
import { useTranslation } from 'react-i18next';
import { CheckCircle } from 'lucide-react';

export default function Contact() {
  const { t } = useTranslation();
  const [submitted, setSubmitted] = useState(false);
  const [form, setForm] = useState({
    name: '', email: '', phone: '', company: '', subject: '', message: '',
  });

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    setForm({ ...form, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    const body = `Ad Soyad: ${form.name}%0ATelefon: ${form.phone}%0AFirma: ${form.company}%0AKonu: ${form.subject}%0AE-posta: ${form.email}%0AMesaj: ${form.message}`;
    window.location.href = `mailto:info@ripehome.com.tr?subject=${encodeURIComponent(form.subject || 'İletişim Formu')}&body=${body}`;
    setSubmitted(true);
  };

  const inputClass = "w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355] outline-none transition bg-white";

  return (
    <div className="min-h-screen pt-32 pb-16 bg-[#F8F6F3]">
      <div className="container mx-auto px-4">
        <div className="text-center mb-12">
          <h1 className="text-4xl md:text-5xl font-light tracking-tight text-gray-900 mb-6">
            {t('contact.title')}
          </h1>
          <div className="w-24 h-[2px] bg-gradient-to-r from-transparent via-[#8B7355] to-transparent mx-auto"></div>
        </div>

        <div className="max-w-5xl mx-auto">
          <div className="bg-white rounded-2xl shadow-sm border border-neutral-100 overflow-hidden">
            <div className="grid md:grid-cols-5">

              {/* Sol panel - iletişim bilgileri */}
              <div className="md:col-span-2 bg-gradient-to-br from-neutral-900 to-neutral-800 p-8 md:p-10 text-white flex flex-col justify-between">
                <div>
                  <h2 className="text-2xl font-light mb-2">{t('contact.reachUs')}</h2>
                  <p className="text-white/60 text-sm mb-8">{t('contact.reachUsDesc')}</p>
                  <div className="space-y-6">
                    <div className="flex items-start gap-4">
                      <div className="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg className="w-4 h-4 text-[#C4A96A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                      </div>
                      <div>
                        <p className="text-white/50 text-xs mb-1">{t('contact.phone')}</p>
                        <a href="tel:+905345730669" className="text-white hover:text-[#C4A96A] transition-colors text-sm font-medium">
                          +90 534 573 06 69
                        </a>
                      </div>
                    </div>
                    <div className="flex items-start gap-4">
                      <div className="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg className="w-4 h-4 text-[#C4A96A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                      </div>
                      <div>
                        <p className="text-white/50 text-xs mb-1">{t('contact.email')}</p>
                        <a href="mailto:info@ripehome.com.tr" className="text-white hover:text-[#C4A96A] transition-colors text-sm font-medium">
                          info@ripehome.com.tr
                        </a>
                      </div>
                    </div>
                    <div className="flex items-start gap-4">
                      <div className="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg className="w-4 h-4 text-[#C4A96A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                      </div>
                      <div>
                        <p className="text-white/50 text-xs mb-1">{t('contact.address')}</p>
                        <p className="text-white/80 text-sm leading-relaxed">
                          Sevindik Mah., 2291 Sok. No:7<br />Merkezefendi, Denizli
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                <div className="mt-10 pt-8 border-t border-white/10">
                  <p className="text-white/40 text-xs">{t('contact.officeHours')}</p>
                </div>
              </div>

              {/* Sağ panel - form */}
              <div className="md:col-span-3 p-8 md:p-10">
                {submitted ? (
                  <div className="h-full flex flex-col items-center justify-center text-center py-12">
                    <CheckCircle className="w-14 h-14 text-green-500 mb-4" />
                    <h3 className="text-xl font-medium text-neutral-800 mb-2">{t('contact.thankYou')}</h3>
                    <p className="text-neutral-500 text-sm">{t('contact.successMessage')}</p>
                  </div>
                ) : (
                  <>
                    <h2 className="text-2xl font-light text-gray-900 mb-6">{t('contact.sendMessage')}</h2>
                    <form onSubmit={handleSubmit} className="space-y-4">
                      <div className="grid sm:grid-cols-2 gap-4">
                        <input
                          type="text"
                          name="name"
                          required
                          placeholder={t('contact.namePlaceholder')}
                          className={inputClass}
                          onChange={handleChange}
                        />
                        <input
                          type="tel"
                          name="phone"
                          placeholder={t('contact.phonePlaceholder')}
                          className={inputClass}
                          onChange={handleChange}
                        />
                      </div>
                      <div className="grid sm:grid-cols-2 gap-4">
                        <input
                          type="email"
                          name="email"
                          required
                          placeholder={t('contact.emailPlaceholder')}
                          className={inputClass}
                          onChange={handleChange}
                        />
                        <input
                          type="text"
                          name="company"
                          placeholder={t('contact.companyPlaceholder')}
                          className={inputClass}
                          onChange={handleChange}
                        />
                      </div>
                      <select
                        name="subject"
                        className={inputClass}
                        onChange={handleChange}
                        defaultValue=""
                      >
                        <option value="" disabled>{t('contact.subjectPlaceholder')}</option>
                        <option value="Toptan Sipariş">{t('contact.subjectWholesale')}</option>
                        <option value="Özel Üretim">{t('contact.subjectCustom')}</option>
                        <option value="Ürün Bilgisi">{t('contact.subjectProduct')}</option>
                        <option value="Fiyat Teklifi">{t('contact.subjectQuote')}</option>
                        <option value="Diğer">{t('contact.subjectOther')}</option>
                      </select>
                      <textarea
                        name="message"
                        required
                        placeholder={t('contact.messagePlaceholder')}
                        rows={5}
                        className={inputClass}
                        onChange={handleChange}
                      />
                      <button
                        type="submit"
                        className="w-full bg-[#8B7355] hover:bg-[#6F5C46] text-white px-6 py-3.5 rounded-xl font-medium transition-colors text-sm"
                      >
                        {t('contact.send')}
                      </button>
                    </form>
                  </>
                )}
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
