// @ts-check
import { defineConfig } from 'astro/config';
import tailwind from '@astrojs/tailwind';
import sitemap from '@astrojs/sitemap';

const SITE_HOST = 'https://genshintop.ru';

export default defineConfig({
  site: SITE_HOST,
  integrations: [
    tailwind({ applyBaseStyles: false }),
    sitemap({
      filter: (page) => {
        if (!page) return false;
        if (page.includes('/404')) return false;
        if (page.includes('/_placeholder')) return false;
        return true;
      },
      serialize(item) {
        if (!item) return undefined;
        const url = item.url;
        let priority = 0.5;
        let changefreq = 'monthly';

        if (url === `${SITE_HOST}/`) {
          priority = 1.0;
          changefreq = 'daily';
        } else if (/\/(guides|characters|lootbar)\/?$/.test(url)) {
          priority = 0.9;
          changefreq = 'weekly';
        } else if (/\/lootbar\/[^/]+\/?$/.test(url)) {
          priority = 0.85;
          changefreq = 'weekly';
        } else if (/\/guides\/(banners|patches|codes|newbie|economy|tier-list|events|tcg|domains|bosses|quests)\/?$/.test(url)) {
          priority = 0.85;
          changefreq = 'weekly';
        } else if (/\/characters\/(pyro|hydro|electro|cryo|anemo|geo|dendro|sword|claymore|polearm|catalyst|bow|4-star|5-star)\/?$/.test(url)) {
          priority = 0.85;
          changefreq = 'weekly';
        } else if (/\/(guides|characters)\/[^/]+\/?$/.test(url)) {
          priority = 0.7;
          changefreq = 'monthly';
        } else if (/\/regions(\/[^/]+)?\/?$/.test(url)) {
          priority = 0.7;
          changefreq = 'monthly';
        } else if (/\/(about|editorial-policy|partnership-disclosure|contacts|content-updates)\/?$/.test(url)) {
          priority = 0.4;
          changefreq = 'monthly';
        }

        return {
          ...item,
          priority,
          changefreq,
        };
      },
    }),
  ],
  compressHTML: true,
});
