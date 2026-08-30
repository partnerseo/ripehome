import { QueryClient, QueryClientProvider } from '@tanstack/react-query';
import { Stack } from 'expo-router';
import { StatusBar } from 'expo-status-bar';
import { useState } from 'react';
import { SafeAreaProvider } from 'react-native-safe-area-context';

import { usePalette } from '@/theme/tokens';

export default function RootLayout() {
  const palette = usePalette();
  const [client] = useState(() => new QueryClient({
    defaultOptions: {
      queries: {
        retry: 1,
        // Hafta yalnızca gün değişince değişir; her odaklanmada yeniden çekmeye gerek yok.
        staleTime: 5 * 60 * 1000,
      },
    },
  }));

  return (
    <QueryClientProvider client={client}>
      <SafeAreaProvider>
        <StatusBar style="auto" />
        <Stack
          screenOptions={{
            headerShown: false,
            contentStyle: { backgroundColor: palette.bg },
            animation: 'fade',
          }}
        />
      </SafeAreaProvider>
    </QueryClientProvider>
  );
}
