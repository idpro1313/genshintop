# Деплой genshintop.ru

Два варианта:

1. **Docker из GHCR** — GitHub Actions собирает Astro в образ и публикует `ghcr.io/idpro1313/genshintop:latest`; сервер только скачивает готовый образ и перезапускает контейнер.
2. **Только статика + шаблон webserver** — собираете `dist/` локально/в CI и монтируете в `templates/static-site` из [webserver](https://github.com/idpro1313/webserver).

Общее требование: на сервере уже поднят **Traefik** и внешняя сеть Docker **`web`** (как в webserver).

---

## Вариант 1: Docker из GHCR

### Подготовка на сервере

```bash
cd /opt   # или ваш каталог
git clone https://github.com/ВАШ_АКК/genshintop.git
cd genshintop
cp deploy/env.example deploy/.env
# отредактируйте deploy/.env — домены в TRAEFIK_RULE
```

Если образ в GHCR приватный, один раз выполните `docker login ghcr.io` с GitHub token, у которого есть право `read:packages`.

### Первый запуск

```bash
cd /opt/genshintop
docker compose --env-file deploy/.env -f deploy/docker-compose.yml pull
docker compose --env-file deploy/.env -f deploy/docker-compose.yml up -d
```

### Обновление с GitHub

```bash
cd /opt/genshintop
chmod +x deploy/update-from-github.sh   # один раз
./deploy/update-from-github.sh          # ветка main по умолчанию
./deploy/update-from-github.sh develop  # другая ветка
```

Переменная окружения **`REMOTE`** (по умолчанию `origin`) задаёт имя remote.

**Windows (Docker Desktop):**

```powershell
cd путь\к\genshintop
Copy-Item deploy\env.example deploy\.env
.\deploy\update-from-github.ps1
```

Скрипт делает `git fetch` + `git merge --ff-only` и затем `docker compose pull` + `up -d`. Локальная сборка на сервере больше не выполняется: образ должен быть опубликован workflow `.github/workflows/docker-image.yml`.

### Локальный просмотр без Traefik

```bash
docker build -t genshintop-web .
docker run --rm -p 8080:80 genshintop-web
# открыть http://localhost:8080
```

---

## Вариант 2: только `dist/` + static-site из webserver

См. прежнюю схему в корне репозитория [`README.md`](../README.md): `npm run build`, копирование `dist/` в `SITE_ROOT`, `templates/static-site` из webserver.

Файл [`docker-compose.example.yml`](./docker-compose.example.yml) остаётся ориентиром для монтирования готовой статики без сборки в Docker.

---

## Файлы

| Файл | Назначение |
|------|------------|
| `Dockerfile` | Node → `npm run build` → nginx |
| `deploy/docker-compose.yml` | Сервис `web`, готовый образ GHCR, labels Traefik |
| `deploy/nginx-docker.conf` | gzip, кэш статики, 404 → `404.html`, XML/robots без SPA-fallback |
| `deploy/SEO-CHECKLIST.md` | Чек-лист после выката: sitemap, `/lootbar`, кабинеты поиска |
| `deploy/env.example` | Шаблон `deploy/.env`, включая `SITE_IMAGE` для GHCR |
| `deploy/update-from-github.sh` | Обновление на Linux: git fast-forward, pull образа, up -d |
| `deploy/update-from-github.ps1` | Обновление на Windows: git fast-forward, pull образа, up -d |
| `.dockerignore` | Исключает `node_modules`, `gi-database` и т.д. из контекста сборки |

`deploy/.env` в репозиторий не коммитится (см. `.gitignore`).
