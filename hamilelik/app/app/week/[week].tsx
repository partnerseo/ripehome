import { useQuery } from '@tanstack/react-query';
import { router, useLocalSearchParams } from 'expo-router';
import { Text, View } from 'react-native';

import { currentPregnancy } from '@/api/pregnancy';
import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { trimesterLabel } from '@/lib/format';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

export default function WeekDetail() {
  const palette = usePalette();
  const params = useLocalSearchParams<{ week: string }>();
  const week = Number(params.week);

  const { data: pregnancy } = useQuery({
    queryKey: ['pregnancy', 'current'],
    queryFn: currentPregnancy,
  });

  const ga = pregnancy?.gestational_age;
  const trimester = week < 14 ? 1 : week < 28 ? 2 : 3;

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>{trimesterLabel(trimester)}</Text>
        <Text style={{ ...type.display, color: palette.ink }}>{week}. hafta</Text>
        {ga?.week === week && (
          <Text style={{ ...type.small, color: palette.accent }}>Şu an buradasınız — {ga.display}</Text>
        )}
      </View>

      <View
        style={{
          backgroundColor: palette.surface,
          borderWidth: 1,
          borderColor: palette.line,
          borderRadius: radius.md,
          padding: spacing.md,
          gap: spacing.sm,
        }}
      >
        <Text style={{ ...type.heading, color: palette.ink }}>Hafta içeriği henüz bağlı değil</Text>
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Bebeğin gelişimi, annede olan değişiklikler ve o haftaya özel ipuçları yönetim
          panelinden girilecek ve buraya gelecek. Şu an yalnızca hafta hesabı çalışıyor.
        </Text>
      </View>

      <View style={{ flexDirection: 'row', gap: spacing.sm }}>
        <Button
          label="Önceki hafta"
          variant="ghost"
          disabled={week <= 1}
          onPress={() => router.replace(`/week/${week - 1}`)}
          style={{ flex: 1 }}
        />
        <Button
          label="Sonraki hafta"
          variant="ghost"
          disabled={week >= 42}
          onPress={() => router.replace(`/week/${week + 1}`)}
          style={{ flex: 1 }}
        />
      </View>

      <Button label="Ana ekrana dön" onPress={() => router.replace('/home')} />
    </Screen>
  );
}
