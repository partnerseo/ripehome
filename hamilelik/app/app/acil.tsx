import { router, useLocalSearchParams } from 'expo-router';
import { Linking, Pressable, Text, View } from 'react-native';

import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { RED_FLAGS } from '@/lib/redFlags';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

/**
 * Acil yönlendirme ekranı.
 *
 * Teşhis koymaz, ne olduğunu söylemez. Tek işi: başvurun demek ve aramayı
 * bir dokunuş uzağa getirmek. Erteleme düğmesi yok.
 */
export default function Emergency() {
  const palette = usePalette();
  const { reason } = useLocalSearchParams<{ reason?: string }>();

  const call = (number: string) => {
    void Linking.openURL(`tel:${number}`);
  };

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View
        style={{
          backgroundColor: palette.dangerSoft,
          borderRadius: radius.md,
          padding: spacing.lg,
          gap: spacing.sm,
        }}
      >
        <Text style={{ ...type.title, color: palette.danger }}>Hemen başvurun</Text>
        <Text style={{ ...type.body, color: palette.danger, lineHeight: 24 }}>
          Bu belirti acil değerlendirme gerektirebilir. Lütfen hemen doktorunuza
          veya en yakın acil servise başvurun.
        </Text>
        {reason !== undefined && reason !== '' && (
          <Text style={{ ...type.small, color: palette.danger }}>Bildirilen: {reason}</Text>
        )}
      </View>

      <View style={{ gap: spacing.sm }}>
        <Pressable
          accessibilityRole="button"
          onPress={() => call('112')}
          style={({ pressed }) => ({
            backgroundColor: palette.danger,
            borderRadius: radius.md,
            paddingVertical: spacing.lg,
            alignItems: 'center',
            opacity: pressed ? 0.85 : 1,
          })}
        >
          <Text style={{ fontSize: 20, fontWeight: '700', color: '#fff' }}>112 Acil'i ara</Text>
        </Pressable>

        <Text style={{ ...type.small, color: palette.muted, textAlign: 'center' }}>
          Doktorunuzun numarasını biliyorsanız önce onu arayabilirsiniz.
        </Text>
      </View>

      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.label, color: palette.faint }}>Acil değerlendirme gerektiren belirtiler</Text>
        {RED_FLAGS.map((flag) => (
          <View
            key={flag.code}
            style={{
              backgroundColor: palette.surface,
              borderWidth: 1,
              borderColor: palette.line,
              borderRadius: radius.sm,
              padding: spacing.md,
            }}
          >
            <Text style={{ ...type.heading, color: palette.ink }}>{flag.title}</Text>
            {flag.detail !== '' && (
              <Text style={{ ...type.small, color: palette.muted, marginTop: 2 }}>{flag.detail}</Text>
            )}
          </View>
        ))}
      </View>

      <Text style={{ ...type.small, color: palette.faint, lineHeight: 20 }}>
        Bu uygulama teşhis koymaz ve tedavi önermez. Belirtilerinizi
        değerlendirebilecek tek kişi sizi muayene eden hekimdir.
      </Text>

      <Button label="Geri dön" variant="ghost" onPress={() => router.back()} />
    </Screen>
  );
}
