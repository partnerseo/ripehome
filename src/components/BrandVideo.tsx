import { useState } from 'react';
import { Play } from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface Props {
  url?: string;
  file?: string;
  title?: string;
  subtitle?: string;
}

function getYouTubeId(url: string): string | null {
  const patterns = [
    /youtube\.com\/watch\?v=([^&]+)/,
    /youtu\.be\/([^?]+)/,
    /youtube\.com\/embed\/([^?]+)/,
  ];
  for (const p of patterns) {
    const m = url.match(p);
    if (m) return m[1];
  }
  return null;
}

export default function BrandVideo({ url, file, title, subtitle }: Props) {
  const [playing, setPlaying] = useState(false);
  const { t } = useTranslation();
  const displayTitle = t('video.title', title || '');
  const displaySubtitle = t('video.subtitle', subtitle || '');

  // Öncelik: dosya varsa dosyayı kullan, yoksa YouTube URL
  const videoId = url ? getYouTubeId(url) : null;
  const isDirectVideo = !!file;
  const hasSource = isDirectVideo || !!videoId;

  if (!hasSource) return null;

  const thumbnailUrl = videoId && !isDirectVideo
    ? `https://img.youtube.com/vi/${videoId}/maxresdefault.jpg`
    : null;

  const embedUrl = videoId && !isDirectVideo
    ? `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0&modestbranding=1`
    : null;

  return (
    <section className="py-16 md:py-24 bg-[#F8F6F3]">
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24">

        {/* Başlık */}
        {(displayTitle || displaySubtitle) && (
          <div className="text-center mb-10 md:mb-14">
            {displayTitle && (
              <h2 className="font-serif text-3xl md:text-5xl text-neutral-800 font-light mb-3">
                {displayTitle}
              </h2>
            )}
            {displaySubtitle && (
              <p className="font-sans text-neutral-500 text-base md:text-lg max-w-2xl mx-auto">
                {displaySubtitle}
              </p>
            )}
          </div>
        )}

        {/* Video kutusu */}
        <div className="relative max-w-sm mx-auto rounded-2xl overflow-hidden shadow-2xl shadow-black/60">
          <div className="aspect-[9/16] bg-[#F8F6F3]">

            {/* Direkt video dosyası */}
            {isDirectVideo ? (
              <video
                src={file}
                controls
                playsInline
                className="w-full h-full object-cover"
                poster={thumbnailUrl || undefined}
              />
            ) : playing && embedUrl ? (
              /* YouTube embed - play'e basınca */
              <iframe
                src={embedUrl}
                className="w-full h-full"
                allow="autoplay; fullscreen"
                allowFullScreen
              />
            ) : (
              /* YouTube thumbnail + play butonu */
              <>
                {thumbnailUrl && (
                  <img
                    src={thumbnailUrl}
                    alt={title || 'Video'}
                    className="w-full h-full object-cover"
                  />
                )}
                <div className="absolute inset-0 bg-black/40" />
                <div className="absolute inset-0 border border-white/10 rounded-2xl pointer-events-none" />

                <button
                  onClick={() => setPlaying(true)}
                  className="absolute inset-0 flex flex-col items-center justify-center gap-4 group"
                  aria-label="Videoyu oynat"
                >
                  <div className="relative">
                    <div className="absolute inset-0 rounded-full bg-white/20 scale-100 group-hover:scale-150 transition-transform duration-700" />
                    <div className="absolute inset-0 rounded-full bg-white/10 scale-100 group-hover:scale-125 transition-transform duration-500 delay-100" />
                    <div className="relative w-16 h-16 md:w-20 md:h-20 bg-white rounded-full flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300">
                      <Play className="w-6 h-6 md:w-8 md:h-8 text-neutral-900 fill-neutral-900 ml-1" />
                    </div>
                  </div>
                </button>
              </>
            )}
          </div>
        </div>

      </div>
    </section>
  );
}
