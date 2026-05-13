# genshintop

Сайт **GenshinTop** (genshintop.ru): **PHP + nginx** в Docker, живой Markdown в **`content/`**, ванильный CSS в **`public/css/`**. В репозитории **нет Node.js, npm и `package.json`**.

## Карта сайта

`public/sitemap.xml` — статический файл в git. При добавлении или удалении страниц обновляйте его вручную вместе с навигацией.

## Docker и обновление с GitHub

```bash
cp docker/env.example docker/.env   # настроить домены
docker compose --env-file docker/.env -f docker/docker-compose.yml pull
docker compose --env-file docker/.env -f docker/docker-compose.yml up -d
./update-from-github.sh               # дальше обновления с тем же compose
```

Подробно: **`docker/README.md`** (откат образом, паритет URL).

## Документация

- **`docs/AGENTS.md`** — карта проекта для агентов и разработчиков  
- **`docs/HISTORY.md`** — журнал итераций  
- **`docker/README.md`** — Docker, Traefik, откат  
- **`grace/`** — GRACE (требования, план, верификация, граф знаний)

Канонический контент на сайте — **`content/`**. Старый каталог `info/` перенесён в архив. Массовая генерация OG и тяжёлые пайплайны — вне этого репо при необходимости.

Версия проекта: **`VERSION`** (SemVer).
