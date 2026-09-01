import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { ActivityIndicator, Pressable, Text, TextInput, View } from 'react-native';

import { addChecklistItem, checklist, toggleChecklistItem, type ChecklistItem } from '@/api/extras';
import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

const GROUPS: { key: ChecklistItem['group']; label: string }[] = [
  { key: 'belgeler', label: 'Belgeler' },
  { key: 'anne', label: 'Anne için' },
  { key: 'bebek', label: 'Bebek için' },
];

export default function HospitalBag() {
  const palette = usePalette();
  const queryClient = useQueryClient();
  const [title, setTitle] = useState('');

  const { data: items, isPending } = useQuery({ queryKey: ['checklist'], queryFn: checklist });

  const invalidate = () => queryClient.invalidateQueries({ queryKey: ['checklist'] });

  const toggle = useMutation({
    mutationFn: ({ id, done }: { id: number; done: boolean }) => toggleChecklistItem(id, done),
    onSuccess: invalidate,
  });

  const add = useMutation({
    mutationFn: () => addChecklistItem(title.trim(), 'anne'),
    onSuccess: () => {
      setTitle('');
      void invalidate();
    },
  });

  const done = items?.filter((i) => i.is_done).length ?? 0;

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>Hazırlık</Text>
        <Text style={{ ...type.title, color: palette.ink }}>Hastane çantası</Text>
        {items !== undefined && (
          <Text style={{ ...type.small, color: palette.muted }}>
            {done} / {items.length} hazır
          </Text>
        )}
      </View>

      {isPending && <ActivityIndicator color={palette.accent} />}

      {GROUPS.map((group) => {
        const groupItems = items?.filter((i) => i.group === group.key) ?? [];

        if (groupItems.length === 0) return null;

        return (
          <View key={group.key} style={{ gap: spacing.xs }}>
            <Text style={{ ...type.label, color: palette.faint }}>{group.label}</Text>
            {groupItems.map((item) => (
              <Pressable
                key={item.id}
                accessibilityRole="checkbox"
                accessibilityState={{ checked: item.is_done }}
                onPress={() => toggle.mutate({ id: item.id, done: !item.is_done })}
                style={{
                  flexDirection: 'row',
                  alignItems: 'center',
                  gap: spacing.md,
                  backgroundColor: palette.surface,
                  borderWidth: 1,
                  borderColor: item.is_done ? palette.teal : palette.line,
                  borderRadius: radius.md,
                  paddingVertical: spacing.md,
                  paddingHorizontal: spacing.md,
                }}
              >
                <View
                  style={{
                    width: 22,
                    height: 22,
                    borderRadius: 6,
                    borderWidth: 2,
                    borderColor: item.is_done ? palette.teal : palette.line,
                    backgroundColor: item.is_done ? palette.teal : 'transparent',
                    alignItems: 'center',
                    justifyContent: 'center',
                  }}
                >
                  {item.is_done && <Text style={{ color: palette.surface, fontWeight: '700' }}>✓</Text>}
                </View>
                <Text
                  style={{
                    ...type.body,
                    flex: 1,
                    color: item.is_done ? palette.faint : palette.ink,
                    textDecorationLine: item.is_done ? 'line-through' : 'none',
                  }}
                >
                  {item.title}
                </Text>
              </Pressable>
            ))}
          </View>
        );
      })}

      <View style={{ gap: spacing.sm }}>
        <Text style={{ ...type.label, color: palette.faint }}>Kendi maddenizi ekleyin</Text>
        <TextInput
          accessibilityLabel="Yeni madde"
          onChangeText={setTitle}
          placeholder="Örneğin: yastık"
          placeholderTextColor={palette.faint}
          style={{
            backgroundColor: palette.surface,
            borderWidth: 1,
            borderColor: palette.line,
            borderRadius: radius.md,
            padding: spacing.md,
            color: palette.ink,
          }}
          value={title}
        />
        <Button
          label="Ekle"
          variant="ghost"
          disabled={title.trim() === ''}
          loading={add.isPending}
          onPress={() => add.mutate()}
        />
      </View>

      <Button label="Ana ekrana dön" variant="ghost" onPress={() => router.replace('/home')} />
    </Screen>
  );
}
