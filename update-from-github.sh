#!/usr/bin/env bash
# Обновление репозитория с GitHub и запуск готового Docker-образа из registry.
# Запуск на сервере (Linux), из корня клонированного репозитория:
#   bash ./update-from-github.sh [ветка]
# По умолчанию ветка: main. Переменная REMOTE (по умолчанию origin).

set -euo pipefail

# Корень репозитория: каталог скрипта; pwd -P — физический путь (без «висящих» symlink в компонентах пути).
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd -P)"
cd "$ROOT"

COMPOSE_FILE="$ROOT/docker/docker-compose.yml"
ENV_FILE="$ROOT/docker/.env"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Ошибка: нет файла $ENV_FILE"
  echo "Скопируйте: cp docker/env.example docker/.env и заполните значения."
  exit 1
fi

BRANCH="${1:-main}"
REMOTE="${REMOTE:-origin}"

echo ">>> репозиторий: $ROOT"
echo ">>> compose: $COMPOSE_FILE"

echo ">>> параметры из $ENV_FILE (проверьте, что это этот сайт, не копия .env с другого):"
grep -E '^(SITE_CONTAINER_NAME|TRAEFIK_ROUTER|SITE_IMAGE)=' "$ENV_FILE" || true

# На сервере часто export COMPOSE_FILE=... под один сайт — Compose тогда мержит файлы и трогает чужой стек.
# Важно: при set -u после unset COMPOSE_FILE нельзя ссылаться на $COMPOSE_FILE — сохраняем путь заранее.
compose_run() {
  local compose_path="$COMPOSE_FILE"
  (
    unset COMPOSE_FILE COMPOSE_PROJECT_NAME
    docker compose \
      --project-directory "$ROOT" \
      --env-file "$ENV_FILE" \
      -f "$compose_path" \
      "$@"
  )
}

echo ">>> git fetch $REMOTE $BRANCH"
git fetch "$REMOTE" "$BRANCH"

echo ">>> git merge --ff-only $REMOTE/$BRANCH"
if ! git merge --ff-only "$REMOTE/$BRANCH"; then
  echo "Не удалось fast-forward. Разрешите конфликты или сделайте rebase вручную."
  exit 1
fi

echo ">>> docker compose pull"
if ! compose_run pull; then
  echo
  echo "Ошибка: Docker не смог скачать образ из GHCR."
  echo "Проверьте, что GitHub package ghcr.io/idpro1313/genshintop публичный"
  echo "или выполните на сервере docker login ghcr.io с GitHub token, у которого есть read:packages."
  echo
  echo "Пример:"
  echo "  echo \"GITHUB_TOKEN\" | docker login ghcr.io -u idpro1313 --password-stdin"
  exit 1
fi

echo ">>> docker compose up -d"
compose_run up -d

docker image prune -f >/dev/null 2>&1 || true

echo "Готово: образ скачан из registry, контейнер обновлён ($REMOTE/$BRANCH)."
