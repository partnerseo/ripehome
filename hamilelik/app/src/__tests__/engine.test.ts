import { calculate } from '@engine';

import vectors from '../../../ga-test-vectors.json';

/**
 * Motorun uygulama paketi içinden de aynı sonucu verdiğini doğrular:
 * paylaşılan dosya gerçekten bağlı mı, kopya mı kalmış.
 */
describe('gebelik yaşı motoru — uygulama içinden', () => {
  it.each(vectors.vectors.map((v) => [v.name, v] as const))('%s', (_name, vector) => {
    const actual = calculate(
      vector.method as Parameters<typeof calculate>[0],
      vector.input_date,
      vector.today,
      vector.cycle_length,
    );

    for (const [key, expected] of Object.entries(vector.expected)) {
      expect(actual[key as keyof typeof actual]).toBe(expected);
    }
  });
});
