import { router } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import { Pressable, Text, View } from 'react-native';

import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { enqueue, flush, newId, type SyncAlert } from '@/lib/queue';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

const TARGET = 10;
const LIMIT_MINUTES = 120;

export default function KickCounter() {
  const palette = usePalette();

  const [startedAt, setStartedAt] = useState<Date | null>(null);
  const [events, setEvents] = useState<Date[]>([]);
  const [elapsed, setElapsed] = useState(0);
  const [alerts, setAlerts] = useState<SyncAlert[]>([]);
  const uuid = useRef<string>(newId());

  const running = startedAt !== null && events.length < TARGET;

  useEffect(() => {
    if (!running) return;

    const timer = setInterval(() => {
      setElapsed(Math.floor((Date.now() - startedAt.getTime()) / 1000));
    }, 1000);

    return () => clearInterval(timer);
  }, [running, startedAt]);

  const minutes = Math.floor(elapsed / 60);
  const overLimit = minutes >= LIMIT_MINUTES;
  const done = events.length >= TARGET;

  /** Oturumu her dokunuşta yerele yazar; gönderim ayrı ve başarısız olabilir. */
  async function persist(all: Date[], finished: boolean) {
    if (startedAt === null) return;

    await enqueue('kick_sessions', {
      client_uuid: uuid.current,
      started_at: startedAt.toISOString(),
      ended_at: finished ? new Date().toISOString() : null,
      target_count: TARGET,
      events: all.map((d) => d.toISOString()),
    } as { client_uuid: string });

    if (finished) {
      try {
        setAlerts(await flush());
      } catch {
        // Bağlantı yoksa kayıt kuyrukta kalır ve sonra gönderilir.
      }
    }
  }

  function start() {
    uuid.current = newId();
    setStartedAt(new Date());
    setEvents([]);
    setElapsed(0);
    setAlerts([]);
  }

  function tap() {
    const next = [...events, new Date()];
    setEvents(next);
    void persist(next, next.length >= TARGET);
  }

  return (
    <Screen contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>Hareket sayacı</Text>
        <Text style={{ ...type.title, color: palette.ink }}>10 hareket sayın</Text>
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Bebeğinizin her hareketinde dokunun. 28. haftadan sonra günde bir kez önerilir.
        </Text>
      </View>

      {startedAt === null ? (
        <Button label="Saymaya başla" onPress={start} />
      ) : (
        <>
          <Pressable
            accessibilityRole="button"
            accessibilityLabel="Hareket kaydet"
            disabled={done}
            onPress={tap}
            style={({ pressed }) => ({
              alignSelf: 'center',
              width: 220,
              height: 220,
              borderRadius: 110,
              alignItems: 'center',
              justifyContent: 'center',
              backgroundColor: done ? palette.surfaceAlt : palette.accentSoft,
              borderWidth: 2,
              borderColor: done ? palette.line : palette.accent,
              opacity: pressed ? 0.85 : 1,
            })}
          >
            <Text style={{ fontSize: 64, fontWeight: '700', color: palette.accent, letterSpacing: -2 }}>
              {events.length}
            </Text>
            <Text style={{ ...type.small, color: palette.muted }}>/ {TARGET} hareket</Text>
          </Pressable>

          <Text style={{ ...type.small, color: palette.muted, textAlign: 'center' }}>
            {minutes} dakika {String(elapsed % 60).padStart(2, '0')} saniye
          </Text>

          {done && (
            <Notice tone="ok">
              10 hareket {minutes} dakikada tamamlandı. Kaydedildi.
            </Notice>
          )}

          {overLimit && !done && (
            <Notice tone="urgent">
              İki saat doldu ve 10 hareket sayılamadı. Lütfen doktorunuza başvurun.
            </Notice>
          )}

          {alerts.map((alert) => (
            <Notice key={alert.reference + alert.type} tone="urgent">
              {alert.detail}
            </Notice>
          ))}

          <Button label={done ? 'Yeni sayım başlat' : 'Sayımı sıfırla'} variant="ghost" onPress={start} />
        </>
      )}

      <Button label="Ana ekrana dön" variant="ghost" onPress={() => router.replace('/home')} />
    </Screen>
  );
}

function Notice({ tone, children }: { tone: 'ok' | 'urgent'; children: React.ReactNode }) {
  const palette = usePalette();
  const urgent = tone === 'urgent';

  return (
    <View
      accessibilityLiveRegion="polite"
      style={{
        backgroundColor: urgent ? palette.dangerSoft : palette.teal + '18',
        borderRadius: radius.md,
        padding: spacing.md,
      }}
    >
      <Text style={{ ...type.small, color: urgent ? palette.danger : palette.teal, lineHeight: 21 }}>
        {children}
      </Text>
    </View>
  );
}
