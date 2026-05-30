const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

const TOKEN_KEY    = 'ripehome_member_token';
const EXPIRES_KEY  = 'ripehome_token_expires';

// ─── Token storage ────────────────────────────────────────────────────────────

export function getStoredToken(): string | null {
  try { return localStorage.getItem(TOKEN_KEY); } catch { return null; }
}

export function setStoredToken(token: string, expiresAt?: string): void {
  try {
    localStorage.setItem(TOKEN_KEY, token);
    if (expiresAt) localStorage.setItem(EXPIRES_KEY, expiresAt);
  } catch {}
}

export function removeStoredToken(): void {
  try {
    localStorage.removeItem(TOKEN_KEY);
    localStorage.removeItem(EXPIRES_KEY);
  } catch {}
}

/** Token'ın client-side'da süresi dolmuş mu? */
export function isTokenExpired(): boolean {
  try {
    const expires = localStorage.getItem(EXPIRES_KEY);
    if (!expires) return false;
    return new Date(expires) <= new Date();
  } catch { return false; }
}

function authHeaders(): HeadersInit {
  const token = getStoredToken();
  return {
    'Content-Type': 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  };
}

// ─── Auth API calls ───────────────────────────────────────────────────────────

export async function apiSendOtp(
  email: string,
  lang?: string,
): Promise<{ message: string; debug_code?: string }> {
  const res = await fetch(`${API_URL}/auth/send-otp`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, ...(lang ? { lang } : {}) }),
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.message || 'Doğrulama kodu gönderilemedi.');
  return data;
}

export async function apiVerifyOtp(email: string, kod: string): Promise<{
  needs_registration: boolean;
  token?: string;
  expires_at?: string;
  email?: string;
  member?: MemberData;
  attempts_left?: number | null;
}> {
  const res = await fetch(`${API_URL}/auth/verify-otp`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, kod }),
  });
  const data = await res.json();
  if (!res.ok) throw Object.assign(new Error(data.message || 'Kod doğrulanamadı.'), {
    attempts_left: data.attempts_left ?? null,
    locked: data.locked ?? false,
  });
  return data;
}

export async function apiRegister(payload: {
  email: string;
  telefon: string;
  ad: string;
  soyad: string;
  firma?: string;
  ulke: string;
}): Promise<{ message: string; token?: string; expires_at?: string; member?: MemberData }> {
  const res = await fetch(`${API_URL}/auth/register`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });
  const data = await res.json();
  if (!res.ok) throw new Error(data.message || 'Kayıt başarısız.');
  return data;
}

export async function apiLogout(): Promise<void> {
  try {
    await fetch(`${API_URL}/auth/logout`, {
      method: 'POST',
      headers: authHeaders(),
    });
  } catch {}
  removeStoredToken();
}

export async function apiGetMe(): Promise<MemberData | null> {
  if (isTokenExpired()) {
    removeStoredToken();
    return null;
  }
  const token = getStoredToken();
  if (!token) return null;
  try {
    const res = await fetch(`${API_URL}/auth/me`, {
      headers: authHeaders(),
    });
    if (res.status === 401) {
      removeStoredToken();
      return null;
    }
    if (!res.ok) return null;
    const data = await res.json();
    return data.member ?? null;
  } catch {
    return null;
  }
}

export async function apiGetMyOrders(): Promise<OrderData[]> {
  if (isTokenExpired()) { removeStoredToken(); return []; }
  const token = getStoredToken();
  if (!token) return [];
  try {
    const res = await fetch(`${API_URL}/uye/siparislerim`, {
      headers: authHeaders(),
    });
    if (res.status === 401) { removeStoredToken(); return []; }
    if (!res.ok) return [];
    const data = await res.json();
    return data.data ?? [];
  } catch {
    return [];
  }
}

// ─── Types ────────────────────────────────────────────────────────────────────

export interface OrderData {
  id: number;
  status: string;
  status_label: string;
  items_count: number;
  total_quantity: number;
  items: Array<{ product_name: string; product_image?: string; quantity: number }>;
  created_at: string;
  admin_notes?: string;
}

export interface MemberData {
  id: number;
  ad: string;
  soyad: string;
  firma?: string;
  email: string;
  telefon?: string;
  ulke: string;
  durum: 'beklemede' | 'onaylandi' | 'pasif';
}
