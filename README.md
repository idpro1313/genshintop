# genshintop

Сайт **GenshinTop** (genshintop.ru): **PHP + nginx** в Docker, Markdown в **`content/`**, ванильный CSS в **`public/css/`**. В репозитории **нет Node.js, npm и `package.json`**.

## Карта сайта (локально / при сборке образа)

При наличии PHP в PATH:

```powershell
php lib/build-sitemap.php
```

Тот же шаг выполняется автоматически при **`docker build -f docker/Dockerfile .`** (контекст — корень репозитория).

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

Канонический контент — только **`content/{guides,characters}`**. Массовая генерация OG, карты редиректов и прочие тяжёлые пайплайны — вне этого репо при необходимости.

Версия проекта: **`VERSION`** (SemVer).
