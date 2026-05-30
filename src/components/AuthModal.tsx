import { useState, useRef, useEffect, type KeyboardEvent } from 'react';
import { useLangNavigate } from '../hooks/useLang';
import { X, Mail, Lock, User, Building2, Globe, Phone, ArrowLeft, CheckCircle, Clock, AlertCircle, PhoneOff } from 'lucide-react';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../context/AuthContext';
import { apiSendOtp, apiVerifyOtp, apiRegister } from '../lib/auth-api';

type Step = 'choose' | 'email' | 'otp' | 'register' | 'success';
type Mode = 'login' | 'signup';

const COUNTRY_CODES = [
  { code: 'TR', dial: '+90',  flag: '🇹🇷', name: 'Türkiye' },
  { code: 'DE', dial: '+49',  flag: '🇩🇪', name: 'Almanya' },
  { code: 'RU', dial: '+7',   flag: '🇷🇺', name: 'Rusya' },
  { code: 'US', dial: '+1',   flag: '🇺🇸', name: 'ABD' },
  { code: 'GB', dial: '+44',  flag: '🇬🇧', name: 'İngiltere' },
  { code: 'SA', dial: '+966', flag: '🇸🇦', name: 'Suudi Arabistan' },
  { code: 'AE', dial: '+971', flag: '🇦🇪', name: 'BAE' },
  { code: 'FR', dial: '+33',  flag: '🇫🇷', name: 'Fransa' },
  { code: 'IT', dial: '+39',  flag: '🇮🇹', name: 'İtalya' },
  { code: 'NL', dial: '+31',  flag: '🇳🇱', name: 'Hollanda' },
  { code: 'PL', dial: '+48',  flag: '🇵🇱', name: 'Polonya' },
  { code: 'UA', dial: '+380', flag: '🇺🇦', name: 'Ukrayna' },
];

