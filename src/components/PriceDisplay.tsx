import { Lock } from 'lucide-react';
import { useAuth } from '../context/AuthContext';

interface PriceDisplayProps {
  price: number | string | null | undefined;
  className?: string;
  size?: 'sm' | 'md' | 'lg';
}

export default function PriceDisplay({ price, className = '', size = 'md' }: PriceDisplayProps) {
  const { isAuthenticated, isLoading, openAuthModal } = useAuth();

  const sizeClasses = {
    sm: { price: 'text-base font-semibold', btn: 'text-xs px-3 py-1.5', icon: 'w-3 h-3' },
    md: { price: 'text-xl font-bold',       btn: 'text-sm px-4 py-2',   icon: 'w-4 h-4' },
    lg: { price: 'text-2xl font-bold',      btn: 'text-sm px-5 py-2.5', icon: 'w-4 h-4' },
  };

  const s = sizeClasses[size];

  if (isLoading) {
    return (
      <div className={`animate-pulse bg-gray-200 rounded h-7 w-24 ${className}`} />
    );
  }

  if (!isAuthenticated || price === null || price === undefined) {
    return (
      <button
        onClick={openAuthModal}
        className={`inline-flex items-center gap-1.5 bg-gray-900 text-white rounded-lg font-medium font-sans transition hover:bg-gray-800 active:scale-95 ${s.btn} ${className}`}
      >
        <Lock className={s.icon} />
        Fiyatı Gör
      </button>
    );
  }

  const formatted = new Intl.NumberFormat('tr-TR', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
  }).format(Number(price));

  return (
    <span className={`text-gray-900 font-sans ${s.price} ${className}`}>
      {formatted}
    </span>
  );
}
