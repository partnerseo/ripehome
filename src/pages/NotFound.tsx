import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

export default function NotFound() {
  const navigate = useNavigate();
  const [countdown, setCountdown] = useState(5);

  useEffect(() => {
    const timer = setInterval(() => {
      setCountdown(prev => prev - 1);
    }, 1000);

    const redirect = setTimeout(() => {
      navigate('/', { replace: true });
    }, 5000);

    return () => {
      clearInterval(timer);
      clearTimeout(redirect);
    };
  }, [navigate]);

  return (
    <div className="min-h-screen bg-[#F8F6F3] pt-20 flex items-center justify-center">
      <div className="text-center px-4">
        <h1 className="font-serif text-8xl md:text-9xl text-[#8B7355] font-light mb-4">404</h1>
        <h2 className="font-serif text-2xl md:text-3xl text-neutral-800 mb-4">Sayfa Bulunamadı</h2>
        <p className="font-sans text-neutral-500 mb-8 max-w-md mx-auto">
          Aradığınız sayfa mevcut değil veya taşınmış olabilir.
          <br />
          <span className="text-sm">{countdown} saniye içinde ana sayfaya yönlendirileceksiniz.</span>
        </p>
        <button
          onClick={() => navigate('/', { replace: true })}
          className="px-8 py-3 bg-[#8B7355] text-white hover:bg-[#6F5C46] transition-colors duration-300 rounded-xl font-sans font-medium"
        >
          Ana Sayfaya Dön
        </button>
      </div>
    </div>
  );
}
