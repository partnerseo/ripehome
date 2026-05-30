import { useLayoutEffect, useEffect, useState, Component, type ReactNode } from 'react';
import { BrowserRouter as Router, Routes, Route, useLocation, useParams, Navigate, Outlet } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { CartProvider } from './context/CartContext';
import { AuthProvider } from './context/AuthContext';
import AuthModal from './components/AuthModal';
import { languages } from './i18n';
import { SUPPORTED_LANGS } from './hooks/useLang';
import { patternsFor, keyForSlug } from './lib/routes';
import { applyHead, setPrerenderReady, setSiteSeo } from './lib/seo';
import { getSiteSeo } from './lib/api';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import StickyContact from './components/StickyContact';
import Home from './pages/Home';
import ProductDetail from './pages/ProductDetail';
import CategoryPage from './pages/CategoryPage';
import WholesaleOrder from './pages/WholesaleOrder';
import About from './pages/About';
import Contact from './pages/Contact';
import FAQ from './pages/FAQ';
import Shipping from './pages/Shipping';
import Returns from './pages/Returns';
import Privacy from './pages/Privacy';
import Terms from './pages/Terms';
import AllProducts from './pages/AllProducts';
import Blog from './pages/Blog';
import BlogPost from './pages/BlogPost';
import MemberPanel from './pages/MemberPanel';
import NotFound from './pages/NotFound';

class ErrorBoundary extends Component<{ children: ReactNode }, { hasError: boolean }> {
  constructor(props: { children: ReactNode }) {
    super(props);
    this.state = { hasError: false };
  }
  static getDerivedStateFromError() {
    return { hasError: true };
  }
  render() {
    if (this.state.hasError) {
      return (
        <div className="min-h-screen flex items-center justify-center bg-[#F8F6F3]">
          <div className="text-center px-4">
            <h2 className="font-serif text-2xl text-neutral-800 mb-3">Bir şeyler ters gitti</h2>
            <p className="font-sans text-neutral-500 mb-6">Sayfa yüklenirken hata oluştu.</p>
            <button
              onClick={() => { this.setState({ hasError: false }); window.location.reload(); }}
              className="px-6 py-3 bg-[#8B7355] text-white rounded-xl font-sans text-sm hover:bg-[#6F5C46] transition-colors"
            >
              Sayfayı Yenile
            </button>
          </div>
        </div>
      );
    }
    return this.props.children;
  }
}

function PageTitle() {
  const { pathname } = useLocation();
  const { t, i18n } = useTranslation();

  useEffect(() => {
    const base = 'Ripe Home';
    const segments = pathname.split('/').filter(Boolean);
    // segments[0] = lang, segments[1] = page, segments[2] = slug
    const page = segments[1] || '';
    const slug = segments[2] || '';

    let title = base;
    const key = page ? keyForSlug(page) : undefined;
    if (!page) {
      title = `${base} — ${t('home.defaultSubtitle')}`;
    } else if (key === 'about') {
      title = `${t('nav.about')} — ${base}`;
    } else if (key === 'contact') {
      title = `${t('nav.contact')} — ${base}`;
    } else if (key === 'blog') {
      title = slug
        ? `${base} Blog`
        : `${t('blog.label')} — ${base}`;
    } else if (key === 'wholesale') {
      title = `${t('nav.wholesale')} — ${base}`;
    } else if (key === 'category') {
      title = slug
        ? `${slug.replace(/-/g, ' ')} — ${base}`
        : `${t('nav.products')} — ${base}`;
    } else if (key === 'product') {
      title = slug
        ? `${slug.replace(/-/g, ' ')} — ${base}`
        : `${base}`;
    } else if (key === 'products') {
      title = `${t('common.products')} — ${base}`;
    } else if (key === 'faq') {
      title = `${t('faq.title')} — ${base}`;
    } else if (key === 'shipping') {
      title = `${t('shipping.title')} — ${base}`;
    } else if (key === 'returns') {
      title = `${t('returns.title')} — ${base}`;
    } else if (key === 'privacy') {
      title = `${t('privacy.title')} — ${base}`;
    } else if (key === 'terms') {
      title = `${t('terms.title')} — ${base}`;
    } else if (key === 'member') {
      title = `${t('member.title')} — ${base}`;
    }

    document.title = title;
  }, [pathname, i18n.language]);

  return null;
}

function ScrollToTop() {
  const { pathname } = useLocation();

  useLayoutEffect(() => {
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }
    window.scrollTo({ top: 0, left: 0, behavior: 'instant' as ScrollBehavior });
    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
    requestAnimationFrame(() => {
      window.scrollTo({ top: 0, left: 0, behavior: 'instant' as ScrollBehavior });
    });
  }, [pathname]);

  return null;
}

function DetectLang() {
  return <Navigate to="/en" replace />;
}

