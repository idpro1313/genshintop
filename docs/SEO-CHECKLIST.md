# SEO после деплоя — чек-лист GenshinTop

Краткий список действий после выката на **genshintop.ru** (или другой прод).

## Техничка

1. Открыть `https://ВАШ_ДОМЕН/sitemap.xml` — ожидается XML **200**, формат Sitemap **0.9** (`urlset`), не HTML главной.
2. Открыть `https://ВАШ_ДОМЕН/robots.txt` — директива **`Sitemap:`** указывает на **`https://genshintop.ru/sitemap.xml`** (один канонический файл).
3. Проверить `https://ВАШ_ДОМЕН/lootbar` — контент хаба LootBar, не дубль главной.
4. Проверить несуществующий URL — **404** через PHP-роутер (не подмена на главную в обход приложения).
5. Убедиться, что контейнер собран из актуального коммита (`docker compose pull` актуального тега/`latest`).
6. **`https://ВАШ_ДОМЕН/rss.xml`** — должен отдавать **404** (RSS в этом стеке не используется); в `<head>` страниц **нет** `link rel="alternate" type="application/rss+xml"`.
7. `https://ВАШ_ДОМЕН/guides?q=баннер` — строка поиска на каталоге подставляется и фильтрует карточки (соответствие **SearchAction** в JSON-LD).
8. В **sitemap.xml** у URL гайдов и персонажей присутствует **`lastmod`** (генерация при сборке образа / `lib/build-sitemap.php`).

## Поисковые кабинеты

1. **Яндекс.Вебмастер** — добавить сайт, подтвердить права, отправить **`https://genshintop.ru/sitemap.xml`**.
2. **Google Search Console** — то же; проверить покрытие и ошибки sitemap.
3. В Метрике завести цели по событиям `reachGoal` для элементов с `data-reach-goal` (см. ниже).

### Имена целей LootBar / партнёрские CTA

Добавьте в Яндекс.Метрике цели типа «JavaScript-событие» с идентификаторами:

| Имя события (`reachGoal`) | Где используется |
|---------------------------|------------------|
| `lootbar_banner_click` | Плашка — CTA «Получить скидку» (`lib/lootbar_banner.php`) |
| `lootbar_hero_cta` | Хаб и мини-hero подстраниц — «Перейти на LootBar» |
| `lootbar_coupon_cta` | Карточка купона — «Активировать на LootBar» |
| `lootbar_coupon_copy` | Кнопка «Копировать код» |
| `lootbar_coupon_empty` | Заглушка при пустом списке купонов |
| `lootbar_table_cta` | Заглушка таблицы цен |
| `lootbar_calc_cta` | Калькулятор молитв |
| `lootbar_bottom_crystals` | Финальный блок хаба — кристаллы |
| `lootbar_bottom_welkin` | Финальный блок хаба — луна |
| `lootbar_promo_cta`, `lootbar_crystals_cta`, `lootbar_welkin_cta`, `lootbar_howto_cta`, `lootbar_safety_cta` | Тематические страницы `/lootbar/*` |

Подписка на клики: **`lib/layout.php`** (обработчик `[data-reach-goal]`).

## Итерации

- Через 7–14 дней: отчёт по показам/CTR в Вебмастере и GSC; при необходимости править title/description у коммерческих страниц.
