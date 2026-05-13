# content/ — опорный корпус GenshinTop (новая редакция)

Каталог построен жёстко по [docs/Plan.md](../docs/Plan.md). Каждый раздел плана получает свою папку с `_index.md`, шаблоном и наполнением. Все тексты написаны заново — без дословного копирования из `info/`, `archive/` и веб-источников.

## Что внутри

| Папка | Раздел PLAN |
|-------|-------------|
| [`characters/`](characters/) | Персонажи (полные wiki-карточки) |
| [`weapons/`](weapons/) | Оружие (отдельный .md на каждое) |
| [`artifacts/`](artifacts/) | Артефакты (отдельный .md на каждый сет) |
| [`materials/`](materials/) | Ресурсы и материалы |
| [`enemies/`](enemies/) | Противники (бестиарий по семействам и боссам) |
| [`guides/`](guides/) | Гайды: `basics`, `advanced`, `walkthroughs` |
| [`tools/`](tools/) | Инструменты и карты (описательные страницы) |
| [`world/`](world/) | Мир и Лор: регионы, лор, фракции, NPC |
| [`news/`](news/) | События, анонсы, баннеры, патчи |
| [`community/`](community/) | Форум, пользовательские гайды, FAQ, лучшие гайды |

## Стандарты

- Конвенции редакции: [`STYLE.md`](STYLE.md).
- YAML-шаблоны frontmatter: [`_templates/`](_templates/).
- Источники: `info/`, `archive/` + веб (см. [`STYLE.md`](STYLE.md) → раздел «Атрибуция»).

## Статусы материалов

В каждом файле в frontmatter есть поле `status`:

- `live` — материал готов и проверен;
- `draft` — каркас и факты есть, требуется редактура и/или сверка с актуальным патчем;
- `stub` — только frontmatter и заголовки, ждёт наполнения.

## Связь с сайтом

Сейчас сайт читает контент из [`info/`](../info/) через [`lib/ContentRepository.php`](../lib/ContentRepository.php). Папка `content/` — параллельный каталог под будущее переключение. До явного миграционного коммита `info/` остаётся живым.

## Карта прогресса

Сводка по разделам (обновляется по завершении батча):

| Раздел | live | draft | stub | total |
|--------|-----:|------:|-----:|------:|
| characters | 114 | 1 | 0 | 115 |
| weapons | 57 | 0 | 0 | 57 |
| artifacts | 38 | 0 | 0 | 38 |
| materials | 20 | 1 | 0 | 21 |
| enemies | 20 | 0 | 0 | 20 |
| guides | 20 | 17 | 0 | 37 |
| tools | 4 | 0 | 0 | 4 |
| world/regions | 7 | 0 | 0 | 7 |
| world/lore | 6 | 0 | 0 | 6 |
| world/factions | 16 | 8 | 0 | 24 |
| world/npc | 7 | 0 | 2 | 9 |
| news/events | 0 | 0 | 0 | 0 |
| news/announcements | 0 | 0 | 0 | 0 |
| news/banners | 19 | 0 | 0 | 19 |
| news/patches | 42 | 1 | 0 | 43 |
| community | 1 | 3 | 0 | 4 |

Снимок: после **Wave 2** (2026-05-13). Wave 3 закроет оставшиеся `draft`/`stub` и расширит `news/events`, `news/announcements`, `world/npc`.
