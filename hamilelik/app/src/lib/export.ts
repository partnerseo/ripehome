import { File, Paths } from 'expo-file-system';
import * as Sharing from 'expo-sharing';

/** Dışa aktarılan veriyi dosyaya yazıp paylaşım penceresini açar. */
export async function saveExport(data: unknown): Promise<boolean> {
  const file = new File(Paths.cache, `hamilelik-verilerim-${new Date().toISOString().slice(0, 10)}.json`);

  if (file.exists) {
    file.delete();
  }

  file.create();
  file.write(JSON.stringify(data, null, 2));

  if (!(await Sharing.isAvailableAsync())) {
    return false;
  }

  await Sharing.shareAsync(file.uri, { mimeType: 'application/json', dialogTitle: 'Verilerim' });

  return true;
}
