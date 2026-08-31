import * as SQLite from 'expo-sqlite';

import type { QueueItem, QueueStore } from '@/lib/store.types';

/**
 * Cihazdaki yazma kuyruğu.
 *
 * Uygulama her kaydı önce buraya yazar, sonra göndermeyi dener. Hastanede
 * internet olmadığı için yerel yazma birincil yoldur: kullanıcı sancı sayarken
 * bağlantı olup olmadığını düşünmemeli.
 */
let database: SQLite.SQLiteDatabase | null = null;

async function db(): Promise<SQLite.SQLiteDatabase> {
  if (database === null) {
    database = await SQLite.openDatabaseAsync('hamilelik.db');

    await database.execAsync(`
      PRAGMA journal_mode = WAL;
      CREATE TABLE IF NOT EXISTS queue (
        client_uuid TEXT PRIMARY KEY NOT NULL,
        kind        TEXT NOT NULL,
        payload     TEXT NOT NULL,
        created_at  INTEGER NOT NULL
      );
    `);
  }

  return database;
}

export const store: QueueStore = {
  async put(item) {
    // Aynı anahtar yeniden yazılır: kullanıcı sayaca devam ederken oturum
    // güncellenir, kuyrukta tek satır kalır.
    await (await db()).runAsync(
      'INSERT OR REPLACE INTO queue (client_uuid, kind, payload, created_at) VALUES (?, ?, ?, ?)',
      item.client_uuid,
      item.kind,
      JSON.stringify(item.payload),
      Date.now(),
    );
  },

  async all() {
    const rows = await (await db()).getAllAsync<{ client_uuid: string; kind: QueueItem['kind']; payload: string }>(
      'SELECT client_uuid, kind, payload FROM queue ORDER BY created_at',
    );

    return rows.map((row) => ({
      client_uuid: row.client_uuid,
      kind: row.kind,
      payload: JSON.parse(row.payload) as unknown,
    }));
  },

  async remove(uuids) {
    if (uuids.length === 0) return;

    const marks = uuids.map(() => '?').join(',');
    await (await db()).runAsync(`DELETE FROM queue WHERE client_uuid IN (${marks})`, ...uuids);
  },

  async count() {
    const row = await (await db()).getFirstAsync<{ total: number }>('SELECT COUNT(*) as total FROM queue');

    return row?.total ?? 0;
  },
};
