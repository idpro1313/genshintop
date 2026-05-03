/**
 * GRACE[M-WEBSITE][guide-taxonomy][BLOCK_TAXONOMY]
 * PURPOSE: Единая таксономия гайдов (тема, статус актуальности, аудитория) и подписи для UI/миграции.
 */

/** Игровые сценарии поверх грубой `category` из миграции. */
export const guideTopics = [
  'banner',
  'patch',
  'codes',
  'newbie',
  'party',
  'economy',
  'lore',
  'tech',
  'general',
] as const;
export type GuideTopic = (typeof guideTopics)[number];

export const guideTopicLabelsRu: Record<GuideTopic, string> = {
  banner: 'Баннеры и молитвы',
  patch: 'Обновления патчей',
  codes: 'Промокоды',
  newbie: 'Новичкам',
  party: 'Отряды и персонажи',
  economy: 'Экономика и фарм',
  lore: 'Лор и сюжет',
  tech: 'ПК, железо, инструменты',
  general: 'Разное',
};

/** Статус актуальности материала для игрока и SEO. */
export const guideStatuses = ['active', 'dated', 'historical'] as const;
export type GuideStatus = (typeof guideStatuses)[number];

export const guideStatusLabelsRu: Record<GuideStatus, string> = {
  active: 'Актуально',
  dated: 'Привязано к версии',
  historical: 'Архив',
};

export const guideAudiences = ['all', 'beginner', 'returning', 'meta'] as const;
export type GuideAudience = (typeof guideAudiences)[number];

export const guideAudienceLabelsRu: Record<GuideAudience, string> = {
  all: 'Всем',
  beginner: 'Новичкам',
  returning: 'Вернувшимся',
  meta: 'Мета / бездна',
};

/** Тип категории коллекции (как при миграции из gi-database). */
export type GuideCategory =
  | 'banner'
  | 'patch'
  | 'newbie'
  | 'codes'
  | 'tier'
  | 'hardware'
  | 'general';

/** Вывод `topic` из унаследованной `category` (если в frontmatter нет своего `topic`). */
export function topicFromCategory(category: GuideCategory): GuideTopic {
  switch (category) {
    case 'banner':
      return 'banner';
    case 'patch':
      return 'patch';
    case 'codes':
      return 'codes';
    case 'newbie':
      return 'newbie';
    case 'tier':
      return 'party';
    case 'hardware':
      return 'tech';
    default:
      return 'general';
  }
}

/** Эвристика темы по пути/имени файла и тексту (для миграции и аудита). */
export function inferTopic(
  category: GuideCategory,
  relPath: string,
  base: string,
  bodySample: string,
): GuideTopic {
  const lower = `${relPath}/${base} ${bodySample.slice(0, 2000)}`.toLowerCase();
  if (category === 'banner') return 'banner';
  if (category === 'patch') return 'patch';
  if (category === 'codes') return 'codes';
  if (category === 'newbie') return 'newbie';
  if (category === 'tier') return 'party';
  if (category === 'hardware') return 'tech';
  if (
    lower.includes('тир') ||
    lower.includes('tier') ||
    lower.includes('отряд') ||
    lower.includes('команд')
  )
    return 'party';
  if (
    lower.includes('примогем') ||
    lower.includes('фарм') ||
    lower.includes('крутк') ||
    lower.includes('экономик')
  )
    return 'economy';
  if (
    lower.includes('лор') ||
    lower.includes('сюжет') ||
    lower.includes('теори') ||
    lower.includes('фатуи')
  )
    return 'lore';
  if (
    lower.includes('ноутбук') ||
    lower.includes('noutbuk') ||
    lower.includes('пк ') ||
    lower.includes(' pk') ||
    lower.includes('железо') ||
    lower.includes('fps') ||
    /\bpc\b/i.test(lower)
  )
    return 'tech';
  return 'general';
}

export function inferAudience(
  category: GuideCategory,
  bodySample: string,
): GuideAudience {
  const lower = bodySample.slice(0, 1500).toLowerCase();
  if (category === 'newbie') return 'beginner';
  if (/бездн|спирал|meta|мета|абисс/i.test(lower)) return 'meta';
  if (/нович|старт|первые шаги/i.test(lower)) return 'beginner';
  return 'all';
}

/** Версия игры вроде 6.5 из имени update-6-5 или текста. */
export function extractGameVersion(
  slug: string,
  bodySample: string,
): string | undefined {
  const fromSlug = slug.match(/(?:^|[-_])update[-_]?(\d+)[-_](\d+)/i);
  if (fromSlug) return `${fromSlug[1]}.${fromSlug[2]}`;
  const m = bodySample.match(/\b(\d+)\.(\d+)\s*«/);
  if (m) return `${m[1]}.${m[2]}`;
  return undefined;
}

export function inferStatus(
  category: GuideCategory,
  date: Date | undefined,
  gameVersion: string | undefined,
): GuideStatus {
  if (category === 'patch' || gameVersion) return 'dated';
  if (category === 'banner' && date) {
    const ageMs = Date.now() - date.getTime();
    if (ageMs > 180 * 24 * 60 * 60 * 1000) return 'historical';
    return 'dated';
  }
  if (category === 'codes') return 'dated';
  return 'active';
}

/** Значения для UI и фильтров, если в frontmatter поля ещё не заполнены. */
export function effectiveGuideTopic(
  topic: GuideTopic | undefined,
  category: GuideCategory,
  slugFileName: string,
  bodyHint = '',
): GuideTopic {
  if (topic) return topic;
  return inferTopic(category, '', slugFileName, bodyHint);
}

export function effectiveGuideGameVersion(
  gv: string | undefined,
  slug: string,
  bodyHint = '',
): string | undefined {
  return gv ?? extractGameVersion(slug, bodyHint);
}

export function effectiveGuideStatus(
  status: GuideStatus | undefined,
  category: GuideCategory,
  date: Date | undefined,
  slug: string,
  bodyHint = '',
): GuideStatus {
  if (status) return status;
  const gv = extractGameVersion(slug, bodyHint);
  return inferStatus(category, date, gv);
}

export function effectiveGuideAudience(
  audience: GuideAudience | undefined,
  category: GuideCategory,
  bodyHint = '',
): GuideAudience {
  if (audience) return audience;
  return inferAudience(category, bodyHint);
}
