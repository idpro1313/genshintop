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
