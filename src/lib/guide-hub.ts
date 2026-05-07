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

function fullText(e: GuideEntry): string {
  const slug = contentSlugFromId(e.id);
  return `${e.data.title} ${slug} ${bodyHint(e)}`.toLowerCase();
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

export function matchHubEvents(e: GuideEntry): boolean {
  const t = fullText(e);
  if (/молитва события/.test(t)) return false;
  return /(^|[^а-я])ивент|event(?!s\.)|игровое событие|временн[оа]е событие|временный режим/.test(
    t,
  );
}

export function matchHubTcg(e: GuideEntry): boolean {
  const t = fullText(e);
  return /tcg|священный призыв|карточн|колод[ау]|инвокаци|geni[uo]s invokation/.test(
    t,
  );
}

export function matchHubDomains(e: GuideEntry): boolean {
  const t = fullText(e);
  return /подземель|подзем[е]|домен[аыу]?\b|фарм артефакт|фарм оружи/.test(t);
}

export function matchHubBosses(e: GuideEntry): boolean {
  const t = fullText(e);
  return /босс[аыу]?\b|еженедельн[ыо]\s+(?:босс|противник)|world boss|босс-?файт/.test(
    t,
  );
}

export function matchHubQuests(e: GuideEntry): boolean {
  const t = fullText(e);
  return /квест архонтов|архонт[\s-]?квест|сюжет.*глав|глава\s+(?:[i]+|\d)|квест легенд|сюжетн[ыа]?\s+квест/.test(
    t,
  );
}
