/** GRACE[M-WEBSITE][partners][BLOCK_PARTNERS] — партнёрские ссылки (аффилиат). Не смешивать с игровыми CDN. */

const LOOTBAR_GENSHIN_PATH = '/ru/top-up/genshin-impact';

/** База без завершающего слэша — собираем через URL API. */
const LOOTBAR_ORIGIN = 'https://lootbar.gg';

export type LootBarUtmCampaign =
  | 'genshin_topup'
  | 'lootbar_hub'
  | 'lootbar_howto'
  | 'lootbar_promo'
  | 'lootbar_crystals'
  | 'lootbar_welkin'
  | 'lootbar_safety'
  | 'lootbar_banner';

/**
 * Партнёрская ссылка на топ-ап Genshin Impact на LootBar.gg.
 * UTM не заменяют aff_short; лишь помогают аналитике (если сервис их принимает).
 */
export function lootbarGenshinTopupUrl(campaign: LootBarUtmCampaign = 'genshin_topup'): string {
  const u = new URL(LOOTBAR_GENSHIN_PATH, LOOTBAR_ORIGIN);
  u.searchParams.set('aff_short', 'dandnagers');
  u.searchParams.set('utm_source', 'genshintop');
  u.searchParams.set('utm_medium', 'referral');
  u.searchParams.set('utm_campaign', campaign);
  return u.toString();
}

/** Совместимость с существующим импортом. */
export const LOOTBAR_GENSHIN_TOPUP_URL = lootbarGenshinTopupUrl('genshin_topup');
