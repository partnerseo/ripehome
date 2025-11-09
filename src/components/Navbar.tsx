import { useState, useEffect } from 'react';
import { ShoppingBag, Search, User, Menu, X } from 'lucide-react';
import { useNavigate } from 'react-router-dom';

const Navbar = () => {
  const navigate = useNavigate();
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 50);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const menuItems = [
    { name: 'Havlu', path: '/category/havlu' },
    { name: 'Nevresim', path: '/category/nevresim' },
    { name: 'Bornoz', path: '/category/bornoz' },
    { name: 'Yatak Örtüsü', path: '/category/yatak-ortusu' },
    { name: 'Çocuk Koleksiyonu', path: '/category/cocuk-koleksiyonu' },
    { name: 'Toptan Sipariş', path: '/toptan-siparis' },
  ];

  return (
    <nav
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${
        isScrolled
          ? 'bg-white/95 backdrop-blur-md shadow-lg py-4'
          : 'bg-white/90 backdrop-blur-sm shadow-md py-6'
      }`}
    >
      <div className="max-w-7xl mx-auto px-6 md:px-12 lg:px-24">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-8">
            <button
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
              className="md:hidden"
            >
              {isMobileMenuOpen ? (
                <X className="w-6 h-6 text-neutral-800" />
              ) : (
                <Menu className="w-6 h-6 text-neutral-800" />
              )}
            </button>

            <button onClick={() => navigate('/')} className="flex items-center">
              <div className="flex flex-col">
                <span className="font-serif text-3xl font-light tracking-wide transition-colors duration-300 text-neutral-800">
                  LUXURY
                </span>
                <span className="font-sans text-xs tracking-[0.3em] uppercase transition-colors duration-300 text-neutral-600">
                  Home Textiles
                </span>
              </div>
            </button>
          </div>

          <div className="hidden md:flex items-center gap-8">
            {menuItems.map((item, index) => (
              <button
                key={index}
                onClick={() => navigate(item.path)}
                className="font-sans text-sm tracking-wide hover:opacity-70 transition-all duration-300 relative group text-neutral-800"
              >
                {item.name}
                <span className="absolute -bottom-1 left-0 w-0 h-px group-hover:w-full transition-all duration-300 bg-neutral-800" />
              </button>
            ))}
          </div>

          <div className="flex items-center gap-6">
            <button className="hover:opacity-70 transition-opacity duration-300 text-neutral-800">
              <Search className="w-5 h-5" strokeWidth={1.5} />
            </button>
            <button className="hover:opacity-70 transition-opacity duration-300 text-neutral-800">
              <User className="w-5 h-5" strokeWidth={1.5} />
            </button>
            <button className="relative hover:opacity-70 transition-opacity duration-300 text-neutral-800">
              <ShoppingBag className="w-5 h-5" strokeWidth={1.5} />
              <span className="absolute -top-2 -right-2 bg-[#8B7355] text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-sans">
                0
              </span>
            </button>
          </div>
        </div>

        {isMobileMenuOpen && (
          <div className="md:hidden mt-6 pt-6 border-t border-neutral-200 max-h-80 overflow-y-auto">
            <div className="flex flex-col gap-4 pb-4">
              {menuItems.map((item, index) => (
                <button
                  key={index}
                  onClick={() => {
                    navigate(item.path);
                    setIsMobileMenuOpen(false);
                  }}
                  className="font-sans text-sm tracking-wide hover:opacity-70 transition-opacity duration-300 py-2 text-left text-neutral-800"
                >
                  {item.name}
                </button>
              ))}
            </div>
          </div>
        )}
      </div>
    </nav>
  );
};

export default Navbar;
