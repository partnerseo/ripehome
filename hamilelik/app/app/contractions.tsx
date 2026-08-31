import { router } from 'expo-router';
import { useEffect, useRef, useState } from 'react';
import { Text, View } from 'react-native';

import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { meetsFiveOneOne, type Contraction as Entry } from '@/lib/contractions';
import { enqueue, flush, newId, type SyncAlert } from '@/lib/queue';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

export default function Contractions() {
  const palette = usePalette();

  const [entries, setEntries] = useState<Entry[]>([]);
  const [current, setCurrent] = useState<Date | null>(null);
  const [tick, setTick] = useState(0);
  const [alerts, setAlerts] = useState<SyncAlert[]>([]);
  const uuid = useRef<string>(newId());
  const sessionStart = useRef<Date | null>(null);

  useEffect(() => {
    if (current === null) return;
    const timer = setInterval(() => setTick(Math.floor((Date.now() - current.getTime()) / 1000)), 1000);

    return () => clearInterval(timer);
  }, [current]);

  const alarm = meetsFiveOneOne(entries);

  async function persist(all: Entry[]) {
    if (sessionStart.current === null) return;

    await enqueue('contraction_sessions', {
      client_uuid: uuid.current,
      started_at: sessionStart.current.toISOString(),
      ended_at: null,
      contractions: all,
    } as { client_uuid: string });

    try {
      setAlerts(await flush());
    } catch {
      // Bağlantı yoksa kuyrukta bekler.
    }
  }

  function begin() {
    sessionStart.current ??= new Date();
    setCurrent(new Date());
    setTick(0);
  }

  function end() {
    if (current === null) return;

    const finished = new Date();
    const previous = entries[entries.length - 1];

    const entry: Entry = {
      started_at: current.toISOString(),
      ended_at: finished.toISOString(),
      duration_sec: Math.max(1, Math.round((finished.getTime() - current.getTime()) / 1000)),
      // Aralık, bir önceki kasılmanın başlangıcından bunun başlangıcına.
      interval_sec: previous
        ? Math.round((current.getTime() - new Date(previous.started_at).getTime()) / 1000)
        : null,
    };

    const next = [...entries, entry];
    setEntries(next);
    setCurrent(null);
    void persist(next);
  }

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>Sancı sayacı</Text>
        <Text style={{ ...type.title, color: palette.ink }}>Kasılmaları ölçün</Text>
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Kasılma başlayınca başlatın, bitince durdurun. Süre ve aralıklar kaydedilir.
        </Text>
      </View>

      <View style={{ alignItems: 'center', gap: spacing.sm }}>
        <Text style={{ fontSize: 52, fontWeight: '700', color: palette.ink, letterSpacing: -1.5 }}>
          {Math.floor(tick / 60)}:{String(tick % 60).padStart(2, '0')}
        </Text>
        <Button
          label={current === null ? 'Kasılma başladı' : 'Kasılma bitti'}
          onPress={current === null ? begin : end}
          style={{ minWidth: 220 }}
        />
      </View>

      {alarm && (
        <View
          accessibilityLiveRegion="assertive"
          style={{ backgroundColor: palette.dangerSoft, borderRadius: radius.md, padding: spacing.md, gap: 4 }}
        >
          <Text style={{ ...type.heading, color: palette.danger }}>Hastaneye başvurma zamanı</Text>
          <Text style={{ ...type.small, color: palette.danger, lineHeight: 21 }}>
            Kasılmalarınız bir saattir 5 dakikada bir geliyor ve yaklaşık 1 dakika sürüyor.
            Doktorunuzu arayın veya hastaneye gidin.
          </Text>
        </View>
      )}

      {alerts.map((alert) => (
        <View
          key={alert.reference + alert.type}
          style={{ backgroundColor: palette.dangerSoft, borderRadius: radius.md, padding: spacing.md }}
        >
          <Text style={{ ...type.small, color: palette.danger }}>{alert.detail}</Text>
        </View>
      ))}

      {entries.length > 0 && (
        <View style={{ gap: spacing.xs }}>
          <Text style={{ ...type.label, color: palette.faint }}>Kayıtlar ({entries.length})</Text>
          {[...entries].reverse().slice(0, 12).map((entry, index) => (
            <View
              key={entry.started_at}
              style={{
                flexDirection: 'row',
                justifyContent: 'space-between',
                backgroundColor: palette.surface,
                borderWidth: 1,
                borderColor: palette.line,
                borderRadius: radius.sm,
                paddingVertical: spacing.sm,
                paddingHorizontal: spacing.md,
              }}
            >
              <Text style={{ ...type.small, color: palette.muted }}>
                {new Date(entry.started_at).toLocaleTimeString('tr-TR', { hour: '2-digit', minute: '2-digit' })}
              </Text>
              <Text style={{ ...type.small, color: palette.ink }}>
                {entry.duration_sec} sn
                {entry.interval_sec !== null
                  ? ` · ${Math.round(entry.interval_sec / 60)} dk arayla`
                  : index === entries.length - 1
                    ? ' · ilk kasılma'
                    : ''}
              </Text>
            </View>
          ))}
        </View>
      )}

      <Button label="Ana ekrana dön" variant="ghost" onPress={() => router.replace('/home')} />
    </Screen>
  );
}
