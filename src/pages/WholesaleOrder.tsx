import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { ArrowLeft, ShoppingBag, Plus, Minus, Trash2, ChevronRight } from 'lucide-react';
import { getProducts, createWholesaleOrder } from '@/lib/api';
import { useCart } from '@/context/CartContext';
import type { Product, WholesaleOrderForm } from '@/types/api';

export default function WholesaleOrder() {
  const navigate = useNavigate();
  const { cart, addToCart, updateQuantity, removeFromCart, clearCart, cartCount } = useCart();

  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);
  const [formData, setFormData] = useState<Omit<WholesaleOrderForm, 'items'>>({
    company_name: '',
    contact_person: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    tax_office: '',
    tax_number: '',
    additional_notes: '',
  });
  const [step, setStep] = useState<'products' | 'form'>('products');
  const [submitting, setSubmitting] = useState(false);
  const [success, setSuccess] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [showAllProducts, setShowAllProducts] = useState(false);

  useEffect(() => {
    async function fetchProducts() {
      try {
        const data = await getProducts(1, 500);
        setProducts(data.data || []);
      } catch (error) {
        console.error('Ürünler yüklenemedi:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchProducts();
  }, []);

  const getCartQuantity = (productId: number) => {
    return cart.find(item => item.product_id === productId)?.quantity || 0;
  };

  const getCartNotes = (productId: number) => {
    return cart.find(item => item.product_id === productId)?.notes || '';
  };

  const handleAddToCart = (productId: number, quantity: number, notes?: string) => {
    if (quantity <= 0) {
      removeFromCart(productId);
      return;
    }
    const existing = cart.find(i => i.product_id === productId);
    if (existing) {
      updateQuantity(productId, quantity, notes);
    } else {
      const product = products.find(p => p.id === productId);
      addToCart({
        product_id: productId,
        product_name: product?.name,
        product_slug: product?.slug,
        product_image: product?.images?.[0],
        quantity,
        notes,
      });
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (cart.length === 0) {
      alert('Lütfen en az bir ürün seçin');
      return;
    }
    setSubmitting(true);
    try {
      const result = await createWholesaleOrder({ ...formData, items: cart });
      if (result.success) {
        setSuccess(true);
        clearCart();
        setFormData({
          company_name: '', contact_person: '', email: '', phone: '',
          address: '', city: '', tax_office: '', tax_number: '', additional_notes: '',
        });
      } else {
        alert(result.message || 'Bir hata oluştu');
      }
    } catch {
      alert('Sipariş gönderilemedi, lütfen tekrar deneyin');
    } finally {
      setSubmitting(false);
    }
  };

  const filteredProducts = products.filter(p =>
    p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    p.category?.name?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  if (loading) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20">
        <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-12">
          <div className="animate-pulse space-y-6">
            <div className="h-10 bg-gray-200 rounded w-64"></div>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {[...Array(8)].map((_, i) => (
                <div key={i} className="bg-white rounded-xl p-4 space-y-3">
                  <div className="aspect-square bg-gray-200 rounded-lg"></div>
                  <div className="h-4 bg-gray-200 rounded w-3/4"></div>
                  <div className="h-8 bg-gray-200 rounded"></div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    );
  }

  if (success) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-20 flex items-center justify-center">
        <div className="max-w-lg mx-auto text-center px-4">
          <div className="bg-white rounded-2xl shadow-lg p-10">
            <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
              <svg className="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
              </svg>
            </div>
            <h1 className="font-serif text-3xl text-neutral-800 mb-4">Talebiniz Alındı!</h1>
            <p className="font-sans text-neutral-600 mb-8 leading-relaxed">
              Toptan sipariş talebiniz başarıyla kaydedildi. En kısa sürede size özel fiyat teklifi ile dönüş yapacağız.
            </p>
            <div className="flex gap-3 justify-center">
              <button
                onClick={() => { setSuccess(false); setStep('products'); }}
                className="px-6 py-3 bg-[#8B7355] text-white rounded-xl font-sans font-medium hover:bg-[#6F5C46] transition-colors"
              >
                Yeni Sipariş Oluştur
              </button>
              <button
                onClick={() => navigate('/')}
                className="px-6 py-3 border border-neutral-200 text-neutral-700 rounded-xl font-sans font-medium hover:bg-white transition-colors"
              >
                Ana Sayfa
              </button>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-[#F8F6F3] pt-20">
      <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-8">
        {/* Header */}
        <div className="mb-8">
          <button
            onClick={() => navigate(-1)}
            className="flex items-center gap-2 text-neutral-500 hover:text-neutral-800 transition-colors mb-4 font-sans text-sm"
          >
            <ArrowLeft className="w-4 h-4" />
            Geri Dön
          </button>
          <h1 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">Toptan Sipariş</h1>
          <p className="font-sans text-neutral-500 mt-2">Ürünlerinizi seçin, adetleri belirleyin ve teklif talep edin</p>
        </div>

        {/* Steps */}
        <div className="flex items-center gap-3 mb-8">
          <button
            onClick={() => setStep('products')}
            className={`flex items-center gap-2 px-5 py-2.5 rounded-full font-sans text-sm font-medium transition-all ${
              step === 'products'
                ? 'bg-[#8B7355] text-white shadow-md'
                : 'bg-white text-neutral-600 border border-neutral-200 hover:border-[#8B7355]'
            }`}
          >
            <ShoppingBag className="w-4 h-4" />
            1. Ürün Seçimi
            {cart.length > 0 && step !== 'products' && (
              <span className="ml-1 bg-white/20 px-2 py-0.5 rounded-full text-xs">{cart.length}</span>
            )}
          </button>
          <ChevronRight className="w-4 h-4 text-neutral-400" />
          <button
            onClick={() => cart.length > 0 && setStep('form')}
            className={`flex items-center gap-2 px-5 py-2.5 rounded-full font-sans text-sm font-medium transition-all ${
              step === 'form'
                ? 'bg-[#8B7355] text-white shadow-md'
                : cart.length > 0
                  ? 'bg-white text-neutral-600 border border-neutral-200 hover:border-[#8B7355] cursor-pointer'
                  : 'bg-gray-100 text-neutral-400 border border-gray-200 cursor-not-allowed'
            }`}
          >
            2. Bilgiler & Gönder
          </button>
        </div>

        {/* Step 1: Product Selection */}
        {step === 'products' && (
          <>
            {/* Cart Items Section */}
            {cart.length > 0 && !showAllProducts && (
              <div className="pb-28">
                <div className="mb-6">
                  <h2 className="font-serif text-xl text-neutral-800 mb-1">Sepetinizdeki Ürünler</h2>
                  <p className="font-sans text-sm text-neutral-500">{cart.length} ürün, toplam {cart.reduce((sum, item) => sum + item.quantity, 0)} adet</p>
                </div>

                <div className="space-y-4 mb-8">
                  {cart.map(item => {
                    const product = products.find(p => p.id === item.product_id);
                    const itemImage = product?.images?.[0] || item.product_image;
                    const itemName = product?.name || item.product_name || 'Ürün';

                    return (
                      <div key={item.product_id} className="bg-white rounded-xl border border-neutral-100 p-4 flex gap-4 items-start hover:shadow-sm transition-all">
                        <div className="w-20 h-20 md:w-24 md:h-24 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                          {itemImage ? (
                            <img src={itemImage} alt={itemName} className="w-full h-full object-cover" />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center text-neutral-300 text-xs">Görsel</div>
                          )}
                        </div>
                        <div className="flex-1 min-w-0">
                          <h4 className="font-sans text-sm md:text-base font-medium text-neutral-800 line-clamp-2">{itemName}</h4>
                          {product?.category && (
                            <p className="font-sans text-xs text-neutral-400 mt-0.5">{product.category.name}</p>
                          )}
                          <div className="flex items-center gap-2 mt-3">
                            <button
                              onClick={() => {
                                const s = product?.min_order && product.min_order >= 50 ? 50 : 10;
                                handleAddToCart(item.product_id, Math.max(0, item.quantity - s), item.notes);
                              }}
                              className="w-8 h-8 flex items-center justify-center bg-neutral-100 text-neutral-600 rounded-lg hover:bg-neutral-200 transition-colors"
                            >
                              <Minus className="w-3.5 h-3.5" />
                            </button>
                            <input
                              type="number"
                              min="0"
                              value={item.quantity}
                              onChange={(e) => handleAddToCart(item.product_id, parseInt(e.target.value) || 0, item.notes)}
                              className="w-20 text-center border border-neutral-200 rounded-lg py-1.5 font-sans text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30"
                            />
                            <button
                              onClick={() => {
                                const s = product?.min_order && product.min_order >= 50 ? 50 : 10;
                                handleAddToCart(item.product_id, item.quantity + s, item.notes);
                              }}
                              className="w-8 h-8 flex items-center justify-center bg-neutral-100 text-neutral-600 rounded-lg hover:bg-neutral-200 transition-colors"
                            >
                              <Plus className="w-3.5 h-3.5" />
                            </button>
                            <span className="font-sans text-xs text-neutral-400">adet</span>
                          </div>
                          <textarea
                            placeholder="Not ekleyin (opsiyonel)"
                            className="w-full border border-neutral-200 rounded-lg px-3 py-2 text-xs font-sans focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 resize-none mt-2"
                            rows={2}
                            value={item.notes || ''}
                            onChange={(e) => handleAddToCart(item.product_id, item.quantity, e.target.value)}
                          />
                        </div>
                        <button
                          onClick={() => removeFromCart(item.product_id)}
                          className="flex-shrink-0 w-9 h-9 flex items-center justify-center text-neutral-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    );
                  })}
                </div>

                <button
                  onClick={() => setShowAllProducts(true)}
                  className="w-full py-4 border-2 border-dashed border-neutral-300 rounded-xl text-neutral-600 hover:border-[#8B7355] hover:text-[#8B7355] transition-colors font-sans font-medium flex items-center justify-center gap-2"
                >
                  <Plus className="w-5 h-5" />
                  Daha Fazla Ürün Ekle
                </button>
              </div>
            )}

            {/* All Products Browser (when cart empty or user clicked "Daha Fazla Ürün Ekle") */}
            {(cart.length === 0 || showAllProducts) && (
              <>
                {showAllProducts && cart.length > 0 && (
                  <button
                    onClick={() => setShowAllProducts(false)}
                    className="mb-4 flex items-center gap-2 text-[#8B7355] hover:text-[#6F5C46] font-sans text-sm font-medium transition-colors"
                  >
                    <ArrowLeft className="w-4 h-4" />
                    Sepete Dön ({cart.length} ürün)
                  </button>
                )}

                {/* Search */}
                <div className="mb-6">
                  <input
                    type="text"
                    placeholder="Ürün veya kategori ara..."
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="w-full md:w-96 px-4 py-3 bg-white border border-neutral-200 rounded-xl font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                  />
                </div>

                {filteredProducts.length === 0 ? (
                  <div className="text-center py-16">
                    <p className="font-sans text-neutral-500">Ürün bulunamadı.</p>
                  </div>
                ) : (
                  <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 pb-28">
                    {filteredProducts.map(product => {
                      const inCart = getCartQuantity(product.id) > 0;
                      return (
                        <div
                          key={product.id}
                          className={`bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 border-2 ${
                            inCart ? 'border-[#8B7355]' : 'border-transparent'
                          }`}
                        >
                          <div className="aspect-square overflow-hidden relative">
                            <img
                              src={product.images?.[0] || '/pexels-cottonbro-4327012.jpg'}
                              alt={product.name}
                              className="w-full h-full object-cover"
                              loading="lazy"
                            />
                            {inCart && (
                              <div className="absolute top-2 right-2 bg-[#8B7355] text-white text-xs font-sans font-medium px-2.5 py-1 rounded-full">
                                {getCartQuantity(product.id)} adet
                              </div>
                            )}
                          </div>
                          <div className="p-3 md:p-4">
                            <h3 className="font-sans text-sm font-medium text-neutral-800 mb-1 line-clamp-2">{product.name}</h3>
                            {product.category && (
                              <p className="font-sans text-xs text-neutral-400 mb-3">{product.category.name}</p>
                            )}

                            {!inCart ? (
                              <button
                                onClick={() => handleAddToCart(product.id, product.min_order || 100)}
                                className="w-full flex items-center justify-center gap-1.5 py-2 bg-[#F8F6F3] text-[#8B7355] rounded-lg font-sans text-sm font-medium hover:bg-[#8B7355] hover:text-white transition-colors"
                              >
                                <Plus className="w-4 h-4" />
                                Sepete Ekle
                              </button>
                            ) : (
                              <div className="space-y-2">
                                <div className="flex items-center gap-2">
                                  <button
                                    onClick={() => {
                                      const current = getCartQuantity(product.id);
                                      const s = product.min_order && product.min_order >= 50 ? 50 : 10;
                                      handleAddToCart(product.id, Math.max(0, current - s), getCartNotes(product.id));
                                    }}
                                    className="w-8 h-8 flex items-center justify-center bg-neutral-100 text-neutral-600 rounded-lg hover:bg-neutral-200 transition-colors"
                                  >
                                    <Minus className="w-3.5 h-3.5" />
                                  </button>
                                  <input
                                    type="number"
                                    min="0"
                                    value={getCartQuantity(product.id)}
                                    onChange={(e) => handleAddToCart(product.id, parseInt(e.target.value) || 0, getCartNotes(product.id))}
                                    className="flex-1 text-center border border-neutral-200 rounded-lg py-1.5 font-sans text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30"
                                  />
                                  <button
                                    onClick={() => {
                                      const current = getCartQuantity(product.id);
                                      const s = product.min_order && product.min_order >= 50 ? 50 : 10;
                                      handleAddToCart(product.id, current + s, getCartNotes(product.id));
                                    }}
                                    className="w-8 h-8 flex items-center justify-center bg-neutral-100 text-neutral-600 rounded-lg hover:bg-neutral-200 transition-colors"
                                  >
                                    <Plus className="w-3.5 h-3.5" />
                                  </button>
                                </div>
                                <textarea
                                  placeholder="Not ekleyin (opsiyonel)"
                                  className="w-full border border-neutral-200 rounded-lg px-3 py-2 text-xs font-sans focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 resize-none"
                                  rows={2}
                                  value={getCartNotes(product.id)}
                                  onChange={(e) => handleAddToCart(product.id, getCartQuantity(product.id), e.target.value)}
                                />
                              </div>
                            )}
                          </div>
                        </div>
                      );
                    })}
                  </div>
                )}
              </>
            )}

            {/* Bottom Cart Bar */}
            {cart.length > 0 && (
              <div className="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-neutral-200 shadow-lg z-50">
                <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-4">
                  <div className="flex items-center justify-between">
                    <div>
                      <div className="font-sans font-medium text-neutral-800">
                        {cart.length} ürün, toplam {cart.reduce((sum, item) => sum + item.quantity, 0)} adet
                      </div>
                    </div>
                    <button
                      onClick={() => setStep('form')}
                      className="flex items-center gap-2 bg-[#8B7355] text-white px-6 py-3 rounded-xl font-sans font-medium hover:bg-[#6F5C46] transition-colors shadow-lg"
                    >
                      Devam Et
                      <ChevronRight className="w-4 h-4" />
                    </button>
                  </div>
                </div>
              </div>
            )}
          </>
        )}

        {/* Step 2: Form */}
        {step === 'form' && (
          <div className="grid lg:grid-cols-5 gap-8">
            {/* Form - Sol */}
            <div className="lg:col-span-3">
              <div className="bg-white rounded-2xl border border-neutral-100 p-6 md:p-8">
                <h2 className="font-serif text-2xl text-neutral-800 mb-6">Şirket Bilgileriniz</h2>
                <form onSubmit={handleSubmit} className="space-y-5">
                  <div className="grid md:grid-cols-2 gap-4">
                    <div>
                      <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">Şirket Adı *</label>
                      <input
                        type="text"
                        required
                        placeholder="Şirket adınız"
                        value={formData.company_name}
                        onChange={(e) => setFormData({ ...formData, company_name: e.target.value })}
                        className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                      />
                    </div>
                    <div>
                      <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">Yetkili Kişi *</label>
                      <input
                        type="text"
                        required
                        placeholder="Ad Soyad"
                        value={formData.contact_person}
                        onChange={(e) => setFormData({ ...formData, contact_person: e.target.value })}
                        className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                      />
                    </div>
                  </div>

                  <div className="grid md:grid-cols-2 gap-4">
                    <div>
                      <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">E-posta *</label>
                      <input
                        type="email"
                        required
                        placeholder="ornek@firma.com"
                        value={formData.email}
                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                      />
                    </div>
                    <div>
                      <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">Telefon *</label>
                      <input
                        type="tel"
                        required
                        placeholder="0555 555 5555"
                        value={formData.phone}
                        onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                        className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                      />
                    </div>
                  </div>

                  <div className="grid md:grid-cols-2 gap-4">
                    <div>
                      <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">Şehir</label>
                      <input
                        type="text"
                        placeholder="İstanbul"
                        value={formData.city}
                        onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                        className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                      />
                    </div>
                    <div>
                      <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">Adres</label>
                      <input
                        type="text"
                        placeholder="Adres"
                        value={formData.address}
                        onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                        className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                      />
                    </div>
                  </div>

                  <div className="grid md:grid-cols-2 gap-4">
                    <div>
                      <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">Vergi Dairesi</label>
                      <input
                        type="text"
                        placeholder="Vergi Dairesi"
                        value={formData.tax_office}
                        onChange={(e) => setFormData({ ...formData, tax_office: e.target.value })}
                        className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                      />
                    </div>
                    <div>
                      <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">Vergi Numarası</label>
                      <input
                        type="text"
                        placeholder="Vergi Numarası"
                        value={formData.tax_number}
                        onChange={(e) => setFormData({ ...formData, tax_number: e.target.value })}
                        className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block font-sans text-sm font-medium text-neutral-700 mb-1.5">Ek Notlarınız</label>
                    <textarea
                      placeholder="Özel istekleriniz, renk/beden tercihleri vb..."
                      rows={4}
                      value={formData.additional_notes}
                      onChange={(e) => setFormData({ ...formData, additional_notes: e.target.value })}
                      className="w-full border border-neutral-200 rounded-xl px-4 py-3 font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355] resize-none"
                    />
                  </div>

                  <button
                    type="submit"
                    disabled={submitting || cart.length === 0}
                    className="w-full bg-[#8B7355] text-white py-4 rounded-xl font-sans font-medium text-lg hover:bg-[#6F5C46] transition-colors shadow-lg shadow-[#8B7355]/20 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:shadow-none"
                  >
                    {submitting ? 'Gönderiliyor...' : 'Teklif Talep Et'}
                  </button>
                </form>
              </div>
            </div>

            {/* Sipariş Özeti - Sağ */}
            <div className="lg:col-span-2">
              <div className="bg-white rounded-2xl border border-neutral-100 p-6 sticky top-28">
                <div className="flex items-center justify-between mb-5">
                  <h2 className="font-serif text-xl text-neutral-800">Sipariş Özeti</h2>
                  <button
                    onClick={() => setStep('products')}
                    className="font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors flex items-center gap-1"
                  >
                    <Plus className="w-3.5 h-3.5" />
                    Ürün Ekle
                  </button>
                </div>

                <div className="space-y-4 max-h-[500px] overflow-y-auto">
                  {cart.map(item => {
                    const product = products.find(p => p.id === item.product_id);
                    const itemImage = product?.images?.[0] || item.product_image;
                    const itemName = product?.name || item.product_name || 'Ürün';

                    return (
                      <div key={item.product_id} className="flex gap-3 pb-4 border-b border-neutral-100">
                        <div className="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                          {itemImage ? (
                            <img src={itemImage} alt={itemName} className="w-full h-full object-cover" />
                          ) : (
                            <div className="w-full h-full flex items-center justify-center text-neutral-300 text-xs">Görsel</div>
                          )}
                        </div>
                        <div className="flex-1 min-w-0">
                          <h4 className="font-sans text-sm font-medium text-neutral-800 line-clamp-2">{itemName}</h4>
                          <div className="flex items-center gap-2 mt-1.5">
                            <button
                              onClick={() => {
                                const step = 50;
                                handleAddToCart(item.product_id, Math.max(0, item.quantity - step), item.notes);
                              }}
                              className="w-6 h-6 flex items-center justify-center bg-neutral-100 text-neutral-500 rounded hover:bg-neutral-200 transition-colors"
                            >
                              <Minus className="w-3 h-3" />
                            </button>
                            <input
                              type="number"
                              min="0"
                              value={item.quantity}
                              onChange={(e) => handleAddToCart(item.product_id, parseInt(e.target.value) || 0, item.notes)}
                              className="w-16 text-center border border-neutral-200 rounded py-1 font-sans text-xs font-medium focus:outline-none"
                            />
                            <button
                              onClick={() => {
                                const step = 50;
                                handleAddToCart(item.product_id, item.quantity + step, item.notes);
                              }}
                              className="w-6 h-6 flex items-center justify-center bg-neutral-100 text-neutral-500 rounded hover:bg-neutral-200 transition-colors"
                            >
                              <Plus className="w-3 h-3" />
                            </button>
                            <span className="font-sans text-xs text-neutral-400">adet</span>
                          </div>
                          {item.notes && (
                            <p className="font-sans text-xs text-neutral-400 mt-1 italic line-clamp-1">Not: {item.notes}</p>
                          )}
                        </div>
                        <button
                          onClick={() => removeFromCart(item.product_id)}
                          className="flex-shrink-0 w-8 h-8 flex items-center justify-center text-neutral-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                        >
                          <Trash2 className="w-4 h-4" />
                        </button>
                      </div>
                    );
                  })}
                </div>

                {cart.length === 0 && (
                  <div className="text-center py-8">
                    <ShoppingBag className="w-10 h-10 text-neutral-300 mx-auto mb-2" />
                    <p className="font-sans text-sm text-neutral-400">Henüz ürün seçilmedi</p>
                    <button
                      onClick={() => setStep('products')}
                      className="mt-3 font-sans text-sm text-[#8B7355] hover:text-[#6F5C46] transition-colors"
                    >
                      Ürün seçmeye başla
                    </button>
                  </div>
                )}

                {cart.length > 0 && (
                  <div className="pt-4 mt-4 border-t border-neutral-200">
                    <div className="flex justify-between items-center font-sans">
                      <span className="text-neutral-500 text-sm">Toplam</span>
                      <span className="text-lg font-medium text-neutral-800">
                        {cart.length} ürün, {cart.reduce((sum, item) => sum + item.quantity, 0)} adet
                      </span>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
