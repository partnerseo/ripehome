import Constants from 'expo-constants';

import { session } from '@/lib/session';

/**
 * API adresi. Geliştirmede Metro'yu barındıran makinenin IP'si kullanılır ki
 * gerçek cihaz da localhost yerine doğru sunucuya baksın.
 */
export const API_URL: string =
  (Constants.expoConfig?.extra?.apiUrl as string | undefined) ??
  process.env.EXPO_PUBLIC_API_URL ??
  'http://localhost:8000/api/v1';

export class ApiError extends Error {
  constructor(
    message: string,
    readonly status: number,
    readonly code?: string,
    readonly errors?: Record<string, string[]>,
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

type RequestOptions = {
  method?: 'GET' | 'POST' | 'PATCH' | 'DELETE';
  body?: unknown;
  auth?: boolean;
};

export async function request<T>(path: string, options: RequestOptions = {}): Promise<T> {
  const { method = 'GET', body, auth = true } = options;

  const headers: Record<string, string> = {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  };

  if (auth) {
    const token = await session.get();

    if (token) {
      headers.Authorization = `Bearer ${token}`;
    }
  }

  const response = await fetch(`${API_URL}${path}`, {
    method,
    headers,
    body: body === undefined ? undefined : JSON.stringify(body),
  });

  const text = await response.text();
  const payload = text.length > 0 ? JSON.parse(text) : null;

  if (!response.ok) {
    throw new ApiError(
      payload?.message ?? 'Bir şeyler ters gitti.',
      response.status,
      payload?.code,
      payload?.errors,
    );
  }

  return payload as T;
}
