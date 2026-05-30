import { createContext, useContext, useState, useEffect, useCallback, type ReactNode } from 'react';
import { useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import {
  apiGetMe,
  apiLogout,
  setStoredToken,
  removeStoredToken,
  isTokenExpired,
  type MemberData,
} from '../lib/auth-api';
import { countryToLang } from '../lib/country-lang';

interface AuthContextType {
  member: MemberData | null;
  isLoading: boolean;
  isAuthenticated: boolean;
  showAuthModal: boolean;
  openAuthModal: () => void;
  closeAuthModal: () => void;
  onLoginSuccess: (token: string, member: MemberData, expiresAt?: string) => void;
  logout: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | null>(null);

export function AuthProvider({ children }: { children: ReactNode }) {
  const [member, setMember] = useState<MemberData | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [showAuthModal, setShowAuthModal] = useState(false);
  const navigate = useNavigate();
  const { i18n } = useTranslation();

  // Sayfa yüklenince: client-side expiry → sonra server kontrol
  useEffect(() => {
    if (isTokenExpired()) {
      removeStoredToken();
      setIsLoading(false);
      return;
    }
    apiGetMe().then(m => {
      setMember(m);
      setIsLoading(false);
    });
  }, []);

  // Global 401 dinleyici: herhangi bir API 401 dönerse oturumu kapat
  useEffect(() => {
    const handler = () => {
      removeStoredToken();
      setMember(null);
    };
    window.addEventListener('auth:unauthorized', handler);
    return () => window.removeEventListener('auth:unauthorized', handler);
  }, []);

  const onLoginSuccess = useCallback((token: string, m: MemberData, expiresAt?: string) => {
    setStoredToken(token, expiresAt);
    setMember(m);
    setShowAuthModal(false);

    const lang = countryToLang(m.ulke);
    try { localStorage.setItem('ripehome-lang', lang); } catch {}
    i18n.changeLanguage(lang);
    navigate(`/${lang}/uye-paneli`, { replace: false });
  }, [navigate, i18n]);

  const logout = useCallback(async () => {
    await apiLogout();
    removeStoredToken();
    setMember(null);
  }, []);

  const openAuthModal  = useCallback(() => setShowAuthModal(true), []);
  const closeAuthModal = useCallback(() => setShowAuthModal(false), []);

  const isAuthenticated = !!member && member.durum === 'onaylandi';

  return (
    <AuthContext.Provider value={{
      member,
      isLoading,
      isAuthenticated,
      showAuthModal,
      openAuthModal,
      closeAuthModal,
      onLoginSuccess,
      logout,
    }}>
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth(): AuthContextType {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth must be used within AuthProvider');
  return ctx;
}
