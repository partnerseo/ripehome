import { useQuery } from '@tanstack/react-query';
import { Redirect } from 'expo-router';
import { ActivityIndicator, View } from 'react-native';

import { me } from '@/api/auth';
import { currentPregnancy } from '@/api/pregnancy';
import { ApiError } from '@/api/client';
import { session } from '@/lib/session';
import { usePalette } from '@/theme/tokens';

/**
 * Açılış kapısı: jeton var mı, aktif gebelik var mı, kullanıcı nereye gitmeli.
 */
export default function Index() {
  const palette = usePalette();

  const { data, isPending, isError } = useQuery({
    queryKey: ['bootstrap'],
    retry: false,
    queryFn: async () => {
      const token = await session.get();

      if (!token) {
        return { authenticated: false, hasPregnancy: false };
      }

      try {
        await me();
      } catch (error) {
        // Jeton iptal edilmiş veya süresi dolmuş: temizle, girişe gönder.
        if (error instanceof ApiError && error.status === 401) {
          await session.clear();

          return { authenticated: false, hasPregnancy: false };
        }

        throw error;
      }

      return { authenticated: true, hasPregnancy: (await currentPregnancy()) !== null };
    },
  });

  if (isPending) {
    return (
      <View style={{ flex: 1, alignItems: 'center', justifyContent: 'center', backgroundColor: palette.bg }}>
        <ActivityIndicator color={palette.accent} />
      </View>
    );
  }

  // Sunucuya ulaşılamıyorsa girişte bekletmek yerine hatayı orada göstermek daha
  // anlaşılır: giriş ekranı yeniden denemeyi zaten sunuyor.
  if (isError || !data?.authenticated) {
    return <Redirect href="/sign-in" />;
  }

  return <Redirect href={data.hasPregnancy ? '/home' : '/onboarding'} />;
}
