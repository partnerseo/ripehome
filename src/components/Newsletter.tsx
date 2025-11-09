import { useState } from 'react';
import { Mail, Check } from 'lucide-react';
import { submitContact } from '../lib/api';

const Newsletter = () => {
  const [email, setEmail] = useState('');
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);
  const [error, setError] = useState('');

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!email) {
      setError('Lütfen e-posta adresinizi girin');
      return;
    }

    setLoading(true);
    setError('');

    try {
      const result = await submitContact({
        name: 'Newsletter Aboneliği',
        email,
        subject: 'Newsletter Abonelik Talebi',
        message: 'Yeni koleksiyonlardan ve özel fırsatlardan haberdar olmak istiyorum.'
      });

      if (result.success) {
        setSuccess(true);
        setEmail('');
        setTimeout(() => setSuccess(false), 5000);
      } else {
        setError(result.message || 'Bir hata oluştu');
      }
    } catch (err) {
      setError('Bir hata oluştu, lütfen tekrar deneyin');
    } finally {
      setLoading(false);
    }
  };

  return (
    <section className="relative py-16 md:py-24 px-4 md:px-12 lg:px-24 overflow-hidden">
      <div className="absolute inset-0 opacity-10">
        <div className="absolute inset-0 bg-[url('/pexels-olly-3765034.jpg')] bg-cover bg-center" />
      </div>

      <div className="relative max-w-4xl mx-auto text-center">
        <Mail className="w-12 h-12 text-[#8B7355] mx-auto mb-6" strokeWidth={1.5} />
        <h2 className="font-serif text-3xl md:text-4xl lg:text-5xl text-neutral-800 mb-4 font-light">
          Yeni Koleksiyonlardan ve Özel Fırsatlardan<br />İlk Siz Haberdar Olun
        </h2>
        <p className="font-sans text-neutral-600 text-lg mb-8">
          Doğal dokuların dünyasına özel erişim kazanın
        </p>

        <form onSubmit={handleSubmit} className="flex flex-col md:flex-row gap-4 max-w-2xl mx-auto">
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            placeholder="E-posta adresiniz"
            disabled={loading || success}
            className="flex-1 px-4 md:px-6 py-3 md:py-4 bg-white border-2 border-[#E5DDD1] focus:border-[#C9B7A1] focus:outline-none font-sans text-neutral-800 transition-colors duration-300 text-sm md:text-base disabled:opacity-50"
          />
          <button
            type="submit"
            disabled={loading || success}
            className="px-6 md:px-8 py-3 md:py-4 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300 font-sans tracking-wide text-sm md:text-base disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
          >
            {loading ? 'Gönderiliyor...' : success ? (
              <>
                <Check className="w-4 h-4" />
                Başarılı!
              </>
            ) : 'Abone Ol'}
          </button>
        </form>

        {error && (
          <p className="mt-4 text-red-600 font-sans text-sm">{error}</p>
        )}
        {success && (
          <p className="mt-4 text-green-600 font-sans text-sm">
            Teşekkürler! E-posta listemize eklendiniz.
          </p>
        )}
      </div>
    </section>
  );
};

export default Newsletter;
