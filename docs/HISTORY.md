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

### Диагностика GHCR unauthorized (0.2.6)
- **Что:** `deploy/update-from-github.sh` теперь при ошибке `docker compose pull` выводит подсказку про публичность GHCR package или `docker login ghcr.io`; `deploy/README.md` дополнен вариантами решения.
- **Почему:** на сервере `docker compose pull` получил `unauthorized` для `ghcr.io/idpro1313/genshintop:latest`.
- **Файлы:** `deploy/update-from-github.sh`, `deploy/README.md`, `VERSION`, `package.json`, `package-lock.json`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** код не хранит токены; доступ решается настройкой публичности package или одноразовым `docker login` на сервере.

### Футер с версией сайта (0.3.0)
- **Что:** `Footer.astro` импортирует корневой `VERSION` и показывает версию сайта в нижней строке футера; версия проекта синхронизирована до `0.3.0`.
- **Почему:** пользователь попросил футер на сайте с версией сайта.
- **Файлы:** `src/components/Footer.astro`, `VERSION`, `package.json`, `package-lock.json`, `docs/AGENTS.md`, `grace/requirements/requirements.xml`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** версия берётся из единого источника `VERSION`, без ручного дублирования в компоненте.

### Яркий баннер LootBar и удаление слова «реклама» (0.4.1)
- **Что:** баннер `LootBarPromoBanner.astro` переделан в яркий «горящий» формат: насыщенный градиент с радиальным glow, золотая граница и shadow, бренд `LootBar.gg` с искрой-SVG, крупный двустрочный оффер, большая CTA «Получить скидку →» с локальным CSS shine-эффектом вместо `animate-pulse`. Полностью убрано слово «реклама» из LootBar-UI: в баннере вместо плашки — мелкая ссылка `партнёрская ссылка` на `/partnership-disclosure`; в `LootBarMiniHero`, `LootBarDisclosure`, hero `lootbar/index.astro` тексты переведены на «партнёрский материал/раздел»; обновлены title/description/тело страницы `partnership-disclosure.astro` (без юр. термина).
- **Почему:** пользователь — текущий баннер тусклый и маленький, а слово «реклама» отпугивало; уточнил, что это не реклама.
- **Файлы:** `src/components/LootBarPromoBanner.astro`, `src/components/LootBarMiniHero.astro`, `src/components/LootBarDisclosure.astro`, `src/pages/lootbar/index.astro`, `src/pages/partnership-disclosure.astro`, `VERSION`, `package.json`, `package-lock.json`, `grace/knowledge-graph/knowledge-graph.xml`, `docs/HISTORY.md`
- **Решение:** PATCH `0.4.0 → 0.4.1` (визуально-текстовое улучшение без новой фичи); прозрачность сохраняется через `rel="sponsored"` и видимую ссылку на раскрытие партнёрства, без слова «реклама».

### Редизайн хаба и баннера LootBar (0.4.0)
- **Что:** добавлен data-слой `src/data/lootbar.ts` (типы купонов и прайса, пустые константы до ответа партнёра); компоненты `LootBarCouponCard`, `LootBarBenefitsGrid`, `LootBarStepsList`, `LootBarPriceTable`, `LootBarPrimogemCalc`, `LootBarMiniHero`; пересобраны `src/pages/lootbar/index.astro` (hero, купоны, шаги, таблица, калькулятор молитв, FAQ, финальный CTA) и `LootBarPromoBanner.astro` (оффер из `lootbarMaxDiscountPercent`, CTA с pulse, пометка «реклама»); подстраницы promokod / kristally / blagoslovenie / kak-popolnit подключены к блокам; безопасность без агрессивного мини-hero; расширены UTM в `src/lib/partners.ts`; чек-лист `deploy/SEO-CHECKLIST.md` с новыми `reachGoal`.
- **Почему:** запрос пользователя — реализовать план продающего формата /lootbar без выдуманных цифр.
- **Файлы:** `src/data/lootbar.ts`, `src/components/LootBar*.astro` (перечисленные), `src/pages/lootbar/*.astro`, `src/lib/partners.ts`, `VERSION`, `package.json`, `package-lock.json`, `deploy/SEO-CHECKLIST.md`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `grace/requirements/requirements.xml`, `docs/HISTORY.md`
- **Решение:** версия `0.3.0` уже была занята футером; для фичи поднят **MINOR** до `0.4.0`. Калькулятор показывается только при непустом прайсе и распарсенных номиналах.

## Фаза 5 — SEO-усиление и закрытие семантических пробелов (0.5.0)

