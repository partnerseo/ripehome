import { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import { Link } from 'react-router-dom';
import { useLangPath } from '../hooks/useLang';
import { getBlogPosts } from '../lib/api';
import { Calendar, Clock, ArrowRight } from 'lucide-react';

interface BlogPost {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  cover_image: string | null;
  published_at: string;
}

function readingTime(excerpt: string): number {
  return Math.max(2, Math.ceil((excerpt?.length || 0) / 500));
}

function PostImage({ src, alt }: { src: string | null; alt: string }) {
  if (src) {
    return (
      <img
        src={`https://ripehome.com.tr/storage/${src}`}
        alt={alt}
        className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
      />
    );
  }
  return (
    <div className="w-full h-full bg-gradient-to-br from-[#E8DDD0] to-[#C9B7A1] flex items-center justify-center">
      <span className="font-serif text-5xl text-white/60">R</span>
    </div>
  );
}

export default function Blog() {
  const { t, i18n } = useTranslation();
  const lp = useLangPath();
  const [posts, setPosts] = useState<BlogPost[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getBlogPosts().then(data => {
      setPosts(Array.isArray(data) ? data : []);
    }).finally(() => setLoading(false));
  }, [i18n.language]);

  const formatDate = (d: string) =>
    new Date(d).toLocaleDateString(i18n.language === 'tr' ? 'tr-TR' : 'en-US', {
      year: 'numeric', month: 'long', day: 'numeric',
    });

  const featured = posts[0];
  const rest = posts.slice(1);

  return (
    <div className="min-h-screen bg-white">
      {/* Hero */}
      <div className="relative bg-neutral-900 pt-20 pb-0 overflow-hidden">
        <div className="absolute inset-0 bg-[url('/pexels-rodolphozanardo-1304110.jpg')] bg-cover bg-center opacity-20" />
        <div className="relative max-w-7xl mx-auto px-4 md:px-12 lg:px-24 pt-16 pb-20 text-center">
          <p className="font-sans text-[#C9B7A1] text-xs tracking-[0.3em] uppercase mb-4">
            {t('blog.label', 'Blog')}
          </p>
          <h1 className="font-serif text-5xl md:text-7xl text-white font-light leading-tight mb-5">
            {t('blog.heading', 'Yaşam & İlham')}
          </h1>
          <p className="font-sans text-white/50 text-sm md:text-base max-w-lg mx-auto">
            {t('blog.subtitle', 'Ev tekstili, dekorasyon trendleri ve yaşam önerileri')}
          </p>
        </div>
        {/* wave divider */}
        <div className="h-8 bg-white" style={{ clipPath: 'ellipse(55% 100% at 50% 100%)' }} />
      </div>

      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-16">
        {loading ? (
          <div className="space-y-12">
            {/* featured skeleton */}
            <div className="animate-pulse grid md:grid-cols-2 gap-10 items-center">
              <div className="aspect-[4/3] bg-neutral-100 rounded-3xl" />
              <div className="space-y-4">
                <div className="h-3 bg-neutral-100 rounded w-1/4" />
                <div className="h-8 bg-neutral-100 rounded w-3/4" />
                <div className="h-4 bg-neutral-100 rounded" />
                <div className="h-4 bg-neutral-100 rounded w-5/6" />
              </div>
            </div>
            <div className="grid md:grid-cols-3 gap-8">
              {[0, 1].map(i => (
                <div key={i} className="animate-pulse space-y-3">
                  <div className="aspect-video bg-neutral-100 rounded-2xl" />
                  <div className="h-4 bg-neutral-100 rounded w-3/4" />
                  <div className="h-3 bg-neutral-100 rounded" />
                </div>
              ))}
            </div>
          </div>
        ) : posts.length === 0 ? (
          <div className="text-center py-32">
            <p className="font-serif text-3xl text-neutral-300 font-light mb-3">Henüz yazı yok</p>
            <p className="font-sans text-neutral-400 text-sm">Yakında güzel içerikler eklenecek.</p>
          </div>
        ) : (
          <>
            {/* Featured post */}
            {featured && (
              <Link to={lp(`/blog/${featured.slug}`)} className="group block mb-20">
                <article className="grid md:grid-cols-2 gap-8 md:gap-14 items-center">
                  <div className="aspect-[4/3] rounded-3xl overflow-hidden shadow-xl">
                    <PostImage src={featured.cover_image} alt={featured.title} />
                  </div>
                  <div>
                    <div className="flex items-center gap-4 mb-5">
                      <span className="font-sans text-xs bg-[#F8F6F3] text-[#8B7355] px-3 py-1 rounded-full tracking-wider uppercase">
                        {t('blog.featured', 'Öne Çıkan')}
                      </span>
                      {featured.published_at && (
                        <span className="flex items-center gap-1.5 font-sans text-xs text-neutral-400">
                          <Calendar className="w-3 h-3" />
                          {formatDate(featured.published_at)}
                        </span>
                      )}
                    </div>
                    <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light leading-tight mb-4 group-hover:text-[#8B7355] transition-colors duration-300">
                      {featured.title}
                    </h2>
                    {featured.excerpt && (
                      <p className="font-sans text-neutral-500 leading-relaxed mb-6 line-clamp-3">
                        {featured.excerpt}
                      </p>
                    )}
                    <div className="flex items-center gap-6">
                      <span className="inline-flex items-center gap-2 font-sans text-sm text-[#8B7355] group-hover:gap-3 transition-all duration-300">
                        {t('common.readMore', 'Devamını Oku')}
                        <ArrowRight className="w-4 h-4" />
                      </span>
                      <span className="flex items-center gap-1.5 font-sans text-xs text-neutral-400">
                        <Clock className="w-3 h-3" />
                        {readingTime(featured.excerpt)} dk okuma
                      </span>
                    </div>
                  </div>
                </article>
              </Link>
            )}

            {/* Divider */}
            {rest.length > 0 && (
              <div className="flex items-center gap-6 mb-12">
                <div className="flex-1 h-px bg-neutral-100" />
                <p className="font-sans text-xs text-neutral-400 tracking-widest uppercase whitespace-nowrap">
                  {t('blog.moreStories', 'Diğer Yazılar')}
                </p>
                <div className="flex-1 h-px bg-neutral-100" />
              </div>
            )}

            {/* Rest grid */}
            {rest.length > 0 && (
              <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                {rest.map(post => (
                  <Link key={post.id} to={lp(`/blog/${post.slug}`)} className="group block">
                    <article>
                      <div className="aspect-[4/3] rounded-2xl overflow-hidden shadow-sm mb-5">
                        <PostImage src={post.cover_image} alt={post.title} />
                      </div>
                      <div className="flex items-center gap-3 mb-3">
                        {post.published_at && (
                          <span className="flex items-center gap-1.5 font-sans text-xs text-neutral-400">
                            <Calendar className="w-3 h-3" />
                            {formatDate(post.published_at)}
                          </span>
                        )}
                        <span className="text-neutral-200">·</span>
                        <span className="flex items-center gap-1 font-sans text-xs text-neutral-400">
                          <Clock className="w-3 h-3" />
                          {readingTime(post.excerpt)} dk
                        </span>
                      </div>
                      <h2 className="font-serif text-xl text-neutral-800 font-light mb-2 group-hover:text-[#8B7355] transition-colors duration-300 line-clamp-2">
                        {post.title}
                      </h2>
                      {post.excerpt && (
                        <p className="font-sans text-sm text-neutral-500 leading-relaxed line-clamp-2">
                          {post.excerpt}
                        </p>
                      )}
                      <span className="inline-flex items-center gap-1.5 font-sans text-xs text-[#8B7355] mt-3 group-hover:gap-2.5 transition-all duration-300">
                        {t('common.readMore', 'Devamını Oku')}
                        <ArrowRight className="w-3 h-3" />
                      </span>
                    </article>
                  </Link>
                ))}
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}
