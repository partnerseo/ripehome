import { router } from 'expo-router';
import { Text, View } from 'react-native';

import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

/**
 * Kayıp sonrası ekran.
 *
 * Burada hafta, geri sayım, bebek boyutu ya da "yeni gebelik başlatın" çağrısı
 * yok. Kullanıcı hazır olduğunda kendisi başlatır.
 */
export default function Closed() {
  const palette = usePalette();

  return (
    <Screen contentStyle={{ justifyContent: 'center', gap: spacing.lg }}>
      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.title, color: palette.ink }}>Başınız sağ olsun</Text>
        <Text style={{ ...type.body, color: palette.inkSoft, lineHeight: 25 }}>
          Kaydınızı kapattık. Bundan sonra size hiçbir hatırlatma veya haftalık
          bildirim göndermeyeceğiz.
        </Text>
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 22 }}>
          Kayıtlarınız duruyor. İstediğiniz zaman indirebilir ya da hesabınızla
          birlikte kalıcı olarak silebilirsiniz.
        </Text>
      </View>

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
        <Text style={{ ...type.heading, color: palette.ink }}>Yalnız değilsiniz</Text>
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Kayıp sonrası destek almak isterseniz doktorunuz sizi bir uzmana
          yönlendirebilir. Konuşmak iyi gelir.
        </Text>
      </View>

      <Button label="Verilerim ve hesabım" variant="ghost" onPress={() => router.replace('/profil')} />
    </Screen>
  );
}
