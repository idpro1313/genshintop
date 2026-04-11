# Обновление с GitHub и пересборка Docker (Windows + Docker Desktop).
# Запуск из корня репозитория:
#   .\deploy\update-from-github.ps1
#   .\deploy\update-from-github.ps1 develop

$ErrorActionPreference = 'Stop'

$Root = Split-Path -Parent $PSScriptRoot
$ComposeFile = Join-Path $PSScriptRoot 'docker-compose.yml'
$EnvFile = Join-Path $PSScriptRoot '.env'

if (-not (Test-Path $EnvFile)) {
    Write-Error "Нет deploy\.env — скопируйте deploy\env.example в deploy\.env и заполните."
}

$Branch = if ($args.Count -ge 1) { $args[0] } else { 'main' }
$Remote = if ($env:REMOTE) { $env:REMOTE } else { 'origin' }

Set-Location $Root

Write-Host ">>> git fetch $Remote $Branch"
git fetch $Remote $Branch

Write-Host ">>> git merge --ff-only ${Remote}/${Branch}"
git merge --ff-only "${Remote}/${Branch}"

Write-Host ">>> docker compose build"
docker compose --env-file $EnvFile -f $ComposeFile build --pull

Write-Host ">>> docker compose up -d"
docker compose --env-file $EnvFile -f $ComposeFile up -d

docker image prune -f 2>$null | Out-Null

Write-Host "Готово: образ пересобран, контейнер обновлён ($Remote/$Branch)."
