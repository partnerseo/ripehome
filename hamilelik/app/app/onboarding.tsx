import { calculate, type Method } from '@engine';
import { useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useMemo, useState } from 'react';
import { Pressable, Text, View } from 'react-native';

import { ApiError } from '@/api/client';
import { createPregnancy } from '@/api/pregnancy';
import { Button } from '@/components/Button';
import { DateField, toIsoDate } from '@/components/DateField';
import { Screen } from '@/components/Screen';
import { formatDate } from '@/lib/format';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

const METHODS: { value: Method; label: string; hint: string }[] = [
  { value: 'lmp', label: 'Son adet tarihim', hint: 'En yaygın yöntem' },
  { value: 'due_date', label: 'Doktorumun verdiği tarih', hint: 'Tahmini doğum tarihi' },
  { value: 'conception', label: 'Gebe kaldığım tarih', hint: 'Biliyorsanız' },
  { value: 'ivf_d5', label: 'IVF — 5. gün transferi', hint: 'Blastosist' },
  { value: 'ivf_d3', label: 'IVF — 3. gün transferi', hint: 'Embriyo' },
];

export default function Onboarding() {
  const palette = usePalette();
  const queryClient = useQueryClient();

  const [method, setMethod] = useState<Method>('lmp');
  const [date, setDate] = useState({ day: '', month: '', year: '' });
  const [cycleLength, setCycleLength] = useState(28);
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const isoDate = toIsoDate(date);

  /**
   * Önizleme istemcide, motorun kendisiyle hesaplanır: kullanıcı kaydetmeden
   * önce hangi haftada olduğunu görür ve sunucuya gitmeye gerek kalmaz.
   */
  const preview = useMemo(() => {
    if (isoDate === null) return null;

    try {
      const today = new Date().toISOString().slice(0, 10);

      return calculate(method, isoDate, today, cycleLength);
    } catch {
      // Gelecek tarih gibi geçersiz girdiler burada sessizce önizlemesiz kalır;
      // kullanıcıya asıl mesajı sunucu doğrulaması verir.
      return null;
    }
  }, [method, isoDate, cycleLength]);

  async function submit() {
    if (isoDate === null) return;

    setError(null);
    setBusy(true);

    try {
      await createPregnancy({
        method,
        input_date: isoDate,
        cycle_length: method === 'lmp' ? cycleLength : undefined,
      });
      await queryClient.invalidateQueries();
      router.replace('/home');
    } catch (e) {
      setError(e instanceof ApiError ? e.message : 'Sunucuya ulaşılamadı. Bağlantınızı kontrol edin.');
    } finally {
      setBusy(false);
    }
  }

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.label, color: palette.faint }}>Kurulum</Text>
        <Text style={{ ...type.title, color: palette.ink }}>Hangi tarihi biliyorsunuz?</Text>
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Bu tarihten gebelik haftanızı ve tahmini doğum tarihinizi hesaplıyoruz. Doktorunuz
          ultrasonda farklı bir hafta söylerse sonradan düzeltebilirsiniz.
        </Text>
      </View>

      <View style={{ gap: spacing.sm }}>
        {METHODS.map((option) => {
          const selected = option.value === method;

          return (
            <Pressable
              accessibilityRole="radio"
              accessibilityState={{ selected }}
              key={option.value}
              onPress={() => setMethod(option.value)}
              style={{
                backgroundColor: selected ? palette.accentSoft : palette.surface,
                borderColor: selected ? palette.accent : palette.line,
                borderWidth: selected ? 2 : 1,
                borderRadius: radius.md,
                padding: spacing.md,
              }}
            >
              <Text style={{ ...type.heading, color: selected ? palette.accent : palette.ink }}>
                {option.label}
              </Text>
              <Text style={{ ...type.small, color: palette.faint, marginTop: 2 }}>{option.hint}</Text>
            </Pressable>
          );
        })}
      </View>

      <DateField value={date} onChange={setDate} />

      {method === 'lmp' && (
        <View style={{ gap: spacing.sm }}>
          <Text style={{ ...type.heading, color: palette.ink }}>Adet döngünüz kaç gün?</Text>
          <Text style={{ ...type.small, color: palette.muted }}>
            Döngünüz 28 günden uzunsa yumurtlama geç olur ve doğum tarihi ileri kayar.
          </Text>
          <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: spacing.sm }}>
            {[21, 26, 28, 30, 32, 35].map((length) => {
              const selected = length === cycleLength;

              return (
                <Pressable
                  accessibilityRole="radio"
                  accessibilityState={{ selected }}
                  key={length}
                  onPress={() => setCycleLength(length)}
                  style={{
                    paddingVertical: spacing.sm,
                    paddingHorizontal: spacing.md,
                    borderRadius: radius.pill,
                    borderWidth: selected ? 2 : 1,
                    borderColor: selected ? palette.accent : palette.line,
                    backgroundColor: selected ? palette.accentSoft : palette.surface,
                  }}
                >
                  <Text style={{ color: selected ? palette.accent : palette.ink, fontWeight: '600' }}>
                    {length}
                  </Text>
                </Pressable>
              );
            })}
          </View>
        </View>
      )}

      {preview !== null && (
        <View
          style={{
            backgroundColor: palette.surface,
            borderRadius: radius.md,
            borderWidth: 1,
            borderColor: palette.line,
            padding: spacing.md,
            gap: 4,
          }}
        >
          <Text style={{ ...type.label, color: palette.faint }}>Önizleme</Text>
          <Text style={{ fontSize: 30, fontWeight: '700', color: palette.accent }}>
            {preview.display}
          </Text>
          <Text style={{ ...type.small, color: palette.muted }}>
            Tahmini doğum: {formatDate(preview.due_date)} · {preview.trimester}. trimester
          </Text>
        </View>
      )}

      {error !== null && (
        <View
          accessibilityLiveRegion="polite"
          style={{ backgroundColor: palette.dangerSoft, borderRadius: radius.sm, padding: spacing.md }}
        >
          <Text style={{ ...type.small, color: palette.danger }}>{error}</Text>
        </View>
      )}

      <Button label="Devam et" onPress={submit} loading={busy} disabled={isoDate === null} />
    </Screen>
  );
}
