/** Web'de dışa aktarma: tarayıcının kendi indirme akışı. */
export async function saveExport(data: unknown): Promise<boolean> {
  try {
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');

    link.href = url;
    link.download = `hamilelik-verilerim-${new Date().toISOString().slice(0, 10)}.json`;
    document.body.appendChild(link);
    link.click();
    link.remove();
    URL.revokeObjectURL(url);

    return true;
  } catch {
    return false;
  }
}