### Тех.SEO-инфраструктура (0.5.0)
- **Что:**
  - `astro.config.mjs`: `sitemap()` теперь с `filter` (исключает `/404`, `/_placeholder`) и `serialize` (priority/changefreq по типу страницы: главная 1.0, хабы 0.9, lootbar/* 0.85, статьи 0.7, regions 0.7, trust 0.4).
  - `public/robots.txt`: добавлен блок `User-agent: Yandex` с `Clean-param` для UTM/affiliate-параметров и `Host: genshintop.ru`.
  - `src/components/Seo.astro`: новые мета-теги `og:image:type`, `og:image:secure_url`, `og:image:alt`, `twitter:image:alt`, и опциональные `yandex-verification` / `google-site-verification` / `mailru-domain` через переменные окружения (`PUBLIC_*` / без префикса).
  - `src/layouts/BaseLayout.astro`: проброс `ogImageAlt`.
  - `deploy/nginx-docker.conf` переписан: расширенный `gzip_types` (включая `application/ld+json`, `font/woff2`, `image/svg+xml`), security headers (`X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`, `X-Frame-Options`), агрессивный `Cache-Control: max-age=31536000 immutable` для `/_astro/*`, `/og/*`, `/fonts/*`, `include /etc/nginx/conf.d/genshintop-redirects.conf*` для slug-редиректов; `Dockerfile` копирует `deploy/genshintop-redirects.conf` (заглушку или сгенерированный) в финальный образ.
- **Файлы:** `astro.config.mjs`, `public/robots.txt`, `src/components/Seo.astro`, `src/layouts/BaseLayout.astro`, `deploy/nginx-docker.conf`, `deploy/genshintop-redirects.conf`, `Dockerfile`.

### OG-картинки и `getOgImageForEntry` (0.5.0)
- **Что:** новый скрипт `scripts/generate-og-images.ts` (devDeps `sharp`): сканирует MD коллекций `characters` и `guides`, рендерит SVG-шаблон с заголовком и категорией/стихией, конвертирует в PNG 1200×630 в `public/og/{collection}/{slug}.png`; пишет манифест `src/data/og-manifest.json`. Также пересобирает `public/og-default.png` из `public/og-default.svg`. В `src/lib/seo.ts` добавлен `getOgImageForEntry(collection, slug)`, отдающий `/og/<col>/<slug>.png` при наличии в манифесте, иначе `DEFAULT_OG_IMAGE_PATH`. Подключён в `src/pages/guides/[slug].astro` и `src/pages/characters/[slug].astro` (в JSON-LD `image` и в OG-метатеги). Заодно исправлена битая UTF-8 строка в `public/og-default.svg`.
- **Файлы:** `scripts/generate-og-images.ts`, `src/lib/seo.ts`, `src/pages/guides/[slug].astro`, `src/pages/characters/[slug].astro`, `src/layouts/ArticleLayout.astro`, `src/data/og-manifest.json`, `public/og-default.svg`, `package.json`.

### Авто-нормализация 424 гайдов (0.5.0)
- **Что:** `scripts/enrich-guides.ts` идемпотентно проходит по `src/content/guides/*.md` и:
  - транслитерирует кириллические slug-имена через свой таблично-русский транслит (минимально совместим с `process-content.ts`), переименовывает файлы и пишет карту `from → to` в `reports/slug-redirects.json` и `deploy/genshintop-redirects.conf` (`rewrite ^/guides/<old>/?$ /guides/<new> permanent;`);
  - заполняет недостающие FM: `topic` (`inferTopic`), `gameVersion` (`extractGameVersion`), `audience` (`inferAudience`), `status` (`inferStatus`), `updatedAt = date`, `reviewedAt = today` (для `active`/`dated`); пересобирает «битый» `summary` (начинается с «Содержание», `ㅤ`, «Молитва события», «Глава …»);
  - добавляет `sources` (HoYoverse + HoYoLAB) для `category: codes|patch`;
  - вычисляет `relatedCharacters` top-5 по упоминаниям имён в title+теле и `relatedGuides` top-5 по совпадению `topic` + ближайшая `gameVersion` (с лёгким бустом активных против архивных);
  - чистит `[label](#)` и `[label]()` → `label`;
  - обновляет `relatedGuides` в guides и characters по карте переименований.
- **Файлы:** `scripts/enrich-guides.ts`, `package.json` (скрипт `content:enrich`).

### Slug-редиректы (0.5.0)
- **Что:** `deploy/genshintop-redirects.conf` (заглушка/генерируемый файл) подключается в `nginx-docker.conf` через `include /etc/nginx/conf.d/genshintop-redirects.conf*`. После запуска `npm run content:enrich` файл наполняется `301`-редиректами для переименованных URL.
- **Файлы:** `deploy/genshintop-redirects.conf`, `deploy/nginx-docker.conf`, `Dockerfile`.

### Внутренняя перелинковка (0.5.0)
- **Что:** `src/pages/guides/[slug].astro` теперь рендерит блок «Связанные персонажи» из `relatedCharacters` (карточки с element/weapon). `src/pages/characters/[slug].astro` рендерит блок «Гайды по персонажу» из `relatedGuides`. Оба блока используют `getCollection` и `contentSlugFromId`. `src/components/Footer.astro` переписан в 4-колоночный layout со ссылками на персонажей по стихии, на хабы гайдов (включая новые `events`/`tcg`/`domains`/`bosses`/`quests`), регионы и LootBar-подстраницы.
- **Файлы:** `src/pages/guides/[slug].astro`, `src/pages/characters/[slug].astro`, `src/components/Footer.astro`.

### Новые хабы и регионы (0.5.0)
- **Что:** в `src/lib/guide-hub.ts` добавлены матчеры `matchHubEvents`, `matchHubTcg`, `matchHubDomains`, `matchHubBosses`, `matchHubQuests`. `src/components/GuideHubPage.astro` поддерживает новые `GuideHubId` и расширен список табов навигации. Новые маршруты: `src/pages/guides/{events,tcg,domains,bosses,quests}.astro`. Новый раздел `/regions`: `src/pages/regions/index.astro` (хаб-карта 7 регионов Тейвата с JSON-LD `CollectionPage` + `ItemList`), `/regions/{sumeru,fontaine,natlan}` (через общий `src/components/RegionPage.astro` с JSON-LD `Place` + `Article`, фильтрацией персонажей по white-list имён, ссылками на связанные хабы). В `Header.astro` добавлен пункт «Регионы».
- **Файлы:** `src/lib/guide-hub.ts`, `src/components/GuideHubPage.astro`, `src/components/RegionPage.astro`, `src/components/Header.astro`, `src/pages/guides/events.astro`, `src/pages/guides/tcg.astro`, `src/pages/guides/domains.astro`, `src/pages/guides/bosses.astro`, `src/pages/guides/quests.astro`, `src/pages/regions/index.astro`, `src/pages/regions/sumeru.astro`, `src/pages/regions/fontaine.astro`, `src/pages/regions/natlan.astro`.

### Стартовые статьи (0.5.0)
- **Что:** опубликованы cornerstone-материалы: `tier-list-aktualniy-6-x.md` (актуальный тир-лист 6.x с командами и ссылкой на архив), `events-aktualnye-6-x.md` (хаб ивентов), `tcg-svyaschennyy-prizyv-semi.md` (правила и тиры карт TCG), `domain-podzemelya-rasspisanie.md` (фарм по дням недели), `bossy-genshin-impact.md` (мировые/еженедельные боссы), `quest-arhontov-roadmap.md` (карта прохождения архонт-квестов). У старого `tier-list-4-8.md` поднят `status: historical`, добавлен `gameVersion: '4.8'`, чистая шапка summary без `ㅤ` и редакционная плашка-ссылка на актуальный тир-лист.
- **Файлы:** `src/content/guides/tier-list-aktualniy-6-x.md`, `src/content/guides/events-aktualnye-6-x.md`, `src/content/guides/tcg-svyaschennyy-prizyv-semi.md`, `src/content/guides/domain-podzemelya-rasspisanie.md`, `src/content/guides/bossy-genshin-impact.md`, `src/content/guides/quest-arhontov-roadmap.md`, `src/content/guides/tier-list-4-8.md`.

### E-E-A-T и расширение schema.org (0.5.0)
- **Что:** в `src/lib/seo.ts` добавлены `editorialTeamPerson()` и `lootbarServiceSchema()`. На страницах статей `author` теперь ссылается на `#editorial-team`, рядом в графе появляется отдельный узел редакции. Бейдж «проверено YYYY-MM-DD» и status уже выводятся на guide-странице из `reviewedAt`/`status`. На `/lootbar/index.astro` добавлен JSON-LD `Service` с вложенным `Offer` (priceCurrency RUB, availability InStock, areaServed RU/BY/KZ/UA, провайдер LootBar.gg). `src/pages/editorial-policy.astro` переписана с разделами «Кто пишет», «Принципы публикации», «Процесс обновления», «Партнёрский контент», «Что мы не делаем» и `AboutPage` JSON-LD с `author=#editorial-team`.
- **Файлы:** `src/lib/seo.ts`, `src/pages/guides/[slug].astro`, `src/pages/characters/[slug].astro`, `src/pages/lootbar/index.astro`, `src/pages/editorial-policy.astro`.

### Производительность для CWV (0.5.0)
- **Что:** удалён блокирующий `@import` Google Fonts из `src/styles/global.css`. В `BaseLayout.astro` добавлены `<link rel="preconnect">` к `fonts.googleapis.com` / `fonts.gstatic.com` и асинхронная загрузка stylesheet через `media="print" onload="this.media='all'"` + `<noscript>` fallback. Скрипт Yandex.Metrika обёрнут в shim-очередь команд `ym(...)` и реальная загрузка `tag.js` отложена до `requestIdleCallback` или `load + 1500ms`. Это снимает критическую нагрузку с LCP, а Метрика всё ещё трекает события (благодаря очереди команд до загрузки тага).
- **Файлы:** `src/styles/global.css`, `src/layouts/BaseLayout.astro`.

### Усиление /lootbar по коммерческим запросам (0.5.0)
- **Что:** новый компонент `src/components/LootBarGlossary.astro` с глоссарием (Кристаллы Сотворения / Genesis Crystals, Благословение Полой Луны / Welkin Moon, Примогемы, Молитвы, Топ-ап, Промокод LootBar) — видимый блок без скрытого текста, со ссылками на подстраницы. На `/lootbar/index.astro` добавлены: глоссарий, расширенный FAQ (12 вопросов, в `<details>` с видимым раскрытием, всё попадает в JSON-LD `FAQPage`), Service+Offer schema, блок «Связанные темы и анкоры» с видимыми внутренними ссылками. На подстраницах `kristally-sotvoreniya`, `blagoslovenie-luny`, `promokod`, `kak-popolnit-genshin-impact` обновлены `title`/`description` (естественные ключи «купить геншин дешевле», «Кристаллы Сотворения», «Благословение Полой Луны», «Welkin Moon», «топ-ап», «UID») и H1, добавлены вводные параграфы с терминами и `<details>`-FAQ блоки. На главной (`src/pages/index.astro`) и на `/about` появился короткий блок «Где купить геншин выгодно» с естественными анкорами.
- **Файлы:** `src/components/LootBarGlossary.astro`, `src/pages/lootbar/index.astro`, `src/pages/lootbar/kristally-sotvoreniya.astro`, `src/pages/lootbar/blagoslovenie-luny.astro`, `src/pages/lootbar/promokod.astro`, `src/pages/lootbar/kak-popolnit-genshin-impact.astro`, `src/pages/index.astro`, `src/pages/about.astro`, `src/lib/seo.ts`.

### VERSION/GRACE/HISTORY (0.5.0)
- **Что:** `VERSION` поднят `0.4.1 → 0.5.0`, `package.json` синхронизирован, `docs/HISTORY.md` дополнен этой записью, `docs/AGENTS.md` отражает новые скрипты (`og:generate`, `content:enrich`), модули (`M-OG-PIPELINE`, расширения `M-WEBSITE` / `M-CONTENT-PIPELINE`) и маршруты (`/regions/*`, `/guides/{events,tcg,domains,bosses,quests}`). GRACE-артефакты обновлены под новую фазу.
- **Файлы:** `VERSION`, `package.json`, `docs/HISTORY.md`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/requirements/requirements.xml`, `grace/verification/verification-plan.xml`.
- **Решение:** MINOR-апдейт (новые большие SEO-системы и маршруты, без ломающих изменений API). Локальная сборка / запуск приложения не выполнялись (правило `no-local-app-verification`); изменения проверены статически через ReadLints. Для рендера OG-картинок и применения slug-редиректов нужен один пользовательский прогон `npm install && npm run content:enrich && npm run og:generate` после деплоя.

### Синхронизация `package-lock.json` для Docker `npm ci` (0.5.1)
- **Что:** пересобран `package-lock.json` под актуальный `package.json`: в lock осталась фаза `0.4.1` без прямого `sharp` в корневых `devDependencies`, тогда как в коммите 0.5.0 в `package.json` добавлен `sharp` и версия `0.5.0` — из‑за этого `npm ci` в Docker завершался с кодом 1. Обновлён lock (включая дерево для `sharp`), поднят PATCH `0.5.0 → 0.5.1` в `VERSION`, `package.json`, корневых полях lock и `grace/knowledge-graph/knowledge-graph.xml`.
- **Почему:** сбой CI/Docker на шаге `RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi`.
- **Файлы:** `package-lock.json`, `package.json`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `docs/HISTORY.md`
- **Решение:** единственный надёжный источник зависимостей для образа — lock, синхронный с манифестом; при добавлении devDependency всегда коммитить обновлённый `package-lock.json`.

## Фаза: визуал Teyvat, копирайт и чистка U+3164 (0.6.0)

### Молодёжный UI и шрифты
- **Что:** шрифт **Onest** (кириллица) как основной гротеск + **Cinzel** для заголовков; усилен фон («звёзды», градиенты), утилита `hero-glow`, pill-навигация и крупные CTA в `Header`; карточки персонажей с `border-l` цвета стихии (`elementCardAccentClass`), гайды с акцентной полосой mint; обновлены `glass-panel`, футер, главная hero и блок LootBar; `prefers-reduced-motion` для shine CTA LootBar.
- **Почему:** запрос — дизайн в духе игры и для молодёжной аудитории; улучшить first impression и воронку LootBar.
- **Файлы:** `tailwind.config.mjs`, `src/styles/global.css`, `src/layouts/BaseLayout.astro`, `src/lib/elements.ts`, `src/components/Header.astro`, `src/components/CharacterCard.astro`, `src/components/GuideCard.astro`, `src/components/Footer.astro`, `src/pages/index.astro`, `src/pages/about.astro`, `src/lib/seo.ts`
- **Решение:** без использования чужого брендинга HoYoverse; только собственная палитра `teyvat.*` / `element.*`.

### SEO и шаблоны
- **Что:** в `stripDescriptionNoise` добавлено удаление **U+3164** (ㅤ) для сниппетов; из `BaseLayout.astro` убраны HTML-комментарии в выдаваемом `<head>`.
- **Файлы:** `src/lib/seo.ts`, `src/layouts/BaseLayout.astro`

### Контент Markdown
- **Что:** символ **ㅤ** (U+3164) удалён из тел и summary затронутых файлов в `src/content/guides/*.md` и `src/content/characters/*.md` (81 файл) — миграционный артефакт без смысловой нагрузки.
- **Почему:** читаемость и единообразие; полная ручная вычитка всех статей остаётся на последующие итерации.

### GRACE и версия
- **Что:** `VERSION` **0.5.1 → 0.6.0** (MINOR: системный UI), синхронизированы `package.json`, `package-lock.json`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, дополнен `docs/AGENTS.md`.
- **Файлы:** `VERSION`, `package.json`, `package-lock.json`, `grace/**`, `docs/AGENTS.md`, `docs/HISTORY.md`

## Фаза: максимизация SEO (0.7.0)

### Sitemap lastmod, RSS, SearchAction, поиск по каталогу, schema, чек-лист
- **Что:** в sitemap через `serialize` добавлен реалистичный `<lastmod>` для гайдов и персонажей (`scripts/sitemap-lastmod.mjs`: приоритет дат frontmatter + mtime файла); подключён `@astrojs/rss`, эндпоинт `/rss.xml` (последние записи гайдов); в `webSiteNode` — `potentialAction` `SearchAction` на `https://genshintop.ru/guides?q={search_term_string}`; на `/guides` — форма/параметр `?q=`, клиентский фильтр по `data-search-haystack` на карточках; JSON-LD `ItemList` на хабе гайдов усечён до 24 свежих элементов при сохранении `numberOfItems`; опциональный `sameAs` у издателя из `PUBLIC_ORGANIZATION_SAME_AS`; в шапке — `theme-color` и `link rel="alternate"` на RSS; обновлены `deploy/SEO-CHECKLIST.md`, `deploy/env.example`, план верификации GRACE.
- **Почему:** план пользователя на усиление SEO без ломки деплоя: свежесть URL в карте сайта, канал подписки, валидный поисковый шаблон, меньший объём разметки на каталоге.
- **Файлы:** `scripts/sitemap-lastmod.mjs`, `astro.config.mjs`, `src/pages/rss.xml.ts`, `src/lib/seo.ts`, `src/layouts/BaseLayout.astro`, `src/components/GuideCard.astro`, `src/pages/guides/index.astro`, `package.json`, `package-lock.json`, `deploy/SEO-CHECKLIST.md`, `deploy/env.example`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `docs/AGENTS.md`, `docs/HISTORY.md`
- **Решение:** MINOR `0.6.0 → 0.7.0` (новые поверхности RSS, lastmod, SearchAction и поведение поиска). Локальный запуск сборки для проверки не выполнялся (правило `no-local-app-verification`).

### VERSION и синхронизация манифестов (0.7.0)
- **Что:** `VERSION` **0.6.0 → 0.7.0**; синхронизированы `package.json` и корневые поля `package-lock.json`; граф знаний уже на `0.7.0`, дополнен `docs/AGENTS.md` и этот журнал.
- **Файлы:** `VERSION`, `package.json`, `package-lock.json`, `docs/AGENTS.md`, `docs/HISTORY.md`

### PHP: PageRenderer, роутер, точка входа, CSS (0.7.1)
- **Что:** реализован **`lib/PageRenderer.php`** со всеми методами, вызываемыми из `Router` (`home`, `guidesIndex`, хабы и статьи гайдов, каталог и хабы персонажей, статьи персонажей, регионы, кластер LootBar); в **`bootstrap.php`** подключён `PageRenderer` до `Router.php`; в **`Router`** добавлен маршрут **`/`** на главную; точка входа **`public/index.php`** загружает конфиг, `OgManifest` и вызывает `Router::dispatch`; добавлен базовый ванильный **`public/css/site.css`** (layout Teyvat, карточки, фильтры, типографика статей); карточка персонажа получила **`data-character-card`**, **`data-element`**/**weapon**/**rarity**/**name** и русские подписи стихий для фильтров каталога.
- **Почему:** продолжение миграции с Astro на PHP: приложение до этого падало из‑за отсутствия `PageRenderer` и неполной маршрутизации главной.
- **Файлы:** `bootstrap.php`, `config.php`, `lib/PageRenderer.php`, `lib/Router.php`, `lib/*.php`, `templates/**`, `content/**`, `data/og-manifest.json`, `public/index.php`, `public/css/site.css`, `VERSION`, `package.json`, `package-lock.json`, `grace/knowledge-graph/knowledge-graph.xml`, `docs/AGENTS.md`, `docs/HISTORY.md`
- **Решение:** PATCH `0.7.0 → 0.7.1`. Контент по-прежнему читается из `content/`; Docker/nginx пока отдаёт статический Astro `dist` — включение PHP-FPM запланировано отдельной фазой. Локальный запуск PHP/nginx для проверки не выполнялся (`no-local-app-verification`).

## Фаза: cutover на PHP + nginx (1.0.0)

### Завершение миграции dandangers-стека (MAJOR 1.0.0)
- **Что:** удалены дерево **`src/`**, **`astro.config.mjs`**, **`tailwind.config.mjs`**, **`scripts/sitemap-lastmod.mjs`**; рантайм сайта — корневой **`Dockerfile`** (php-fpm-alpine + nginx + supervisor), активный nginx — **`docker/nginx-default.conf`**; единый **`public/sitemap.xml`** через **`scripts/build-sitemap.php`** при сборке образа; **`/rss.xml`** — 404 в **`lib/Router.php`**; скрипты миграции переведены на **`content/`** и локальные **`scripts/guide-taxonomy.ts`**, **`scripts/seo-helpers.ts`** (паритет с PHP). Обновлены **`docs/AGENTS.md`**, **`README.md`**, **`deploy/README.md`** (откат образом, паритет URL), **`deploy/SEO-CHECKLIST.md`**, **`grace/**/*.xml`**, **`package-lock.json`** после `npm install`.
- **Почему:** запрос пользователя — полный переход с Astro SSG на стек как у dandangers с сохранением URL и без RSS.
- **Файлы:** удалены `src/**`, `astro.config.mjs`, `tailwind.config.mjs`, `scripts/sitemap-lastmod.mjs`; добавлены `scripts/guide-taxonomy.ts`, `scripts/seo-helpers.ts`; правки в `scripts/*.ts`, `public/css/site.css`, `grace/**`, `docs/**`, `deploy/**`, `README.md`, `package-lock.json`, и др. по диффу.
- **Решение:** версия **`VERSION` = 1.0.0** (MAJOR cutover). Локальный запуск приложения для проверки не выполнялся (`no-local-app-verification`).

### Чистка устаревших файлов после PHP-cutover (1.0.1)
- **Что:** удалены **`deploy/nginx-docker.conf`** (дубль документации при активном **`docker/nginx-default.conf`**), **`deploy/docker-compose.example.yml`** (схема только для старого `dist/` + static-site), пустой каталог **`tests/`**; обновлены комментарии в **`deploy/genshintop-redirects.conf`**, **`scripts/enrich-guides.ts`**, таблица в **`deploy/README.md`**, формулировка в **`docs/AGENTS.md`**; версия **`VERSION` → 1.0.1**, синхронизирован **`package.json`** и узел версии в **`grace/knowledge-graph/knowledge-graph.xml`**.
- **Почему:** запрос пользователя убрать всё ненужное в проекте после миграции — меньше путаницы с nginx и legacy compose.
- **Файлы:** удалены перечисленные; правки `deploy/README.md`, `deploy/genshintop-redirects.conf`, `scripts/enrich-guides.ts`, `docs/AGENTS.md`, `docs/HISTORY.md`, `VERSION`, `package.json`, `grace/knowledge-graph/knowledge-graph.xml`
- **Решение:** не трогали **`gi-database/`** и сгенерированные отчёты — это рабочие данные/артефакты пайплайна; **`scripts/sitemap-lastmod.mjs`** в дереве уже отсутствовал.

### Удаление Node/npm из репозитория (1.0.2)
- **Что:** удалены **`package.json`**, **`package-lock.json`**, **`tsconfig.json`**, все **`scripts/*.ts`** (миграция, аудит, enrich, OG через sharp); в образ копируется только **`scripts/build-sitemap.php`** (`Dockerfile`: явный `COPY …/build-sitemap.php`). Обновлены **`README.md`**, **`docs/AGENTS.md`**, **`grace/**`** (граф, план, technology, requirements, verification), **`deploy/genshintop-redirects.conf`** / **`deploy/README.md`**, правило **`.cursor/rules/github-actions.mdc`**. Локально удалён каталог **`node_modules`**, если присутствовал.
- **Почему:** запрос пользователя убрать npm и node_modules из проекта — сайт полностью на PHP в проде.
- **Файлы:** удалены перечисленные; правки `Dockerfile`, `VERSION`, `docs/HISTORY.md`, и др. по диффу.
- **Решение:** PATCH **1.0.2**. Пайплайны переноса **`gi-database` → `content/`**, массовой генерации OG и enrich редиректов остаются **вне репозитория** (ручной процесс или отдельный инструмент).

### Удаление каталога gi-database из репозитория (1.0.3)
- **Что:** удалён весь каталог **`gi-database/`** из git; убран узел **M-GI-DATABASE** и CrossLink из **`grace/knowledge-graph/knowledge-graph.xml`**; поток **DF-CONTENT-MIGRATE** переименован по смыслу на редакционное ведение **`content/`**; **UC-002** без миграции из gi-database; обновлены **`README.md`**, **`docs/AGENTS.md`**, **`grace/plan/development-plan.xml`**, **`grace/requirements/requirements.xml`**, **`grace/technology/technology.xml`** (версия стека **1.0.3**); из **`.dockerignore`** удалена строка **`gi-database`**.
- **Почему:** запрос пользователя — исходный корпус gi-database больше не нужен в проекте; канон — только **`content/`**.
- **Файлы:** удалён **`gi-database/**`**; правки перечисленных файлов, **`VERSION` → 1.0.3**, **`docs/HISTORY.md`**.
- **Решение:** PATCH **1.0.3**. История в журнале о прошлых фазах Astro/npm сохранена как архив; массовые пайплайны при необходимости живут вне репо.

### Исправление parse error в HtmlComponents heredoc (1.0.4)
- **Что:** в **`lib/HtmlComponents.php`** (`guideCatalogCard`) условный блок excerpt вынесен в переменную **`$excerptBlock`** — внутри heredoc недопустимо **`{$excerpt !== '' ? …}`** (парсер ожидает простую интерполяцию после `{`).
- **Почему:** падение **`docker build`** на шаге **`RUN php scripts/build-sitemap.php`** (PHP parse error на строке 84).
- **Файлы:** `lib/HtmlComponents.php`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/plan/development-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.0.4**.

### nginx 403 Forbidden на главной и каталогах без индекса (1.0.5)
- **Что:** в **`docker/nginx-default.conf`** добавлены **`index index.php index.html`**, в **`location /`** заменено **`try_files $uri $uri/ …`** на **`try_files $uri /index.php$is_args$args`** (без **`$uri/`**): цепочка **`$uri/`** для **`/`** приводила к поиску только **`index.html`** по умолчанию → **403** при запрете листинга каталогов.
- **Почему:** в проде и при локальном Docker отображался **403 Forbidden** с заголовком nginx на главной.
- **Файлы:** `docker/nginx-default.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/plan/development-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.0.5**.

### Документация ACME Traefik (tls: unrecognized name) (1.0.6)
- **Что:** в **`deploy/README.md`** добавлен раздел «Traefik и Let's Encrypt»: причина ошибки TLS-ALPN/SNI, проверки DNS и портов, диагностика **`openssl s_client`**, переход резолвера **`le`** на **HTTP-01** (`httpChallenge.entryPoint: web`); в **`deploy/env.example`** — короткая отсылка к разделу.
- **Почему:** пользовательская ошибка выдачи сертификата Let's Encrypt через Traefik (`genshintop@docker`, certresolver `le`).
- **Файлы:** `deploy/README.md`, `deploy/env.example`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/plan/development-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.0.6** — правки только документации и метаданных версии; конфиг Traefik живёт в репозитории webserver.

### nginx: цикл редиректов ERR_TOO_MANY_REDIRECTS (1.0.7)
- **Что:** удалён блок **`location = /index.php`** с **`return 301 …/`** в **`docker/nginx-default.conf`** (оставлен комментарий почему). Внутренний переход **`try_files` → `/index.php`** попадал в тот же **`location =`** и снова отдавал **301** на **`/`**, что давало бесконечный редирект в браузере (часто за Traefik).
- **Почему:** сообщение пользователя «слишком много переадресаций» на **`genshintop.ru`**.
- **Файлы:** `docker/nginx-default.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.0.7** — убран конфликтующий **301** с внутренним **try_files**. Способ передачи **`/index.php`** в FastCGI зафиксирован в **1.0.8** (см. ниже).

### nginx: отдача исходников PHP вместо выполнения (1.0.8)
- **Что:** для **`/index.php`** задано **`location = /index.php`** с FastCGI и явным **`SCRIPT_FILENAME`** (`$document_root/index.php`); добавлено **`location ~ \.php$ { deny all; }`** для любых других `.php` под **`public/`**. Блок **`location ~ ^/index\.php$`** убран — точное совпадение **`=`** однозначно отдаёт запись только в PHP-FPM.
- **Почему:** в браузере отображалась «простыня» исходного кода (на скриншоте — класс Parsedown); признак того, что **`index.php`** отдавался как статика без PHP-FPM.
- **Файлы:** `docker/nginx-default.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/plan/development-plan.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.0.8**. После выката проверить **`docker compose logs`** на **`php-fpm`** и nginx 502; если симптом сохраняется — убедиться, что Traefik ведёт на контейнер образа GHCR, а не на статический хост без PHP.

### nginx: /favicon.ico → favicon.svg (1.0.9)
- **Что:** в **`docker/nginx-default.conf`** добавлено **`location = /favicon.ico`** с **`rewrite … /favicon.svg last`** (в **`public/`** только **`favicon.svg`**, в **`layout.php`** уже указан **`rel=icon`** на SVG).
- **Почему:** консоль браузера — **404** на **`/favicon.ico`** при автоматическом запросе вкладки.
- **Файлы:** `docker/nginx-default.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/plan/development-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.0.9**.

### Перенос templates/ и data/og-manifest.json в lib/ (1.1.0)
- **Что:** удалены корневые **`templates/`** и **`data/`**; шаблоны — **`lib/templates/`** (`layout.php`, **`partials/`**); манифест OG — **`lib/og-manifest.json`**; **`Router.php`** подключает **`lib/templates/layout.php`**; **`public/index.php`** вызывает **`OgManifest::load(.../lib/og-manifest.json)`**; **`Dockerfile`** больше не копирует отдельно **`templates/`** и **`data/`** (входит в **`COPY lib`**). Обновлены **`docs/AGENTS.md`**, **`docs/SEO-CHECKLIST.md`**, **`grace/**/*.xml`**.
- **Почему:** запрос пользователя — держать код сайта в **`lib/`**, без отдельных **`templates/`** и **`data/`**.
- **Файлы:** перемещены в **`lib/templates/**`**, **`lib/og-manifest.json`**; удалены **`templates/**`**, **`data/og-manifest.json`**; правки `Dockerfile`, `lib/Router.php`, `public/index.php`, `VERSION`, GRACE, документация.
- **Решение:** MINOR **1.1.0** — только реорганизация путей; URL сайта не менялись.

### Весь PHP приложения под lib/, тонкий public/index.php (1.2.0)
- **Что:** **`bootstrap.php`** и **`config.php`** перенесены в **`lib/`** (`SITE_ROOT` в **`lib/bootstrap.php`** = корень репозитория); добавлен **`lib/web_dispatch.php`** (bootstrap + config + OgManifest + `Router::dispatch`); **`public/index.php`** только **`require …/lib/web_dispatch.php`**; **`scripts/build-sitemap.php`** заменён на **`lib/build-sitemap.php`** (`php lib/build-sitemap.php`); **`Dockerfile`**: без отдельного **`COPY`** корневых **`bootstrap`/`config`** и **`scripts/`**, **`RUN php lib/build-sitemap.php`**. Чек-лист SEO — только **`docs/SEO-CHECKLIST.md`**; ссылки на **`deploy/SEO-CHECKLIST.md`** заменены на **`docs/…`** в **`deploy/README.md`**, **`docs/AGENTS.md`**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/plan/development-plan.xml`**. Обновлены **`README.md`**, **`docs/SEO-CHECKLIST.md`**, **`grace/**/*.xml`**.
- **Почему:** запрос пользователя — собрать все PHP в **`lib/`** (в **`public/`** остаётся минимальная точка входа для nginx/FastCGI); единый канонический путь SEO-чеклиста под **`docs/`**.
- **Файлы:** `lib/bootstrap.php`, `lib/config.php`, `lib/web_dispatch.php`, `lib/build-sitemap.php`, `public/index.php`, `Dockerfile`, `VERSION`, удалены корневые `bootstrap.php`, `config.php`, `scripts/build-sitemap.php`, при необходимости **`deploy/SEO-CHECKLIST.md`** (дубликат).
- **Решение:** MINOR **1.2.0**.

### Плоская структура lib без подпапок (1.2.1)
- **Что:** удалены **`lib/templates/`** и **`lib/templates/partials/`**; **`layout.php`**, **`header.php`**, **`footer.php`**, **`lootbar_banner.php`** перенесены в корень **`lib/`**; **`Router.php`** подключает **`lib/layout.php`**; в **`layout.php`** обновлены **`require`** на одноуровневые пути. Обновлены **`docs/AGENTS.md`**, **`docs/SEO-CHECKLIST.md`**, **`grace/**/*.xml`**, **`VERSION` → 1.2.1**.
- **Почему:** запрос пользователя — в **`lib/`** без подпапок (единый каталог PHP и шаблонов).
- **Файлы:** `lib/layout.php`, `lib/header.php`, `lib/footer.php`, `lib/lootbar_banner.php`, `lib/Router.php`, удалены `lib/templates/**`, `VERSION`, `docs/AGENTS.md`, `docs/SEO-CHECKLIST.md`, `docs/HISTORY.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`
- **Решение:** PATCH **1.2.1** — только файловая структура и документация; публичные URL не менялись.

### Удаление каталога reports/ (1.2.2)
- **Что:** удалены **`reports/`** (**`.gitkeep`**, **`migration-report.json`** — артефакты старого пайплайна Astro/npm); убрана строка **`reports`** из **`.dockerignore`**; в **`docs/AGENTS.md`** удалены упоминания **`reports/`** и строка в блоке структуры репозитория; **`VERSION`**, узел **`Project`** в **`grace/knowledge-graph/knowledge-graph.xml`** и **`TechnologyStack`** в **`grace/technology/technology.xml`** → **1.2.2**.
- **Почему:** запрос пользователя — каталог не используется рантаймом сайта и не нужен для текущего PHP-стека.
- **Файлы:** удалён **`reports/**`**; правки `.dockerignore`, `docs/AGENTS.md`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.2.2** — локальные отчёты аудита при необходимости создаются вне репозитория.

### Dockerfile перенесён в docker/Dockerfile (1.2.3)
- **Что:** **`Dockerfile`** из корня перенесён в **`docker/Dockerfile`**; в комментарии в файле указана сборка **`docker build -f docker/Dockerfile .`** (контекст — корень репозитория, пути **`COPY`** без изменений); **`.github/workflows/docker-image.yml`** — **`file: ./docker/Dockerfile`**; обновлены **`README.md`**, **`deploy/README.md`**, **`docs/AGENTS.md`**, **`.cursor/rules/github-actions.mdc`**, **`grace/**/*.xml`**, **`VERSION` → 1.2.3**.
- **Почему:** запрос пользователя — держать Dockerfile рядом с остальной docker-конфигурацией; исторически корень репо использовался как дефолт для CI и **`docker build .`**.
- **Файлы:** `docker/Dockerfile` (перемещён из корня), `.github/workflows/docker-image.yml`, `README.md`, `deploy/README.md`, `docs/AGENTS.md`, `.cursor/rules/github-actions.mdc`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.2.3**.

### Объединение deploy/ в docker/, скрипт обновления в корне (1.3.0)
- **Что:** каталог **`deploy/`** убран; **`docker-compose.yml`**, **`genshintop-redirects.conf`**, **`env.example`**, **`README.md`** деплоя перенесены в **`docker/`**; **`update-from-github.sh`** — в **корень репозитория** (пути к **`docker/docker-compose.yml`** и **`docker/.env`**); **`docker/Dockerfile`**: **`COPY docker/genshintop-redirects.conf`**; **`.gitignore`**: **`docker/.env`**; обновлены **`README.md`**, **`docker/README.md`**, **`docs/AGENTS.md`**, **`grace/**/*.xml`**, **`VERSION` → 1.3.0**.
- **Почему:** запрос пользователя — один каталог **`docker/`** для образа и прод-оркестрации; скрипт серверного обновления удобнее вызывать из корня клона.
- **Файлы:** `docker/docker-compose.yml`, `docker/genshintop-redirects.conf`, `docker/env.example`, `docker/README.md`, `update-from-github.sh`, удалён `deploy/**`, правки `docker/Dockerfile`, `.gitignore`, `README.md`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/plan/development-plan.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** MINOR **1.3.0** — операторы должны перенести секреты из **`deploy/.env`** в **`docker/.env`** и вызывать **`./update-from-github.sh`** вместо **`deploy/update-from-github.sh`**.

### Восстановление lib/Parsedown.php (1.3.1)
- **Что:** файл **`lib/Parsedown.php`** был повреждён (отсутствовали **`<?php`**, объявление **`class Parsedown`**, метод **`text()`** и начало класса); заменён на каноническую копию **[Parsedown 1.7.4](https://github.com/erusev/parsedown)** (upstream **`erusev/parsedown`**). **`VERSION`**, узел **`Project`** и **`TechnologyStack`** → **1.3.1**.
- **Почему:** сообщение пользователя с фрагментом кода выявило обрыв библиотеки; без восстановления **`ContentRepository::markdownToHtml`** и сборка образа ломались бы при загрузке Parsedown.
- **Файлы:** `lib/Parsedown.php`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.1** — поведение API **`Parsedown::text()`** / **`setSafeMode()`** без изменений относительно ожиданий **`ContentRepository`**.

### Parsedown 1.8.0 (1.3.2)
- **Что:** **`lib/Parsedown.php`** обновлён до **[Parsedown 1.8.0](https://github.com/erusev/parsedown/releases/tag/1.8.0)** из репозитория **[erusev/parsedown](https://github.com/erusev/parsedown)** (сырой файл с тега **`1.8.0`**). **`VERSION`**, узел **`Project`** и **`TechnologyStack`** → **1.3.2**.
- **Почему:** запрос пользователя — использовать актуальный релиз **1.8.0**.
- **Файлы:** `lib/Parsedown.php`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.2** — **`ContentRepository::markdownToHtml`** по-прежнему использует **`Parsedown::text()`** и **`setSafeMode(false)`**.

### Compose: проект genshintop и защита от чужого docker/.env (1.3.3)
- **Что:** в **`docker/docker-compose.yml`** добавлено верхнеуровневое **`name: genshintop`** (изолированное имя проекта Compose); **`docker/env.example`** и **`docker/README.md`** — предупреждение не копировать **`docker/.env`** между сайтами на одном хосте и проверка **`SITE_CONTAINER_NAME`**/**`TRAEFIK_ROUTER`**; **`update-from-github.sh`** перед **`git fetch`** выводит строки **`SITE_CONTAINER_NAME`**, **`TRAEFIK_ROUTER`**, **`SITE_IMAGE`** из **`docker/.env`** для визуальной проверки; **`grace/verification`** уточнён чек по compose.
- **Почему:** на сервере при обновлении из каталога genshintop пересоздавался контейнер **`dandangers_web`** — типичный случай **`SITE_CONTAINER_NAME`** из чужого **`.env`**.
- **Файлы:** `docker/docker-compose.yml`, `docker/env.example`, `docker/README.md`, `update-from-github.sh`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.3** — на проде в **`genshintop/docker/.env`** должны быть **`SITE_CONTAINER_NAME=genshintop_web`** и **`TRAEFIK_ROUTER=genshintop`** (не значения с dandangers).

### update-from-github.sh: изоляция docker compose от COMPOSE_FILE (1.3.4)
- **Что:** вызовы **`docker compose`** обёрнуты в подпроцесс с **`unset COMPOSE_FILE`** и **`unset COMPOSE_PROJECT_NAME`** (если на сервере они экспортированы под другой сайт, Compose раньше мог подмешивать чужой compose и пересоздавать не тот контейнер при уникальном **`docker/.env`**); добавлены **`--project-directory "$ROOT"`**, вывод путей **`ROOT`** и **`COMPOSE_FILE`**; **`ROOT`** через **`pwd -P`**. В **`docker/README.md`** — предупреждение не выставлять глобально **`COMPOSE_FILE`** / **`COMPOSE_PROJECT_NAME`** в shell.
- **Почему:** пользователь подтвердил уникальность env; типичная причина — переменные окружения shell от первого сайта.
- **Файлы:** `update-from-github.sh`, `docker/README.md`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.4**.

### Светлый wiki-like UI главной и шаблонов (1.3.5)
- **Что:** **`public/css/site.css`** переведён на светлую палитру (фон страницы, белые карточки, тени, бирюзовый акцент в духу dandangers.ru): шапка `sticky`, навигация с активным pill, hero-карточка с логотипом **GT**, секции с заголовками и вертикальным акцентом; карточки персонажей и гайдов — горизонтальный блок-превью слева (первая буква имени / две буквы заголовка); баннер LootBar — жёлтая полоска и горизонтальный ряд с иконкой и CTA. **`lib/layout.php`**: шрифты **Inter + Onest**, `theme-color` под светлую тему. Обновлены **`lib/PageRenderer.php`** (разметка `home()`), **`lib/lootbar_banner.php`**, **`lib/HtmlComponents.php`** (карточки). Синхронизированы **`grace/knowledge-graph`**, **`grace/technology`**, **`grace/verification`** (`check-15`), **`docs/AGENTS.md`**, **`VERSION` → 1.3.5**.
- **Почему:** запрос пользователя — поправить вёрстку и оформление по качественному эталону [Tiles Survive Wiki](https://dandangers.ru/).
- **Файлы:** `public/css/site.css`, `lib/layout.php`, `lib/PageRenderer.php`, `lib/lootbar_banner.php`, `lib/HtmlComponents.php`, `VERSION`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.5** — визуальный слой без изменения URL и контрактов данных.

### update-from-github.sh: COMPOSE_FILE и set -u (1.3.6)
- **Что:** в **`compose_run`** путь к compose сохраняется в **`compose_path`** до **`unset COMPOSE_FILE`**; для **`docker compose -f`** используется **`$compose_path`**, чтобы при **`set -u`** не было ошибки «COMPOSE_FILE: unbound variable» после unset.
- **Почему:** отчёт с прод-сервера: **`docker compose pull`** падал на строке с unset/unbound **`COMPOSE_FILE`**.
- **Файлы:** `update-from-github.sh`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.6**.

### Баннер LootBar в виде карточки как на dandangers.ru (1.3.7)
- **Что:** **`lib/lootbar_banner.php`** — разметка карточки: иконка в оранжевом скруглённом квадрате, жирный оранжевый заголовок, серый подзаголовок, pill-кнопка «Подробнее» на партнёрский URL; в тексте ссылка **Раздел на GenshinTop** → **`/lootbar`** (`lootbar_banner_hub_link`). **`public/css/site.css`** — градиентный фон карточки, рамка, тени, адаптивная вёрстка на узких экранах. **`grace/verification`** — учтён новый reachGoal в VF-002.
- **Почему:** запрос пользователя — такой же привлекательный промо-баннер, как на [dandangers.ru](https://dandangers.ru/).
- **Файлы:** `lib/lootbar_banner.php`, `public/css/site.css`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.7** — текст промо по образцу вики; проценты/купоны при необходимости синхронизировать с актуальными условиями LootBar.

### Страница /lootbar/skidki-i-kupony и баннер на неё (1.3.8)
- **Что:** добавлен лендинг **`/lootbar/skidki-i-kupony`** по образцу [lootbar-discounts на dandangers.ru](https://dandangers.ru/lootbar-discounts.html): блоки про купоны 10%/6%, шаги получения, таблица преимуществ, FAQ и партнёрские CTA (`lootbar_discounts_*`). **`lib/lootbar_banner.php`** — кнопка «Подробнее» ведёт на эту страницу (`lootbar_banner_discounts_page`). **`lib/SiteRoutes.php`** — маршрут в sitemap; хаб **`/lootbar`** — пункт «Скидки и купоны». **`public/css/site.css`** — таблицы `.lootbar-benefits-table`. Обновлён VF-002 в **`grace/verification`**.
- **Почему:** запрос пользователя — чтобы баннер вёл на такую же по смыслу страницу, как у dandangers.
- **Файлы:** `lib/PageRenderer.php`, `lib/lootbar_banner.php`, `lib/SiteRoutes.php`, `public/css/site.css`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.8** — точные рублёвые строки как у другой игры не копировались; акцент на актуальные цены на LootBar.gg и локальной инструкции.

### Инструкция пополнения под витрину lootbar.gg/ru/top-up/genshin-impact (1.3.9)
- **Что:** **`lib/LootbarConfig.php`** — расширены шаги HowTo под реальный поток Top Up Genshin Impact на LootBar. **`lib/PageRenderer.php`** — **`/lootbar/kak-popolnit-genshin-impact`** вынесена в **`lootbarHowToGenshinLanding`**: развёрнутый текст, явная отсылка к пути **`lootbar.gg/ru/top-up/genshin-impact`** и к ссылкам с параметрами вроде **`utm_campaign=p_invite`**, кнопка «Открыть пополнение…», блок про UID и купоны, JSON-LD **HowTo**. **`public/css/site.css`** — стили списка шагов. **`grace/verification`** — цель **`lootbar_howto_open_vitrina`** в VF-002.
- **Почему:** запрос пользователя — опереться на официальную витрину [LootBar Genshin Impact](https://lootbar.gg/ru/top-up/genshin-impact?utm_source=copy&utm_medium=social&utm_campaign=p_invite); SPA LootBar не отдаёт полный текст через простой fetch, поэтому на сайте зафиксирован типовой сценарий формы и предупреждение сверяться с актуальным UI партнёра.
- **Файлы:** `lib/LootbarConfig.php`, `lib/PageRenderer.php`, `public/css/site.css`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.3.9**.

### Оформление и стили по эталону idpro1313/dandangers (1.4.0)
- **Что:** **`public/css/site.css`** — переход на тёмную палитру и паттерны как у репозитория **[idpro1313/dandangers](https://github.com/idpro1313/dandangers)** (`modern-styles.css`): токены teal/violet, градиенты, свечение фона, карточки и шапка, стилизация **`.prose`** (списки-карточки, таблицы, blockquote, code), кнопки и таблицы LootBar; алиасы legacy-переменных (**`--wiki-accent`**, **`--surface`** и т.д.) для совместимости классов; блок **`prefers-color-scheme: light`** для пользователей со светлой ОС. **`lib/layout.php`** — **`theme-color`** **`#0a0e14`** под тёмный дефолт. Обновлены **`grace/knowledge-graph`**, **`grace/technology`**, **`grace/verification`** (**check-15**), **`docs/AGENTS.md`**, **`VERSION` → 1.4.0**.
- **Почему:** запрос пользователя — взять «более красивое» оформление с сайта/репо dandangers.
- **Файлы:** `public/css/site.css`, `lib/layout.php`, `VERSION`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** MINOR **1.4.0** — только визуальный слой и документация; разметка PHP без перестройки под новые классы.

### Рефакторинг базы гайдов: стандарты, инвентаризация, W0 и редирект banner→banners (1.5.0)
- **Что:** добавлены **`docs/GUIDE_EDITORIAL.md`** (редакционный стандарт), **`docs/guides-refactor-waves.md`** (волны W0–W3), **`docs/GUIDES_MERGE_SPLIT.md`** (merge/split и nginx); **`scripts/guides-refactor-inventory.php`** и **`scripts/guides-refactor-inventory.ps1`** → **`reports/guides-refactor-inventory.json`**. Волна **W0**: переписан **`content/guides/promocodes.md`**, выправлены связность и факты в cornerstone-гайдах (тир-лист 6.x, домены, боссы, ивенты, TCG, квесты архонтов), усилены **`relatedGuides`** и даты **`reviewedAt`**. Удалён монолит **`content/guides/banner.md`**, добавлен **301** **`/guides/banner` → `/guides/banners`** в **`docker/genshintop-redirects.conf`**. GRACE: модуль **M-CONTENT-GUIDE-REFACTOR**, **UC-009**, **V-M-CONTENT-GUIDE-REFACTOR**, **Phase-6**, обновлены knowledge-graph, development-plan, requirements, technology (**VERSION 1.5.0**); **`docs/AGENTS.md`** — команды и ссылки на документы.
- **Почему:** план пользователя — редакционный рефакторинг контента гайдов и ИА; инструментарий для волн W1–W3 и отчёта по «лесенке»/дублям заголовков.
- **Файлы:** `docs/GUIDE_EDITORIAL.md`, `docs/guides-refactor-waves.md`, `docs/GUIDES_MERGE_SPLIT.md`, `scripts/guides-refactor-inventory.php`, `scripts/guides-refactor-inventory.ps1`, `reports/guides-refactor-inventory.json`, `docker/genshintop-redirects.conf`, `content/guides/promocodes.md`, `content/guides/tier-list-aktualniy-6-x.md`, `content/guides/domain-podzemelya-rasspisanie.md`, `content/guides/bossy-genshin-impact.md`, `content/guides/events-aktualnye-6-x.md`, `content/guides/tcg-svyaschennyy-prizyv-semi.md`, `content/guides/quest-arhontov-roadmap.md`, удалён `content/guides/banner.md`, `VERSION`, `grace/**`, `docs/AGENTS.md`, `docs/HISTORY.md`
- **Решение:** MINOR **1.5.0** — новый контент-пайплайн документации и CLI; массовая правка всех ~430 гайдов перенесена на волны по отчёту.

### nginx: редиректы не в conf.d (1.5.1)
- **Что:** **`docker/genshintop-redirects.conf`** копируется в образ как **`/etc/nginx/snippets/genshintop-redirects.conf`**; **`docker/nginx-default.conf`** делает **`include`** из **`server {}`**; в **`docker/Dockerfile`** добавлен **`mkdir /etc/nginx/snippets`** и комментарий, почему не **`conf.d`**. Обновлены **`docker/README.md`**, **`docs/AGENTS.md`**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/verification/verification-plan.xml`**, комментарий в **`docker/genshintop-redirects.conf`**, **`VERSION` → 1.5.1**.
- **Почему:** в логах контейнера nginx падал с **`rewrite directive is not allowed here`** — в Alpine образе **`/etc/nginx/conf.d/*.conf`** подключается в контексте **`http {}`**, где **`rewrite`** недопустим (файл парсился дважды: на уровне `http` и внутри `server`).
- **Файлы:** `docker/Dockerfile`, `docker/nginx-default.conf`, `docker/genshintop-redirects.conf`, `docker/README.md`, `docs/AGENTS.md`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/verification/verification-plan.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.5.1** — только путь подключения редиректов в образе; содержимое **`docker/genshintop-redirects.conf`** в репозитории без изменения формата строк **`rewrite … permanent`**.

### Волна W1: датированные дубли баннеров, правки ссылок и пример W2 paralogism-5-6 (1.5.2)
- **Что:** добавлены **`scripts/wave-w1-merge-banner-dated.ps1`** (группы по **`mergeCandidatesByTitle`** из **`reports/guides-refactor-inventory.json`**: канонический slug без датированного суффикса, редиректы **`rewrite … permanent`** в **`docker/genshintop-redirects.conf`**, замены **`/guides/…`** в **`content/**/*.md`**, удаление датированных **`content/guides/banner-*.md`**) и **`scripts/normalize-short-line-runons.ps1`** (склейка «лесенки» в теле одного файла по **`-RelativePath`**). Удалено **194** дубликата баннеров; **`content/characters/*.md`** — обновлены ссылки на канонические URL гайдов. Редакторская доработка **`content/guides/paralogism-5-6.md`**: frontmatter, оглавление, **`##`** с якорями, блок спойлеров. Перегенерирован **`reports/guides-refactor-inventory.json`**. Обновлены **`docs/guides-refactor-waves.md`**, **`docs/AGENTS.md`**, **`grace/knowledge-graph`**, **`grace/technology`**, **`grace/plan/development-plan.xml`** (**VERSION 1.5.2**).
- **Почему:** продолжение волн рефакторинга гайдов после **W0**: пользователь запросил **«продолжи волны»** (W1–W3).
- **Файлы:** **`scripts/wave-w1-merge-banner-dated.ps1`**, **`scripts/normalize-short-line-runons.ps1`**, **`content/guides/paralogism-5-6.md`**, массовое удаление **`content/guides/banner-*`** (датированные дубли), **`content/characters/*.md`**, **`reports/guides-refactor-inventory.json`**, **`VERSION`**, **`docs/guides-refactor-waves.md`**, **`docs/AGENTS.md`**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`grace/plan/development-plan.xml`**, **`docs/HISTORY.md`** (этот раздел).
- **Решение:** PATCH **1.5.2** — контент и утилиты пайплайна; редиректы nginx уже были в дереве после **1.5.1**. Две группы **`mergeCandidatesByTitle`** без «бездатного» slug в **W1** закрыты вручную в **1.5.3** (волна **W2**).

### Волна W2: остаточные дубли баннеров Essentsiya / Blagoslovenie + 301 (1.5.3)
- **Что:** добавлен канонический **`content/guides/banner-essentsiya-ambrozii.md`** (по содержанию последнего баннера), удалены **`banner-essentsiya-ambrozii-06-08-2024.md`**, **`banner-essentsiya-ambrozii-08-07-2025.md`**, **`banner-blagoslovenie-plameni-15-04-2025.md`**, **`banner-blagoslovenie-plameni-23-12-2025.md`**; в **`docker/genshintop-redirects.conf`** — четыре **301** на каноны; **`content/guides/banner-blagoslovenie-plameni.md`** — исправлен **`title`** и краткий **`summary`**, **`reviewedAt`**; **`relatedGuides`** у затронутых персонажей переведены на канонические slug. Перегенерирован **`reports/guides-refactor-inventory.json`** (**`totalGuides`** **232**, **`mergeCandidatesByTitle`** пуст). Обновлены **`docs/guides-refactor-waves.md`**, **`docs/AGENTS.md`**, **`grace/**`**, **`VERSION` → 1.5.3**.
- **Почему:** пользователь запросил **«следующая волна»** (**W2**): закрыть хвост **`mergeCandidatesByTitle`** после **W1**.
- **Файлы:** **`content/guides/banner-essentsiya-ambrozii.md`**, удалённые **`content/guides/banner-essentsiya-ambrozii-*.md`**, **`banner-blagoslovenie-plameni-*.md`**, **`content/guides/banner-blagoslovenie-plameni.md`**, **`docker/genshintop-redirects.conf`**, **`content/characters/{emiliya,kachina,ororon,setos,lan-yan,shilonen,daliya,ayno,fremine,ka-min,shevryez,ifa,iansan}.md`**, **`reports/guides-refactor-inventory.json`**, **`VERSION`**, **`docs/guides-refactor-waves.md`**, **`docs/AGENTS.md`**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`grace/plan/development-plan.xml`**, **`docs/HISTORY.md`**.
- **Решение:** PATCH **1.5.3** — только контент баннеров, nginx и перелинковка; массовая правка **`update-*`** отложена (высокий **`ladderRatio`** там в основном из-за списков имён, не абзацев).

## Фаза: опорный корпус гайдов в info/ и архив (1.6.0)

### Новые столпы, архив guides и редиректы
- **Что:** в **`info/`** добавлены **`info/README.md`** (матрица ИА) и **20** новых статей в **`info/guides/*.md`** (тексты с нуля по **`docs/GUIDE_EDITORIAL.md`**). Все **232** прежних файла из **`content/guides/`** перенесены в **`content/guides-archive/`**; в **`content/guides/`** размещён новый опорный набор (копия из **`info/guides/`**). **`docker/genshintop-redirects.conf`** упрощён: префиксные **301** (`banner-*`, `update-*`, `tier-list-*`, `events-*`, `domain-*`, `bossy-*`, `tcg-*`, `quest-*`, `genshin-updates-*`) на соответствующие столпы и отрицательный lookahead для прочих устаревших slug → **`/guides`**. В **`content/characters/*.md`** нормализованы **`relatedGuides`** под новый корпус (скрипт на PowerShell). Обновлены **`VERSION` → 1.6.0**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`docs/AGENTS.md`**, **`docs/HISTORY.md`**.
- **Почему:** запрос пользователя — «чистый лист» опорных гайдов по плану: собрать смысл из **`content`**, новое дерево, перенос старого в архив без удаления из истории git мусорным способом.
- **Файлы:** `info/**`, `content/guides/*.md`, `content/guides-archive/*.md`, `content/characters/*.md`, `docker/genshintop-redirects.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/AGENTS.md`, `docs/HISTORY.md`
- **Решение:** MINOR **1.6.0** — смена редакционной модели гайдов; **`reports/guides-refactor-inventory.json`** перегенерировать при следующем запуске **`php scripts/guides-refactor-inventory.php`** (в среде с PHP).

### Волна расширения опорного корпуса: +9 гайдов (1.6.1)
- **Что:** в **`info/guides/`** и **`content/guides/`** добавлены **9** статей волны 2 (стихии и реакции, мора, окуляры и статуи, репутация, кооператив, еда, циклические боевые режимы, лор без спойлеров, справочник приключений). Обновлены **`info/README.md`**, **`docker/genshintop-redirects.conf`** (расширен negative lookahead для fallback **301**), **`VERSION` → 1.6.1**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`docs/HISTORY.md`**.
- **Почему:** запрос пользователя продолжить написание новой базы гайдов поверх первого набора из 20 столпов.
- **Файлы:** `info/guides/*.md`, `content/guides/*.md`, `info/README.md`, `docker/genshintop-redirects.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.6.1** — только контент гайдов, редиректы и метаданные версии.

### Волна расширения опорного корпуса: +8 гайдов, slug чайника (1.6.2)
- **Что:** в **`info/guides/`** и **`content/guides/`** добавлены **8** статей волны 3 (таланты и короны, энергия и взрыв стихии, чеклист материалов на героя, введение в Чайник безмятежности, созвездия и инвестиции в крутки, щит и лечение, элементальное мастерство, гаджеты исследования). Файл **`chaynik-bespechnosti-vvedenie.md`** переименован в **`chaynik-bezmyatezhnosti-vvedenie.md`** (транслит «безмятежности»). В **`docker/genshintop-redirects.conf`** расширен negative lookahead на новые slug. Обновлены **`info/README.md`**, **`VERSION` → 1.6.2**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`docs/HISTORY.md`**.
- **Почему:** продолжение выкатки новой базы гайдов после волны 2.
- **Файлы:** `info/guides/*.md`, `content/guides/*.md`, `info/README.md`, `docker/genshintop-redirects.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.6.2** — контент, редиректы, версия в GRACE; без изменений PHP.

### Редакционное зеркало персонажей info/characters (1.6.3)
- **Что:** добавлен каталог **`info/characters/`** с **197** файлами `*.md` — начальное зеркало **`content/characters/`** для того же workflow, что у гайдов (правки в `info/`, копирование в `content/` для публикации). Обновлены **`info/README.md`** (структура «гайды + персонажи»), **`docs/AGENTS.md`**, **`grace/knowledge-graph/knowledge-graph.xml`** (путь **`info/characters/`**, аннотация, **VERSION 1.6.3**), **`grace/technology/technology.xml`**, **`VERSION` → 1.6.3**, **`docs/HISTORY.md`**.
- **Почему:** запрос пользователя сделать с персонажами **«так же»**, как с опорным корпусом гайдов — единый редакционный контур.
- **Файлы:** `info/characters/*.md`, `info/README.md`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `VERSION`, `docs/HISTORY.md`
- **Решение:** PATCH **1.6.3** — дублирование контента намеренное для редакции; **`content/characters/`** в этом коммите не менялся (уже совпадает с копией).

### Волна расширения опорного корпуса: +8 гайдов (1.6.4)
- **Что:** в **`info/guides/`** и **`content/guides/`** добавлены **8** статей волны 4 (криты, бонус стихии и резисты, магазин блеска и звёздного блеска, сюжетные ключи и легенды, порядок слотов отряда и снимок бафов, компас сокровищ, кодекс противников, растворитель снов и конвертация материалов боссов). Обновлены **`docker/genshintop-redirects.conf`** (whitelist новых slug), **`info/README.md`** (волна 4, итого **45** статей), **`VERSION` → 1.6.4**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`docs/HISTORY.md`**.
- **Почему:** запрос пользователя **«следующая волна»** опорных гайдов после волны 3.
- **Файлы:** `info/guides/*.md`, `content/guides/*.md`, `info/README.md`, `docker/genshintop-redirects.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.6.4** — только новые гайды, редиректы и метаданные версии.

### Уточнение текста гайда магазина блеска (1.6.4)
- **Что:** в **`info/guides/magazin-bleska-prioritety-zvezdnogo.md`** и **`content/guides/magazin-bleska-prioritety-zvezdnogo.md`** заменена неточная формулировка про стандартный баннер на нейтральную («вечный баннер», приоритет молитв).
- **Почему:** вычитка после выката волны 4.
- **Файлы:** `info/guides/magazin-bleska-prioritety-zvezdnogo.md`, `content/guides/magazin-bleska-prioritety-zvezdnogo.md`, `docs/HISTORY.md`
- **Решение:** версия **1.6.4** без изменения **`VERSION`**.

### Карточки персонажей: новый корпус и архив (1.7.0)
- **Что:** добавлен **`docs/CHARACTER_EDITORIAL.md`**; скрипт **`scripts/rebuild-character-pages.ps1`** копирует текущие файлы в **`content/characters-archive/`** и генерирует **новое тело** всех **`content/characters/*.md`** из сохранённого frontmatter (профильные страницы — развёрнутый справочный каркас со ссылками на опорные гайды; вспомогательные slug вроде `why-pull-*` / `*-vs-*` — компактный формат). В **`info/characters/`** синхронизировано зеркало; обновлены **`info/README.md`**, **`docs/AGENTS.md`**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`VERSION` → 1.7.0**, **`docs/HISTORY.md`**.
- **Почему:** запрос пользователя — не переносить миграционный мусор, а **переписать** карточки как редакционные материалы.
- **Файлы:** `docs/CHARACTER_EDITORIAL.md`, `scripts/rebuild-character-pages.ps1`, `content/characters/*.md`, `content/characters-archive/*.md`, `info/characters/*.md`, `info/README.md`, `docs/AGENTS.md`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `VERSION`, `docs/HISTORY.md`
- **Решение:** MINOR **1.7.0** — новая редакционная модель тел страниц персонажей; детальные тексты талантов убраны в пользу структуры и внешних гайдов; **`relatedWeapons`/`relatedArtifacts`** в YAML сохранены для дальнейшей ручной чистки.

### GRACE: контракт M-CONTENT и персонажи (1.7.1)
- **Что:** в **`grace/plan/development-plan.xml`** расширен контракт **M-CONTENT-GUIDE-REFACTOR**: карточки персонажей, **`docs/CHARACTER_EDITORIAL.md`**, **`scripts/rebuild-character-pages.ps1`**. **`VERSION` → 1.7.1**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`docs/HISTORY.md`**.
- **Почему:** синхронизация GRACE с выкатом **1.7.0** (правило **grace-artifact-sync**).
- **Файлы:** `grace/plan/development-plan.xml`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** PATCH **1.7.1** — только артефакты плана и метаданные версии.

## Фаза: опорный корпус гайдов — волны 5–7 (1.8.0)

### Волны расширения: +24 гайда (уровень мира, QoL, экономика, безопасность)
- **Что:** в **`info/guides/`** и **`content/guides/`** добавлены **24** опорные статьи (волны **5–7** по **8** slug): уровень мира, региональная валюта и магазины, рыбалка, почта и раздачи, аккаунт HoYoverse и кросс-сейв, бесплатные герои с ивентов, одноразовость сундуков, метки карты, сундук артефактов и обмен, экспедиции, локальные специалитеты, недельный рутин, фоторежим, безопасность и 2FA, сторонние инструменты, стол алхимии, настройки клиента, смена времени суток, персональные телепорты, лимитные торговцы, инвентарь, повторный проход подземелий, скрытые ачивки и карточки профиля, книги рецептов. В **`docker/genshintop-redirects.conf`** расширен negative lookahead на новые canonical slug (исправлена опечатка **`magazinel-bleska`** → **`magazin-bleska`** в whitelist). Обновлены **`info/README.md`** (итого **69** статей), **`VERSION` → 1.8.0**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`docs/HISTORY.md`**.
- **Почему:** запрос пользователя **«продолжи все волны с гайдами»** — довести опорный корпус тремя волнами по плану редактуры.
- **Файлы:** `info/guides/*.md`, `content/guides/*.md`, `info/README.md`, `docker/genshintop-redirects.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** MINOR **1.8.0** — существенное расширение тематики гайдов без смены контракта сайта.

### Волны расширения: +24 гайда, волны 8–10 (1.9.0)
- **Что:** в **`info/guides/`** и **`content/guides/`** добавлены **24** опорные статьи: боевой пропуск, параметрический преобразователь, лей-линии, ежедневные поручения, конденсированная смола, дружба, заготовки оружия, hangout’ы, HoYoLAB, пробники, опасности среды, выносливость, типы урона, стихийные щиты, верстак чайника, валюты чайника, графика, пити и события оружия, источники AR, пробуждение оружия, навигация по квестам, ивентовая валюта, предзагрузка патча, Genesis Crystals. В **`docker/genshintop-redirects.conf`** расширен negative lookahead. Обновлены **`info/README.md`** (итого **93** статьи), **`VERSION` → 1.9.0**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`docs/HISTORY.md`**.
- **Почему:** запрос пользователя **«продолжи»** после волн 5–7 — следующие три волны опорного корпуса.
- **Файлы:** `info/guides/*.md`, `content/guides/*.md`, `info/README.md`, `docker/genshintop-redirects.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** MINOR **1.9.0** — расширение гайдов и метаданных версии.

### Финальные волны опорного корпуса: +32 гайда, волны 11–14 (1.10.0)
- **Что:** в **`info/guides/`** и **`content/guides/`** добавлены **32** статьи (завершение запланированного расширения без архивного merge): деревья подношений, сили/головоломки, руда оружия, Везервёр, озвучка, возвращение ветеранов, мировые квесты vs архонты, путеводитель, региональные гаджеты, типы сундуков, испытания времени, театр демо, пресеты отряда, кик в коопе, официальная карта, выбор сервера, TCG-рейтинг, неймкарты, соло/кооп доменов, корона талантов, параметры экрана, опросы HoYoverse, мобильные ресурсы, пинг, конвертация книг, боссы vs домены, партнёрский раздел `/lootbar`, восстановление аккаунта, уведомления, возрастной рейтинг, косметика, политика читов. В **`docker/genshintop-redirects.conf`** расширен negative lookahead. Обновлены **`info/README.md`** (итого **125** статей), **`VERSION` → 1.10.0**, **`grace/knowledge-graph/knowledge-graph.xml`**, **`grace/technology/technology.xml`**, **`docs/HISTORY.md`**.
- **Почему:** запрос пользователя **«закончи с оставшимися всеми гайдами»** в контексте опорного корпуса (не W3 архива).
- **Файлы:** `info/guides/*.md`, `content/guides/*.md`, `info/README.md`, `docker/genshintop-redirects.conf`, `VERSION`, `grace/knowledge-graph/knowledge-graph.xml`, `grace/technology/technology.xml`, `docs/HISTORY.md`
- **Решение:** MINOR **1.10.0** — закрывающий блок волн 11–14; архив **`content/guides-archive/`** по-прежнему вне этого этапа.
