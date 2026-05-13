/**
 * GRACE[M-CONTENT-PIPELINE][seo-helpers][BLOCK_SHARED]
 * PURPOSE: Очистка summary/description для frontmatter в скриптах миграции.
 * Держать в соответствии с lib/Seo.php (stripDescriptionNoise, cleanMetaDescription).
 */

export function stripDescriptionNoise(raw: string): string {
  let t = raw;
  t = t.replace(/\u200b|\ufeff|\u3164/gu, '');
  t = t.replace(/^#{1,6}\s+/gm, '');
  t = t.replace(/!\[[^\]]*]\([^)]*\)/g, '');
  t = t.replace(/\[([^\]]+)]\([^)]*\)/g, '$1');
  t = t.replace(/\*\*([^*]+)\*\*/g, '$1');
  t = t.replace(/__([^_]+)__/g, '$1');
  t = t.replace(/`([^`]+)`/g, '$1');
  t = t.replace(/<[^>]+>/g, ' ');
  t = t.replace(/\s+/g, ' ');
  return t.trim();
}

const chars = (s: string) => [...s];

export function cleanMetaDescription(
  input: string | null | undefined,
  fallback: string,
  maxLen = 160,
): string {
  if (input === null || input === undefined || input === '') {
    return fallback;
  }
  const t = stripDescriptionNoise(input);
  if (t === '') {
    return fallback;
  }
  const ch = chars(t);
  if (ch.length <= maxLen) {
    return t;
  }
  const cut = ch.slice(0, maxLen).join('');
  let lastSpace = cut.lastIndexOf(' ');
  const safe =
    lastSpace > Math.floor(maxLen * 0.55) ? cut.slice(0, lastSpace) : cut;
  return `${safe.trim()}…`;
}
