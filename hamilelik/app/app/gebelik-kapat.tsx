import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { Pressable, Text, View } from 'react-native';

import { currentPregnancy, endPregnancy, type EndReason } from '@/api/pregnancy';
import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

/**
 * Gebelik kaydını kapatma.
 *
 * Uygulamanın en çok özen isteyen ekranı. Kurallar:
 * - Sebep sormak zorunlu değil; seçmeden de kapatılabilir
 * - Dil yargısız ve kısa: burada "emin misiniz?" tonuna yer yok
 * - Kapandığı an tüm hatırlatmalar ve haftalık bildirimler susar
 * - Veriler silinmez, arşivlenir; kullanıcı isterse ayrıca siler
 */
const REASONS: { value: EndReason; label: string; hint: string }[] = [
  { value: 'birth', label: 'Bebeğim doğdu', hint: 'Tebrikler.' },
  { value: 'loss', label: 'Gebeliğimi kaybettim', hint: 'Başınız sağ olsun.' },
  { value: 'other', label: 'Belirtmek istemiyorum', hint: 'Sebep sormuyoruz.' },
];

export default function EndPregnancy() {
  const palette = usePalette();
  const queryClient = useQueryClient();

  const [reason, setReason] = useState<EndReason | null>(null);
  const { data: pregnancy } = useQuery({ queryKey: ['pregnancy', 'current'], queryFn: currentPregnancy });

  const close = useMutation({
    mutationFn: () => endPregnancy(pregnancy!.id, reason ?? undefined),
    onSuccess: async () => {
      await queryClient.invalidateQueries();
      router.replace(reason === 'loss' ? '/kapandi' : '/onboarding');
    },
  });

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.title, color: palette.ink }}>Gebelik kaydını kapat</Text>
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 22 }}>
          Kaydı kapattığınızda haftalık bildirimler ve randevu hatırlatmaları hemen
          durur. Kayıtlarınız silinmez; dilediğiniz zaman indirebilir veya kalıcı
          olarak silebilirsiniz.
        </Text>
      </View>

      <View style={{ gap: spacing.sm }}>
        {REASONS.map((option) => {
          const selected = reason === option.value;

          return (
            <Pressable
              key={option.value}
              accessibilityRole="radio"
              accessibilityState={{ selected }}
              onPress={() => setReason(option.value)}
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

      <Button
        label="Kaydı kapat"
        onPress={() => close.mutate()}
        loading={close.isPending}
        disabled={pregnancy === null || pregnancy === undefined}
      />
      <Button label="Vazgeç" variant="ghost" onPress={() => router.back()} />
    </Screen>
  );
}
