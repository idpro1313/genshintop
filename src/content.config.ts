import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

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
      relatedWeapons: z.array(z.string()).optional(),
      relatedArtifacts: z.array(z.string()).optional(),
      relatedGuides: z.array(z.string()).optional(),
    }),
  }),
  guides: defineCollection({
    loader: glob({ pattern: '**/*.md', base: './src/content/guides' }),
    schema: z.object({
      title: z.string(),
      category: guideCategoryEnum,
      date: z.coerce.date().optional(),
      summary: z.string().optional(),
      sourceSlug: z.string(),
      sourcePath: z.string().optional(),
    }),
  }),
};
