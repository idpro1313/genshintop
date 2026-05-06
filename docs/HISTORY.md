# История проекта

Журнал итераций для агентов (правило **project-history** в `.cursor/rules`). Дописывайте запись после каждой завершённой задачи: что сделано, почему, какие файлы затронуты.

## Инициализация из шаблона

### Развёртывание каркаса agentrules в genshintop
- **Что:** в репозиторий с **`gi-database/`** скопирован каркас **`_template`**: `.cursor/rules`, `.kilo`, `grace/**`, `docs/AGENTS.md`, `docs/HISTORY.md`, `VERSION`, корневые **`AGENTS.md`**, **`README.md`**. В `grace/*` и **`docs/AGENTS.md`** подставлены имя проекта **genshintop** и описание контентной базы.
- **Почему:** единые правила Cursor/Kilo и GRACE для дальнейшей доработки корпуса и возможного кода.
- **Файлы:** `grace/requirements/requirements.xml`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/AGENTS.md`, `docs/HISTORY.md` (этот раздел).

## Фаза: сайт GenshinTop (Astro)

### Реализация genshintop.ru (MVP)
- **Что:** Astro 5 + Tailwind + sitemap; страницы главная, персонажи, гайды, о проекте; дизайн в стилистике Teyvat; SEO (meta, OG, JSON-LD); скрипты `content:audit`, `content:migrate`, `content:verify`; документация деплоя под Traefik (`deploy/`); заглушки контента до полной миграции.
- **Почему:** план публичного SEO-сайта с переносом знаний из `gi-database` в `src/content/`.
- **Файлы:** `package.json`, `astro.config.mjs`, `tailwind.config.mjs`, `tsconfig.json`, `src/**`, `public/**`, `scripts/*.ts`, `deploy/**`, `reports/.gitkeep`, `VERSION`, `README.md`, `docs/AGENTS.md`, `grace/**/*.xml`
- **Решение:** Content Collections с `glob`-лоадером; фильтры каталогов на клиенте; место под партнёрский блок — текст на главной.

### Docker и скрипт обновления с GitHub (0.1.2)
- **Что:** `Dockerfile` (Node build + nginx), `deploy/docker-compose.yml` под Traefik, `deploy/nginx-docker.conf`, `deploy/env.example`, `deploy/update-from-github.sh` / `.ps1`, `.dockerignore`; обновлены `deploy/README.md`, корневой `README.md`, `docs/AGENTS.md`, `grace/technology/technology.xml`.
- **Почему:** запуск сайта в Docker и обновление с GitHub одной командой на сервере.
- **Файлы:** `Dockerfile`, `deploy/*`, `.dockerignore`, `.gitignore` (игнор `deploy/.env`), `VERSION`, `package.json`
- **Решение:** сборка статики внутри образа; `deploy/.env` не в git.

### Включение сгенерированного контента и lockfile в репозиторий
- **Что:** в git добавлены `package-lock.json`, `reports/migration-report.json`, полный набор `src/content/characters/*.md` и `src/content/guides/*.md` после `content:migrate`; удалены заглушки `genshintop-placeholder.md` в коллекциях.
- **Почему:** фиксация состояния после миграции для Docker/CI и воспроизводимых сборок (`npm ci`).
- **Файлы:** `package-lock.json`, `reports/migration-report.json`, `src/content/**`, `docs/HISTORY.md`
- **Решение:** контент — канонический источник для сайта; lockfile обязателен для стабильного `npm ci` в Dockerfile.

### Исправление YAML frontmatter relatedWeapons (0.1.1)
- **Что:** в `relatedWeapons` попадал slug `-` (файл `-.md` в `02_weapons`), из-за чего строка `  - -` в YAML читалась как вложенный массив и ломала Zod. Исправлены генератор frontmatter (элементы списков через `JSON.stringify`), фильтр slug’ов в `process-content.ts`, препроцессор в `content.config.ts`, удалена битая строка в `al-khaytam.md` и `varka.md`.
- **Почему:** ошибка сборки `InvalidContentEntryDataError` при `astro build`.
- **Файлы:** `scripts/process-content.ts`, `src/content.config.ts`, `src/content/characters/al-khaytam.md`, `src/content/characters/varka.md`, `VERSION`, `package.json`, `docs/HISTORY.md`
- **Решение:** не допускать slug `-` в связанных списках; списки в YAML только как явные JSON-строки.

### Доработка вёрстки статей
- **Что:** ограничена максимальная ширина текста статьи (`prose max-w-3xl`) для читаемости.
- **Почему:** длинные строки на широких экранах ухудшают UX.
- **Файлы:** `src/layouts/ArticleLayout.astro`, `docs/HISTORY.md`
- **Решение:** оставить `prose` + `max-w-3xl` без центрирования всей страницы.

### Аудит оформления страниц гайдов
- **Что:** статически проверены 424 Markdown-страницы `src/content/guides/*.md`, схема коллекции `src/content.config.ts`, список и страница гайда.
- **Почему:** запрос пользователя проверить все страницы гайдов на правильное оформление и ошибки.
- **Файлы:** `src/content/guides/*.md`, `src/content.config.ts`, `src/pages/guides/index.astro`, `src/pages/guides/[slug].astro`, `src/layouts/ArticleLayout.astro`, `src/components/GuideCard.astro`, `docs/HISTORY.md`
- **Решение:** выявлены системные проблемы миграции: дублирующие H1, Markdown/навигация в `summary`, legacy-ссылки `../index.html`/`#`, пустые изображения, кириллические и технические slug; автоматические исправления не выполнялись без отдельного подтверждения.

### Чистка оформления страниц гайдов
- **Что:** добавлен и применён `scripts/cleanup-guides-formatting.ps1`; нормализованы 424 Markdown-гайда: убраны дублирующие H1, legacy-хлебные крошки, пустые рекламные изображения, одиночные артефакты `[`/`**`, слитые ссылки и Markdown в `summary`; улучшены классы `ArticleLayout`.
- **Почему:** пользователь попросил сделать страницы гайдов красивыми без потери контента.
- **Файлы:** `scripts/cleanup-guides-formatting.ps1`, `src/content/guides/*.md`, `src/layouts/ArticleLayout.astro`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** выбран консервативный форматтер, который удаляет только миграционный мусор и дубли, а смысловой текст сохраняет; скрипт оставлен ASCII-safe для Windows PowerShell и проверен повторным запуском без новых изменений; сборку приложения локально не запускали по правилу проекта.

### SEO: OG, schema.org, очистка описаний (0.1.4)
- **Что:** добавлены `src/lib/seo.ts` (canonical, очистка meta description, фрагменты JSON-LD), дефолтное изображение `public/og-default.svg`; расширены `Seo.astro`, `BaseLayout`, `ArticleLayout` — `og:image`, размеры, `robots`, `article:published_time`/`modified_time`; страницы статей — `@graph` с Organization, `Article`/`BlogPosting`, `BreadcrumbList`; каталоги — `CollectionPage` + `ItemList`, SEO-блоки с внутренними ссылками; карточки гайдов и `process-content.ts` используют `cleanMetaDescription` для сниппетов.
- **Почему:** план усиления видимости в поиске (Яндекс/Google): корректные сниппеты, соц. превью, структурированные данные.
- **Файлы:** `src/lib/seo.ts`, `public/og-default.svg`, `src/components/Seo.astro`, `src/layouts/BaseLayout.astro`, `src/layouts/ArticleLayout.astro`, `src/pages/index.astro`, `src/pages/about.astro`, `src/pages/characters/index.astro`, `src/pages/guides/index.astro`, `src/pages/characters/[slug].astro`, `src/pages/guides/[slug].astro`, `src/components/GuideCard.astro`, `scripts/process-content.ts`, `VERSION`, `package.json`, `docs/AGENTS.md`, `docs/HISTORY.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `grace/technology/technology.xml`
- **Решение:** единый дефолт OG до появления изображений в контенте; даты гайдов дублируются в `modified` при отсутствии отдельного поля.

### Нормализация гайдов: таксономия, аудит, UI (0.1.6)
- **Что:** добавлены `src/lib/guide-taxonomy.ts` (темы `topic`, `status`, `audience`, эвристики `effective*`), расширена схема коллекции `guides` в `src/content.config.ts`; скрипт `scripts/audit-guides-content.ts` и `npm run content:audit-guides` → `reports/guides-audit.json`; миграция `scripts/process-content.ts` (транслитерация slug, дата из текста, поля topic/status/audience/gameVersion, улучшенный summary, классификация «железа»); доработан `scripts/cleanup-guides-formatting.ps1` (склейки `**…**####`, summary после «Содержание»); каталог и карточки гайдов с фильтрами по типу, теме и статусу; страница гайда с бейджами, `updatedAt` в schema.org, блок «Связанные материалы» по `relatedGuides`.
- **Почему:** план нормализации набора гайдов для игроков и редакции.
- **Файлы:** `src/lib/guide-taxonomy.ts`, `src/content.config.ts`, `scripts/process-content.ts`, `scripts/audit-guides-content.ts`, `scripts/cleanup-guides-formatting.ps1`, `package.json`, `VERSION`, `src/components/GuideCard.astro`, `src/pages/guides/index.astro`, `src/pages/guides/[slug].astro`, `src/pages/index.astro`, `docs/AGENTS.md`, `docs/HISTORY.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`
- **Решение:** необязательные поля frontmatter не ломают существующие 424 файла; UI и аудит опираются на эвристики до следующего `content:migrate`.

### Яндекс.Метрика для всех страниц (0.1.7)
- **Что:** встроен счётчик Яндекс.Метрики `109020836` в общий layout сайта: inline-скрипт в `<head>` и `noscript` fallback в `<body>`.
- **Почему:** пользователь попросил добавить счётчик аналитики на сайт.
- **Файлы:** `src/layouts/BaseLayout.astro`, `VERSION`, `package.json`, `package-lock.json`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** счётчик размещён в `BaseLayout`, чтобы он попадал на все Astro-страницы без дублирования в маршрутах.

### Партнёрский раздел LootBar (0.1.8)
- **Что:** добавлен маршрут `/lootbar` с SEO/JSON-LD, внешними CTA на LootBar.gg и дисклеймерами; добавлена глобальная плашка под шапкой, пункт «Пополнение» в навигации, ссылка на главной и централизованная партнёрская ссылка `LOOTBAR_GENSHIN_TOPUP_URL`.
- **Почему:** пользователь попросил сделать баннер/раздел LootBar по аналогии с `lootbar-discounts` на dandangers.ru, но с переходом на партнёрскую ссылку `https://lootbar.gg/ru/top-up/genshin-impact?aff_short=dandnagers`.
- **Файлы:** `src/pages/lootbar.astro`, `src/components/LootBarPromoBanner.astro`, `src/components/Header.astro`, `src/layouts/BaseLayout.astro`, `src/pages/index.astro`, `src/lib/partners.ts`, `VERSION`, `package.json`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `grace/requirements/requirements.xml`, `docs/HISTORY.md`
- **Решение:** плашка ведёт на внутренний SEO-раздел `/lootbar`, а внешняя партнёрская ссылка с `aff_short=dandnagers` используется только на странице раздела и помечена `rel="noopener noreferrer sponsored"`.
