import { useLayoutEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, useLocation } from 'react-router-dom';
import { CartProvider } from './context/CartContext';
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
import NotFound from './pages/NotFound';

function ScrollToTop() {
  const { pathname } = useLocation();

  useLayoutEffect(() => {
    if ('scrollRestoration' in history) {
      history.scrollRestoration = 'manual';
    }
    window.scrollTo(0, 0);
    document.documentElement.scrollTop = 0;
    document.body.scrollTop = 0;
  }, [pathname]);

  return null;
}

function App() {
  return (
    <Router>
      <CartProvider>
      <ScrollToTop />
      <div className="min-h-screen bg-[#F8F6F3]">
        <Navbar />
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/kategori/:slug" element={<CategoryPage />} />
          <Route path="/urun/:slug" element={<ProductDetail />} />
          <Route path="/toptan-siparis" element={<WholesaleOrder />} />
          <Route path="/hakkimizda" element={<About />} />
          <Route path="/iletisim" element={<Contact />} />
          <Route path="/sss" element={<FAQ />} />
          <Route path="/kargo-teslimat" element={<Shipping />} />
          <Route path="/iade-degisim" element={<Returns />} />
          <Route path="/gizlilik-politikasi" element={<Privacy />} />
          <Route path="/kullanim-kosullari" element={<Terms />} />
          <Route path="*" element={<NotFound />} />
        </Routes>
        <Footer />
        <StickyContact />
      </div>
      </CartProvider>
    </Router>
  );
}

export default App;
