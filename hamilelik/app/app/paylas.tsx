import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { Share as NativeShare, Text, TextInput, View } from 'react-native';

import { invitePartner, revokeShare, shares } from '@/api/extras';
import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

export default function ShareScreen() {
  const palette = usePalette();
  const queryClient = useQueryClient();

  const [email, setEmail] = useState('');
  const [error, setError] = useState<string | null>(null);

  const { data: list } = useQuery({ queryKey: ['shares'], queryFn: shares });
  const invalidate = () => queryClient.invalidateQueries({ queryKey: ['shares'] });

  const invite = useMutation({
    mutationFn: () => invitePartner(email.trim()),
    onSuccess: async (share) => {
      setEmail('');
      setError(null);
      await invalidate();
      await NativeShare.share({
        message: `Gebelik takibimi seninle paylaştım. Davet kodun: ${share.token}`,
      }).catch(() => undefined);
    },
    onError: () => setError('Davet gönderilemedi. E-postayı kontrol edin.'),
  });

  const revoke = useMutation({ mutationFn: revokeShare, onSuccess: invalidate });

  return (
    <Screen scroll contentStyle={{ gap: spacing.lg }}>
      <View style={{ gap: 2 }}>
        <Text style={{ ...type.label, color: palette.faint }}>Paylaşım</Text>
        <Text style={{ ...type.title, color: palette.ink }}>Eşinizle paylaşın</Text>
        <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
          Davet ettiğiniz kişi gebelik haftanızı ve takviminizi yalnızca görebilir.
          Kayıt ekleyemez, değiştiremez. Erişimi istediğiniz an kaldırabilirsiniz.
        </Text>
      </View>

      <View style={{ gap: spacing.sm }}>
        <TextInput
          accessibilityLabel="Davet edilecek e-posta"
          autoCapitalize="none"
          autoCorrect={false}
          inputMode="email"
          onChangeText={setEmail}
          placeholder="es@eposta.com"
          placeholderTextColor={palette.faint}
          style={{
            backgroundColor: palette.surface,
            borderWidth: 1,
            borderColor: palette.line,
            borderRadius: radius.md,
            padding: spacing.md,
            color: palette.ink,
          }}
          value={email}
        />
        {error !== null && <Text style={{ ...type.small, color: palette.danger }}>{error}</Text>}
        <Button
          label="Davet gönder"
          onPress={() => invite.mutate()}
          loading={invite.isPending}
          disabled={!email.includes('@')}
        />
      </View>

      {list !== undefined && list.length > 0 && (
        <View style={{ gap: spacing.sm }}>
          <Text style={{ ...type.label, color: palette.faint }}>Paylaşılanlar</Text>
          {list.map((share) => (
            <View
              key={share.id}
              style={{
                backgroundColor: palette.surface,
                borderWidth: 1,
                borderColor: palette.line,
                borderRadius: radius.md,
                padding: spacing.md,
                gap: spacing.xs,
              }}
            >
              <Text style={{ ...type.heading, color: palette.ink }}>{share.invited_email}</Text>
              <Text style={{ ...type.small, color: share.accepted_at !== null ? palette.teal : palette.faint }}>
                {share.accepted_at !== null ? 'Daveti kabul etti · görüntüleyebilir' : 'Davet bekliyor'}
              </Text>
              <Button
                label="Erişimi kaldır"
                variant="ghost"
                onPress={() => revoke.mutate(share.id)}
                loading={revoke.isPending && revoke.variables === share.id}
              />
            </View>
          ))}
        </View>
      )}

      <Button label="Ana ekrana dön" variant="ghost" onPress={() => router.replace('/home')} />
    </Screen>
  );
}
