import { formatDate, formatDaysLeft, trimesterLabel } from '@/lib/format';

describe('formatDate', () => {
  it('tarihi Türkçe yazar', () => {
    expect(formatDate('2026-10-12')).toBe('12 Ekim 2026');
    expect(formatDate('2026-01-05')).toBe('5 Ocak 2026');
    expect(formatDate('2026-12-31')).toBe('31 Aralık 2026');
  });
});

describe('formatDaysLeft', () => {
  it('geri sayımı okunur hâle getirir', () => {
    expect(formatDaysLeft(112)).toBe('112 gün kaldı');
    expect(formatDaysLeft(1)).toBe('Yarın');
    expect(formatDaysLeft(0)).toBe('Bugün termin günü');
  });

  it('termin geçtiğinde negatif gün göstermez', () => {
    expect(formatDaysLeft(-3)).toBe('Termin 3 gün önceydi');
  });
});

describe('trimesterLabel', () => {
  it('trimesteri etiketler', () => {
    expect(trimesterLabel(1)).toBe('1. trimester');
    expect(trimesterLabel(3)).toBe('3. trimester');
  });
});
