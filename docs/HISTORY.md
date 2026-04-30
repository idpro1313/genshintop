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
