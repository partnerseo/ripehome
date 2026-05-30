import { useState, useEffect } from 'react';
import { useLangNavigate } from '../hooks/useLang';
import { ArrowLeft, ShoppingBag, Plus, Minus, Trash2, ChevronRight } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { getProducts, getCategories, createWholesaleOrder } from '@/lib/api';
import { useCart } from '@/context/CartContext';
import { useAuth } from '@/context/AuthContext';
import type { Product, Category, WholesaleOrderForm } from '@/types/api';

export default function WholesaleOrder() {
  const { t } = useTranslation();
  const navigate = useLangNavigate();
  const { cart, addToCart, updateQuantity, removeFromCart, clearCart, cartCount } = useCart();
  const { member, isAuthenticated, openAuthModal } = useAuth();

  const [products, setProducts] = useState<Product[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
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

  // Üye giriş yapmışsa formu otomatik doldur
  useEffect(() => {
    if (isAuthenticated && member) {
      setFormData(prev => ({
        ...prev,
        contact_person: prev.contact_person || `${member.ad} ${member.soyad}`.trim(),
        company_name:   prev.company_name   || (member.firma ?? ''),
        email:          prev.email          || (member.email ?? ''),
        phone:          prev.phone          || (member.telefon ?? ''),
      }));
    }
  }, [isAuthenticated, member]);
  const [step, setStep] = useState<'products' | 'form'>('products');
  const [submitting, setSubmitting] = useState(false);
  const [success, setSuccess] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [showAllProducts, setShowAllProducts] = useState(false);
  const [minOrderError, setMinOrderError] = useState(false);
  const [cartOpen, setCartOpen] = useState(false);
  const [inputValues, setInputValues] = useState<Record<number, string>>({});

  const MIN_PER_PRODUCT = 3;
  const MIN_TOTAL = 20;
  const totalQuantity = cart.reduce((sum, item) => sum + item.quantity, 0);

  useEffect(() => {
    async function fetchData() {
      try {
        const [productData, categoryData] = await Promise.all([
          getProducts(1, 500),
          getCategories(),
        ]);
        setProducts(productData.data || []);
        setCategories(categoryData || []);
      } catch (error) {
        console.error('Veriler yüklenemedi:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchData();
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
    // Enforce minimum 3 per product
    if (quantity < MIN_PER_PRODUCT) quantity = MIN_PER_PRODUCT;
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

  const handleGoToForm = () => {
    if (!isAuthenticated) {
      openAuthModal();
      return;
    }
    if (totalQuantity < MIN_TOTAL) {
      setMinOrderError(true);
      return;
    }
    setMinOrderError(false);
    setStep('form');
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (cart.length === 0) {
      alert(t('wholesale.selectAtLeastOne'));
      return;
    }
    if (totalQuantity < MIN_TOTAL) {
      setMinOrderError(true);
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
      alert(t('wholesale.orderFailed'));
    } finally {
      setSubmitting(false);
    }
  };

  const filteredProducts = products.filter(p =>
    p.name.toLowerCase().includes(searchQuery.toLowerCase()) ||
    p.category?.name?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  // Kategorilerin API'den geldiği sıraya göre grupla
  const groupedProducts = categories
    .map(cat => ({
      category: cat,
      products: filteredProducts.filter(p => p.category?.id === cat.id),
    }))
    .filter(g => g.products.length > 0);

  // Kategorisi olmayan ürünleri sona ekle
  const uncategorized = filteredProducts.filter(p => !p.category);
  if (uncategorized.length > 0) {
    groupedProducts.push({ category: { id: 0, name: 'Diğer', slug: '', image: null, products_count: 0 } as Category, products: uncategorized });
  }

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
            <h1 className="font-serif text-3xl text-neutral-800 mb-4">{t('wholesale.success')}</h1>
            <p className="font-sans text-neutral-600 mb-8 leading-relaxed">
              {t('wholesale.successText')}
            </p>
            <div className="flex gap-3 justify-center">
              <button
                onClick={() => { setSuccess(false); setStep('products'); }}
                className="px-6 py-3 bg-[#8B7355] text-white rounded-xl font-sans font-medium hover:bg-[#6F5C46] transition-colors"
              >
                {t('wholesale.newOrder')}
              </button>
              <button
                onClick={() => navigate('/')}
                className="px-6 py-3 border border-neutral-200 text-neutral-700 rounded-xl font-sans font-medium hover:bg-white transition-colors"
              >
                {t('common.homePage')}
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
            {t('common.back')}
          </button>
          <h1 className="font-serif text-3xl md:text-4xl text-neutral-800 font-light">{t('wholesale.title')}</h1>
          <p className="font-sans text-neutral-500 mt-2">{t('wholesale.subtitle')}</p>
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
            {t('wholesale.step1')}
            {cart.length > 0 && step !== 'products' && (
              <span className="ml-1 bg-white/20 px-2 py-0.5 rounded-full text-xs">{cart.length}</span>
            )}
          </button>
          <ChevronRight className="w-4 h-4 text-neutral-400" />
          <button
            onClick={() => cart.length > 0 && handleGoToForm()}
            className={`flex items-center gap-2 px-5 py-2.5 rounded-full font-sans text-sm font-medium transition-all ${
              step === 'form'
                ? 'bg-[#8B7355] text-white shadow-md'
                : cart.length > 0
                  ? 'bg-white text-neutral-600 border border-neutral-200 hover:border-[#8B7355] cursor-pointer'
                  : 'bg-gray-100 text-neutral-400 border border-gray-200 cursor-not-allowed'
            }`}
          >
            {t('wholesale.step2')}
          </button>
        </div>

        {/* Step 1: Product Selection */}
        {step === 'products' && (
          <>
            {/* Search */}
                <div className="mb-6">
                  <input
                    type="text"
                    placeholder={t('common.searchProductCategory')}
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    className="w-full md:w-96 px-4 py-3 bg-white border border-neutral-200 rounded-xl font-sans text-sm focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 focus:border-[#8B7355]"
                  />
                </div>

                {filteredProducts.length === 0 ? (
                  <div className="text-center py-16">
                    <p className="font-sans text-neutral-500">{t('common.productNotFound')}</p>
                  </div>
                ) : (
                  <div className="pb-28 space-y-10">
                    {groupedProducts.map(({ category, products: groupProds }) => (
                      <div key={category.id}>
                        <h2 className="font-serif text-xl text-neutral-700 mb-4 pb-2 border-b border-neutral-200">
                          {category.name}
                          <span className="font-sans text-sm text-neutral-400 ml-2 font-normal">{groupProds.length} ürün</span>
                        </h2>
                        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                          {groupProds.map(product => {
                            const inCart = getCartQuantity(product.id) > 0;
                            return (
                              <div
                                key={product.id}
                                className={`rounded-xl overflow-hidden shadow-sm transition-all duration-300 border-2 ${
                                  inCart
                                    ? 'border-[#8B7355] bg-[#FAF7F4] shadow-md ring-1 ring-[#8B7355]/20'
                                    : 'border-transparent bg-white hover:shadow-md'
                                }`}
                              >
                                <div className="aspect-square overflow-hidden relative">
                                  <img
                                    src={product.images?.[0] || '/pexels-cottonbro-4327012.jpg'}
                                    alt={product.name}
                                    className="w-full h-full object-cover"
                                    loading="lazy"
                                  />
                                  {inCart && <div className="absolute inset-0 bg-[#8B7355]/10" />}
                                  {inCart && (
                                    <div className="absolute top-2 left-2 bg-[#8B7355] text-white text-xs font-sans font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                                      <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M5 13l4 4L19 7"/></svg>
                                      {getCartQuantity(product.id)} {t('common.piece')}
                                    </div>
                                  )}
                                </div>
                                <div className="p-3 md:p-4">
                                  <h3 className="font-sans text-sm font-medium text-neutral-800 mb-1 line-clamp-2">{product.name}</h3>
                                  {isAuthenticated && product.price != null ? (
                                    <p className="text-[#8B7355] font-semibold text-sm mb-2">
                                      ${Number(product.price).toFixed(2)} <span className="text-neutral-400 font-normal text-xs">/ adet</span>
                                    </p>
                                  ) : (
                                    <p className="text-neutral-400 text-xs mb-2 cursor-pointer hover:text-neutral-600" onClick={openAuthModal}>
                                      Fiyat için giriş yapın
                                    </p>
                                  )}
                                  <div className="flex items-center gap-1.5">
                                    <button
                                      onClick={() => handleAddToCart(product.id, Math.max(0, getCartQuantity(product.id) - 1), getCartNotes(product.id))}
                                      className="w-8 h-8 flex items-center justify-center bg-neutral-100 text-neutral-600 rounded-lg hover:bg-neutral-200 transition-colors flex-shrink-0"
                                    >
                                      <Minus className="w-3.5 h-3.5" />
                                    </button>
                                    <input
                                      type="number"
                                      min="0"
                                      value={product.id in inputValues ? inputValues[product.id] : (getCartQuantity(product.id) || '')}
                                      placeholder="0"
                                      onChange={(e) => {
                                        setInputValues(prev => ({ ...prev, [product.id]: e.target.value }));
                                      }}
                                      onBlur={(e) => {
                                        const parsed = parseInt(e.target.value) || 0;
                                        setInputValues(prev => { const n = { ...prev }; delete n[product.id]; return n; });
                                        handleAddToCart(product.id, parsed, getCartNotes(product.id));
                                      }}
                                      className="flex-1 text-center border border-neutral-200 rounded-lg py-1.5 font-sans text-sm font-medium focus:outline-none focus:ring-2 focus:ring-[#8B7355]/30 min-w-0"
                                    />
                                    <button
                                      onClick={() => handleAddToCart(product.id, getCartQuantity(product.id) + 1, getCartNotes(product.id))}
                                      className="w-8 h-8 flex items-center justify-center bg-neutral-100 text-neutral-600 rounded-lg hover:bg-neutral-200 transition-colors flex-shrink-0"
                                    >
                                      <Plus className="w-3.5 h-3.5" />
                                    </button>
                                  </div>
                                </div>
                              </div>
                            );
                          })}
                        </div>
                      </div>
                    ))}
                  </div>
                )}

            {/* Bottom Cart Bar */}
            {cart.length > 0 && (
              <div className="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-md border-t border-neutral-200 shadow-lg z-50">
                {/* Expandable cart list */}
                {cartOpen && (
                  <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 pt-3 pb-1 max-h-52 overflow-y-auto">
                    <div className="space-y-2">
                      {cart.map(item => {
                        const product = products.find(p => p.id === item.product_id);
                        const img = product?.images?.[0] || item.product_image;
                        const name = product?.name || item.product_name || 'Ürün';
                        return (
                          <div key={item.product_id} className="flex items-center gap-3">
                            <div className="w-9 h-9 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                              {img ? <img src={img} alt={name} className="w-full h-full object-cover" /> : null}
                            </div>
                            <span className="font-sans text-sm text-neutral-700 flex-1 truncate">{name}</span>
                            <span className="font-sans text-sm font-medium text-[#8B7355] flex-shrink-0">{item.quantity} adet</span>
                            <button onClick={() => removeFromCart(item.product_id)} className="text-neutral-300 hover:text-red-400 transition-colors flex-shrink-0">
                              <Trash2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        );
                      })}
                    </div>
                  </div>
                )}

                <div className="max-w-7xl mx-auto px-4 md:px-12 lg:px-24 py-3">
                  {minOrderError && totalQuantity < MIN_TOTAL && (
                    <div className="text-center text-red-600 text-xs font-sans font-medium mb-2">
                      Toplam sipariş en az {MIN_TOTAL} adet olmalıdır. (Şu an: {totalQuantity} adet)
                    </div>
                  )}
                  <div className="flex items-center justify-between">
                    <button
                      onClick={() => setCartOpen(o => !o)}
                      className="flex items-center gap-2 text-left"
                    >
                      <div>
                        <div className="font-sans font-medium text-neutral-800 flex items-center gap-1.5">
                          {cart.length} ürün, toplam {totalQuantity} adet
                          <svg className={`w-4 h-4 text-neutral-400 transition-transform ${cartOpen ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 15l7-7 7 7" />
                          </svg>
                        </div>
                        <div className="font-sans text-xs text-neutral-400 mt-0.5">
                          Min. {MIN_PER_PRODUCT} adet/ürün · Toplam min. {MIN_TOTAL} adet
                        </div>
                      </div>
                    </button>
                    <button
                      onClick={handleGoToForm}
                      className={`flex items-center gap-2 px-6 py-3 rounded-xl font-sans font-medium shadow-lg transition-colors ${
                        totalQuantity >= MIN_TOTAL
                          ? 'bg-[#8B7355] hover:bg-[#6F5C46] text-white'
                          : 'bg-neutral-300 text-neutral-500 cursor-not-allowed'
                      }`}
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
                    {submitting ? 'Gönderiliyor...' : 'Siparişi Gönder'}
                  </button>
                </form>
              </div>
            </div>

            {/* Sipariş Özeti - Sağ */}
            <div className="lg:col-span-2">
              <div className="sticky top-28 space-y-4">
              <div className="bg-white rounded-2xl border border-neutral-100 p-6">
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
                            <div className="w-full h-full flex items-center justify-center text-neutral-300 text-xs">{t('common.image')}</div>
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
                              value={item.product_id in inputValues ? inputValues[item.product_id] : item.quantity}
                              onChange={(e) => {
                                setInputValues(prev => ({ ...prev, [item.product_id]: e.target.value }));
                              }}
                              onBlur={(e) => {
                                const parsed = parseInt(e.target.value) || 0;
                                setInputValues(prev => { const n = { ...prev }; delete n[item.product_id]; return n; });
                                handleAddToCart(item.product_id, parsed, item.notes);
                              }}
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
                            <span className="font-sans text-xs text-neutral-400">{t('common.piece')}</span>
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

              {/* Info badges */}
              <div className="grid grid-cols-2 gap-3">
                {[
                  { value: '40+', label: 'Min. Sipariş Adedi' },
                  { value: '2–3 Hafta', label: 'Üretim Süresi' },
                  { value: '30+', label: 'İhracat Ülkesi' },
                  { value: '24S', label: 'Teklif Süresi' },
                ].map((badge) => (
                  <div key={badge.label} className="bg-[#F8F6F3] rounded-xl p-3 text-center border border-neutral-100">
                    <div className="font-serif text-xl text-[#8B7355] font-medium">{badge.value}</div>
                    <div className="font-sans text-xs text-neutral-500 mt-0.5 leading-tight">{badge.label}</div>
                  </div>
                ))}
              </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
