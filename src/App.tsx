import { useEffect } from 'react';
import { BrowserRouter as Router, Routes, Route, useLocation } from 'react-router-dom';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import Home from './pages/Home';
import ProductDetail from './pages/ProductDetail';
import CategoryPage from './pages/CategoryPage';
import WholesaleOrder from './pages/WholesaleOrder';
import About from './pages/About';
import Contact from './pages/Contact';
import FAQ from './pages/FAQ';
import Shipping from './pages/Shipping';
import Returns from './pages/Returns';

// Scroll to top on route change and page load
function ScrollToTop() {
  const { pathname } = useLocation();

  useEffect(() => {
    window.scrollTo({
      top: 0,
      left: 0,
      behavior: 'instant'
    });
  }, [pathname]);

  return null;
}

function App() {
  return (
    <Router>
      <ScrollToTop />
      <div className="min-h-screen bg-[#F8F6F3]">
        <Navbar />
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/category/:slug" element={<CategoryPage />} />
          <Route path="/product/:slug" element={<ProductDetail />} />
          <Route path="/toptan-siparis" element={<WholesaleOrder />} />
          <Route path="/hakkimizda" element={<About />} />
          <Route path="/iletisim" element={<Contact />} />
          <Route path="/sss" element={<FAQ />} />
          <Route path="/kargo" element={<Shipping />} />
          <Route path="/iade-ve-degisim" element={<Returns />} />
        </Routes>
        <Footer />
      </div>
    </Router>
  );
}

export default App;
