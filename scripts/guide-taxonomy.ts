/**
 * GRACE[M-CONTENT-PIPELINE][guide-taxonomy][BLOCK_SHARED]
 * PURPOSE: Эвристики темы/статуса/аудитории гайдов для Node-скриптов миграции и аудита.
 * Держать в соответствии с lib/GuideTaxonomy.php.
 */

export type GuideCategory =
  | 'banner'
  | 'patch'
  | 'codes'
  | 'newbie'
  | 'tier'
  | 'hardware'
  | 'general';

export function topicFromCategory(category: GuideCategory | string): string {
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

function inferTopicHeuristic(lower: string): string {
  if (
    lower.includes('тир') ||
    lower.includes('tier') ||
    lower.includes('отряд') ||
    lower.includes('команд')
  ) {
    return 'party';
  }
  if (
    lower.includes('примогем') ||
    lower.includes('фарм') ||
    lower.includes('крутк') ||
    lower.includes('экономик')
  ) {
    return 'economy';
  }
  if (
    lower.includes('лор') ||
    lower.includes('сюжет') ||
    lower.includes('теори') ||
    lower.includes('фатуи')
  ) {
    return 'lore';
  }
  if (
    lower.includes('ноутбук') ||
    lower.includes('noutbuk') ||
    lower.includes('пк ') ||
    lower.includes(' pk') ||
    lower.includes('железо') ||
    lower.includes('fps') ||
    /\bpc\b/i.test(lower)
  ) {
    return 'tech';
  }
  return 'general';
}

/** Совместимо с вызовами process-content: (category, relPath, fileName, body). */
export function inferTopic(
  category: GuideCategory | string,
  relPath: string,
  fileName: string,
  bodySample: string,
): string {
  const slugHint = `${relPath} ${fileName}`.trim();
  const lower = `${slugHint} ${bodySample.slice(0, 2000)}`.toLowerCase();
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
      return inferTopicHeuristic(lower);
  }
}

export function extractGameVersion(slug: string, bodySample: string): string | undefined {
  const u = /(?:^|[-_])update[-_]?(\d+)[-_](\d+)/i.exec(slug);
  if (u) return `${u[1]}.${u[2]}`;
  const b = /\b(\d+)\.(\d+)\s*«/u.exec(bodySample);
  if (b) return `${b[1]}.${b[2]}`;
  return undefined;
}

function parseDateTs(d: unknown): number | null {
  if (d === null || d === undefined || d === '') return null;
  if (typeof d === 'number' && Number.isFinite(d)) return d;
  if (d instanceof Date && !Number.isNaN(d.getTime())) return Math.floor(d.getTime() / 1000);
  if (typeof d === 'string') {
    const ts = Date.parse(d);
    return Number.isNaN(ts) ? null : Math.floor(ts / 1000);
  }
  return null;
}

export function inferStatus(
  category: GuideCategory | string,
  date?: Date | number | null,
  gameVersion?: string | null,
): string {
  const cat = category as GuideCategory;
  const gv = gameVersion ?? undefined;
  const dateTs = parseDateTs(date);

  if (cat === 'patch' || gv) {
    return 'dated';
  }
  if (cat === 'banner' && dateTs !== null) {
    const ageSec = Math.floor(Date.now() / 1000) - dateTs;
    if (ageSec > 180 * 24 * 60 * 60) {
      return 'historical';
    }
    return 'dated';
  }
  if (cat === 'codes') {
    return 'dated';
  }
  return 'active';
}

export function inferAudience(category: GuideCategory | string, bodySample: string): string {
  const cat = category as GuideCategory;
  const lower = bodySample.slice(0, 1500).toLowerCase();
  if (cat === 'newbie') {
    return 'beginner';
  }
  if (/бездн|спирал|meta|мета|абисс/i.test(lower)) {
    return 'meta';
  }
  if (/нович|старт|первые шаги/i.test(lower)) {
    return 'beginner';
  }
  return 'all';
}
