import { useRef } from 'react';
import { Text, TextInput, View } from 'react-native';

import { radius, spacing, type, usePalette } from '@/theme/tokens';

type Props = {
  value: { day: string; month: string; year: string };
  onChange: (value: { day: string; month: string; year: string }) => void;
};

/**
 * Gün / ay / yıl alanları.
 *
 * Yerel tarih seçici yerine üç sayı alanı: takvimde ay ay geriye gitmek yerine
 * kullanıcı tarihi doğrudan yazar — çoğu kişi son adet tarihini zaten bilir.
 */
export function DateField({ value, onChange }: Props) {
  const palette = usePalette();
  const monthRef = useRef<TextInput>(null);
  const yearRef = useRef<TextInput>(null);

  const box = {
    backgroundColor: palette.surface,
    borderWidth: 1,
    borderColor: palette.line,
    borderRadius: radius.md,
    paddingVertical: spacing.md,
    fontSize: 20,
    color: palette.ink,
    textAlign: 'center' as const,
  };

  return (
    <View style={{ gap: spacing.sm }}>
      <View style={{ flexDirection: 'row', gap: spacing.sm }}>
        <TextInput
          accessibilityLabel="Gün"
          inputMode="numeric"
          keyboardType="number-pad"
          maxLength={2}
          onChangeText={(day) => {
            onChange({ ...value, day });
            if (day.length === 2) monthRef.current?.focus();
          }}
          placeholder="GG"
          placeholderTextColor={palette.faint}
          style={[box, { flex: 1 }]}
          value={value.day}
        />
        <TextInput
          accessibilityLabel="Ay"
          inputMode="numeric"
          keyboardType="number-pad"
          maxLength={2}
          onChangeText={(month) => {
            onChange({ ...value, month });
            if (month.length === 2) yearRef.current?.focus();
          }}
          placeholder="AA"
          placeholderTextColor={palette.faint}
          ref={monthRef}
          style={[box, { flex: 1 }]}
          value={value.month}
        />
        <TextInput
          accessibilityLabel="Yıl"
          inputMode="numeric"
          keyboardType="number-pad"
          maxLength={4}
          onChangeText={(year) => onChange({ ...value, year })}
          placeholder="YYYY"
          placeholderTextColor={palette.faint}
          ref={yearRef}
          style={[box, { flex: 1.4 }]}
          value={value.year}
        />
      </View>
      <Text style={{ ...type.small, color: palette.faint }}>Gün · Ay · Yıl</Text>
    </View>
  );
}

/** Alanları "YYYY-AA-GG" hâline getirir; eksik veya geçersizse null döner. */
export function toIsoDate(value: { day: string; month: string; year: string }): string | null {
  const day = Number(value.day);
  const month = Number(value.month);
  const year = Number(value.year);

  if (!Number.isInteger(day) || !Number.isInteger(month) || !Number.isInteger(year)) return null;
  if (value.year.length !== 4 || day < 1 || day > 31 || month < 1 || month > 12) return null;

  const iso = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

  // 31 Şubat gibi takvimde olmayan tarihleri ele.
  const parsed = new Date(`${iso}T00:00:00Z`);

  return parsed.toISOString().slice(0, 10) === iso ? iso : null;
}
