import { View } from 'react-native';
import Svg, { Circle } from 'react-native-svg';

import { usePalette } from '@/theme/tokens';

type Props = {
  /** 0–1 arası. Termin aşımında sunucu bunu 1'de kilitler. */
  progress: number;
  size?: number;
  thickness?: number;
  children?: React.ReactNode;
};

export function ProgressRing({ progress, size = 220, thickness = 12, children }: Props) {
  const palette = usePalette();
  const radius = (size - thickness) / 2;
  const circumference = 2 * Math.PI * radius;
  const clamped = Math.max(0, Math.min(1, progress));

  return (
    <View style={{ width: size, height: size, alignItems: 'center', justifyContent: 'center' }}>
      <Svg width={size} height={size} style={{ position: 'absolute' }}>
        <Circle
          cx={size / 2}
          cy={size / 2}
          r={radius}
          stroke={palette.line}
          strokeWidth={thickness}
          fill="none"
        />
        <Circle
          cx={size / 2}
          cy={size / 2}
          r={radius}
          stroke={palette.accent}
          strokeWidth={thickness}
          strokeLinecap="round"
          fill="none"
          strokeDasharray={circumference}
          strokeDashoffset={circumference * (1 - clamped)}
          // Halka tepeden başlasın.
          transform={`rotate(-90 ${size / 2} ${size / 2})`}
        />
      </Svg>
      {children}
    </View>
  );
}
