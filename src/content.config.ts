import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';
import {
  guideAudiences,
  guideStatuses,
  guideTopics,
} from './lib/guide-taxonomy';

const guideTopicEnum = z.enum(
  guideTopics as unknown as [string, ...string[]],
);
const guideStatusEnum = z.enum(
  guideStatuses as unknown as [string, ...string[]],
);
const guideAudienceEnum = z.enum(
  guideAudiences as unknown as [string, ...string[]],
);

const elementEnum = z.enum([
  'Pyro',
  'Hydro',
  'Electro',
  'Cryo',
  'Anemo',
  'Geo',
  'Dendro',
]);

const weaponEnum = z.enum([
  'Одноручное',
  'Двуручное',
  'Древковое',
  'Катализатор',
  'Лук',
  'Прочее',
]);

const guideCategoryEnum = z.enum([
  'banner',
  'patch',
  'newbie',
  'codes',
  'tier',
  'hardware',
  'general',
]);

/** Нормализация списков из frontmatter: плоские строки, без пустых и без ошибочных вложенных массивов из YAML `  - -`. */
function stringListField() {
  return z.preprocess((val: unknown) => {
    if (val == null) return undefined;
    if (!Array.isArray(val)) return undefined;
    const out: string[] = [];
    for (const item of val) {
      if (typeof item === 'string' && item.length > 0 && item !== '-') {
        out.push(item);
      } else if (Array.isArray(item)) {
        for (const x of item) {
          if (typeof x === 'string' && x.length > 0 && x !== '-') out.push(x);
        }
      }
    }
    return out.length > 0 ? out : undefined;
  }, z.array(z.string()).optional());
}

export const collections = {
  characters: defineCollection({
    loader: glob({ pattern: '**/*.md', base: './src/content/characters' }),
    schema: z.object({
      name: z.string(),
      title: z.string().optional(),
      element: elementEnum,
      weapon: weaponEnum,
      rarity: z.union([z.literal(4), z.literal(5)]).optional(),
      rating: z.string().optional(),
      sourceSlug: z.string(),
      relatedWeapons: stringListField(),
      relatedArtifacts: stringListField(),
      relatedGuides: stringListField(),
    }),
  }),
  guides: defineCollection({
    loader: glob({ pattern: '**/*.md', base: './src/content/guides' }),
    schema: z.object({
      title: z.string(),
      category: guideCategoryEnum,
      /** Сценарий для игрока (тоньше, чем `category`). */
      topic: guideTopicEnum.optional(),
      /** Версия игры, например 6.5 — для фильтров и SEO. */
      gameVersion: z.string().max(32).optional(),
      /** Актуальность материала. */
      status: guideStatusEnum.optional(),
      /** Целевая аудитория. */
      audience: guideAudienceEnum.optional(),
      /** Slug/id связанных персонажей (имена файлов без .md из коллекции characters). */
      relatedCharacters: stringListField(),
      /** Slug/id связанных гайдов (имена файлов без .md из этой коллекции). */
      relatedGuides: stringListField(),
      date: z.coerce.date().optional(),
      /** Дата последней редакции (если отличается от `date`). */
      updatedAt: z.coerce.date().optional(),
      summary: z.string().optional(),
      sourceSlug: z.string(),
      sourcePath: z.string().optional(),
    }),
  }),
};
