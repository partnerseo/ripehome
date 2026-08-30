import { request } from '@/api/client';

export type AppointmentCategory = 'usg' | 'lab' | 'vaccine' | 'visit';

export interface Appointment {
  id: number;
  title: string;
  category: AppointmentCategory;
  description: string | null;
  is_optional: boolean;
  window: {
    start_week: number | null;
    end_week: number | null;
    start_on: string | null;
    end_on: string | null;
  };
  scheduled_at: string | null;
  location: string | null;
  doctor_name: string | null;
  notes: string | null;
  completed_at: string | null;
  source: 'auto' | 'manual';
}

export async function appointments(): Promise<Appointment[]> {
  const { data } = await request<{ data: Appointment[] }>('/appointments');

  return data;
}

export async function completeAppointment(id: number): Promise<Appointment> {
  const { data } = await request<{ data: Appointment }>(`/appointments/${id}`, {
    method: 'PATCH',
    body: { completed_at: new Date().toISOString() },
  });

  return data;
}

export async function registerDevice(token: string, platform: string): Promise<void> {
  await request('/devices', {
    method: 'POST',
    body: {
      expo_push_token: token,
      platform,
      timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
    },
  });
}
