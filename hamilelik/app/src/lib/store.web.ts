import type { QueueItem, QueueStore } from '@/lib/store.types';

/**
 * Web'deki yazma kuyruğu.
 *
 * Web geliştirme ve önizleme hedefi; SQLite yerine localStorage yeterli.
 * Sözleşme cihaz sürümüyle aynı olduğu için çağıran taraf farkı bilmez.
 */
const KEY = 'hamilelik.queue';

function read(): QueueItem[] {
  try {
    return JSON.parse(globalThis.localStorage?.getItem(KEY) ?? '[]') as QueueItem[];
  } catch {
    return [];
  }
}

function write(rows: QueueItem[]): void {
  try {
    globalThis.localStorage?.setItem(KEY, JSON.stringify(rows));
  } catch {
    // Gizli sekmede yazma engellenebilir; kayıt o oturumla sınırlı kalır.
  }
}

export const store: QueueStore = {
  async put(item) {
    write([...read().filter((row) => row.client_uuid !== item.client_uuid), item]);
  },

  async all() {
    return read();
  },

  async remove(uuids) {
    write(read().filter((row) => !uuids.includes(row.client_uuid)));
  },

  async count() {
    return read().length;
  },
};
