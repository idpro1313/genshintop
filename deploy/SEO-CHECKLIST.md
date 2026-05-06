# SEO после деплоя — чек-лист GenshinTop

Краткий список действий после выката сборки на **genshintop.ru** (или другой прод).

## Техничка

1. Открыть `https://ВАШ_ДОМЕН/sitemap-index.xml` — ожидается XML **200**, не HTML главной.
2. Открыть `https://ВАШ_ДОМЕН/robots.txt` — директива `Sitemap` указывает на доступный индекс.
3. Проверить `https://ВАШ_ДОМЕН/lootbar` — контент хаба LootBar, не дубль главной.
4. Проверить несуществующий URL — **404** и страница `/404.html`, не подмена на главную.
5. Убедиться, что контейнер/статика собраны из актуального коммита (`docker compose build` или копирование `dist/`).

## Поисковые кабинеты

1. **Яндекс.Вебмастер** — добавить сайт, подтвердить права, отправить `sitemap-index.xml`.
2. **Google Search Console** — то же; проверить покрытие и ошибки sitemap.
3. В Метрике завести цели по событиям `reachGoal` для кликов по элементам с `data-reach-goal` (см. ниже).

### Имена целей LootBar / партнёрские CTA

Добавьте в Яндекс.Метрике цели типа «JavaScript-событие» с идентификаторами:

| Имя события (`reachGoal`) | Где используется |
|---------------------------|------------------|
| `lootbar_banner_click` | Плашка `LootBarPromoBanner` — CTA «Получить скидку» |
| `lootbar_hero_cta` | Хаб и мини-hero подстраниц — «Перейти на LootBar» |
| `lootbar_coupon_cta` | Карточка купона — «Активировать на LootBar» |
| `lootbar_coupon_copy` | Кнопка «Копировать код» (если код задан в данных) |
| `lootbar_coupon_empty` | Заглушка «акции на LootBar» при пустом списке купонов |
| `lootbar_table_cta` | Заглушка таблицы цен — открыть витрину |
| `lootbar_calc_cta` | Калькулятор молитв — «Открыть на LootBar» |
| `lootbar_bottom_crystals` | Финальный блок хаба — кристаллы |
| `lootbar_bottom_welkin` | Финальный блок хаба — луна |
| `lootbar_hub_cta_top` / `lootbar_hub_cta_bottom` | устарели на хабе; могли остаться в старых сборках |
| `lootbar_promo_cta`, `lootbar_crystals_cta`, `lootbar_welkin_cta`, `lootbar_howto_cta`, `lootbar_safety_cta` | Тематические страницы `/lootbar/*` |

Скрипт подписки на клики: `src/layouts/BaseLayout.astro` (обработчик `data-reach-goal`).

## Итерации

- Через 7–14 дней: отчёт по показам/CTR в Вебмастере и GSC; при необходимости править title/description у коммерческих страниц.
