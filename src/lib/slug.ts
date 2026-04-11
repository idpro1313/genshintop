/** Нормализованный slug из id записи Content Layer (без .md, слеши как в URL). */
export function contentSlugFromId(id: string): string {
  return id.replace(/\\/g, '/').replace(/\.md$/i, '');
}
