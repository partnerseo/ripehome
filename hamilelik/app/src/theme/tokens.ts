import { useColorScheme } from 'react-native';

/**
 * Renkler plandaki kimlikle aynı: soğuk kağıt zemin, erik vurgu.
 * Bileşenler doğrudan renk yazmaz, hep bu jetonlardan okur.
 */
const light = {
  bg: '#F6F7F4',
  surface: '#FFFFFF',
  surfaceAlt: '#EFF2ED',
  ink: '#18201E',
  inkSoft: '#3A4643',
  muted: '#5C6764',
  faint: '#8A9491',
  line: '#DDE3DE',
  accent: '#7B3B58',
  accentSoft: '#F2E5EB',
  teal: '#1E5B57',
  danger: '#A82E22',
  dangerSoft: '#FAE8E4',
};

export type Palette = typeof light;

const dark: Palette = {
  bg: '#0F1413',
  surface: '#161C1B',
  surfaceAlt: '#1D2523',
  ink: '#E9EEEB',
  inkSoft: '#C3CDC9',
  muted: '#94A19D',
  faint: '#6E7B77',
  line: '#26302E',
  accent: '#DE9CB8',
  accentSoft: '#2C1B23',
  teal: '#63BDB2',
  danger: '#F2887A',
  dangerSoft: '#2C1714',
};

export const spacing = { xs: 4, sm: 8, md: 16, lg: 24, xl: 32, xxl: 48 } as const;

export const radius = { sm: 6, md: 12, lg: 20, pill: 999 } as const;

export const type = {
  display: { fontSize: 44, fontWeight: '700', letterSpacing: -1 },
  title: { fontSize: 24, fontWeight: '700', letterSpacing: -0.4 },
  heading: { fontSize: 17, fontWeight: '600' },
  body: { fontSize: 16, fontWeight: '400' },
  small: { fontSize: 14, fontWeight: '400' },
  label: { fontSize: 12, fontWeight: '600', letterSpacing: 1.1, textTransform: 'uppercase' },
} as const;

export function usePalette(): Palette {
  return useColorScheme() === 'dark' ? dark : light;
}
