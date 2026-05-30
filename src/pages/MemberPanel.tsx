import { useState, useEffect } from 'react';
import { User, Building2, Globe, LogOut, ShoppingBag, Lock, ArrowRight, Package, Tag, ClipboardList, ChevronDown, ChevronUp } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../context/AuthContext';
import { useLangNavigate } from '../hooks/useLang';
import { apiGetMyOrders, type OrderData } from '../lib/auth-api';

const COUNTRY_NAMES: Record<string, string> = {
  TR: '🇹🇷 Türkiye', DE: '🇩🇪 Almanya', RU: '🇷🇺 Rusya',
  US: '🇺🇸 ABD', GB: '🇬🇧 İngiltere', SA: '🇸🇦 Suudi Arabistan',
  AE: '🇦🇪 BAE', FR: '🇫🇷 Fransa', IT: '🇮🇹 İtalya',
  NL: '🇳🇱 Hollanda', PL: '🇵🇱 Polonya', UA: '🇺🇦 Ukrayna',
};

const STATUS_COLOR: Record<string, string> = {
  pending:   'bg-amber-100 text-amber-700',
  reviewing: 'bg-blue-100 text-blue-700',
  approved:  'bg-green-100 text-green-700',
  rejected:  'bg-red-100 text-red-700',
  completed: 'bg-emerald-100 text-emerald-700',
};

