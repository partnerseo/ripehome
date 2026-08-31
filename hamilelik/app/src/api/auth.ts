import { request } from '@/api/client';
import type { User } from '@/api/types';
import { session } from '@/lib/session';

export async function requestCode(email: string): Promise<void> {
  await request('/auth/otp/request', { method: 'POST', body: { email }, auth: false });
}

export async function verifyCode(email: string, code: string): Promise<User> {
  const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

  const result = await request<{ token: string; user: User }>('/auth/otp/verify', {
    method: 'POST',
    body: { email, code, timezone },
    auth: false,
  });

  await session.set(result.token);

  return result.user;
}

export async function acceptConsent(): Promise<void> {
  await request('/consents', { method: 'POST' });
}

export async function exportAccountData(): Promise<unknown> {
  return request('/me/export');
}

export async function deleteAccount(email: string): Promise<void> {
  await request('/me', { method: 'DELETE', body: { confirm_email: email } });
  await session.clear();
}

export async function me(): Promise<User> {
  const { user } = await request<{ user: User }>('/me');

  return user;
}

export async function logout(): Promise<void> {
  try {
    await request('/auth/logout', { method: 'POST' });
  } finally {
    // Sunucu ulaşılmasa bile yerel jeton silinir; kullanıcı çıkış yaptığını görür.
    await session.clear();
  }
}
