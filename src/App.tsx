import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import Home from './pages/Home';
import ProductDetail from './pages/ProductDetail';
import CategoryPage from './pages/CategoryPage';
import WholesaleOrder from './pages/WholesaleOrder';

function App() {
  return (
    <Router>
      <div className="min-h-screen bg-[#F8F6F3]">
        <Navbar />
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/category/:slug" element={<CategoryPage />} />
          <Route path="/product/:slug" element={<ProductDetail />} />
          <Route path="/toptan-siparis" element={<WholesaleOrder />} />
        </Routes>
        <Footer />
      </div>
    </Router>
  );
}

export default App;
