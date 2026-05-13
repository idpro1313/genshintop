# Деплой genshintop.ru

Основной способ: **Docker из GHCR**. Образ содержит **nginx + PHP-FPM + приложение** (стек как dandangers); статические файлы в **`public/`**, страницы рендерит **`public/index.php`**.

**Legacy:** прежняя схема «чистая статика Astro в `dist/`» после релиза **1.0.0** не является основной; см. исторический контент в `docs/HISTORY.md`.

Общее требование: на сервере уже поднят **Traefik** и внешняя сеть Docker **`web`** (как в [webserver](https://github.com/idpro1313/webserver)).

---

## Docker из GHCR

### Подготовка на сервере

```bash
cd /opt   # или ваш каталог
git clone https://github.com/ВАШ_АКК/genshintop.git
cd genshintop
cp deploy/env.example deploy/.env
# отредактируйте deploy/.env — домены в TRAEFIK_RULE
```

Если при `docker compose pull` появляется `unauthorized`, значит GHCR package приватный или сервер не залогинен.

Вариант A: сделайте package публичным в GitHub: **Packages → genshintop → Package settings → Change visibility → Public**.

Вариант B: один раз выполните на сервере login с GitHub token, у которого есть право `read:packages`:

```bash
echo "GITHUB_TOKEN" | docker login ghcr.io -u idpro1313 --password-stdin
```

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

Скрипт делает `git fetch` + `git merge --ff-only` и затем `docker compose pull` + `up -d`. Сборка образа выполняется в CI (**`.github/workflows/docker-image.yml`**), не на прод-сервере.

### Локальный просмотр без Traefik

```bash
docker build -f docker/Dockerfile -t genshintop-web .
docker run --rm -p 8080:80 genshintop-web
# открыть http://localhost:8080
```

---

## Откат после выката

1. В GitHub Packages найти предыдущий образ по digest или по тегу (`sha-<gitsha>` публикует workflow).
2. На сервере в `deploy/.env` задать `SITE_IMAGE=ghcr.io/…@sha256:…` (или конкретный тег).
3. Выполнить `docker compose --env-file deploy/.env -f deploy/docker-compose.yml pull && … up -d`.

Без смены тега `latest` откат — только через указание другого `SITE_IMAGE`.

---

## Паритет URL и SEO (кратко)

После миграции на PHP проверьте вручную (или по **`docs/SEO-CHECKLIST.md`**):

- Ключевые маршруты (`/`, `/guides`, `/characters`, хабы, `/lootbar/*`, `/regions/*`, trust-страницы) открываются с ожидаемым HTTP-кодом.
- **`https://genshintop.ru/sitemap.xml`** — единый `urlset`, без индекса из нескольких файлов.
- **`robots.txt`** содержит одну строку **`Sitemap:`** на этот файл.
- **`/rss.xml`** возвращает **404** (RSS отключён).
- Редиректы slug работают (**`deploy/genshintop-redirects.conf`** подключается из **`docker/nginx-default.conf`**).

---

## Traefik и Let's Encrypt (`tls: unrecognized name`)

Проблема **не в образе genshintop** (контейнер слушает только **HTTP :80**; TLS терминирует **Traefik**). Ошибка возникает на стороне **[webserver](https://github.com/idpro1313/webserver)** и маршрута до Traefik.

### Смысл ошибки

Сообщение вида `urn:ietf:params:acme:error:tls` и **`remote error: tls: unrecognized name`** при выдаче сертификата обычно означает: Let's Encrypt для проверки **TLS-ALPN-01** подключился к вашему IP на **443**, но TLS-ответ **не тот**, который ожидается для челленджа (часто на 443 отвечает **не Traefik**, другой прокси без нужного SNI, или трафик не доходит до Traefik).

### Что проверить

1. **DNS:** **A** / **AAAA** для `genshintop.ru` и `www` указывают на сервер с Traefik. «Битая» запись **AAAA** (IPv6 уходит не туда) часто даёт расхождение IP при проверке LE.
2. **Порты:** с интернета **80 и 443** проброшены на контейнер **Traefik**, не на хостовый nginx без ACME и не напрямую на образ сайта.
3. **Кто слушает 443** на хосте — должен быть Docker/Traefik, без конфликтующего TLS-сервера поверх.

### Диагностика TLS снаружи

```bash
openssl s_client -connect genshintop.ru:443 -servername genshintop.ru </dev/null 2>/dev/null | openssl x509 -noout -subject -issuer -dates
```

Если рукопожатие странное или сертификат «не тот», сначала исправьте маршрутизацию до Traefik.

### Обходной путь: HTTP-01 вместо TLS-ALPN

В **статической конфигурации Traefik** для резолвера **`le`** задайте **HTTP challenge** на entrypoint **`web`** (имя должно совпадать с вашим `traefik`/compose):

```yaml
certificatesResolvers:
  le:
    acme:
      email: ваш-email@example.com
      storage: /letsencrypt/acme.json
      httpChallenge:
        entryPoint: web
```

У этого же резолвера не используйте параллельно **`tlsChallenge`**. После правки перезапустите Traefik.

Ответы на **`/.well-known/acme-challenge/`** отдаёт **Traefik**; nginx внутри образа genshintop к проверке LE не участвует.

---

## Файлы

| Файл | Назначение |
|------|------------|
| `docker/Dockerfile` | php-fpm-alpine + nginx + supervisor; `RUN php lib/build-sitemap.php`; сборка: **`docker build -f docker/Dockerfile .`** из корня репо |
| `docker/nginx-default.conf` | Активный server-блок в образе: gzip, заголовки, try_files → `index.php`, типы `.xml`/`.txt`, include редиректов |
| `deploy/docker-compose.yml` | Сервис `web`, образ GHCR, labels Traefik |
| `deploy/genshintop-redirects.conf` | Редиректы slug (ручная правка или внешний генератор) |
| `docs/SEO-CHECKLIST.md` | Чек-лист после выката |
| `deploy/env.example` | Шаблон `deploy/.env`, включая `SITE_IMAGE` |
| `deploy/update-from-github.sh` | Обновление на Linux: git fast-forward, pull образа, up -d |
| `.dockerignore` | Контекст сборки образа |

`deploy/.env` в репозиторий не коммитится (см. `.gitignore`).
