#!/usr/bin/env bash
# Обновление репозитория с GitHub и запуск готового Docker-образа из registry.
# Запуск с сервера (Linux), из любой директории:
#   bash deploy/update-from-github.sh [ветка]
# По умолчанию ветка: main. Переменная REMOTE (по умолчанию origin).

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

COMPOSE_FILE="$ROOT/deploy/docker-compose.yml"
ENV_FILE="$ROOT/deploy/.env"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Ошибка: нет файла $ENV_FILE"
  echo "Скопируйте: cp deploy/env.example deploy/.env и заполните значения."
  exit 1
fi

BRANCH="${1:-main}"
REMOTE="${REMOTE:-origin}"

echo ">>> git fetch $REMOTE $BRANCH"
git fetch "$REMOTE" "$BRANCH"

echo ">>> git merge --ff-only $REMOTE/$BRANCH"
if ! git merge --ff-only "$REMOTE/$BRANCH"; then
  echo "Не удалось fast-forward. Разрешите конфликты или сделайте rebase вручную."
  exit 1
fi

echo ">>> docker compose pull"
docker compose --env-file "$ENV_FILE" -f "$COMPOSE_FILE" pull

echo ">>> docker compose up -d"
docker compose --env-file "$ENV_FILE" -f "$COMPOSE_FILE" up -d

docker image prune -f >/dev/null 2>&1 || true

echo "Готово: образ скачан из registry, контейнер обновлён ($REMOTE/$BRANCH)."
