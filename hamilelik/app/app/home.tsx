import { useQuery } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useEffect } from 'react';
import { ActivityIndicator, Text, View } from 'react-native';

import { appointments } from '@/api/appointments';
import { currentPregnancy } from '@/api/pregnancy';
import { registerForPushNotifications } from '@/lib/push';
import { Button } from '@/components/Button';
import { ProgressRing } from '@/components/ProgressRing';
import { Screen } from '@/components/Screen';
import { formatDate, formatDaysLeft, trimesterLabel } from '@/lib/format';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

export default function Home() {
  const palette = usePalette();
  const { data: pregnancy, isPending, isError, refetch } = useQuery({
    queryKey: ['pregnancy', 'current'],
    queryFn: currentPregnancy,
  });

  const { data: schedule } = useQuery({
    queryKey: ['appointments'],
    queryFn: appointments,
  });

  // Bildirim izni ana ekranda istenir, giriş anında değil: kullanıcı önce
  // uygulamanın ne işe yaradığını görsün, sonra izin sorusuyla karşılaşsın.
  useEffect(() => {
    void registerForPushNotifications();
  }, []);

  if (isPending) {
    return (
      <Screen contentStyle={{ alignItems: 'center', justifyContent: 'center' }}>
        <ActivityIndicator color={palette.accent} />
      </Screen>
    );
  }

  if (isError) {
    return (
      <Screen contentStyle={{ justifyContent: 'center', gap: spacing.md }}>
        <Text style={{ ...type.title, color: palette.ink }}>Bağlantı kurulamadı</Text>
        <Text style={{ ...type.small, color: palette.muted }}>
          Kayıtlarınız güvende. İnternet bağlantınızı kontrol edip tekrar deneyin.
        </Text>
        <Button label="Tekrar dene" onPress={() => refetch()} />
      </Screen>
    );
  }

  if (pregnancy === null) {
    return (
      <Screen contentStyle={{ justifyContent: 'center', gap: spacing.md }}>
        <Text style={{ ...type.title, color: palette.ink }}>Aktif gebelik kaydı yok</Text>
        <Button label="Kurulumu başlat" onPress={() => router.replace('/onboarding')} />
      </Screen>
    );
  }

  const ga = pregnancy.gestational_age;
  const nextAppointment = schedule?.find((a) => a.completed_at === null);

  // Sunucu kapanmış gebelikte bu alanı hiç göndermez.
  if (ga === undefined) {
    return (
      <Screen contentStyle={{ justifyContent: 'center', gap: spacing.md }}>
        <Text style={{ ...type.title, color: palette.ink }}>Bu gebelik kaydı kapatılmış</Text>
        <Button label="Yeni kayıt oluştur" onPress={() => router.replace('/onboarding')} />
      </Screen>
    );
  }

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>Bugün</Text>
        <Text style={{ ...type.heading, color: palette.muted }}>{trimesterLabel(ga.trimester)}</Text>
      </View>

      <View style={{ alignItems: 'center', paddingVertical: spacing.md }}>
        <ProgressRing progress={ga.progress}>
          <View style={{ alignItems: 'center' }}>
            <Text style={{ fontSize: 52, fontWeight: '700', color: palette.ink, letterSpacing: -1.5 }}>
              {ga.week}
            </Text>
            <Text style={{ ...type.small, color: palette.muted }}>hafta {ga.day} gün</Text>
          </View>
        </ProgressRing>
      </View>

      <View style={{ gap: spacing.sm }}>
        <Row label="Tahmini doğum" value={formatDate(ga.due_date)} />
        <Row
          label="Geri sayım"
          value={formatDaysLeft(ga.days_left)}
          emphasis={ga.is_overdue}
        />
        <Row label="Gebelik günü" value={`${ga.ga_days} gün`} />
      </View>

      {ga.needs_completion_prompt && (
        <View
          style={{
            backgroundColor: palette.surface,
            borderWidth: 1,
            borderColor: palette.line,
            borderRadius: radius.md,
            padding: spacing.md,
            gap: spacing.sm,
          }}
        >
          <Text style={{ ...type.heading, color: palette.ink }}>Bu gebelik tamamlandı mı?</Text>
          <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
            Kayıt 44 haftayı geçti. Doğum gerçekleştiyse kaydı kapatabilirsiniz.
          </Text>
        </View>
      )}

      {nextAppointment !== undefined && (
        <View
          style={{
            backgroundColor: palette.surface,
            borderWidth: 1,
            borderColor: palette.line,
            borderRadius: radius.md,
            padding: spacing.md,
            gap: 2,
          }}
        >
          <Text style={{ ...type.label, color: palette.faint }}>Sıradaki</Text>
          <Text style={{ ...type.heading, color: palette.ink }}>{nextAppointment.title}</Text>
          <Text style={{ ...type.small, color: palette.muted }}>
            {nextAppointment.scheduled_at !== null
              ? `Randevu: ${formatDate(nextAppointment.scheduled_at.slice(0, 10))}`
              : nextAppointment.window.start_on !== null
                ? `${formatDate(nextAppointment.window.start_on)} tarihinden itibaren`
                : ''}
          </Text>
        </View>
      )}

      <Button
        label={`${ga.week}. hafta detayı`}
        variant="ghost"
        onPress={() => router.push(`/week/${ga.week}`)}
      />

      <Button label="Takvim" variant="ghost" onPress={() => router.push('/calendar')} />

      <View style={{ flexDirection: 'row', gap: spacing.sm }}>
        <Button
          label="Hareket sayacı"
          variant="ghost"
          onPress={() => router.push('/kick')}
          style={{ flex: 1 }}
        />
        <Button
          label="Sancı sayacı"
          variant="ghost"
          onPress={() => router.push('/contractions')}
          style={{ flex: 1 }}
        />
      </View>

      <Button label="Günlük" variant="ghost" onPress={() => router.push('/belirtiler')} />

      <Button label="Profil ve verilerim" variant="ghost" onPress={() => router.push('/profil')} />
    </Screen>
  );
}

function Row({ label, value, emphasis }: { label: string; value: string; emphasis?: boolean }) {
  const palette = usePalette();

  return (
    <View
      style={{
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center',
        backgroundColor: palette.surface,
        borderWidth: 1,
        borderColor: palette.line,
        borderRadius: radius.md,
        paddingVertical: spacing.md,
        paddingHorizontal: spacing.md,
      }}
    >
      <Text style={{ ...type.small, color: palette.muted }}>{label}</Text>
      <Text style={{ ...type.heading, color: emphasis ? palette.accent : palette.ink }}>{value}</Text>
    </View>
  );
}
