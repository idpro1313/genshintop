/** SEO helpers: canonical URLs, meta descriptions, schema.org fragments. */
import ogManifest from '../data/og-manifest.json';

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
  t = t.replace(/\u200b|\ufeff|\u3164/g, '');
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

/** «Редакция GenshinTop» для использования как author в Article/BlogPosting. */
export function editorialTeamPerson(): Record<string, unknown> {
  return {
    '@type': 'Organization',
    '@id': `${SITE_URL}/#editorial-team`,
    name: 'Редакция GenshinTop',
    url: `${SITE_URL}/editorial-policy`,
    parentOrganization: { '@id': `${SITE_URL}/#organization` },
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

/** Schema.org FAQ для страниц с блоком вопрос–ответ. */
export function faqPageSchema(
  faqs: Array<{ question: string; answer: string }>,
): Record<string, unknown> {
  return {
    '@type': 'FAQPage',
    mainEntity: faqs.map((f) => ({
      '@type': 'Question',
      name: f.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: f.answer,
      },
    })),
  };
}

/** Schema.org HowTo (без Offer — продажа на стороннем сайте). */
export function howToSchema(params: {
  name: string;
  description: string;
  steps: string[];
}): Record<string, unknown> {
  return {
    '@type': 'HowTo',
    name: params.name,
    description: params.description,
    step: params.steps.map((text, i) => ({
      '@type': 'HowToStep',
      position: i + 1,
      name: `Шаг ${i + 1}`,
      text,
    })),
  };
}

/**
 * Schema.org Service для коммерческого партнёрского кластера /lootbar.
 * Provider — внешний сервис (LootBar.gg), мы публикуем обзор и партнёрскую ссылку.
 */
export function lootbarServiceSchema(params: {
  name: string;
  description: string;
  url: string;
  affiliateUrl: string;
}): Record<string, unknown> {
  return {
    '@type': 'Service',
    '@id': `${absoluteUrl(params.url)}#service`,
    name: params.name,
    description: params.description,
    serviceType: 'Genshin Impact top-up',
    areaServed: ['RU', 'BY', 'KZ', 'UA'],
    inLanguage: 'ru-RU',
    provider: {
      '@type': 'Organization',
      name: 'LootBar.gg',
      url: 'https://lootbar.gg/',
    },
    audience: {
      '@type': 'Audience',
      audienceType: 'Игроки Genshin Impact',
    },
    isRelatedTo: {
      '@type': 'VideoGame',
      name: 'Genshin Impact',
      publisher: 'HoYoverse',
    },
    offers: {
      '@type': 'Offer',
      url: params.affiliateUrl,
      priceCurrency: 'RUB',
      availability: 'https://schema.org/InStock',
      category: 'in-game-currency',
    },
    review: undefined,
    mainEntityOfPage: absoluteUrl(params.url),
  };
}

interface OgManifest {
  generatedAt: string | null;
  entries: string[];
}

const manifest = ogManifest as OgManifest;
const manifestSet = new Set(manifest.entries ?? []);

/**
 * Возвращает путь к сгенерированной OG-картинке для записи коллекции.
 * Если PNG ещё не создан скриптом `npm run og:generate`, отдаёт дефолтную SVG.
 */
export function getOgImageForEntry(
  collection: 'guides' | 'characters' | 'lootbar' | 'hubs',
  slug: string,
): string {
  const key = `${collection}/${slug}`;
  if (manifestSet.has(key)) {
    return `/og/${key}.png`;
  }
  return DEFAULT_OG_IMAGE_PATH;
}
