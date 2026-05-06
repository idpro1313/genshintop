/**
 * GRACE[M-WEBSITE][guide-hub][BLOCK_GUIDE_HUB]
 * PURPOSE: Фильтры коллекции guides для статических хабов /guides/*.
 */
import type { CollectionEntry } from 'astro:content';
import { contentSlugFromId } from './slug';
import type { GuideCategory } from './guide-taxonomy';
import {
  effectiveGuideTopic,
} from './guide-taxonomy';

export type GuideEntry = CollectionEntry<'guides'>;

function bodyHint(e: GuideEntry): string {
  return e.data.summary ?? '';
}

export function matchHubBanners(e: GuideEntry): boolean {
  const cat = e.data.category as GuideCategory;
  const slug = `${contentSlugFromId(e.id)}.md`;
  const topic = effectiveGuideTopic(e.data.topic, cat, slug, bodyHint(e));
  return cat === 'banner' || topic === 'banner';
}

export function matchHubCodes(e: GuideEntry): boolean {
  const cat = e.data.category as GuideCategory;
  const slug = `${contentSlugFromId(e.id)}.md`;
  const topic = effectiveGuideTopic(e.data.topic, cat, slug, bodyHint(e));
  return cat === 'codes' || topic === 'codes';
}

export function matchHubPatches(e: GuideEntry): boolean {
  const cat = e.data.category as GuideCategory;
  const slug = `${contentSlugFromId(e.id)}.md`;
  const topic = effectiveGuideTopic(e.data.topic, cat, slug, bodyHint(e));
  return cat === 'patch' || topic === 'patch';
}

export function matchHubNewbie(e: GuideEntry): boolean {
  const cat = e.data.category as GuideCategory;
  const slug = `${contentSlugFromId(e.id)}.md`;
  const topic = effectiveGuideTopic(e.data.topic, cat, slug, bodyHint(e));
  return cat === 'newbie' || topic === 'newbie';
}

export function matchHubEconomy(e: GuideEntry): boolean {
  const cat = e.data.category as GuideCategory;
  const slug = `${contentSlugFromId(e.id)}.md`;
  const topic = effectiveGuideTopic(e.data.topic, cat, slug, bodyHint(e));
  const t = `${e.data.title} ${slug} ${bodyHint(e)}`.toLowerCase();
  return (
    topic === 'economy' ||
    /примогем|молитв|донат|крутк|genesis|кристалл|пополн/i.test(t)
  );
}

export function matchHubTierList(e: GuideEntry): boolean {
  const cat = e.data.category as GuideCategory;
  const slug = `${contentSlugFromId(e.id)}.md`;
  const topic = effectiveGuideTopic(e.data.topic, cat, slug, bodyHint(e));
  const t = `${e.data.title} ${bodyHint(e)}`.toLowerCase();
  return cat === 'tier' || topic === 'party' || /тир[\s-]?лист|tier/i.test(t);
}
