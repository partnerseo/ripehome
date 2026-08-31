import { router } from 'expo-router';
import { useState } from 'react';
import { Pressable, Text, TextInput, View } from 'react-native';

import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { enqueue, flush, newId } from '@/lib/queue';
import { COMMON_SYMPTOMS, RED_FLAGS, urgentAmong } from '@/lib/redFlags';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

const MOODS = ['Çok kötü', 'Kötü', 'İdare eder', 'İyi', 'Çok iyi'];

export default function Symptoms() {
  const palette = usePalette();

  const [selected, setSelected] = useState<string[]>([]);
  const [mood, setMood] = useState<number | null>(null);
  const [note, setNote] = useState('');
  const [busy, setBusy] = useState(false);

  const urgent = urgentAmong(selected);

  function toggle(code: string) {
    setSelected((current) =>
      current.includes(code) ? current.filter((c) => c !== code) : [...current, code],
    );
  }

  async function save() {
    setBusy(true);

    try {
      await enqueue('symptom_logs', {
        client_uuid: newId(),
        logged_on: new Date().toISOString().slice(0, 10),
        symptoms: selected,
        mood,
        note: note.trim() === '' ? null : note.trim(),
      } as { client_uuid: string });

      try {
        await flush();
      } catch {
        // Bağlantı yoksa kuyrukta bekler; kayıt kaybolmaz.
      }

      // Acil belirti seçildiyse yönlendirme kaydetmeye bağlı değil: kayıt
      // gitmese bile kullanıcı doğru ekranı görmeli.
      const first = RED_FLAGS.find((flag) => flag.code === urgent[0]);
      router.replace(
        urgent.length > 0
          ? { pathname: '/acil', params: { reason: first?.title ?? '' } }
          : '/home',
      );
    } finally {
      setBusy(false);
    }
  }

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>Günlük</Text>
        <Text style={{ ...type.title, color: palette.ink }}>Bugün nasılsınız?</Text>
      </View>

      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.heading, color: palette.ink }}>Ruh hâliniz</Text>
        <View style={{ flexDirection: 'row', gap: spacing.xs }}>
          {MOODS.map((label, index) => {
            const value = index + 1;
            const active = mood === value;

            return (
              <Pressable
                key={label}
                accessibilityRole="radio"
                accessibilityState={{ selected: active }}
                accessibilityLabel={label}
                onPress={() => setMood(value)}
                style={{
                  flex: 1,
                  paddingVertical: spacing.sm,
                  borderRadius: radius.sm,
                  borderWidth: active ? 2 : 1,
                  borderColor: active ? palette.accent : palette.line,
                  backgroundColor: active ? palette.accentSoft : palette.surface,
                  alignItems: 'center',
                }}
              >
                <Text style={{ fontSize: 12, color: active ? palette.accent : palette.muted, textAlign: 'center' }}>
                  {label}
                </Text>
              </Pressable>
            );
          })}
        </View>
      </View>

      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.heading, color: palette.ink }}>Belirtiler</Text>
        <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs }}>
          {COMMON_SYMPTOMS.map((symptom) => (
            <Chip
              key={symptom.code}
              label={symptom.title}
              active={selected.includes(symptom.code)}
              onPress={() => toggle(symptom.code)}
            />
          ))}
        </View>
      </View>

      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.heading, color: palette.ink }}>Bunlardan biri var mı?</Text>
        <Text style={{ ...type.small, color: palette.muted }}>
          Bu belirtiler acil değerlendirme gerektirebilir.
        </Text>
        <View style={{ flexDirection: 'row', flexWrap: 'wrap', gap: spacing.xs }}>
          {RED_FLAGS.map((flag) => (
            <Chip
              key={flag.code}
              label={flag.title}
              active={selected.includes(flag.code)}
              urgent
              onPress={() => toggle(flag.code)}
            />
          ))}
        </View>
      </View>

      {urgent.length > 0 && (
        <View
          accessibilityLiveRegion="assertive"
          style={{ backgroundColor: palette.dangerSoft, borderRadius: radius.md, padding: spacing.md, gap: 4 }}
        >
          <Text style={{ ...type.heading, color: palette.danger }}>Bu belirti beklemez</Text>
          <Text style={{ ...type.small, color: palette.danger, lineHeight: 21 }}>
            Kaydettikten sonra sizi başvuru ekranına yönlendireceğiz.
          </Text>
        </View>
      )}

      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.heading, color: palette.ink }}>Not</Text>
        <TextInput
          accessibilityLabel="Not"
          multiline
          numberOfLines={3}
          onChangeText={setNote}
          placeholder="Eklemek istediğiniz bir şey var mı?"
          placeholderTextColor={palette.faint}
          style={{
            backgroundColor: palette.surface,
            borderWidth: 1,
            borderColor: palette.line,
            borderRadius: radius.md,
            padding: spacing.md,
            color: palette.ink,
            minHeight: 90,
            textAlignVertical: 'top',
          }}
          value={note}
        />
      </View>

      <Button label="Kaydet" onPress={save} loading={busy} />
      <Button label="Vazgeç" variant="ghost" onPress={() => router.replace('/home')} />
    </Screen>
  );
}

function Chip({
  label,
  active,
  urgent,
  onPress,
}: {
  label: string;
  active: boolean;
  urgent?: boolean;
  onPress: () => void;
}) {
  const palette = usePalette();
  const accent = urgent === true ? palette.danger : palette.accent;
  const soft = urgent === true ? palette.dangerSoft : palette.accentSoft;

  return (
    <Pressable
      accessibilityRole="checkbox"
      accessibilityState={{ checked: active }}
      onPress={onPress}
      style={{
        paddingVertical: spacing.sm,
        paddingHorizontal: spacing.md,
        borderRadius: radius.pill,
        borderWidth: active ? 2 : 1,
        borderColor: active ? accent : palette.line,
        backgroundColor: active ? soft : palette.surface,
      }}
    >
      <Text style={{ ...type.small, color: active ? accent : palette.ink, fontWeight: active ? '600' : '400' }}>
        {label}
      </Text>
    </Pressable>
  );
}
