# genshintop

Сайт **GenshinTop** (genshintop.ru): **PHP + nginx** в Docker, Markdown в **`content/`**, ванильный CSS в **`public/css/`**. В репозитории **нет Node.js, npm и `package.json`**.

## Карта сайта (локально / при сборке образа)

При наличии PHP в PATH:

```powershell
php scripts/build-sitemap.php
```

Тот же шаг выполняется автоматически при **`docker build`** (см. корневой **`Dockerfile`**).

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

Исходный корпус Markdown (не входит в рантайм сайта): **`gi-database/`** (`INDEX.md`, `database.json`). Синхронизация с **`content/`** — вручную или отдельным инструментом вне этого репо.

Версия проекта: **`VERSION`** (SemVer).
