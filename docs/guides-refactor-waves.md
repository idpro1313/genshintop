# Волны редакционного рефакторинга гайдов

Дополняет [`docs/GUIDE_EDITORIAL.md`](GUIDE_EDITORIAL.md). **Архив** массового корпуса после **W1** и **W2** — порядка **~232** файла в **`content/guides-archive/`**; **живые** опорные статьи на сайте — **`info/guides/`** (матрица и счётчики — [`info/README.md`](../info/README.md)). Инвентаризация и кандидаты merge/split — [`reports/guides-refactor-inventory.json`](../reports/guides-refactor-inventory.json).

## Инструменты

| Действие | Команда / артефакт |
|----------|-------------------|
| Инвентаризация slug, метаданных, «лесенки», кандидатов merge/split | `php scripts/guides-refactor-inventory.php` или `pwsh scripts/guides-refactor-inventory.ps1` → `reports/guides-refactor-inventory.json` |
| **W1:** слияние датированных дублей `banner-*` (отчёт `mergeCandidatesByTitle`) | `pwsh scripts/wave-w1-merge-banner-dated.ps1` из корня репозитория |
| Склейка «лесенки» в одном файле (не массово) | `pwsh scripts/normalize-short-line-runons.ps1 -RelativePath info/guides/<slug>.md` |

Поля отчёта (ключевые):

- `guides[].ladderRatio` — доля коротких строк-тела (0–1); высокие значения → приоритет правки.
- `mergeCandidatesByTitle` — одинаковые нормализованные `title`.
- `splitCandidates` — очень длинный текст при малом числе заголовков.

## W0 — Cornerstone / хабы

**Состав:** материалы с максимальной перелинковкой и трафиком: актуальный тир-лист, ивенты, домены, боссы, квесты архонтов, TCG, промокоды; правки по [`GUIDE_EDITORIAL.md`](GUIDE_EDITORIAL.md).

**Готово, когда:** связные абзацы, осмысленные `##`, актуальный frontmatter (`reviewedAt`, `relatedGuides`), нет «лесенки» и битых `[](#)`.

## W1 — Кластеры баннеров и патчей с дублями

**Состав:** файлы `banner-*`, `update-*`, приоритет по `mergeCandidatesByTitle` и по высокому `ladderRatio`.

**Готово, когда:** дубли объединены или явно разведены редакционно; все смены slug с редиректами из [`docker/genshintop-redirects.conf`](../docker/genshintop-redirects.conf).

**Прогресс:** автоматически обработаны датированные дубли **banner** с каноническим slug без даты (**~194** удалённых файла); группы без «бездатного» slug в паре требуют ручного решения (скрипт выводит предупреждение).

## W2 — Остальные баннеры и патчи

**Состав:** хвост `banner-*` / `update-*`, не попавший в W1.

**Готово, когда:** как W1; по возможности единый формат «дата / тип молитвы / персонажи и оружие».

**Пример качества текста:** **`content/guides-archive/paralogism-5-6.md`** — структура **`##`**, оглавление, якоря; при необходимости препроцессинг абзацев через **`normalize-short-line-runons.ps1`**.

**Прогресс:** остаточные дубли из **`mergeCandidatesByTitle`** (без «бездатного» slug в группе) сведены к канону **`banner-essentsiya-ambrozii`** и существующему **`banner-blagoslovenie-plameni`** + **301** в **`docker/genshintop-redirects.conf`**; отчёт **`mergeCandidatesByTitle`** после правок пуст. Дальше — редакторская нормализация **`update-*`** и прочих **`banner-*`** (высокий **`ladderRatio`** у патчей — чаще списки имён, не «лесенка» абзацев).

## W3 — Общий зал (`general`), техно, кириллические slug

**Состав:** прочие категории; файлы с нестандартными именами (кириллица в slug) — нормализация транслитом при сохранении `sourceSlug`.

**Готово, когда:** slug в ASCII; редиректы со старых URL; корректные `topic` / `status`.

## Стоп-критерии волны

- Локально проверить отчёт инвентаризации после правок: доля исправленных slug в выборке не регрессирует по битым ссылкам.
- Вручную или через поиск: отсутствуют ссылки на удалённые `/guides/<slug>`.
- Запись в [`docs/HISTORY.md`](HISTORY.md); версия в [`VERSION`](../VERSION) по правилам репозитория.
