import { useQueryClient } from '@tanstack/react-query';
import { router } from 'expo-router';
import { useState } from 'react';
import { KeyboardAvoidingView, Platform, Text, TextInput, View } from 'react-native';

import { requestCode, verifyCode } from '@/api/auth';
import { ApiError } from '@/api/client';
import { currentPregnancy } from '@/api/pregnancy';
import { Button } from '@/components/Button';
import { Screen } from '@/components/Screen';
import { radius, spacing, type, usePalette } from '@/theme/tokens';

type Step = 'email' | 'code';

export default function SignIn() {
  const palette = usePalette();
  const queryClient = useQueryClient();

  const [step, setStep] = useState<Step>('email');
  const [email, setEmail] = useState('');
  const [code, setCode] = useState('');
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const inputStyle = {
    backgroundColor: palette.surface,
    borderWidth: 1,
    borderColor: palette.line,
    borderRadius: radius.md,
    paddingHorizontal: spacing.md,
    paddingVertical: spacing.md,
    fontSize: 17,
    color: palette.ink,
  };

  async function submitEmail() {
    setError(null);
    setBusy(true);

    try {
      await requestCode(email.trim());
      setStep('code');
    } catch (e) {
      setError(e instanceof ApiError ? e.message : 'Sunucuya ulaşılamadı. Bağlantınızı kontrol edin.');
    } finally {
      setBusy(false);
    }
  }

  async function submitCode() {
    setError(null);
    setBusy(true);

    try {
      await verifyCode(email.trim(), code.trim());
      // Kapı ekranını atlayıp doğrudan doğru yere gidiyoruz: yeni kullanıcı
      // kuruluma, dönen kullanıcı ana ekrana.
      const pregnancy = await currentPregnancy();
      await queryClient.invalidateQueries();
      router.replace(pregnancy ? '/home' : '/onboarding');
    } catch (e) {
      setError(e instanceof ApiError ? e.message : 'Sunucuya ulaşılamadı. Bağlantınızı kontrol edin.');
    } finally {
      setBusy(false);
    }
  }

  return (
    <Screen>
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        style={{ flex: 1, justifyContent: 'center', gap: spacing.lg }}
      >
        <View style={{ gap: spacing.sm }}>
          <Text style={{ ...type.label, color: palette.faint }}>Hamilelik</Text>
          <Text style={{ ...type.title, color: palette.ink }}>
            {step === 'email' ? 'Giriş yapın' : 'Kodu girin'}
          </Text>
          <Text style={{ ...type.small, color: palette.muted, lineHeight: 21 }}>
            {step === 'email'
              ? 'E-posta adresinize tek kullanımlık bir giriş kodu göndereceğiz. Şifre yok.'
              : `${email} adresine gönderdiğimiz 6 haneli kodu girin. Kod 10 dakika geçerli.`}
          </Text>
        </View>

        {step === 'email' ? (
          <TextInput
            accessibilityLabel="E-posta adresi"
            autoCapitalize="none"
            autoComplete="email"
            autoCorrect={false}
            inputMode="email"
            keyboardType="email-address"
            onChangeText={setEmail}
            placeholder="ornek@eposta.com"
            placeholderTextColor={palette.faint}
            style={inputStyle}
            value={email}
          />
        ) : (
          <TextInput
            accessibilityLabel="Giriş kodu"
            autoComplete="one-time-code"
            inputMode="numeric"
            keyboardType="number-pad"
            maxLength={6}
            onChangeText={setCode}
            placeholder="000000"
            placeholderTextColor={palette.faint}
            style={{ ...inputStyle, fontSize: 28, letterSpacing: 8, textAlign: 'center' }}
            value={code}
          />
        )}

        {error !== null && (
          <View
            accessibilityLiveRegion="polite"
            style={{
              backgroundColor: palette.dangerSoft,
              borderRadius: radius.sm,
              padding: spacing.md,
            }}
          >
            <Text style={{ ...type.small, color: palette.danger }}>{error}</Text>
          </View>
        )}

        {step === 'email' ? (
          <Button label="Kod gönder" onPress={submitEmail} loading={busy} disabled={!email.includes('@')} />
        ) : (
          <View style={{ gap: spacing.sm }}>
            <Button label="Giriş yap" onPress={submitCode} loading={busy} disabled={code.length !== 6} />
            <Button
              label="E-postayı değiştir"
              variant="ghost"
              onPress={() => {
                setStep('email');
                setCode('');
                setError(null);
              }}
            />
          </View>
        )}
      </KeyboardAvoidingView>
    </Screen>
  );
}
