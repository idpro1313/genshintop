# Деплой genshintop.ru

Два варианта:

1. **Docker в этом репозитории** — образ сам собирает Astro (`npm run build`) и отдаёт статику через nginx. Удобно вместе со скриптом обновления с GitHub.
2. **Только статика + шаблон webserver** — собираете `dist/` локально/в CI и монтируете в `templates/static-site` из [webserver](https://github.com/idpro1313/webserver).

Общее требование: на сервере уже поднят **Traefik** и внешняя сеть Docker **`web`** (как в webserver).

---

## Вариант 1: Docker (сборка внутри образа)

### Подготовка на сервере

```bash
cd /opt   # или ваш каталог
git clone https://github.com/ВАШ_АКК/genshintop.git
cd genshintop
cp deploy/env.example deploy/.env
# отредактируйте deploy/.env — домены в TRAEFIK_RULE
```

### Первый запуск

```bash
cd /opt/genshintop
docker compose --env-file deploy/.env -f deploy/docker-compose.yml up -d --build
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

Скрипт делает `git fetch` + `git merge --ff-only` и затем `docker compose build` + `up -d`. Если на ветке есть локальные коммиты без push, fast-forward может не сработать — тогда обновите git вручную.

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
| `deploy/docker-compose.yml` | Сервис `web`, labels Traefik |
| `deploy/nginx-docker.conf` | gzip, кэш статики |
| `deploy/env.example` | Шаблон `deploy/.env` |
| `deploy/update-from-github.sh` | Обновление на Linux |
| `deploy/update-from-github.ps1` | Обновление на Windows |
| `.dockerignore` | Исключает `node_modules`, `gi-database` и т.д. из контекста сборки |

`deploy/.env` в репозиторий не коммитится (см. `.gitignore`).
