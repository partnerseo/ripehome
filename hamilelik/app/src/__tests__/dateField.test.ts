import { toIsoDate } from '@/components/DateField';

describe('toIsoDate', () => {
  it('alanları ISO tarihe çevirir', () => {
    expect(toIsoDate({ day: '5', month: '1', year: '2026' })).toBe('2026-01-05');
    expect(toIsoDate({ day: '12', month: '10', year: '2026' })).toBe('2026-10-12');
  });

  it('eksik alanda null döner', () => {
    expect(toIsoDate({ day: '', month: '', year: '' })).toBeNull();
    expect(toIsoDate({ day: '5', month: '1', year: '26' })).toBeNull();
  });

  it('takvimde olmayan tarihi reddeder', () => {
    expect(toIsoDate({ day: '30', month: '2', year: '2026' })).toBeNull();
    expect(toIsoDate({ day: '31', month: '4', year: '2026' })).toBeNull();
  });

  it('artık yılda 29 Şubatı kabul eder', () => {
    expect(toIsoDate({ day: '29', month: '2', year: '2024' })).toBe('2024-02-29');
    expect(toIsoDate({ day: '29', month: '2', year: '2026' })).toBeNull();
  });
});
