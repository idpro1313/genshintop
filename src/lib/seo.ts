/** SEO helpers: canonical URLs, meta descriptions, schema.org fragments. */

export const SITE_URL = 'https://genshintop.ru';

/** Default Open Graph / Twitter image (also Organization logo in JSON-LD). */
export const DEFAULT_OG_IMAGE_PATH = '/og-default.svg';

export const OG_IMAGE_WIDTH = 1200;
export const OG_IMAGE_HEIGHT = 630;

/** Абсолютный URL пути вида `/guides/foo` или полный URL. */
export function absoluteUrl(pathOrUrl: string): string {
  if (/^https?:\/\//i.test(pathOrUrl)) return pathOrUrl;
  const path = pathOrUrl.startsWith('/') ? pathOrUrl : `/${pathOrUrl}`;
  return new URL(path.replace(/\/+/g, '/'), `${SITE_URL}/`).toString();
}

/** Убирает типичный мусор миграции: markdown, крошки, обрезает по длине. */
export function stripDescriptionNoise(raw: string): string {
  let t = raw;
  t = t.replace(/\u200b|\ufeff/g, '');
  t = t.replace(/^#{1,6}\s+/gm, '');
  t = t.replace(/!\[[^\]]*]\([^)]*\)/g, '');
  t = t.replace(/\[([^\]]+)]\([^)]*\)/g, '$1');
  t = t.replace(/\*\*([^*]+)\*\*/g, '$1');
  t = t.replace(/__([^_]+)__/g, '$1');
  t = t.replace(/`([^`]+)`/g, '$1');
  t = t.replace(/<[^>]+>/g, ' ');
  t = t.replace(/\s+/g, ' ').trim();
  return t;
}

/**
 * Чистый meta description для `<meta name="description">` и schema.org.
 * @param maxLen рекомендуется 150–160 для сниппетов; для карточек можно 220–280.
 */
export function cleanMetaDescription(
  input: string | undefined | null,
  fallback: string,
  maxLen = 160,
): string {
  if (input == null || typeof input !== 'string') return fallback;
  let t = stripDescriptionNoise(input);
  if (!t) return fallback;
  if (t.length <= maxLen) return t;
  const cut = t.slice(0, maxLen);
  const lastSpace = cut.lastIndexOf(' ');
  const safe =
    lastSpace > maxLen * 0.55 ? cut.slice(0, lastSpace).trim() : cut.trim();
  return `${safe}…`;
}

export function publisherOrganization(): Record<string, unknown> {
  return {
    '@type': 'Organization',
    '@id': `${SITE_URL}/#organization`,
    name: 'GenshinTop',
    url: SITE_URL,
    logo: {
      '@type': 'ImageObject',
      url: absoluteUrl(DEFAULT_OG_IMAGE_PATH),
      width: OG_IMAGE_WIDTH,
      height: OG_IMAGE_HEIGHT,
    },
  };
}

export function webSiteNode(): Record<string, unknown> {
  return {
    '@type': 'WebSite',
    '@id': `${SITE_URL}/#website`,
    name: 'GenshinTop',
    url: SITE_URL,
    inLanguage: 'ru-RU',
    publisher: { '@id': `${SITE_URL}/#organization` },
  };
}

export function breadcrumbListSchema(
  items: Array<{ label: string; href: string }>,
): Record<string, unknown> {
  return {
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, i) => ({
      '@type': 'ListItem',
      position: i + 1,
      name: item.label,
      item: absoluteUrl(item.href),
    })),
  };
}

/** Объединяет несколько узлов schema.org в один `@graph`. */
export function jsonLdGraph(nodes: Record<string, unknown>[]): Record<string, unknown> {
  return {
    '@context': 'https://schema.org',
    '@graph': nodes,
  };
}
