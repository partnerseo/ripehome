import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { ActivityIndicator, Pressable, Text, View } from 'react-native';

import { appointments, completeAppointment, type Appointment } from '@/api/appointments';
import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { formatDate } from '@/lib/format';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

const CATEGORY_LABELS: Record<Appointment['category'], string> = {
  usg: 'Ultrason',
  lab: 'Laboratuvar',
  vaccine: 'Aşı',
  visit: 'Muayene',
};

export default function CalendarScreen() {
  const palette = usePalette();
  const queryClient = useQueryClient();

  const { data, isPending, isError, refetch } = useQuery({
    queryKey: ['appointments'],
    queryFn: appointments,
  });

  const complete = useMutation({
    mutationFn: completeAppointment,
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['appointments'] }),
  });

  const upcoming = data?.filter((a) => a.completed_at === null) ?? [];
  const done = data?.filter((a) => a.completed_at !== null) ?? [];

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>Takvim</Text>
        <Text style={{ ...type.title, color: palette.ink }}>Tetkikler ve randevular</Text>
      </View>

      {isPending && <ActivityIndicator color={palette.accent} />}

      {isError && (
        <View style={{ gap: spacing.md }}>
          <Text style={{ ...type.small, color: palette.muted }}>
            Takvim yüklenemedi. Bağlantınızı kontrol edip tekrar deneyin.
          </Text>
          <Button label="Tekrar dene" onPress={() => refetch()} />
        </View>
      )}

      {!isPending && !isError && upcoming.length === 0 && done.length === 0 && (
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
          <Text style={{ ...type.heading, color: palette.ink }}>Takvim henüz hazır değil</Text>
          <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
            Tetkik takvimi, bir kadın doğum uzmanı tarafından gözden geçirildikten sonra
            burada görünecek.
          </Text>
        </View>
      )}

      {upcoming.length > 0 && (
        <View style={{ gap: spacing.sm }}>
          {upcoming.map((appointment) => (
            <AppointmentCard
              key={appointment.id}
              appointment={appointment}
              onComplete={() => complete.mutate(appointment.id)}
              busy={complete.isPending && complete.variables === appointment.id}
            />
          ))}
        </View>
      )}

      {done.length > 0 && (
        <View style={{ gap: spacing.sm }}>
          <Text style={{ ...type.label, color: palette.faint }}>Tamamlananlar</Text>
          {done.map((appointment) => (
            <AppointmentCard key={appointment.id} appointment={appointment} />
          ))}
        </View>
      )}

      <Button label="Ana ekrana dön" variant="ghost" onPress={() => router.replace('/home')} />
    </Screen>
  );
}

function AppointmentCard({
  appointment,
  onComplete,
  busy,
}: {
  appointment: Appointment;
  onComplete?: () => void;
  busy?: boolean;
}) {
  const palette = usePalette();
  const isDone = appointment.completed_at !== null;

  const timing =
    appointment.scheduled_at !== null
      ? formatDate(appointment.scheduled_at.slice(0, 10))
      : appointment.window.start_on !== null && appointment.window.end_on !== null
        ? `${formatDate(appointment.window.start_on)} – ${formatDate(appointment.window.end_on)}`
        : null;

  const weeks =
    appointment.window.start_week !== null && appointment.window.end_week !== null
      ? appointment.window.start_week === appointment.window.end_week
        ? `${appointment.window.start_week}. hafta`
        : `${appointment.window.start_week}–${appointment.window.end_week}. hafta`
      : null;

  return (
    <View
      style={{
        backgroundColor: palette.surface,
        borderWidth: 1,
        borderColor: palette.line,
        borderRadius: radius.md,
        padding: spacing.md,
        gap: spacing.xs,
        opacity: isDone ? 0.6 : 1,
      }}
    >
      <View style={{ flexDirection: 'row', justifyContent: 'space-between', alignItems: 'center' }}>
        <Text style={{ ...type.label, color: palette.faint }}>
          {CATEGORY_LABELS[appointment.category]}
          {appointment.is_optional ? ' · tercihe bağlı' : ''}
        </Text>
        {weeks !== null && <Text style={{ ...type.small, color: palette.faint }}>{weeks}</Text>}
      </View>

      <Text style={{ ...type.heading, color: palette.ink }}>{appointment.title}</Text>

      {timing !== null && (
        <Text style={{ ...type.small, color: appointment.scheduled_at !== null ? palette.accent : palette.muted }}>
          {appointment.scheduled_at !== null ? `Randevu: ${timing}` : timing}
        </Text>
      )}

      {appointment.description !== null && (
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 20 }}>
          {appointment.description}
        </Text>
      )}

      {!isDone && onComplete !== undefined && (
        <Pressable
          accessibilityRole="button"
          disabled={busy}
          onPress={onComplete}
          style={{ alignSelf: 'flex-start', paddingVertical: spacing.xs }}
        >
          <Text style={{ ...type.small, color: palette.teal, fontWeight: '600' }}>
            {busy ? 'Kaydediliyor…' : 'Yapıldı olarak işaretle'}
          </Text>
        </Pressable>
      )}
    </View>
  );
}
