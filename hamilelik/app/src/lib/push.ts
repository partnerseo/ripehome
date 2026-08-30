import Constants from 'expo-constants';
import * as Device from 'expo-device';
import * as Notifications from 'expo-notifications';
import { Platform } from 'react-native';

import { registerDevice } from '@/api/appointments';

/**
 * Bildirim izni ister ve jetonu sunucuya kaydeder.
 *
 * Sessizce başarısız olur: bildirim izni verilmemesi uygulamayı kullanmaya
 * engel değil. Kullanıcı izni reddettiyse hatırlatmalar gelmez, gerisi çalışır.
 *
 * @returns Kayıt yapıldıysa true.
 */
export async function registerForPushNotifications(): Promise<boolean> {
  // Gerçek cihaz gerekiyor: emülatör ve tarayıcı jeton üretmez.
  if (!Device.isDevice || Platform.OS === 'web') {
    return false;
  }

  try {
    const existing = await Notifications.getPermissionsAsync();
    const granted =
      existing.granted || (await Notifications.requestPermissionsAsync()).granted;

    if (!granted) {
      return false;
    }

    const projectId =
      Constants.expoConfig?.extra?.eas?.projectId ?? Constants.easConfig?.projectId;

    const { data: token } = await Notifications.getExpoPushTokenAsync(
      projectId ? { projectId } : undefined,
    );

    await registerDevice(token, Platform.OS);

    return true;
  } catch {
    return false;
  }
}
