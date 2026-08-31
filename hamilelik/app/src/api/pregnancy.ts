import { request } from '@/api/client';
import type { Method, Pregnancy } from '@/api/types';

export async function currentPregnancy(): Promise<Pregnancy | null> {
  // Aktif gebelik yoksa sunucu 200 ile data: null döner — hata yolu tetiklenmez.
  const { data } = await request<{ data: Pregnancy | null }>('/pregnancies/current');

  return data;
}

export async function createPregnancy(input: {
  method: Method;
  input_date: string;
  cycle_length?: number;
  baby_count?: number;
}): Promise<Pregnancy> {
  const { data } = await request<{ data: Pregnancy }>('/pregnancies', {
    method: 'POST',
    body: input,
  });

  return data;
}

export type EndReason = 'birth' | 'loss' | 'other';

export async function endPregnancy(id: number, reason?: EndReason): Promise<Pregnancy> {
  const { data } = await request<{ data: Pregnancy }>(`/pregnancies/${id}/end`, {
    method: 'POST',
    body: { reason },
  });

  return data;
}
