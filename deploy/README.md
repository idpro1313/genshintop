# Деплой genshintop.ru (Traefik + static-site)

Сервер настроен по схеме [idpro1313/webserver](https://github.com/idpro1313/webserver): общий **Traefik** на 80/443, отдельный контейнер **nginx** на сайт.

## Сборка

В корне репозитория (с установленным Node.js):

```powershell
npm install
npm run content:migrate
npm run build
```

Артефакт: папка **`dist/`** — её содержимое нужно раздавать как статику.

## На сервере (шаблон `templates/static-site`)

1. Склонировать [webserver](https://github.com/idpro1313/webserver) в `/opt/webserver` (или аналог).
2. Скопировать шаблон и настроить `.env`:

```bash
cd /opt/webserver
cp -r templates/static-site sites/genshintop
cd sites/genshintop
cp env.example .env
```

В `.env` задайте:

- `SITE_CONTAINER_NAME` — уникально, например `genshintop_nginx`
- `SITE_ROOT` — абсолютный путь к каталогу со **содержимым** `dist/` (например `/opt/genshintop/html`)
- `TRAEFIK_ROUTER` — уникально, например `genshintop`
- `TRAEFIK_RULE` — например ``Host(`genshintop.ru`) || Host(`www.genshintop.ru`)``

3. Залейте файлы из локального `dist/` в `SITE_ROOT` (rsync/scp/GitHub Actions).
4. `docker compose up -d` в каталоге сайта.

DNS: **A**-запись `genshintop.ru` и при необходимости `www` на IP сервера.

## Локальный пример compose

Файл [`docker-compose.example.yml`](./docker-compose.example.yml) — ориентир по переменным; на проде используйте копию из репозитория **webserver** и свой `.env`.
