import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useLangPath } from '../hooks/useLang';
import { getBlogPosts } from '../lib/api';
import { Calendar, ArrowRight } from 'lucide-react';

interface BlogPost {
  id: number;
  title: string;
  slug: string;
  excerpt: string;
  cover_image: string | null;
  published_at: string;
}

export default function BlogPreview() {
  const { t, i18n } = useTranslation();
  const lp = useLangPath();
  const [posts, setPosts] = useState<BlogPost[]>([]);

  useEffect(() => {
    getBlogPosts().then(data => {
      if (Array.isArray(data) && data.length > 0) setPosts(data.slice(0, 3));
    });
  }, [i18n.language]);

  if (posts.length === 0) return null;

  return (
    <section className="py-16 md:py-24 px-4 md:px-12 lg:px-24 bg-[#F8F6F3]">
      <div className="max-w-7xl mx-auto">
        <div className="flex items-end justify-between mb-10">
          <div>
            <p className="font-sans text-[#8B7355] text-xs tracking-[0.25em] uppercase mb-2">
              {t('blog.label', 'Blog')}
            </p>
            <h2 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">
              {t('blog.latestPosts', 'Son Yazılar')}
            </h2>
          </div>
          <Link
            to={lp('/blog')}
            className="hidden md:flex items-center gap-2 font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors"
          >
            {t('common.seeAll')}
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>

        <div className="grid md:grid-cols-3 gap-6">
          {posts.map(post => (
            <Link key={post.id} to={lp(`/blog/${post.slug}`)} className="group block">
              <article className="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
                <div className="aspect-video overflow-hidden">
                  {post.cover_image ? (
                    <img
                      src={`https://ripehome.com.tr/storage/${post.cover_image}`}
                      alt={post.title}
                      className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                  ) : (
                    <div className="w-full h-full bg-gradient-to-br from-[#F8F6F3] to-[#E5DDD1] flex items-center justify-center">
                      <span className="font-serif text-4xl text-[#C9B7A1]">R</span>
                    </div>
                  )}
                </div>
                <div className="p-5">
                  {post.published_at && (
                    <div className="flex items-center gap-1.5 text-xs text-neutral-400 font-sans mb-2">
                      <Calendar className="w-3 h-3" />
                      {new Date(post.published_at).toLocaleDateString(
                        i18n.language === 'tr' ? 'tr-TR' : 'en-US',
                        { year: 'numeric', month: 'long', day: 'numeric' }
                      )}
                    </div>
                  )}
                  <h3 className="font-serif text-lg text-neutral-800 font-light group-hover:text-[#8B7355] transition-colors line-clamp-2">
                    {post.title}
                  </h3>
                  {post.excerpt && (
                    <p className="font-sans text-sm text-neutral-500 mt-2 line-clamp-2 leading-relaxed">
                      {post.excerpt}
                    </p>
                  )}
                </div>
              </article>
            </Link>
          ))}
        </div>

        <div className="mt-8 text-center md:hidden">
          <Link
            to={lp('/blog')}
            className="inline-flex items-center gap-2 font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors"
          >
            {t('common.seeAll')} <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </section>
  );
}
