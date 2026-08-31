export interface Contraction {
  started_at: string;
  ended_at: string;
  duration_sec: number;
  interval_sec: number | null;
}

/**
 * 5-1-1 kuralı: 5 dakikada bir gelen, 1 dakika süren, 1 saattir devam eden.
 *
 * Sunucudaki ContractionSession::meetsFiveOneOne() ile aynı kuralı uygular.
 * İstemcide de bulunması gerekiyor çünkü uyarı bağlantı olmadan da çıkmalı —
 * doğum sancısı hastaneye giderken başlar, orada internet olmayabilir.
 */
export const RULE = { intervalSec: 300, durationSec: 60, windowSec: 3600 } as const;

export function meetsFiveOneOne(entries: Contraction[], now: number = Date.now()): boolean {
  const recent = entries.filter((e) => new Date(e.started_at).getTime() >= now - RULE.windowSec * 1000);

  if (recent.length < 2) return false;

  const span =
    (new Date(recent[recent.length - 1].started_at).getTime() - new Date(recent[0].started_at).getTime()) / 1000;

  if (span < RULE.windowSec) return false;

  const intervals = recent.map((e) => e.interval_sec).filter((v): v is number => v !== null);

  if (intervals.length === 0) return false;

  const avgInterval = intervals.reduce((a, b) => a + b, 0) / intervals.length;
  const avgDuration = recent.reduce((a, e) => a + e.duration_sec, 0) / recent.length;

  return avgInterval <= RULE.intervalSec && avgDuration >= RULE.durationSec;
}
