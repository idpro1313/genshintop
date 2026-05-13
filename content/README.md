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

| Раздел | live | draft | stub |
|--------|-----:|------:|-----:|
| characters | — | — | — |
| weapons | — | — | — |
| artifacts | — | — | — |
| materials | — | — | — |
| enemies | — | — | — |
| guides/basics | — | — | — |
| guides/advanced | — | — | — |
| guides/walkthroughs | — | — | — |
| tools | — | — | — |
| world/regions | — | — | — |
| world/lore | — | — | — |
| world/factions | — | — | — |
| world/npc | — | — | — |
| news/events | — | — | — |
| news/announcements | — | — | — |
| news/banners | — | — | — |
| news/patches | — | — | — |
| community | — | — | — |

Цифры заполняет финальный батч синхронизации.
