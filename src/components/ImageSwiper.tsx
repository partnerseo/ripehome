import { useState, useRef, useCallback, useEffect } from 'react';
import { ChevronLeft, ChevronRight, ZoomIn, X } from 'lucide-react';

interface ImageSwiperProps {
  images: string[];
  alt: string;
  aspectRatio?: string;
  showArrows?: boolean;
  showDots?: boolean;
  showThumbnails?: boolean;
  enableZoom?: boolean;
  className?: string;
}

export default function ImageSwiper({
  images,
  alt,
  aspectRatio = 'aspect-square',
  showArrows = true,
  showDots = true,
  showThumbnails = true,
  enableZoom = true,
  className = '',
}: ImageSwiperProps) {
  const [current, setCurrent] = useState(0);
  const [zoomed, setZoomed] = useState(false);
  const touchStart = useRef(0);
  const touchEnd = useRef(0);
  const thumbnailsRef = useRef<HTMLDivElement>(null);

  const count = images.length;

  const goTo = useCallback((index: number) => {
    setCurrent(Math.max(0, Math.min(index, count - 1)));
  }, [count]);

  const prev = useCallback((e?: React.MouseEvent) => {
    e?.preventDefault();
    e?.stopPropagation();
    goTo(current === 0 ? count - 1 : current - 1);
  }, [current, count, goTo]);

  const next = useCallback((e?: React.MouseEvent) => {
    e?.preventDefault();
    e?.stopPropagation();
    goTo(current === count - 1 ? 0 : current + 1);
  }, [current, count, goTo]);

  const onTouchStart = (e: React.TouchEvent) => {
    touchStart.current = e.targetTouches[0].clientX;
  };

  const onTouchMove = (e: React.TouchEvent) => {
    touchEnd.current = e.targetTouches[0].clientX;
  };

  const onTouchEnd = () => {
    const diff = touchStart.current - touchEnd.current;
    if (Math.abs(diff) > 50) {
      if (diff > 0) next();
      else prev();
    }
  };

  // Keyboard navigation
  useEffect(() => {
    const handleKey = (e: KeyboardEvent) => {
      if (e.key === 'ArrowLeft') prev();
      if (e.key === 'ArrowRight') next();
      if (e.key === 'Escape') setZoomed(false);
    };
    window.addEventListener('keydown', handleKey);
    return () => window.removeEventListener('keydown', handleKey);
  }, [prev, next]);

  // Scroll thumbnail into view
  useEffect(() => {
    if (thumbnailsRef.current) {
      const thumb = thumbnailsRef.current.children[current] as HTMLElement;
      if (thumb) {
        thumb.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      }
    }
  }, [current]);

  if (!images || images.length === 0) {
    return (
      <div className={`${aspectRatio} bg-neutral-100 rounded-2xl flex items-center justify-center text-neutral-400 ${className}`}>
        <div className="text-center">
          <div className="text-5xl mb-2">📷</div>
          <p className="font-sans text-sm">Görsel Yok</p>
        </div>
      </div>
    );
  }

  if (images.length === 1) {
    return (
      <div className={className}>
        <div
          className={`${aspectRatio} bg-white rounded-2xl overflow-hidden relative group cursor-pointer`}
          onClick={() => enableZoom && setZoomed(true)}
        >
          <img
            src={images[0]}
            alt={alt}
            className="w-full h-full object-cover"
          />
          {enableZoom && (
            <div className="absolute bottom-3 right-3 bg-white/80 p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
              <ZoomIn className="w-4 h-4 text-neutral-700" />
            </div>
          )}
        </div>

        {/* Zoom Modal */}
        {zoomed && (
          <div
            className="fixed inset-0 z-50 bg-black/90 flex items-center justify-center p-4"
            onClick={() => setZoomed(false)}
          >
            <button className="absolute top-4 right-4 text-white/80 hover:text-white" onClick={() => setZoomed(false)}>
              <X className="w-8 h-8" />
            </button>
            <img src={images[0]} alt={alt} className="max-w-full max-h-full object-contain" />
          </div>
        )}
      </div>
    );
  }

  return (
    <div className={className}>
      {/* Main image */}
      <div
        className={`${aspectRatio} bg-white rounded-2xl overflow-hidden relative group cursor-pointer`}
        onTouchStart={onTouchStart}
        onTouchMove={onTouchMove}
        onTouchEnd={onTouchEnd}
        onClick={() => enableZoom && setZoomed(true)}
      >
        <img
          src={images[current]}
          alt={`${alt} ${current + 1}`}
          className="w-full h-full object-cover transition-opacity duration-300"
        />

        {/* Arrows */}
        {showArrows && (
          <>
            <button
              onClick={(e) => { e.stopPropagation(); prev(e); }}
              className="absolute left-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white p-2.5 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110"
              aria-label="Önceki"
            >
              <ChevronLeft className="w-5 h-5 text-neutral-700" />
            </button>
            <button
              onClick={(e) => { e.stopPropagation(); next(e); }}
              className="absolute right-3 top-1/2 -translate-y-1/2 bg-white/90 hover:bg-white p-2.5 rounded-full shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 hover:scale-110"
              aria-label="Sonraki"
            >
              <ChevronRight className="w-5 h-5 text-neutral-700" />
            </button>
          </>
        )}

        {/* Dots */}
        {showDots && count > 1 && (
          <div className="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1.5">
            {images.map((_, i) => (
              <button
                key={i}
                onClick={(e) => { e.preventDefault(); e.stopPropagation(); goTo(i); }}
                className={`rounded-full transition-all duration-300 ${
                  i === current
                    ? 'w-6 h-2 bg-white shadow-md'
                    : 'w-2 h-2 bg-white/50 hover:bg-white/80'
                }`}
                aria-label={`Resim ${i + 1}`}
              />
            ))}
          </div>
        )}

        {/* Counter */}
        <div className="absolute top-3 right-3 bg-black/50 text-white text-xs px-2.5 py-1 rounded-full backdrop-blur-sm">
          {current + 1}/{count}
        </div>

        {/* Zoom hint */}
        {enableZoom && (
          <div className="absolute bottom-3 right-3 bg-white/80 p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm">
            <ZoomIn className="w-4 h-4 text-neutral-700" />
          </div>
        )}
      </div>

      {/* Thumbnails */}
      {showThumbnails && count > 1 && (
        <div ref={thumbnailsRef} className="flex gap-2 mt-3 overflow-x-auto pb-2 scrollbar-hide">
          {images.map((image, i) => (
            <button
              key={i}
              onClick={() => goTo(i)}
              className={`flex-shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden transition-all duration-300 ${
                i === current
                  ? 'ring-2 ring-[#8B7355] ring-offset-2 opacity-100 scale-105'
                  : 'opacity-50 hover:opacity-80'
              }`}
            >
              <img
                loading="lazy"
                src={image}
                alt={`${alt} ${i + 1}`}
                className="w-full h-full object-cover"
              />
            </button>
          ))}
        </div>
      )}

      {/* Zoom Modal */}
      {zoomed && (
        <div
          className="fixed inset-0 z-50 bg-black/95 flex items-center justify-center"
          onClick={() => setZoomed(false)}
        >
          <button className="absolute top-4 right-4 text-white/80 hover:text-white z-10" onClick={() => setZoomed(false)}>
            <X className="w-8 h-8" />
          </button>

          {/* Zoomed arrows */}
          <button
            onClick={(e) => { e.stopPropagation(); prev(e); }}
            className="absolute left-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 p-3 rounded-full transition-colors"
          >
            <ChevronLeft className="w-6 h-6 text-white" />
          </button>
          <button
            onClick={(e) => { e.stopPropagation(); next(e); }}
            className="absolute right-4 top-1/2 -translate-y-1/2 bg-white/20 hover:bg-white/40 p-3 rounded-full transition-colors"
          >
            <ChevronRight className="w-6 h-6 text-white" />
          </button>

          <img
            src={images[current]}
            alt={`${alt} ${current + 1}`}
            className="max-w-[90vw] max-h-[85vh] object-contain"
            onClick={(e) => e.stopPropagation()}
          />

          {/* Zoom counter */}
          <div className="absolute bottom-4 left-1/2 -translate-x-1/2 bg-white/20 text-white text-sm px-4 py-1.5 rounded-full backdrop-blur-sm">
            {current + 1} / {count}
          </div>
        </div>
      )}
    </div>
  );
}
