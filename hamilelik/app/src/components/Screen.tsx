import { ScrollView, View, type StyleProp, type ViewStyle } from 'react-native';
import { useSafeAreaInsets } from 'react-native-safe-area-context';

import { spacing, usePalette } from '@/theme/tokens';

type Props = {
  children: React.ReactNode;
  scroll?: boolean;
  contentStyle?: StyleProp<ViewStyle>;
};

export function Screen({ children, scroll = false, contentStyle }: Props) {
  const palette = usePalette();
  const insets = useSafeAreaInsets();

  const padding = {
    paddingTop: insets.top + spacing.lg,
    paddingBottom: insets.bottom + spacing.xl,
    paddingHorizontal: spacing.lg,
  };

  if (scroll) {
    return (
      <ScrollView
        style={{ flex: 1, backgroundColor: palette.bg }}
        contentContainerStyle={[padding, contentStyle]}
      >
        {children}
      </ScrollView>
    );
  }

  return <View style={[{ flex: 1, backgroundColor: palette.bg }, padding, contentStyle]}>{children}</View>;
}