export default function AuthModal() {
  const { showAuthModal, closeAuthModal, onLoginSuccess, isAuthenticated, isLoading } = useAuth();
  const navigate = useLangNavigate();
  const { t, i18n } = useTranslation();

  const [step, setStep] = useState<Step>('choose');
  const [mode, setMode] = useState<Mode>('login');
  const [email, setEmail] = useState('');
  const [otpDigits, setOtpDigits] = useState(['', '', '', '', '', '']);
  const [form, setForm] = useState({ ad: '', soyad: '', firma: '', ulke: 'TR', countryCode: COUNTRY_CODES[0], phoneLocal: '' });
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);
  const [countdown, setCountdown] = useState(0);
  const [attemptsLeft, setAttemptsLeft] = useState<number | null>(null);
  const [isLocked, setIsLocked] = useState(false);

  const otpRefs = useRef<(HTMLInputElement | null)[]>([]);
  const emailInputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (countdown <= 0) return;
    const timer = setTimeout(() => setCountdown(c => c - 1), 1000);
    return () => clearTimeout(timer);
  }, [countdown]);

  useEffect(() => {
    if (showAuthModal && step === 'email') {
      setTimeout(() => emailInputRef.current?.focus(), 100);
    }
  }, [showAuthModal, step]);

  useEffect(() => {
    if (showAuthModal && !isLoading && isAuthenticated) {
      closeAuthModal();
      navigate('/uye-paneli');
    }
  }, [showAuthModal, isAuthenticated, isLoading]);

  if (!showAuthModal) return null;

  const fullPhone = `${form.countryCode.dial}${form.phoneLocal.replace(/^0/, '')}`;

  // ─── Handlers ───────────────────────────────────────────────────────────────

  async function handleSendOtp() {
    setError('');
    if (!email.trim()) { setError('E-posta adresinizi girin.'); return; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      setError('Geçerli bir e-posta adresi girin.'); return;
    }
    setLoading(true);
    try {
      const res = await apiSendOtp(email.trim().toLowerCase(), i18n.language);
      setStep('otp');
      setCountdown(60);
      if (res.debug_code) {
        const digits = res.debug_code.split('');
        setOtpDigits(digits);
        setTimeout(() => verifyCode(res.debug_code!), 300);
      } else {
        setTimeout(() => otpRefs.current[0]?.focus(), 100);
      }
    } catch (e: any) {
      setError(e.message || 'Doğrulama kodu gönderilemedi.');
    } finally {
      setLoading(false);
    }
  }

  async function handleResendOtp() {
    if (countdown > 0 && !isLocked) return;
    setError('');
    setLoading(true);
    try {
      await apiSendOtp(email.trim().toLowerCase(), i18n.language);
      setCountdown(60);
      setOtpDigits(['', '', '', '', '', '']);
      setAttemptsLeft(null);
      setIsLocked(false);
      setTimeout(() => otpRefs.current[0]?.focus(), 100);
    } catch (e: any) {
      setError(e.message);
    } finally {
      setLoading(false);
    }
  }

  async function verifyCode(code: string) {
    setError('');
    setLoading(true);
    try {
      const res = await apiVerifyOtp(email.trim().toLowerCase(), code);
      if (res.needs_registration) {
        setStep('register');
      } else if (res.token && res.member) {
        onLoginSuccess(res.token, res.member, res.expires_at);
        setStep('success');
      }
    } catch (e: any) {
      setError(e.message || 'Kod doğrulanamadı.');
      setAttemptsLeft(e.attempts_left ?? null);
      if (e.locked) {
        setIsLocked(true);
      } else {
        setOtpDigits(['', '', '', '', '', '']);
        otpRefs.current[0]?.focus();
      }
    } finally {
      setLoading(false);
    }
  }

  async function handleRegister() {
    if (!form.ad.trim() || !form.soyad.trim()) {
      setError('Ad ve soyad zorunludur.'); return;
    }
    if (!form.phoneLocal.trim()) {
      setError('Telefon numarası zorunludur.'); return;
    }
    setError('');
    setLoading(true);
    try {
      const res = await apiRegister({
        email: email.trim().toLowerCase(),
        telefon: fullPhone,
        ad: form.ad.trim(),
        soyad: form.soyad.trim(),
        firma: form.firma.trim() || undefined,
        ulke: form.ulke,
      });
      onLoginSuccess(res.token!, res.member!, res.expires_at);
      setStep('success');
    } catch (e: any) {
      setError(e.message || 'Kayıt başarısız.');
    } finally {
      setLoading(false);
    }
  }

  function handleOtpInput(idx: number, val: string) {
    const digit = val.replace(/\D/g, '').slice(-1);
    const next = [...otpDigits];
    next[idx] = digit;
    setOtpDigits(next);
    if (digit && idx < 5) otpRefs.current[idx + 1]?.focus();
    if (next.join('').length === 6) verifyCode(next.join(''));
  }

  function handleOtpKeyDown(idx: number, e: KeyboardEvent<HTMLInputElement>) {
    if (e.key === 'Backspace' && !otpDigits[idx] && idx > 0) {
      otpRefs.current[idx - 1]?.focus();
    }
  }

  function handleOtpPaste(e: React.ClipboardEvent) {
    const pasted = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
    if (pasted.length === 6) {
      e.preventDefault();
      setOtpDigits(pasted.split(''));
      otpRefs.current[5]?.focus();
    }
  }

  function handleClose() {
    closeAuthModal();
    setTimeout(() => {
      setStep('choose');
      setEmail('');
      setOtpDigits(['', '', '', '', '', '']);
      setError('');
      setAttemptsLeft(null);
      setIsLocked(false);
      setForm({ ad: '', soyad: '', firma: '', ulke: 'TR', countryCode: COUNTRY_CODES[0], phoneLocal: '' });
    }, 300);
  }

  // ─── Render ─────────────────────────────────────────────────────────────────

  return (
    <div
      className="fixed inset-0 z-[100] flex items-center justify-center p-4"
      onClick={e => { if (e.target === e.currentTarget) handleClose(); }}
    >
      <div className="absolute inset-0 bg-black/60 backdrop-blur-sm" />

      <div className="relative bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden">
        {/* Header */}
        <div className="bg-gradient-to-r from-gray-900 to-gray-800 px-6 py-5 flex items-center justify-between">
          <div className="flex items-center gap-3">
            {(step === 'otp' || step === 'email') && (
              <button
                onClick={() => { setStep(step === 'otp' ? 'email' : 'choose'); setError(''); }}
                className="text-white/70 hover:text-white transition"
              >
                <ArrowLeft className="w-4 h-4" />
              </button>
            )}
            <div>
              <h2 className="font-serif text-lg text-white font-light tracking-wide">
                {step === 'choose'   && t('auth.chooseTitle')}
                {step === 'email'    && (mode === 'login' ? t('auth.emailTitleLogin') : t('auth.emailTitleSignup'))}
                {step === 'otp'     && t('auth.otpTitle')}
                {step === 'register'&& t('auth.registerTitle')}
                {step === 'success' && t('auth.successTitle')}
              </h2>
              <p className="text-white/50 text-xs mt-0.5 font-sans">{t('auth.subtitle')}</p>
            </div>
          </div>
          <button onClick={handleClose} className="text-white/60 hover:text-white transition">
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Body */}
        <div className="px-6 py-8">

          {/* ─── Choose Step ────────────────────────────────────────────────── */}
          {step === 'choose' && (
            <div className="space-y-4">
              <div className="text-center mb-6">
                <div className="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-2xl mb-3">
                  <User className="w-6 h-6 text-gray-700" />
                </div>
                <p className="text-sm text-gray-500 font-sans">{t('auth.chooseDesc')}</p>
              </div>

              <button
                onClick={() => { setMode('login'); setStep('email'); setError(''); }}
                className="w-full flex items-center gap-4 px-5 py-4 border-2 border-gray-900 rounded-xl hover:bg-gray-900 hover:text-white transition-all duration-200 group"
              >
                <div className="w-9 h-9 bg-gray-900 group-hover:bg-white rounded-lg flex items-center justify-center flex-shrink-0 transition-colors">
                  <Lock className="w-4 h-4 text-white group-hover:text-gray-900 transition-colors" />
                </div>
                <div className="text-left">
                  <p className="font-medium text-sm">{t('auth.loginBtn')}</p>
                  <p className="text-xs text-gray-400 group-hover:text-white/70 transition-colors">{t('auth.loginSub')}</p>
                </div>
              </button>

              <button
                onClick={() => { setMode('signup'); setStep('email'); setError(''); }}
                className="w-full flex items-center gap-4 px-5 py-4 border-2 border-gray-200 rounded-xl hover:border-gray-400 transition-all duration-200 group"
              >
                <div className="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors">
                  <User className="w-4 h-4 text-gray-600" />
                </div>
                <div className="text-left">
                  <p className="font-medium text-sm text-gray-800">{t('auth.signupBtn')}</p>
                  <p className="text-xs text-gray-400">{t('auth.signupSub')}</p>
                </div>
              </button>

              <p className="text-center text-xs text-gray-400 font-sans pt-2">{t('auth.otpNote')}</p>
            </div>
          )}

          {/* ─── Email Step ─────────────────────────────────────────────────── */}
          {step === 'email' && (
            <div className="space-y-5">
              <div className="text-center mb-6">
                <div className="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-2xl mb-3">
                  <Mail className="w-6 h-6 text-gray-700" />
                </div>
                <p className="text-sm text-gray-500 font-sans">
                  {mode === 'login' ? t('auth.emailDescLogin') : t('auth.emailDescSignup')}
                </p>
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-600 mb-2 tracking-wide uppercase">
                  {t('auth.emailLabel')}
                </label>
                <input
                  ref={emailInputRef}
                  type="email"
                  inputMode="email"
                  placeholder={t('auth.emailPlaceholder')}
                  value={email}
                  onChange={e => setEmail(e.target.value)}
                  onKeyDown={e => e.key === 'Enter' && handleSendOtp()}
                  className="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 font-sans"
                />
              </div>

              {error && (
                <p className="text-red-500 text-xs font-sans bg-red-50 px-3 py-2 rounded-lg">{error}</p>
              )}

              <button
                onClick={handleSendOtp}
                disabled={loading}
                className="w-full bg-gray-900 text-white py-3.5 rounded-xl font-medium text-sm hover:bg-gray-800 transition disabled:opacity-50 font-sans"
              >
                {loading ? t('auth.sending') : t('auth.sendCode')}
              </button>

              <p className="text-center text-xs text-gray-400 font-sans">
                {mode === 'login' ? t('auth.noAccount') + ' ' : t('auth.hasAccount') + ' '}
                <button
                  type="button"
                  onClick={() => { setMode(mode === 'login' ? 'signup' : 'login'); setError(''); }}
                  className="underline underline-offset-2 text-gray-600 hover:text-gray-900 transition"
                >
                  {mode === 'login' ? t('auth.switchSignup') : t('auth.switchLogin')}
                </button>
              </p>
            </div>
          )}

          {/* ─── OTP Step ───────────────────────────────────────────────────── */}
          {step === 'otp' && (
            <div className="space-y-6">
              <div className="text-center">
                <div className="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-2xl mb-3">
                  <Lock className="w-6 h-6 text-gray-700" />
                </div>
                <p className="text-sm text-gray-600 font-sans">
                  <span className="font-medium text-gray-900">{email}</span><br />{t('auth.otpDesc')}
                </p>
              </div>

              <div className="flex justify-center gap-3" onPaste={handleOtpPaste}>
                {otpDigits.map((d, i) => (
                  <input
                    key={i}
                    ref={el => { otpRefs.current[i] = el; }}
                    type="text"
                    inputMode="numeric"
                    maxLength={1}
                    value={d}
                    disabled={isLocked || loading}
                    onChange={e => handleOtpInput(i, e.target.value)}
                    onKeyDown={e => handleOtpKeyDown(i, e)}
                    className={`w-11 h-14 text-center text-xl font-bold border-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-gray-900 transition font-sans
                      ${isLocked ? 'border-red-200 bg-red-50 cursor-not-allowed opacity-60' :
                        d ? 'border-gray-900 bg-gray-50' : 'border-gray-200 bg-white'}`}
                  />
                ))}
              </div>

              {error && (
                <div className="border rounded-xl px-4 py-3 text-center space-y-1 bg-red-50 border-red-100">
                  <p className="text-red-600 text-xs font-sans font-medium">{error}</p>
                  {!isLocked && attemptsLeft !== null && attemptsLeft > 0 && (
                    <p className="text-red-400 text-xs font-sans">{attemptsLeft} {t('auth.attemptsLeft')}</p>
                  )}
                  {isLocked && (
                    <p className="text-red-400 text-xs font-sans">{t('auth.lockedHint')}</p>
                  )}
                </div>
              )}

              {loading && (
                <p className="text-center text-sm text-gray-500 font-sans">{t('auth.verifying')}</p>
              )}

              <div className="text-center space-y-2">
                {isLocked ? (
                  <button
                    onClick={handleResendOtp}
                    disabled={loading}
                    className="text-xs text-red-600 underline font-sans hover:text-red-800 transition font-medium"
                  >
                    {t('auth.resendLocked')}
                  </button>
                ) : countdown > 0 ? (
                  <p className="text-xs text-gray-400 font-sans flex items-center justify-center gap-1">
                    <Clock className="w-3 h-3" />
                    {countdown} {t('auth.resendTimer')}
                  </p>
                ) : (
                  <button
                    onClick={handleResendOtp}
                    disabled={loading}
                    className="text-xs text-gray-600 underline font-sans hover:text-gray-900 transition"
                  >
                    {t('auth.resend')}
                  </button>
                )}
                <p className="text-xs text-gray-400 font-sans">{t('auth.spamHint')}</p>
              </div>
            </div>
          )}

          {/* ─── Register Step ──────────────────────────────────────────────── */}
          {step === 'register' && (
            <div className="space-y-4">
              <div className="text-center mb-2">
                <div className="inline-flex items-center justify-center w-14 h-14 bg-gray-100 rounded-2xl mb-3">
                  <User className="w-6 h-6 text-gray-700" />
                </div>
                <p className="text-sm text-gray-500 font-sans">{t('auth.registerDesc')}</p>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="block text-xs font-medium text-gray-600 mb-1.5">{t('auth.firstName')} *</label>
                  <input
                    type="text"
                    value={form.ad}
                    onChange={e => setForm(f => ({ ...f, ad: e.target.value }))}
                    className="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 font-sans"
                    placeholder="Ahmet"
                    autoFocus
                  />
                </div>
                <div>
                  <label className="block text-xs font-medium text-gray-600 mb-1.5">{t('auth.lastName')} *</label>
                  <input
                    type="text"
                    value={form.soyad}
                    onChange={e => setForm(f => ({ ...f, soyad: e.target.value }))}
                    className="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 font-sans"
                    placeholder="Yılmaz"
                  />
                </div>
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-600 mb-1.5">
                  <Phone className="w-3 h-3 inline mr-1" />
                  {t('auth.phone')} *
                </label>
                <div className="flex gap-2">
                  <select
                    value={form.countryCode.dial}
                    onChange={e => {
                      const found = COUNTRY_CODES.find(c => c.dial === e.target.value);
                      if (found) setForm(f => ({ ...f, countryCode: found }));
                    }}
                    className="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 font-sans"
                  >
                    {COUNTRY_CODES.map(c => (
                      <option key={c.code} value={c.dial}>{c.flag} {c.dial}</option>
                    ))}
                  </select>
                  <input
                    type="tel"
                    inputMode="numeric"
                    placeholder="5xxxxxxxxx"
                    value={form.phoneLocal}
                    onChange={e => setForm(f => ({ ...f, phoneLocal: e.target.value.replace(/\D/g, '') }))}
                    className="flex-1 border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 font-sans"
                  />
                </div>
                <p className="text-xs text-gray-400 mt-1 font-sans">{t('auth.phoneHint')}</p>
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-600 mb-1.5">
                  <Building2 className="w-3 h-3 inline mr-1" />
                  {t('auth.company')}
                </label>
                <input
                  type="text"
                  value={form.firma}
                  onChange={e => setForm(f => ({ ...f, firma: e.target.value }))}
                  className="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 font-sans"
                  placeholder={t('auth.companyPlaceholder')}
                />
              </div>

              <div>
                <label className="block text-xs font-medium text-gray-600 mb-1.5">
                  <Globe className="w-3 h-3 inline mr-1" />
                  {t('auth.country')} *
                </label>
                <select
                  value={form.ulke}
                  onChange={e => setForm(f => ({ ...f, ulke: e.target.value }))}
                  className="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 bg-white font-sans"
                >
                  {COUNTRY_CODES.map(c => (
                    <option key={c.code} value={c.code}>{c.flag} {c.name}</option>
                  ))}
                </select>
              </div>

              <div className="bg-gray-50 rounded-xl px-3 py-2.5 text-xs text-gray-500 font-sans flex items-center gap-2">
                <Mail className="w-3 h-3 flex-shrink-0" />
                <span>{email}</span>
              </div>

              {error && (
                <div className={`rounded-xl px-4 py-3.5 flex gap-3 items-start ${
                  error.includes('e-posta') && error.includes('kayıtlı')
                    ? 'bg-blue-50 border border-blue-100'
                    : error.includes('telefon') && error.includes('kayıtlı')
                    ? 'bg-amber-50 border border-amber-100'
                    : 'bg-red-50 border border-red-100'
                }`}>
                  <div className="flex-shrink-0 mt-0.5">
                    {error.includes('e-posta') && error.includes('kayıtlı') ? (
                      <Mail className="w-4 h-4 text-blue-500" />
                    ) : error.includes('telefon') && error.includes('kayıtlı') ? (
                      <PhoneOff className="w-4 h-4 text-amber-500" />
                    ) : (
                      <AlertCircle className="w-4 h-4 text-red-500" />
                    )}
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className={`text-xs font-medium font-sans leading-relaxed ${
                      error.includes('e-posta') && error.includes('kayıtlı') ? 'text-blue-700'
                      : error.includes('telefon') && error.includes('kayıtlı') ? 'text-amber-700'
                      : 'text-red-600'
                    }`}>{error}</p>
                    {error.includes('e-posta') && error.includes('kayıtlı') && (
                      <button
                        type="button"
                        onClick={() => { setStep('email'); setError(''); }}
                        className="mt-2 text-xs font-medium text-blue-600 underline underline-offset-2 hover:text-blue-800 transition font-sans"
                      >
                        {t('auth.backToLogin')}
                      </button>
                    )}
                  </div>
                </div>
              )}

              <button
                onClick={handleRegister}
                disabled={loading}
                className="w-full bg-gray-900 text-white py-3.5 rounded-xl font-medium text-sm hover:bg-gray-800 transition disabled:opacity-50 mt-2 font-sans"
              >
                {loading ? t('auth.registering') : t('auth.register')}
              </button>
            </div>
          )}

          {/* ─── Success Step ───────────────────────────────────────────────── */}
          {step === 'success' && (
            <div className="text-center py-4 space-y-4">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-green-100 rounded-2xl">
                <CheckCircle className="w-8 h-8 text-green-600" />
              </div>
              <h3 className="font-serif text-xl text-gray-900">{t('auth.successTitle')}</h3>
              <p className="text-sm text-gray-500 font-sans leading-relaxed">{t('auth.successDesc')}</p>
              <div className="flex flex-col gap-2 mt-2">
                <button
                  onClick={() => { handleClose(); navigate('/toptan-siparis'); }}
                  className="w-full bg-gray-900 text-white py-3 rounded-xl font-medium text-sm hover:bg-gray-800 transition font-sans"
                >
                  {t('auth.goOrder')}
                </button>
                <button
                  onClick={handleClose}
                  className="w-full bg-gray-100 text-gray-700 py-2.5 rounded-xl font-medium text-sm hover:bg-gray-200 transition font-sans"
                >
                  {t('auth.browseProducts')}
                </button>
              </div>
            </div>
          )}

        </div>
      </div>
    </div>
  );
}
