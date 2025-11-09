import { useState, useEffect } from 'react';
import { getProducts, createWholesaleOrder } from '@/lib/api';
import type { Product, WholesaleOrderItem, WholesaleOrderForm } from '@/types/api';

export default function WholesaleOrder() {
  // Ürünler
  const [products, setProducts] = useState<Product[]>([]);
  const [loading, setLoading] = useState(true);

  // Sepet (seçilen ürünler)
  const [cart, setCart] = useState<WholesaleOrderItem[]>([]);

  // Form verileri
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

  // UI state
  const [step, setStep] = useState<'products' | 'form'>('products');
  const [submitting, setSubmitting] = useState(false);
  const [success, setSuccess] = useState(false);

  // Ürünleri yükle
  useEffect(() => {
    async function fetchProducts() {
      try {
        const data = await getProducts();
        setProducts(data.data || []);
      } catch (error) {
        console.error('Ürünler yüklenemedi:', error);
      } finally {
        setLoading(false);
      }
    }
    fetchProducts();
  }, []);

  // Sepete ürün ekle/güncelle
  const updateCart = (productId: number, quantity: number, notes?: string) => {
    if (quantity === 0) {
      // Sıfırsa sepetten çıkar
      setCart(cart.filter(item => item.product_id !== productId));
    } else {
      const existingIndex = cart.findIndex(item => item.product_id === productId);
      if (existingIndex >= 0) {
        // Güncelle
        const newCart = [...cart];
        newCart[existingIndex] = { ...newCart[existingIndex], quantity, notes };
        setCart(newCart);
      } else {
        // Yeni ekle
        setCart([...cart, { product_id: productId, quantity, notes }]);
      }
    }
  };

  // Sepetteki ürün adedi
  const getCartQuantity = (productId: number) => {
    return cart.find(item => item.product_id === productId)?.quantity || 0;
  };

  // Sepetteki ürün notunu al
  const getCartNotes = (productId: number) => {
    return cart.find(item => item.product_id === productId)?.notes || '';
  };

  // Form gönder
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (cart.length === 0) {
      alert('Lütfen en az bir ürün seçin');
      return;
    }

    setSubmitting(true);

    const orderData: WholesaleOrderForm = {
      ...formData,
      items: cart,
    };

    try {
      const result = await createWholesaleOrder(orderData);
      if (result.success) {
        setSuccess(true);
        setCart([]);
        setFormData({
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
      } else {
        alert(result.message || 'Bir hata oluştu');
      }
    } catch (error) {
      alert('Sipariş gönderilemedi, lütfen tekrar deneyin');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50">
        <div className="container mx-auto px-4 py-24 mt-20">
          <div className="text-center">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-[#2B5F82]"></div>
            <p className="mt-4 text-gray-600">Ürünler yükleniyor...</p>
          </div>
        </div>
      </div>
    );
  }

  if (success) {
    return (
      <div className="min-h-screen bg-gray-50">
        <div className="container mx-auto px-4 py-24 mt-20">
          <div className="max-w-2xl mx-auto text-center">
            <div className="bg-white rounded-lg shadow-lg p-12">
              <div className="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg className="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                </svg>
              </div>
              
              <h1 className="text-4xl font-bold mb-4 text-gray-800">Talebiniz Alındı! ✅</h1>
              <p className="text-lg text-gray-600 mb-8">
                Toptan sipariş talebiniz başarıyla kaydedildi. 
                En kısa sürede size dönüş yapacağız.
              </p>
              <button 
                onClick={() => { setSuccess(false); setStep('products'); }}
                className="bg-[#2B5F82] text-white px-8 py-3 rounded-lg hover:bg-[#1e4a66] transition-colors font-semibold"
              >
                Yeni Sipariş Oluştur
              </button>
            </div>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="container mx-auto px-4 py-12 mt-20">
        <div className="max-w-7xl mx-auto">
          <h1 className="text-4xl font-bold mb-2 text-gray-800">Toptan Sipariş</h1>
          <p className="text-gray-600 mb-8">Ürünlerinizi seçin ve toptan sipariş talebinizi oluşturun</p>

          {/* Adım göstergesi */}
          <div className="flex items-center justify-center gap-4 mb-8">
            <div className={`flex-1 max-w-xs p-4 rounded-lg text-center transition-all duration-300 ${
              step === 'products' 
                ? 'bg-[#2B5F82] text-white shadow-lg' 
                : 'bg-white border-2 border-gray-200 text-gray-600'
            }`}>
              <div className="font-semibold">Adım 1</div>
              <div className="text-sm mt-1">Ürün Seçimi</div>
              {cart.length > 0 && step !== 'products' && (
                <div className="text-xs mt-1 opacity-80">✓ {cart.length} ürün seçildi</div>
              )}
            </div>

            <div className={`flex-1 max-w-xs p-4 rounded-lg text-center transition-all duration-300 ${
              step === 'form' 
                ? 'bg-[#2B5F82] text-white shadow-lg' 
                : 'bg-white border-2 border-gray-200 text-gray-600'
            }`}>
              <div className="font-semibold">Adım 2</div>
              <div className="text-sm mt-1">Bilgileriniz</div>
            </div>
          </div>

          {step === 'products' && (
            <>
              <h2 className="text-2xl font-bold mb-6 text-gray-800">Ürünlerinizi Seçin</h2>
              
              {products.length === 0 ? (
                <div className="text-center py-12">
                  <p className="text-gray-500">Henüz ürün bulunmamaktadır.</p>
                </div>
              ) : (
                <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 mb-24">
                  {products.map(product => (
                    <div key={product.id} className="bg-white rounded-lg shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden">
                      <div className="aspect-square bg-gray-100 relative">
                        {product.images && product.images[0] ? (
                          <img 
                            src={product.images[0]} 
                            alt={product.name}
                            className="w-full h-full object-cover"
                          />
                        ) : (
                          <div className="w-full h-full flex items-center justify-center text-gray-400">
                            Görsel Yok
                          </div>
                        )}
                      </div>

                      <div className="p-4">
                        <h3 className="font-semibold text-lg mb-1 text-gray-800">{product.name}</h3>
                        {product.category && (
                          <p className="text-sm text-gray-500 mb-3">{product.category.name}</p>
                        )}
                        
                        <div className="space-y-2">
                          <label className="text-sm font-medium text-gray-700">
                            Adet:
                          </label>
                          <input
                            type="number"
                            min="0"
                            value={getCartQuantity(product.id)}
                            onChange={(e) => updateCart(product.id, parseInt(e.target.value) || 0, getCartNotes(product.id))}
                            className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-2 focus:ring-[#2B5F82] focus:border-transparent"
                            placeholder="0"
                          />

                          {getCartQuantity(product.id) > 0 && (
                            <textarea
                              placeholder="Not ekleyin (opsiyonel)"
                              className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-[#2B5F82] focus:border-transparent"
                              rows={2}
                              value={getCartNotes(product.id)}
                              onChange={(e) => {
                                const item = cart.find(i => i.product_id === product.id);
                                if (item) updateCart(product.id, item.quantity, e.target.value);
                              }}
                            />
                          )}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}

              {/* Sabit alt bar */}
              {cart.length > 0 && (
                <div className="fixed bottom-0 left-0 right-0 bg-white border-t shadow-lg z-50">
                  <div className="container mx-auto px-4 py-4">
                    <div className="flex items-center justify-between max-w-7xl mx-auto">
                      <div>
                        <div className="font-semibold text-lg text-gray-800">
                          {cart.length} farklı ürün seçildi
                        </div>
                        <div className="text-sm text-gray-600">
                          Toplam {cart.reduce((sum, item) => sum + item.quantity, 0)} adet
                        </div>
                      </div>
                      <button 
                        onClick={() => setStep('form')}
                        className="bg-[#2B5F82] text-white px-8 py-3 rounded-lg hover:bg-[#1e4a66] transition-colors font-semibold"
                      >
                        Devam Et →
                      </button>
                    </div>
                  </div>
                </div>
              )}
            </>
          )}

          {step === 'form' && (
            <>
              <button 
                onClick={() => setStep('products')}
                className="mb-6 text-[#2B5F82] hover:underline font-semibold flex items-center gap-2"
              >
                ← Ürün Seçimine Dön
              </button>

              <div className="grid md:grid-cols-2 gap-8">
                {/* Form */}
                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h2 className="text-2xl font-bold mb-6 text-gray-800">Şirket Bilgileriniz</h2>
                  <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Şirket Adı *
                      </label>
                      <input
                        type="text"
                        placeholder="Şirket Adı"
                        required
                        value={formData.company_name}
                        onChange={(e) => setFormData({...formData, company_name: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Yetkili Kişi Adı *
                      </label>
                      <input
                        type="text"
                        placeholder="Yetkili Kişi Adı"
                        required
                        value={formData.contact_person}
                        onChange={(e) => setFormData({...formData, contact_person: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        E-posta *
                      </label>
                      <input
                        type="email"
                        placeholder="ornek@firma.com"
                        required
                        value={formData.email}
                        onChange={(e) => setFormData({...formData, email: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Telefon *
                      </label>
                      <input
                        type="tel"
                        placeholder="0555 555 5555"
                        required
                        value={formData.phone}
                        onChange={(e) => setFormData({...formData, phone: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Şehir
                      </label>
                      <input
                        type="text"
                        placeholder="İstanbul"
                        value={formData.city}
                        onChange={(e) => setFormData({...formData, city: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Adres
                      </label>
                      <textarea
                        placeholder="Adres"
                        rows={3}
                        value={formData.address}
                        onChange={(e) => setFormData({...formData, address: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Vergi Dairesi
                      </label>
                      <input
                        type="text"
                        placeholder="Vergi Dairesi"
                        value={formData.tax_office}
                        onChange={(e) => setFormData({...formData, tax_office: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Vergi Numarası
                      </label>
                      <input
                        type="text"
                        placeholder="Vergi Numarası"
                        value={formData.tax_number}
                        onChange={(e) => setFormData({...formData, tax_number: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>
                    
                    <div>
                      <label className="block text-sm font-semibold text-gray-700 mb-1">
                        Ek Notlarınız
                      </label>
                      <textarea
                        placeholder="Ek notlarınız..."
                        rows={4}
                        value={formData.additional_notes}
                        onChange={(e) => setFormData({...formData, additional_notes: e.target.value})}
                        className="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-[#2B5F82]"
                      />
                    </div>

                    <button 
                      type="submit" 
                      disabled={submitting}
                      className="w-full bg-[#2B5F82] text-white py-3 rounded-lg hover:bg-[#1e4a66] transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed"
                    >
                      {submitting ? 'Gönderiliyor...' : 'Siparişi Gönder'}
                    </button>
                  </form>
                </div>

                {/* Sepet özeti */}
                <div>
                  <div className="bg-white border border-gray-200 rounded-lg p-6 sticky top-4">
                    <h2 className="text-2xl font-bold mb-6 text-gray-800">Sipariş Özeti</h2>
                    <div className="space-y-4 max-h-[600px] overflow-y-auto">
                      {cart.map(item => {
                        const product = products.find(p => p.id === item.product_id);
                        return (
                          <div key={item.product_id} className="flex gap-4 pb-4 border-b border-gray-200">
                            <img 
                              src={product?.images?.[0] || '/placeholder.jpg'}
                              alt={product?.name}
                              className="w-20 h-20 object-cover rounded"
                            />
                            <div className="flex-1">
                              <h4 className="font-bold text-gray-800">{product?.name}</h4>
                              <p className="text-sm text-gray-600">Adet: <strong>{item.quantity}</strong></p>
                              {item.notes && (
                                <p className="text-sm text-gray-500 mt-1 italic">Not: {item.notes}</p>
                              )}
                            </div>
                          </div>
                        );
                      })}
                    </div>
                    
                    <div className="pt-4 mt-4 border-t border-gray-200">
                      <div className="flex justify-between items-center font-bold text-lg text-gray-800">
                        <span>Toplam:</span>
                        <span>{cart.reduce((sum, item) => sum + item.quantity, 0)} adet</span>
                      </div>
                      <p className="text-sm text-gray-600 mt-2">
                        {cart.length} farklı ürün
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}



