import { meetsFiveOneOne, type Contraction } from '@/lib/contractions';

const NOW = new Date('2026-06-22T11:00:00Z').getTime();

/** i. dakikada başlayan, verilen saniye kadar süren kasılma. */
function at(minute: number, durationSec: number, intervalSec: number | null): Contraction {
  const start = new Date(NOW - 60 * 60 * 1000 + minute * 60 * 1000);

  return {
    started_at: start.toISOString(),
    ended_at: new Date(start.getTime() + durationSec * 1000).toISOString(),
    duration_sec: durationSec,
    interval_sec: intervalSec,
  };
}

describe('5-1-1 kuralı', () => {
  it('bir saattir 5 dakikada bir gelen 70 saniyelik kasılmalarda uyarır', () => {
    const entries = Array.from({ length: 13 }, (_, i) => at(i * 5, 70, i === 0 ? null : 300));

    expect(meetsFiveOneOne(entries, NOW)).toBe(true);
  });

  it('seyrek kasılmalarda uyarmaz', () => {
    expect(meetsFiveOneOne([at(0, 30, null), at(25, 35, 1500)], NOW)).toBe(false);
  });

  it('sık ama kısa kasılmalarda uyarmaz', () => {
    // 5 dakikada bir geliyor ama 30 saniye sürüyor: kural sağlanmıyor.
    const entries = Array.from({ length: 13 }, (_, i) => at(i * 5, 30, i === 0 ? null : 300));

    expect(meetsFiveOneOne(entries, NOW)).toBe(false);
  });

  it('bir saati doldurmayan seride uyarmaz', () => {
    // Yarım saattir 5 dakikada bir, 70 saniye: henüz erken.
    const entries = Array.from({ length: 7 }, (_, i) => at(30 + i * 5, 70, i === 0 ? null : 300));

    expect(meetsFiveOneOne(entries, NOW)).toBe(false);
  });

  it('tek kasılmada uyarmaz', () => {
    expect(meetsFiveOneOne([at(0, 70, null)], NOW)).toBe(false);
    expect(meetsFiveOneOne([], NOW)).toBe(false);
  });

  it('bir saatten eski kayıtları hesaba katmaz', () => {
    const old = Array.from({ length: 13 }, (_, i) => at(i * 5 - 120, 70, i === 0 ? null : 300));

    expect(meetsFiveOneOne(old, NOW)).toBe(false);
  });
});
