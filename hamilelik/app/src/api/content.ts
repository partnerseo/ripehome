import { ApiError, request } from '@/api/client';

export interface WeekContent {
  week: number;
  locale: string;
  baby_size_label: string | null;
  baby_length_mm: number | null;
  baby_weight_g: number | null;
  baby_body: string | null;
  mother_body: string | null;
  tips_body: string | null;
  review: { reviewed_by: string | null; reviewed_at: string | null };
  sources: { label: string; url: string | null }[];
}

/**
 * Bir haftanın içeriği.
 *
 * Yayında olmayan hafta 404 döner — gözden geçirilmemiş metin sunucudan hiç
 * çıkmaz. Bu bir hata değil, "bu hafta henüz hazır değil" durumu, o yüzden
 * null'a çevriliyor.
 */
export async function weekContent(week: number): Promise<WeekContent | null> {
  try {
    const { data } = await request<{ data: WeekContent }>(`/weeks/${week}`);

    return data;
  } catch (error) {
    if (error instanceof ApiError && error.code === 'week_content_unavailable') {
      return null;
    }

    throw error;
  }
}
