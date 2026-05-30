import { useState, useEffect } from 'react';
import { useParams, Link, useLocation, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useLangPath, useLang } from '../hooks/useLang';
import { slugFor } from '../lib/routes';
import { applyHead, setPrerenderReady } from '../lib/seo';
import { getBlogPost } from '../lib/api';

const IS_PRERENDER =
  typeof navigator !== 'undefined' && /PrerenderBot/i.test(navigator.userAgent);
import { Calendar, Clock, ArrowLeft, Share2 } from 'lucide-react';

interface BlogPostData {
  id: number;
  title: string;
  slug: string;
  content: string;
  excerpt: string;
  cover_image: string | null;
  published_at: string;
}

export default function BlogPost() {
  const { slug } = useParams<{ slug: string }>();
  const { i18n } = useTranslation();
  const lp = useLangPath();
  const lang = useLang();
  const location = useLocation();
  const navigate = useNavigate();
  const [post, setPost] = useState<BlogPostData | null>(null);
  const [loading, setLoading] = useState(true);
  const [notFound, setNotFound] = useState(false);
  const [copied, setCopied] = useState(false);

  useEffect(() => {
    if (!slug) return;
    setLoading(true);
    setNotFound(false);
    getBlogPost(slug).then(data => {
      if (!data || data.message) setNotFound(true);
      else setPost(data);
    }).finally(() => setLoading(false));
  }, [slug, i18n.language]);

  useEffect(() => {
    if (!post) return;
    const canonical = `/${lang}/${slugFor('blog', lang)}/${post.slug}`;
    applyHead({
      pathname: location.pathname,
      lang,
      title: (post as { meta_title?: string }).meta_title || post.title,
      description: ((post as { meta_description?: string }).meta_description
        || post.excerpt || post.content || '')
        .replace(/<[^>]*>/g, '').slice(0, 160),
      canonicalPath: canonical,
      image: typeof post.cover_image === 'string' ? post.cover_image : undefined,
    });
    setPrerenderReady(true);
    if (slug && post.slug && slug !== post.slug && !IS_PRERENDER) {
      navigate(canonical, { replace: true });
    }
  }, [post, lang]);

  const handleShare = () => {
    navigator.clipboard.writeText(window.location.href).then(() => {
      setCopied(true);
      setTimeout(() => setCopied(false), 2000);
    });
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-white pt-20">
        <div className="animate-pulse">
          <div className="w-full h-72 md:h-[480px] bg-neutral-100" />
          <div className="max-w-2xl mx-auto px-4 py-12 space-y-4">
            <div className="h-3 bg-neutral-100 rounded w-1/4" />
            <div className="h-10 bg-neutral-100 rounded w-3/4" />
            <div className="h-10 bg-neutral-100 rounded w-1/2" />
            <div className="space-y-2 pt-4">
              {[...Array(6)].map((_, i) => <div key={i} className="h-4 bg-neutral-100 rounded" />)}
            </div>
          </div>
        </div>
      </div>
    );
  }

  if (notFound || !post) {
    return (
      <div className="min-h-screen bg-white pt-20 flex items-center justify-center">
        <div className="text-center">
          <p className="font-serif text-3xl text-neutral-300 font-light mb-4">Yazı bulunamadı</p>
          <Link to={lp('/blog')} className="inline-flex items-center gap-2 font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors">
            <ArrowLeft className="w-4 h-4" /> Bloga Dön
          </Link>
        </div>
      </div>
    );
  }

  const readMins = Math.max(2, Math.ceil((post.content?.replace(/<[^>]+>/g, '').length || 0) / 1000));
  const formatDate = (d: string) =>
    new Date(d).toLocaleDateString(i18n.language === 'tr' ? 'tr-TR' : 'en-US', {
      year: 'numeric', month: 'long', day: 'numeric',
    });

  return (
    <div className="min-h-screen bg-white">
      {/* Cover */}
      <div className="relative pt-20">
        {post.cover_image ? (
          <div className="w-full h-72 md:h-[480px] overflow-hidden">
            <img
              src={`https://ripehome.com.tr/storage/${post.cover_image}`}
              alt={post.title}
              className="w-full h-full object-cover"
            />
            <div className="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent" />
          </div>
        ) : (
          <div className="w-full h-48 bg-gradient-to-br from-[#F8F6F3] to-[#E5DDD1]" />
        )}
      </div>

      {/* Content */}
      <div className="max-w-2xl mx-auto px-4 md:px-8">
        {/* Back + share */}
        <div className="flex items-center justify-between py-8">
          <Link
            to={lp('/blog')}
            className="inline-flex items-center gap-2 font-sans text-sm text-neutral-500 hover:text-[#8B7355] transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Tüm Yazılar
          </Link>
          <button
            onClick={handleShare}
            className="inline-flex items-center gap-2 font-sans text-sm text-neutral-500 hover:text-[#8B7355] transition-colors"
          >
            <Share2 className="w-4 h-4" />
            {copied ? 'Kopyalandı!' : 'Paylaş'}
          </button>
        </div>

        {/* Meta */}
        <div className="flex items-center gap-4 mb-6">
          {post.published_at && (
            <span className="flex items-center gap-1.5 font-sans text-xs text-neutral-400">
              <Calendar className="w-3.5 h-3.5" />
              {formatDate(post.published_at)}
            </span>
          )}
          <span className="text-neutral-200">·</span>
          <span className="flex items-center gap-1.5 font-sans text-xs text-neutral-400">
            <Clock className="w-3.5 h-3.5" />
            {readMins} dakika okuma
          </span>
        </div>

        {/* Title */}
        <h1 className="font-serif text-3xl md:text-5xl text-neutral-800 font-light leading-tight mb-6">
          {post.title}
        </h1>

        {/* Excerpt */}
        {post.excerpt && (
          <p className="font-sans text-lg text-neutral-500 leading-relaxed mb-10 border-l-2 border-[#C9B7A1] pl-5 italic">
            {post.excerpt}
          </p>
        )}

        {/* Divider */}
        <div className="flex items-center gap-4 mb-10">
          <div className="flex-1 h-px bg-neutral-100" />
          <span className="font-serif text-[#C9B7A1] text-lg">✦</span>
          <div className="flex-1 h-px bg-neutral-100" />
        </div>

        {/* Body */}
        <div
          className="font-sans text-neutral-700 leading-[1.9] prose prose-neutral prose-lg max-w-none
            prose-headings:font-serif prose-headings:font-light prose-headings:text-neutral-800
            prose-p:text-neutral-600 prose-p:leading-[1.9]
            prose-a:text-[#8B7355] prose-a:no-underline hover:prose-a:underline
            prose-strong:text-neutral-800 prose-strong:font-medium
            prose-img:rounded-2xl prose-img:shadow-md
            mb-16"
          dangerouslySetInnerHTML={{ __html: post.content }}
        />

        {/* Footer */}
        <div className="border-t border-neutral-100 pt-10 pb-20 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
          <Link
            to={lp('/blog')}
            className="inline-flex items-center gap-2 font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors"
          >
            <ArrowLeft className="w-4 h-4" />
            Tüm Yazılara Dön
          </Link>
          <button
            onClick={handleShare}
            className="inline-flex items-center gap-2 font-sans text-sm bg-[#F8F6F3] text-[#8B7355] hover:bg-[#EDE8E1] px-4 py-2 rounded-full transition-colors"
          >
            <Share2 className="w-4 h-4" />
            {copied ? 'Link Kopyalandı!' : 'Yazıyı Paylaş'}
          </button>
        </div>
      </div>
    </div>
  );
}
