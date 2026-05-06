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

### SEO: хабы, кластер LootBar, nginx 404/XML, E-E-A-T (0.2.0)
- **Что:** статические хабы `/guides/banners|codes|patches|newbie|economy|tier-list`, хабы `/characters` по стихии/редкости/оружию; кластер `/lootbar` с подстраницами (how-to, промокод, кристаллы, луна, безопасность); страницы `editorial-policy`, `partnership-disclosure`, `contacts`, `content-updates`; `404.astro`; schema.org `FAQPage`/`HowTo` где уместно; `faqPageSchema`/`howToSchema` в `seo.ts`; партнёрские ссылки через `lootbarGenshinTopupUrl` + UTM; Яндекс.Метрика `reachGoal` по `[data-reach-goal]`; поля `reviewedAt`/`sources` у гайдов; вывод источников и даты проверки на странице гайда; nginx: `error_page 404 /404.html`, отдельный `location` для `.xml`/`.txt` без fallback на `index.html`; `deploy/SEO-CHECKLIST.md`; правки `promocodes.md`, редакционная вставка в `skam-izbejat.md`.
- **Почему:** реализация плана SEO-аудита: индексация, семантические кластеры, доверие к affiliate-контенту, отсутствие подмены sitemap/lootbar главной при ошибочном nginx.
- **Файлы:** `deploy/nginx-docker.conf`, `deploy/README.md`, `deploy/SEO-CHECKLIST.md`, `src/pages/404.astro`, `src/pages/lootbar/**`, `src/pages/guides/banners.astro` (и др. хабы), `src/pages/characters/pyro.astro` (и др.), `src/components/GuideHubPage.astro`, `src/components/CharacterElementHub.astro`, `src/components/CharacterFilterHub.astro`, `src/components/LootBarOutboundLink.astro`, `src/components/LootBarDisclosure.astro`, `src/layouts/BaseLayout.astro`, `src/components/Footer.astro`, `src/components/LootBarPromoBanner.astro`, `src/pages/index.astro`, `src/pages/about.astro`, `src/pages/guides/index.astro`, `src/pages/characters/index.astro`, `src/pages/guides/[slug].astro`, `src/lib/guide-hub.ts`, `src/lib/character-hub.ts`, `src/lib/partners.ts`, `src/lib/seo.ts`, `src/content.config.ts`, `src/content/guides/promocodes.md`, `src/content/guides/skam-izbejat.md`, `docs/AGENTS.md`, `grace/**`, `VERSION`, `package.json`
- **Решение:** удалён монолитный `lootbar.astro` в пользу `lootbar/index.astro`; локальную сборку для проверки не запускали (правило проекта).

### Уточнение локального типа GuideHubPage (0.2.0)
- **Что:** `GuideHubId` в `src/components/GuideHubPage.astro` оставлен локальным типом без экспорта.
- **Почему:** тип используется внутри компонента и не должен расширять внешний контракт модуля.
- **Файлы:** `src/components/GuideHubPage.astro`, `docs/HISTORY.md`
- **Решение:** версия не менялась, так как это точечная синтаксическая правка в рамках `0.2.0`.

### Правило для GitHub Actions (0.2.1)
- **Что:** добавлено Cursor-правило `.cursor/rules/github-actions.mdc` для workflow-файлов `.github/workflows/**/*.yml|yaml`: `npm ci`, секреты через `secrets.*`, Docker build/push в CI и осторожность с production deploy.
- **Почему:** пользователь попросил добавить правило для GitHub Actions; проект планирует ускорять Docker-деплой через CI вместо долгой сборки на сервере.
- **Файлы:** `.cursor/rules/github-actions.mdc`, `VERSION`, `package.json`, `package-lock.json`, `docs/HISTORY.md`
- **Решение:** правило сделано file-specific, чтобы включаться при работе с GitHub Actions; версия поднята PATCH до `0.2.1`.

### GitHub Actions Docker image workflow (0.2.2)
- **Что:** добавлен `.github/workflows/docker-image.yml`: build Docker-образа через Buildx, cache GitHub Actions, push в `ghcr.io/${{ github.repository }}` на `main`, pull request только собирает без публикации.
- **Почему:** пользователь уточнил, что нужен именно файл workflow `genshintop/.github/workflows/docker-image.yml in main`, а не Cursor-правило.
- **Файлы:** `.github/workflows/docker-image.yml`, `VERSION`, `package.json`, `package-lock.json`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** автодеплой на production не добавлялся без согласования секретов/окружения; используется штатный `GITHUB_TOKEN` для GHCR.

### Серверный запуск через GHCR image (0.2.3)
- **Что:** `deploy/docker-compose.yml` переведён с локального `build` на `SITE_IMAGE`/`ghcr.io/idpro1313/genshintop:latest`; `update-from-github.sh` и `.ps1` теперь делают `docker compose pull` + `up -d`; обновлены `deploy/env.example` и `deploy/README.md`.
- **Почему:** пользователь попросил изменить скрипт запуска после добавления GitHub Actions, чтобы не ждать долгую сборку на сервере.
- **Файлы:** `deploy/docker-compose.yml`, `deploy/update-from-github.sh`, `deploy/update-from-github.ps1`, `deploy/env.example`, `deploy/README.md`, `VERSION`, `package.json`, `package-lock.json`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** локальная сборка на сервере удалена из основного сценария; если GHCR package приватный, сервер должен быть залогинен в `ghcr.io`.

### Удаление PowerShell-скриптов (0.2.4)
- **Что:** удалены все `.ps1` скрипты из проекта: `deploy/update-from-github.ps1` и `scripts/cleanup-guides-formatting.ps1`; активная документация и GRACE больше не ссылаются на PowerShell-сценарии.
- **Почему:** пользователь уточнил, что `.ps1` скрипты в проекте вообще не нужны.
- **Файлы:** `deploy/update-from-github.ps1`, `scripts/cleanup-guides-formatting.ps1`, `deploy/README.md`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `VERSION`, `package.json`, `package-lock.json`, `docs/HISTORY.md`
- **Решение:** оставлен только Linux-сценарий `deploy/update-from-github.sh`; версия поднята до `0.2.4`.

### Исполняемый бит для update-from-github.sh (0.2.5)
- **Что:** для `deploy/update-from-github.sh` выставлен git executable bit `100755`.
- **Почему:** пользователь попросил передать бит исполняемости для `.sh` в GitHub, чтобы скрипт запускался на сервере без ручного `chmod`.
- **Файлы:** `deploy/update-from-github.sh`, `VERSION`, `package.json`, `package-lock.json`, `grace/knowledge-graph/knowledge-graph.xml`, `docs/HISTORY.md`
- **Решение:** использован `git update-index --chmod=+x`; версия поднята PATCH до `0.2.5`.
