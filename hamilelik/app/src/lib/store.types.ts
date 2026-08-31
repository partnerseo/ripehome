export type QueueKind = 'health_logs' | 'kick_sessions' | 'contraction_sessions' | 'symptom_logs';

export interface QueueItem {
  client_uuid: string;
  kind: QueueKind;
  payload: unknown;
}

/**
 * Çevrimdışı yazma kuyruğunun sözleşmesi.
 *
 * İki uygulaması var: cihazda SQLite (store.ts), web'de localStorage
 * (store.web.ts). Metro platforma göre doğru dosyayı seçer — böylece
 * expo-sqlite web paketine hiç girmez.
 */
export interface QueueStore {
  put(item: QueueItem): Promise<void>;
  all(): Promise<QueueItem[]>;
  remove(uuids: string[]): Promise<void>;
  count(): Promise<number>;
}
