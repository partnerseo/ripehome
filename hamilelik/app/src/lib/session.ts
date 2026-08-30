import * as SecureStore from 'expo-secure-store';
import { Platform } from 'react-native';

const TOKEN_KEY = 'hamilelik.token';

/**
 * Erişim jetonunun saklandığı yer.
 *
 * expo-secure-store web'de yok; orada localStorage'a düşüyoruz. Web yalnızca
 * geliştirme ve önizleme hedefi — sağlık verisi taşıyan üretim istemcisi
 * cihazın güvenli deposunu kullanır.
 */
export const session = {
  async get(): Promise<string | null> {
    if (Platform.OS === 'web') {
      try {
        return globalThis.localStorage?.getItem(TOKEN_KEY) ?? null;
      } catch {
        return null;
      }
    }

    return SecureStore.getItemAsync(TOKEN_KEY);
  },

  async set(token: string): Promise<void> {
    if (Platform.OS === 'web') {
      try {
        globalThis.localStorage?.setItem(TOKEN_KEY, token);
      } catch {
        // Gizli sekmede yazma engellenebilir; oturum o sekmeyle sınırlı kalır.
      }

      return;
    }

    await SecureStore.setItemAsync(TOKEN_KEY, token);
  },

  async clear(): Promise<void> {
    if (Platform.OS === 'web') {
      try {
        globalThis.localStorage?.removeItem(TOKEN_KEY);
      } catch {
        // yok sayılır
      }

      return;
    }

    await SecureStore.deleteItemAsync(TOKEN_KEY);
  },
};
