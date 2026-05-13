# genshintop

Сайт **GenshinTop** (genshintop.ru): **PHP + nginx** в Docker, Markdown в **`content/`**, ванильный CSS в **`public/css/`**. SEO URL сохранены (гайды, персонажи, LootBar, регионы).

## Быстрый старт (контент и утилиты)

```powershell
npm install
npm run content:migrate    # при необходимости из gi-database → content/
npm run og:generate        # опционально: OG PNG + data/og-manifest.json
```

Сборка **sitemap** в образе сайта: `php scripts/build-sitemap.php` (вызывается из **`Dockerfile`** при `docker build`). Локально: `npm run sitemap:build`, если в PATH есть PHP.

## Docker и обновление с GitHub

```bash
cp deploy/env.example deploy/.env   # настроить домены
docker compose --env-file deploy/.env -f deploy/docker-compose.yml pull
docker compose --env-file deploy/.env -f deploy/docker-compose.yml up -d
./deploy/update-from-github.sh      # дальше обновления с тем же compose
```

Подробно: **`deploy/README.md`** (откат образом, паритет URL).

## Документация

- **`docs/AGENTS.md`** — карта проекта для агентов и разработчиков  
- **`docs/HISTORY.md`** — журнал итераций  
- **`deploy/README.md`** — Docker, Traefik, откат  
- **`grace/`** — GRACE (требования, план, верификация, граф знаний)

Исходный корпус Markdown: **`gi-database/`** (`INDEX.md`, `database.json`).

Версия проекта: **`VERSION`** (SemVer).