export default function MemberPanel() {
  const { member, isAuthenticated, isLoading, logout, openAuthModal } = useAuth();
  const navigate = useLangNavigate();
  const { t } = useTranslation();
  const [tab, setTab] = useState<'profile' | 'orders'>('profile');
  const [orders, setOrders] = useState<OrderData[]>([]);
  const [ordersLoading, setOrdersLoading] = useState(false);
  const [expandedOrder, setExpandedOrder] = useState<number | null>(null);

  useEffect(() => {
    if (tab === 'orders' && isAuthenticated) {
      setOrdersLoading(true);
      apiGetMyOrders().then(data => {
        setOrders(data);
        setOrdersLoading(false);
      });
    }
  }, [tab, isAuthenticated]);

  if (isLoading) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-24 flex items-center justify-center">
        <div className="w-8 h-8 border-2 border-[#C9B7A1] border-t-[#8B7355] rounded-full animate-spin" />
      </div>
    );
  }

  if (!isAuthenticated || !member) {
    return (
      <div className="min-h-screen bg-[#F8F6F3] pt-24 flex items-center justify-center px-4">
        <div className="text-center max-w-sm">
          <div className="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-lg mb-6">
            <Lock className="w-8 h-8 text-neutral-300" />
          </div>
          <h2 className="font-serif text-3xl text-neutral-800 font-light mb-3">{t('member.loginTitle')}</h2>
          <p className="font-sans text-neutral-500 text-sm mb-8 leading-relaxed whitespace-pre-line">
            {t('member.loginDesc')}
          </p>
          <button
            onClick={openAuthModal}
            className="bg-gray-900 text-white px-10 py-3.5 rounded-2xl font-medium font-sans hover:bg-gray-800 transition-all hover:shadow-lg"
          >
            {t('member.loginBtn')}
          </button>
        </div>
      </div>
    );
  }

  const initials = `${member.ad?.[0] || ''}${member.soyad?.[0] || ''}`.toUpperCase();

  return (
    <div className="min-h-screen bg-[#F8F6F3]">

      {/* Hero */}
      <div className="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 pt-28 pb-24 px-4 relative overflow-hidden">
        {/* Decorative circles */}
        <div className="absolute top-0 right-0 w-96 h-96 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/3" />
        <div className="absolute bottom-0 left-0 w-64 h-64 bg-white/5 rounded-full translate-y-1/2 -translate-x-1/3" />

        <div className="max-w-2xl mx-auto relative">
          <div className="flex items-center gap-5">
            {/* Avatar */}
            <div className="w-20 h-20 rounded-2xl bg-gradient-to-br from-[#C9B7A1] to-[#8B7355] flex items-center justify-center shadow-2xl flex-shrink-0">
              <span className="font-serif text-2xl text-white font-medium">{initials}</span>
            </div>
            <div>
              <p className="font-sans text-white/50 text-xs tracking-[0.2em] uppercase mb-1">{t('member.wholesaleMember')}</p>
              <h1 className="font-serif text-2xl md:text-3xl text-white font-light">
                {member.ad} {member.soyad}
              </h1>
              {member.firma && (
                <p className="font-sans text-white/60 text-sm mt-1">{member.firma}</p>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* Membership Card */}
      <div className="max-w-2xl mx-auto px-4 -mt-8 relative z-10">
        <div className="bg-white rounded-3xl shadow-xl shadow-black/10 overflow-hidden">

          {/* Card header strip */}
          <div className="h-1.5 bg-gradient-to-r from-[#C9B7A1] via-[#8B7355] to-[#C9B7A1]" />

          {/* Tabs */}
          <div className="flex border-b border-neutral-100">
            <button
              onClick={() => setTab('profile')}
              className={`flex-1 py-4 text-sm font-medium font-sans flex items-center justify-center gap-2 transition-colors ${
                tab === 'profile' ? 'text-gray-900 border-b-2 border-gray-900' : 'text-neutral-400 hover:text-neutral-600'
              }`}
            >
              <User className="w-4 h-4" />
              Profilim
            </button>
            <button
              onClick={() => setTab('orders')}
              className={`flex-1 py-4 text-sm font-medium font-sans flex items-center justify-center gap-2 transition-colors ${
                tab === 'orders' ? 'text-gray-900 border-b-2 border-gray-900' : 'text-neutral-400 hover:text-neutral-600'
              }`}
            >
              <ClipboardList className="w-4 h-4" />
              Siparişlerim
            </button>
          </div>

          <div className="p-6 md:p-8">

            {/* ─── Siparişler Sekmesi ─── */}
            {tab === 'orders' && (
              <div>
                {ordersLoading ? (
                  <div className="flex justify-center py-12">
                    <div className="w-6 h-6 border-2 border-[#C9B7A1] border-t-[#8B7355] rounded-full animate-spin" />
                  </div>
                ) : orders.length === 0 ? (
                  <div className="text-center py-12">
                    <div className="inline-flex items-center justify-center w-14 h-14 bg-[#F8F6F3] rounded-2xl mb-4">
                      <ClipboardList className="w-6 h-6 text-neutral-300" />
                    </div>
                    <p className="font-sans text-neutral-400 text-sm">Henüz siparişiniz bulunmuyor.</p>
                    <button
                      onClick={() => navigate('/toptan-siparis')}
                      className="mt-4 text-sm text-[#8B7355] font-medium font-sans hover:underline"
                    >
                      İlk siparişinizi verin →
                    </button>
                  </div>
                ) : (
                  <div className="space-y-3">
                    {orders.map(order => (
                      <div key={order.id} className="border border-neutral-100 rounded-2xl overflow-hidden">
                        {/* Order header */}
                        <button
                          className="w-full flex items-center justify-between px-5 py-4 hover:bg-[#F8F6F3] transition-colors"
                          onClick={() => setExpandedOrder(expandedOrder === order.id ? null : order.id)}
                        >
                          <div className="flex items-center gap-3 text-left">
                            <div>
                              <div className="flex items-center gap-2 mb-1">
                                <span className="font-sans text-sm font-medium text-neutral-800">
                                  Sipariş #{order.id}
                                </span>
                                <span className={`text-[10px] font-medium px-2 py-0.5 rounded-full font-sans ${STATUS_COLOR[order.status] || 'bg-gray-100 text-gray-600'}`}>
                                  {order.status_label}
                                </span>
                              </div>
                              <p className="font-sans text-xs text-neutral-400">
                                {order.items_count} ürün · {order.total_quantity} adet · {new Date(order.created_at).toLocaleDateString('tr-TR')}
                              </p>
                            </div>
                          </div>
                          {expandedOrder === order.id
                            ? <ChevronUp className="w-4 h-4 text-neutral-400 flex-shrink-0" />
                            : <ChevronDown className="w-4 h-4 text-neutral-400 flex-shrink-0" />
                          }
                        </button>

                        {/* Order detail */}
                        {expandedOrder === order.id && (
                          <div className="border-t border-neutral-100 px-5 py-4 bg-[#FDFBF9] space-y-3">
                            {order.items.map((item, i) => (
                              <div key={i} className="flex items-center gap-3">
                                {item.product_image && (
                                  <img src={item.product_image} alt={item.product_name}
                                    className="w-10 h-12 object-cover rounded-lg flex-shrink-0" />
                                )}
                                <div className="flex-1 min-w-0">
                                  <p className="font-sans text-sm text-neutral-700 font-medium truncate">{item.product_name}</p>
                                  <p className="font-sans text-xs text-neutral-400">{item.quantity} adet</p>
                                </div>
                              </div>
                            ))}
                            {order.admin_notes && (
                              <div className="mt-3 pt-3 border-t border-neutral-100">
                                <p className="font-sans text-xs text-neutral-500 font-medium mb-1">Admin Notu:</p>
                                <p className="font-sans text-sm text-neutral-600">{order.admin_notes}</p>
                              </div>
                            )}
                          </div>
                        )}
                      </div>
                    ))}
                  </div>
                )}
              </div>
            )}

            {/* ─── Profil Sekmesi ─── */}
            {tab === 'profile' && <>
            {/* Status badge */}
            <div className="flex items-center justify-between mb-8">
              <div className="flex items-center gap-2">
                <div className="w-2 h-2 bg-green-400 rounded-full animate-pulse" />
                <span className="font-sans text-xs text-green-600 font-medium tracking-wide uppercase">{t('member.activeMembership')}</span>
              </div>
              <div className="flex items-center gap-1.5 bg-[#F8F6F3] px-3 py-1.5 rounded-full">
                <svg width="10" height="10" viewBox="0 0 20 20">
                  <circle cx="10" cy="10" r="9" fill="#E30A17"/>
                  <circle cx="8.5" cy="10" r="5.5" fill="white"/>
                  <circle cx="10" cy="10" r="4" fill="#E30A17"/>
                  <polygon points="14,10 15.5,8 15.5,12" fill="white"/>
                </svg>
                <span className="font-sans text-[11px] text-neutral-500 font-medium">Ripe Home</span>
              </div>
            </div>

            {/* Info grid */}
            <div className="grid grid-cols-2 gap-4 mb-8">
              <div className="bg-[#F8F6F3] rounded-2xl p-4">
                <div className="flex items-center gap-2 mb-2">
                  <User className="w-3.5 h-3.5 text-[#8B7355]" />
                  <p className="font-sans text-[10px] text-neutral-400 uppercase tracking-wider">{t('member.fullName')}</p>
                </div>
                <p className="font-sans text-sm font-medium text-neutral-800">{member.ad} {member.soyad}</p>
              </div>

              <div className="bg-[#F8F6F3] rounded-2xl p-4">
                <div className="flex items-center gap-2 mb-2">
                  <Globe className="w-3.5 h-3.5 text-[#8B7355]" />
                  <p className="font-sans text-[10px] text-neutral-400 uppercase tracking-wider">{t('member.country')}</p>
                </div>
                <p className="font-sans text-sm font-medium text-neutral-800">
                  {COUNTRY_NAMES[member.ulke] || member.ulke}
                </p>
              </div>

              {member.firma && (
                <div className="bg-[#F8F6F3] rounded-2xl p-4 col-span-2">
                  <div className="flex items-center gap-2 mb-2">
                    <Building2 className="w-3.5 h-3.5 text-[#8B7355]" />
                    <p className="font-sans text-[10px] text-neutral-400 uppercase tracking-wider">{t('member.company')}</p>
                  </div>
                  <p className="font-sans text-sm font-medium text-neutral-800">{member.firma}</p>
                </div>
              )}
            </div>

            {/* Privilege info */}
            <div className="border border-[#E5DDD1] rounded-2xl p-4 mb-8 bg-[#FDFBF9]">
              <p className="font-sans text-xs text-neutral-500 mb-3 uppercase tracking-wider font-medium">{t('member.privileges')}</p>
              <div className="space-y-2">
                {(['priv1', 'priv2', 'priv3'] as const).map((key) => (
                  <div key={key} className="flex items-center gap-2.5">
                    <div className="w-4 h-4 rounded-full bg-[#8B7355]/10 flex items-center justify-center flex-shrink-0">
                      <div className="w-1.5 h-1.5 rounded-full bg-[#8B7355]" />
                    </div>
                    <span className="font-sans text-sm text-neutral-600">{t(`member.${key}`)}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Action buttons */}
            <div className="space-y-3">
              <button
                onClick={() => navigate('/urunler')}
                className="w-full flex items-center justify-between bg-gray-900 text-white px-5 py-4 rounded-2xl hover:bg-gray-800 transition-all group"
              >
                <div className="flex items-center gap-3">
                  <Package className="w-4 h-4 text-white/70" />
                  <span className="font-sans font-medium text-sm">{t('member.browseProducts')}</span>
                </div>
                <ArrowRight className="w-4 h-4 text-white/50 group-hover:translate-x-1 transition-transform" />
              </button>

              <button
                onClick={() => navigate('/toptan-siparis')}
                className="w-full flex items-center justify-between bg-[#F8F6F3] border border-[#E5DDD1] text-neutral-700 px-5 py-4 rounded-2xl hover:bg-[#F0EBE4] transition-all group"
              >
                <div className="flex items-center gap-3">
                  <ShoppingBag className="w-4 h-4 text-[#8B7355]" />
                  <span className="font-sans font-medium text-sm">{t('member.createOrder')}</span>
                </div>
                <ArrowRight className="w-4 h-4 text-neutral-400 group-hover:translate-x-1 transition-transform" />
              </button>

              <button
                onClick={() => navigate('/urunler')}
                className="w-full flex items-center justify-between bg-[#F8F6F3] border border-[#E5DDD1] text-neutral-700 px-5 py-4 rounded-2xl hover:bg-[#F0EBE4] transition-all group"
              >
                <div className="flex items-center gap-3">
                  <Tag className="w-4 h-4 text-[#8B7355]" />
                  <span className="font-sans font-medium text-sm">{t('member.browseCategories')}</span>
                </div>
                <ArrowRight className="w-4 h-4 text-neutral-400 group-hover:translate-x-1 transition-transform" />
              </button>
            </div>
            </>}
          </div>
        </div>

        {/* Logout */}
        <button
          onClick={async () => { await logout(); navigate('/'); }}
          className="w-full flex items-center justify-center gap-2 py-4 mt-4 mb-12 text-neutral-400 hover:text-red-400 transition font-sans text-sm"
        >
          <LogOut className="w-4 h-4" />
          {t('member.logout')}
        </button>
      </div>
    </div>
  );
}
