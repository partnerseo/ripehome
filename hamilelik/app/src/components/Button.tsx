import { ActivityIndicator, Pressable, Text, type StyleProp, type ViewStyle } from 'react-native';

import { radius, spacing, usePalette } from '@/theme/tokens';

type Props = {
  label: string;
  onPress: () => void;
  variant?: 'primary' | 'ghost';
  disabled?: boolean;
  loading?: boolean;
  style?: StyleProp<ViewStyle>;
};

export function Button({ label, onPress, variant = 'primary', disabled, loading, style }: Props) {
  const palette = usePalette();
  const isPrimary = variant === 'primary';
  const inactive = disabled || loading;

  return (
    <Pressable
      accessibilityRole="button"
      accessibilityState={{ disabled: inactive }}
      disabled={inactive}
      onPress={onPress}
      style={({ pressed }) => [
        {
          paddingVertical: spacing.md,
          paddingHorizontal: spacing.lg,
          borderRadius: radius.md,
          alignItems: 'center',
          justifyContent: 'center',
          backgroundColor: isPrimary ? palette.accent : 'transparent',
          borderWidth: isPrimary ? 0 : 1,
          borderColor: palette.line,
          opacity: inactive ? 0.5 : pressed ? 0.85 : 1,
        },
        style,
      ]}
    >
      {loading ? (
        <ActivityIndicator color={isPrimary ? palette.surface : palette.accent} />
      ) : (
        <Text style={{ fontSize: 16, fontWeight: '600', color: isPrimary ? palette.surface : palette.ink }}>
          {label}
        </Text>
      )}
    </Pressable>
  );
}
