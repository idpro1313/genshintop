/** GRACE[M-WEBSITE][lootbar-data][BLOCK_LOOTBAR_DATA] — данные витрины LootBar (заполняет партнёр; пустые значения = честный fallback на страницах). */

export interface LootBarCoupon {
  code?: string;
  title: string;
  percent: number;
  maxDiscountRub?: number;
  minOrderRub?: number;
  validDays?: number;
  conditions: string[];
}

export interface LootBarPriceRow {
  /** Человекочитаемая подпись, часто с номиналом («Кристаллы 6480»). */
  product: string;
  officialRub: number;
  lootbarRub: number;
}

/** Заполняется после согласования с LootBar. */
export const lootbarCoupons: LootBarCoupon[] = [];

/** Пара строк для таблицы «в игре / на LootBar» — только после подтверждения цифр. */
export const lootbarPrices: LootBarPriceRow[] = [];

/**
 * Для hero/баннера: максимальная заявленная скидка топ-апа относительно цены в игре (%).
 * Пока `null` — без конкретных процентов в интерфейсе.
 */
export const lootbarMaxDiscountPercent: number | null = null;

/** Опционально подсветка одной строки прайса в hero. */
export const lootbarHighlightedProduct: LootBarPriceRow | null = null;

/** ISO-дата обновления прайса (страница / JSON-LD). */
export const lootbarPricesUpdatedAt: string | null = null;

/** Тексты шагов для визуального списка и HowTo в JSON-LD хаба. */
export const lootbarHowToStepTexts: string[] = [
  'Откройте страницу Genshin Impact на LootBar.gg по партнёрской ссылке с GenshinTop.',
  'Зарегистрируйтесь или войдите в аккаунт LootBar, если сервис этого требует.',
  'Проверьте раздел акций, баннеры или купоны в кабинете и при наличии скопируйте код.',
  'Выберите товар и регион аккаунта так, как указано в карточке заказа.',
  'На шаге оплаты примените купон и убедитесь, что скидка отобразилась до полного подтверждения платежа.',
];

/** Парсинг номинала из подписи (для калькулятора молитв). */
export function lootbarProductQuantity(product: string): number | null {
  const m = product.match(/(\d[\d\s]*)/);
  if (!m) return null;
  const n = parseInt(m[1].replace(/\s/g, ''), 10);
  return Number.isFinite(n) && n > 0 ? n : null;
}
