# genshintop

Сайт **GenshinTop** (genshintop.ru) на **Astro 5**: гайды и каталог персонажей **Genshin Impact**, SEO, статическая сборка в **`dist/`**.

## Быстрый старт

```powershell
npm install
npm run content:migrate
npm run build
```

Перед первой полной миграцией можно оставить заглушки в `src/content/**` — они заменятся при `content:migrate`. После переноса **`gi-database/`** можно удалить (см. `npm run content:verify`).

## Документация

- **`docs/AGENTS.md`** — карта проекта для агентов и разработчиков  
- **`docs/HISTORY.md`** — журнал итераций  
- **`deploy/README.md`** — деплой на VPS (Traefik + nginx, шаблон [webserver](https://github.com/idpro1313/webserver))  
- **`grace/`** — GRACE (требования, план, верификация, граф знаний)

Исходный корпус Markdown: **`gi-database/`** (`INDEX.md`, `database.json`).

Версия проекта: **`VERSION`** (SemVer).
