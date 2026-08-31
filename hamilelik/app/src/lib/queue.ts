import * as Crypto from 'expo-crypto';

import { request } from '@/api/client';
import { store } from '@/lib/store';
import type { QueueItem, QueueKind } from '@/lib/store.types';

export interface SyncAlert {
  type: 'blood_pressure' | 'fetal_movement' | 'contractions' | 'symptom';
  reference: string;
  detail: string;
}

export function newId(): string {
  return Crypto.randomUUID();
}

/** Kaydı yerele yazar. Gönderim ayrı bir adımdır ve başarısız olabilir. */
export async function enqueue(kind: QueueKind, payload: { client_uuid: string }): Promise<void> {
  await store.put({ client_uuid: payload.client_uuid, kind, payload });
}

export async function pendingCount(): Promise<number> {
  return store.count();
}

/**
 * Kuyruğu sunucuya gönderir.
 *
 * Gönderim başarısız olursa kuyruk olduğu gibi kalır ve bir sonraki denemede
 * tekrar gönderilir. Sunucu client_uuid'yi idempotency anahtarı olarak
 * kullandığı için tekrar gönderim kopya üretmez — yanıt alınamadan kopan bir
 * bağlantıdan sonra bile.
 *
 * @returns Sunucunun döndürdüğü uyarılar (yüksek tansiyon, 5-1-1 gibi).
 */
export async function flush(): Promise<SyncAlert[]> {
  const items = await store.all();

  if (items.length === 0) {
    return [];
  }

  const body: Record<string, unknown[]> = {};

  for (const item of items) {
    (body[item.kind] ??= []).push(item.payload);
  }

  const response = await request<{ alerts: SyncAlert[] }>('/sync', { method: 'POST', body });

  // Yalnızca sunucunun kabul ettiği kayıtlar silinir.
  await store.remove(items.map((i: QueueItem) => i.client_uuid));

  return response.alerts ?? [];
}