function LangWrapper() {
  const { lang } = useParams<{ lang: string }>();
  const { i18n } = useTranslation();
  const isValid = !!lang && SUPPORTED_LANGS.includes(lang as any);

  useEffect(() => {
    if (!isValid || !lang) return;
    if (i18n.language !== lang) {
      i18n.changeLanguage(lang);
    }
    try { localStorage.setItem('ripehome-lang', lang); } catch { /* ignore */ }
    const langData = languages.find(l => l.code === lang);
    document.documentElement.dir = langData?.dir || 'ltr';
    document.documentElement.lang = lang;
  }, [lang, isValid]);

  if (!isValid) {
    return <Navigate to="/tr" replace />;
  }

  return <Outlet key={lang} />;
}

function SeoManager() {
  const { pathname } = useLocation();
  const { lang } = useParams<{ lang: string }>();
  const { i18n } = useTranslation();
  const [siteSeoTick, setSiteSeoTick] = useState(0);
  const [siteSeoDone, setSiteSeoDone] = useState(false);
  // Panel "Ayarlar → Site SEO" → seo_site_title/description çek.
  // Bitene kadar prerender bekler (varsayılan başlık yakalanmasın).
  useEffect(() => {
    let alive = true;
    getSiteSeo().then((s) => {
      if (!alive) return;
      setSiteSeo({ title: s.title, description: s.description });
    }).catch(() => {}).finally(() => {
      if (!alive) return;
      setSiteSeoTick((n) => n + 1);
      setSiteSeoDone(true);
    });
    return () => { alive = false; };
  }, []);
  useEffect(() => {
    const segs = pathname.split('/').filter(Boolean); // [lang, seg0, ...]
    const key = keyForSlug(segs[1] || '');
    // Bilinmeyen/404 yol: NotFound yönetsin, SeoManager dokunmasın
    // (yoksa NotFound'un prerender-status-code 404 meta'sını ezer).
    if (segs.length >= 2 && !key) return;
    // Dinamik detay (ürün/kategori/blog detay): SEO'yu o sayfa yönetir,
    // SeoManager dokunmaz (yoksa dil init'iyle title'ı ezer).
    const isDataPage =
      segs.length >= 3 && !!key && ['product', 'category', 'blog'].includes(key);
    if (isDataPage) return;
    applyHead({ pathname, lang: lang || 'tr' });
    if (siteSeoDone) setPrerenderReady(true);
  }, [pathname, lang, i18n.language, siteSeoTick, siteSeoDone]);
  return null;
}

function AppLayout() {
  return (
    <div className="min-h-screen bg-[#F8F6F3]">
      <PageTitle />
      <SeoManager />
      <Navbar />
      <ErrorBoundary>
        <Outlet />
      </ErrorBoundary>
      <Footer />
      <StickyContact />
      <AuthModal />
    </div>
  );
}

function App() {
  return (
    <Router>
      <CartProvider>
        <AuthProvider>
        <ScrollToTop />
        <Routes>
          <Route path="/" element={<DetectLang />} />
          <Route path="/:lang" element={<LangWrapper />}>
            <Route element={<AppLayout />}>
              <Route index element={<Home />} />
              {patternsFor('category', ':slug').map((p) => (
                <Route key={`cat-${p}`} path={p} element={<CategoryPage />} />
              ))}
              {patternsFor('product', ':slug').map((p) => (
                <Route key={`prd-${p}`} path={p} element={<ProductDetail />} />
              ))}
              {patternsFor('products').map((p) => (
                <Route key={`prds-${p}`} path={p} element={<AllProducts />} />
              ))}
              {patternsFor('wholesale').map((p) => (
                <Route key={`wh-${p}`} path={p} element={<WholesaleOrder />} />
              ))}
              {patternsFor('about').map((p) => (
                <Route key={`ab-${p}`} path={p} element={<About />} />
              ))}
              {patternsFor('contact').map((p) => (
                <Route key={`ct-${p}`} path={p} element={<Contact />} />
              ))}
              {patternsFor('faq').map((p) => (
                <Route key={`faq-${p}`} path={p} element={<FAQ />} />
              ))}
              {patternsFor('shipping').map((p) => (
                <Route key={`sh-${p}`} path={p} element={<Shipping />} />
              ))}
              {patternsFor('returns').map((p) => (
                <Route key={`rt-${p}`} path={p} element={<Returns />} />
              ))}
              {patternsFor('privacy').map((p) => (
                <Route key={`pv-${p}`} path={p} element={<Privacy />} />
              ))}
              {patternsFor('terms').map((p) => (
                <Route key={`tm-${p}`} path={p} element={<Terms />} />
              ))}
              {patternsFor('blog').map((p) => (
                <Route key={`bl-${p}`} path={p} element={<Blog />} />
              ))}
              {patternsFor('blog', ':slug').map((p) => (
                <Route key={`blp-${p}`} path={p} element={<BlogPost />} />
              ))}
              {patternsFor('member').map((p) => (
                <Route key={`mb-${p}`} path={p} element={<MemberPanel />} />
              ))}
              <Route path="*" element={<NotFound />} />
            </Route>
          </Route>
          <Route path="*" element={<DetectLang />} />
        </Routes>
      </AuthProvider>
      </CartProvider>
    </Router>
  );
}

export default App;
