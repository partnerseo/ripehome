import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { Text, TextInput, View } from 'react-native';

import { acceptConsent, deleteAccount, exportAccountData, logout, me } from '@/api/auth';
import { currentPregnancy } from '@/api/pregnancy';
import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { saveExport } from '@/lib/export';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

export default function Profile() {
  const palette = usePalette();
  const queryClient = useQueryClient();

  const { data: user } = useQuery({ queryKey: ['me'], queryFn: me });
  const { data: pregnancy } = useQuery({ queryKey: ['pregnancy', 'current'], queryFn: currentPregnancy });

  const [confirmEmail, setConfirmEmail] = useState('');
  const [status, setStatus] = useState<string | null>(null);

  const consent = useMutation({
    mutationFn: acceptConsent,
    onSuccess: () => queryClient.invalidateQueries({ queryKey: ['me'] }),
  });

  const exportData = useMutation({
    mutationFn: async () => saveExport(await exportAccountData()),
    onSuccess: (shared) =>
      setStatus(shared ? 'Verileriniz dosya olarak hazırlandı.' : 'Dosya hazırlandı ancak paylaşım açılamadı.'),
    onError: () => setStatus('Veriler alınamadı. Bağlantınızı kontrol edin.'),
  });

  const remove = useMutation({
    mutationFn: () => deleteAccount(confirmEmail.trim()),
    onSuccess: () => {
      queryClient.clear();
      router.replace('/sign-in');
    },
    onError: () => setStatus('Silme onaylanamadı. E-postanızı birebir yazdığınızdan emin olun.'),
  });

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>Profil</Text>
        <Text style={{ ...type.title, color: palette.ink }}>Hesap ve verileriniz</Text>
        {user !== undefined && (
          <Text style={{ ...type.small, color: palette.muted }}>{user.email}</Text>
        )}
      </View>

      {status !== null && (
        <View
          accessibilityLiveRegion="polite"
          style={{ backgroundColor: palette.surfaceAlt, borderRadius: radius.md, padding: spacing.md }}
        >
          <Text style={{ ...type.small, color: palette.inkSoft }}>{status}</Text>
        </View>
      )}

      <Card title="Açık rıza">
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Hamilelik verisi KVKK kapsamında özel nitelikli kişisel veridir. Bu veriyi
          işleyebilmemiz için açık rızanız gerekir. Sağlık kayıtlarınız reklam veya
          analitik amacıyla hiçbir yere gönderilmez.
        </Text>
        {user?.has_consent === true ? (
          <Text style={{ ...type.small, color: palette.teal, fontWeight: '600' }}>Rızanız kayıtlı.</Text>
        ) : (
          <Button
            label="Okudum, onaylıyorum"
            onPress={() => consent.mutate()}
            loading={consent.isPending}
          />
        )}
      </Card>

      <Card title="Verilerimi indir">
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Tüm kayıtlarınızı okunabilir bir dosya olarak alabilirsiniz.
        </Text>
        <Button
          label="Verilerimi dışa aktar"
          variant="ghost"
          onPress={() => exportData.mutate()}
          loading={exportData.isPending}
        />
      </Card>

      {pregnancy !== null && pregnancy !== undefined && (
        <Card title="Gebelik kaydı">
          <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
            Gebeliğiniz sonlandıysa kaydı kapatabilirsiniz. Kapattığınız anda tüm
            hatırlatmalar ve haftalık bildirimler durur.
          </Text>
          <Button
            label="Gebelik kaydını kapat"
            variant="ghost"
            onPress={() => router.push('/gebelik-kapat')}
          />
        </Card>
      )}

      <Card title="Hesabımı sil">
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Hesabınız ve tüm kayıtlarınız kalıcı olarak silinir. Bu işlem geri alınamaz.
          Onaylamak için e-posta adresinizi yazın.
        </Text>
        <TextInput
          accessibilityLabel="Silme onayı için e-posta"
          autoCapitalize="none"
          inputMode="email"
          onChangeText={setConfirmEmail}
          placeholder={user?.email ?? 'ornek@eposta.com'}
          placeholderTextColor={palette.faint}
          style={{
            backgroundColor: palette.surface,
            borderWidth: 1,
            borderColor: palette.line,
            borderRadius: radius.md,
            padding: spacing.md,
            color: palette.ink,
          }}
          value={confirmEmail}
        />
        <Button
          label="Hesabımı kalıcı olarak sil"
          variant="ghost"
          disabled={confirmEmail.trim().toLowerCase() !== (user?.email ?? '')}
          onPress={() => remove.mutate()}
          loading={remove.isPending}
        />
      </Card>

      <Button
        label="Çıkış yap"
        variant="ghost"
        onPress={async () => {
          await logout();
          queryClient.clear();
          router.replace('/sign-in');
        }}
      />

      <Button label="Ana ekrana dön" variant="ghost" onPress={() => router.replace('/home')} />
    </Screen>
  );
}

function Card({ title, children }: { title: string; children: React.ReactNode }) {
  const palette = usePalette();

  return (
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
      <Text style={{ ...type.heading, color: palette.ink }}>{title}</Text>
      {children}
    </View>
  );
}
