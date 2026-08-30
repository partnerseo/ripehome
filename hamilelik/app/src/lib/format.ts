const MONTHS_TR = [
  'Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran',
  'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık',
];

/** "2026-10-12" → "12 Ekim 2026" */
export function formatDate(iso: string): string {
  const [year, month, day] = iso.split('-').map(Number);

  return `${day} ${MONTHS_TR[month - 1]} ${year}`;
}

/** Geri sayımı okunur hâle getirir; termin geçtiyse ayrı ifade kullanılır. */
export function formatDaysLeft(daysLeft: number): string {
  if (daysLeft > 1) return `${daysLeft} gün kaldı`;
  if (daysLeft === 1) return 'Yarın';
  if (daysLeft === 0) return 'Bugün termin günü';

  return `Termin ${Math.abs(daysLeft)} gün önceydi`;
}

export function trimesterLabel(trimester: number): string {
  return `${trimester}. trimester`;
}
