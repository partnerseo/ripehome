import { useQuery } from '@tanstack/react-query';
import { router, useLocalSearchParams } from 'expo-router';
import { ActivityIndicator, Text, View } from 'react-native';

import { weekContent } from '@/api/content';
import { currentPregnancy } from '@/api/pregnancy';
import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { formatDate, trimesterLabel } from '@/lib/format';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

export default function WeekDetail() {
  const palette = usePalette();
  const params = useLocalSearchParams<{ week: string }>();
  const week = Number(params.week);

  const { data: pregnancy } = useQuery({
    queryKey: ['pregnancy', 'current'],
    queryFn: currentPregnancy,
  });

  const { data: content, isPending } = useQuery({
    queryKey: ['week', week],
    queryFn: () => weekContent(week),
    enabled: Number.isInteger(week),
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

      {isPending && <ActivityIndicator color={palette.accent} />}

      {!isPending && content === null && (
        <Card>
          <Text style={{ ...type.heading, color: palette.ink }}>Bu haftanın içeriği henüz hazır değil</Text>
          <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
            İçerik, bir kadın doğum uzmanı tarafından gözden geçirildikten sonra yayınlanıyor.
            Hafta hesabınız ve takviminiz çalışmaya devam ediyor.
          </Text>
        </Card>
      )}

      {content !== null && content !== undefined && (
        <>
          {content.baby_size_label !== null && (
            <Card>
              <Text style={{ ...type.label, color: palette.faint }}>Bu hafta bebeğiniz</Text>
              <Text style={{ ...type.title, color: palette.accent }}>{content.baby_size_label}</Text>
              <Text style={{ ...type.small, color: palette.muted }}>
                {[
                  content.baby_length_mm !== null ? `${(content.baby_length_mm / 10).toFixed(1)} cm` : null,
                  content.baby_weight_g !== null ? `${content.baby_weight_g} g` : null,
                ]
                  .filter(Boolean)
                  .join(' · ')}
              </Text>
            </Card>
          )}

          {content.baby_body !== null && <Section title="Bebekte neler oluyor" body={content.baby_body} />}
          {content.mother_body !== null && <Section title="Sizde neler oluyor" body={content.mother_body} />}
          {content.tips_body !== null && <Section title="Bu hafta ipuçları" body={content.tips_body} />}

          {content.review.reviewed_by !== null && (
            <View style={{ gap: 4, paddingTop: spacing.xs }}>
              <Text style={{ ...type.small, color: palette.faint }}>
                Tıbbi gözden geçirme: {content.review.reviewed_by}
                {content.review.reviewed_at !== null ? ` · ${formatDate(content.review.reviewed_at)}` : ''}
              </Text>
              {content.sources.length > 0 && (
                <Text style={{ ...type.small, color: palette.faint }}>
                  Kaynaklar: {content.sources.map((s) => s.label).join(', ')}
                </Text>
              )}
            </View>
          )}
        </>
      )}

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

function Card({ children }: { children: React.ReactNode }) {
  const palette = usePalette();

  return (
    <View
      style={{
        backgroundColor: palette.surface,
        borderWidth: 1,
        borderColor: palette.line,
        borderRadius: radius.md,
        padding: spacing.md,
        gap: spacing.xs,
      }}
    >
      {children}
    </View>
  );
}

function Section({ title, body }: { title: string; body: string }) {
  const palette = usePalette();

  return (
    <View style={{ gap: spacing.xs }}>
      <Text style={{ ...type.heading, color: palette.ink }}>{title}</Text>
      <Text style={{ ...type.body, color: palette.inkSoft, lineHeight: 24 }}>{body}</Text>
    </View>
  );
}
