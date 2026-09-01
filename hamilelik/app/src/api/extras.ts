import { API_URL, request } from '@/api/client';
import { session } from '@/lib/session';

export interface ChecklistItem {
  id: number;
  title: string;
  group: 'anne' | 'bebek' | 'belgeler';
  is_done: boolean;
}

export interface Share {
  id: number;
  invited_email: string;
  role: string;
  accepted_at: string | null;
  token: string;
}

export interface BellyPhoto {
  id: number;
  week: number;
  taken_on: string;
}

export async function checklist(): Promise<ChecklistItem[]> {
  const { data } = await request<{ data: ChecklistItem[] }>('/checklist');

  return data;
}

export async function toggleChecklistItem(id: number, done: boolean): Promise<void> {
  await request(`/checklist/${id}`, { method: 'PATCH', body: { is_done: done } });
}

export async function addChecklistItem(title: string, group: ChecklistItem['group']): Promise<void> {
  await request('/checklist', { method: 'POST', body: { title, group } });
}

export async function shares(): Promise<Share[]> {
  const { data } = await request<{ data: Share[] }>('/shares');

  return data;
}

export async function invitePartner(email: string): Promise<Share> {
  const { data } = await request<{ data: Share }>('/shares', { method: 'POST', body: { email } });

  return data;
}

export async function revokeShare(id: number): Promise<void> {
  await request(`/shares/${id}`, { method: 'DELETE' });
}

export async function bellyPhotos(): Promise<BellyPhoto[]> {
  const { data } = await request<{ data: BellyPhoto[] }>('/belly-photos');

  return data;
}

/** Fotoğraf herkese açık bir adreste durmaz; istek jetonla yapılır. */
export async function bellyPhotoHeaders(): Promise<Record<string, string>> {
  const token = await session.get();

  return token === null ? {} : { Authorization: `Bearer ${token}` };
}

export function bellyPhotoUrl(id: number): string {
  return `${API_URL}/belly-photos/${id}/file`;
}

export async function uploadBellyPhoto(uri: string, week: number): Promise<void> {
  const token = await session.get();
  const body = new FormData();

  // React Native'de dosya bu üçlüyle gönderilir; web'de blob'a çevrilir.
  if (uri.startsWith('blob:') || uri.startsWith('data:')) {
    const blob = await (await fetch(uri)).blob();
    body.append('photo', blob, 'karin.jpg');
  } else {
    body.append('photo', { uri, name: 'karin.jpg', type: 'image/jpeg' } as unknown as Blob);
  }

  body.append('week', String(week));

  const response = await fetch(`${API_URL}/belly-photos`, {
    method: 'POST',
    headers: { Accept: 'application/json', ...(token !== null ? { Authorization: `Bearer ${token}` } : {}) },
    body,
  });

  if (!response.ok) {
    throw new Error('Fotoğraf yüklenemedi.');
  }
}
