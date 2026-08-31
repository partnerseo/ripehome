export type Method = 'lmp' | 'due_date' | 'conception' | 'ivf_d5' | 'ivf_d3';

export interface GestationalAge {
  lmp_date: string;
  due_date: string;
  ga_days: number;
  week: number;
  day: number;
  display: string;
  trimester: number;
  days_left: number;
  progress: number;
  is_overdue: boolean;
  needs_completion_prompt: boolean;
}

export interface Pregnancy {
  id: number;
  method: Method;
  input_date: string;
  cycle_length: number;
  baby_count: number;
  status: 'active' | 'ended';
  ended_at: string | null;
  ended_reason: string | null;
  /** Kapanmış gebelikte sunucu bu alanı hiç göndermez. */
  gestational_age?: GestationalAge;
}

export interface User {
  id: number;
  email: string;
  name: string | null;
  locale: string;
  timezone: string;
  has_consent: boolean;
}
