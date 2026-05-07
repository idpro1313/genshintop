import rss from '@astrojs/rss';
import type { APIRoute } from 'astro';
import { getCollection } from 'astro:content';
import { contentSlugFromId } from '../lib/slug';
import { SITE_URL, cleanMetaDescription } from '../lib/seo';

export const GET: APIRoute = async (context) => {
  const guides = await getCollection('guides');
  const sorted = guides
    .filter((e) => !e.data.sourceSlug.startsWith('_placeholder'))
    .sort((a, b) => {
      const ta = (a.data.updatedAt ?? a.data.date)?.getTime() ?? 0;
      const tb = (b.data.updatedAt ?? b.data.date)?.getTime() ?? 0;
      return tb - ta;
    })
    .slice(0, 40);

  const site = context.site ?? new URL(SITE_URL);

  return rss({
    title: 'GenshinTop — гайды Genshin Impact',
    description:
      'Свежие и обновлённые гайды по Genshin Impact: баннеры, патчи, промокоды, тир-листы и советы для игроков.',
    site,
    items: sorted.map((e) => {
      const slug = contentSlugFromId(e.id);
      return {
        link: `/guides/${slug}`,
        title: e.data.title,
        description: cleanMetaDescription(e.data.summary, e.data.title, 280),
        pubDate: e.data.updatedAt ?? e.data.date ?? new Date(),
      };
    }),
  });
};
