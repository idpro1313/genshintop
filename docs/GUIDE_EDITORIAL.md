# Редакционный стандарт гайдов (GenshinTop)

Канон: **`info/guides/*.md`**. Архив **`content/guides-archive/`** — только источник для адаптации, не второй канон на ту же тему.

## Обязательный frontmatter

- **`title`**, **`category`**, **`sourceSlug`** (совпадает с именем файла без `.md`).
- **`planTrack`**: `basics` | `advanced` | `walkthroughs` — столп [`PLAN.md`](PLAN.md); распределение slug см. [`info/README.md`](../info/README.md).

Рекомендуется: **`topic`**, **`status`**, **`audience`**, **`gameVersion`**, **`summary`** (одна строка), **`updatedAt`**, **`reviewedAt`**, **`relatedGuides`** (только существующие slug), при необходимости **`sources`**.

## Содержание

- Один материал — одна зона ответственности; общие тезисы не дублируем, отсылаем к «ведущему» slug по [info/README.md](../info/README.md).
- Один **`#`** заголовок в теле не использовать (заголовок страницы из `title`).
- Внутренние ссылки только на **`/guides/<slug>`**, которые реально есть в репозитории.

## Соответствие PLAN

Три столпа обучения отражены в **`planTrack`** и хабах **`/guides/game-basics`**, **`/guides/advanced-guides`**, **`/guides/quest-walkthroughs`** (плюс тематические хабы баннеров, экономики и т.д.).
