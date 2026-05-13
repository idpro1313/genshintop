# Пересборка тел страниц персонажей из существующего frontmatter.
# Перед записью копирует *.md в content/characters-archive/ (резервная копия «как было»).
# Запуск из корня репозитория: pwsh scripts/rebuild-character-pages.ps1

[CmdletBinding()]
param()

$ErrorActionPreference = 'Stop'
$RepoRoot = Resolve-Path (Join-Path $PSScriptRoot '..')
$CharDir = Join-Path $RepoRoot 'content/characters'
$ArchiveDir = Join-Path $RepoRoot 'content/characters-archive'

if (-not (Test-Path -LiteralPath $CharDir)) {
    throw "Не найден каталог: $CharDir"
}

New-Item -ItemType Directory -Force -Path $ArchiveDir | Out-Null
Copy-Item -Path (Join-Path $CharDir '*.md') -Destination $ArchiveDir -Force

$ElRuMap = @{
    'Pyro'    = 'Пиро'
    'Hydro'   = 'Гидро'
    'Electro' = 'Электро'
    'Cryo'    = 'Крио'
    'Anemo'   = 'Анемо'
    'Geo'     = 'Гео'
    'Dendro'  = 'Дендро'
}

function Get-MetaValue([string]$Block, [string]$Key) {
    if ($Block -match "(?m)^${Key}:\s*(.+)\s*$") {
        return $Matches[1].Trim().Trim('"')
    }
    return $null
}

function Test-IsCoreCharacterPage([string]$Slug) {
    if ($Slug -match '[\u0400-\u04FF]') { return $false }
    if ($Slug -match 'why-pull-|where-to-pull-|-vs-|reasons-for|budget-|discord|pronounce|personages|slovar|top-characters|promo-|bojestvenoe|besplatnie|fauti-|skins-|vse-ots|zachem-|personashy|^-vs-|^s-dnem|^s-dr-|-lore$|secret-kak|choise|raiden-miko|raiden-pricihini|kaveh-razbor|-obzor|^Подробный|^Линнея') {
        return $false
    }
    return $true
}

function Get-CoreBody {
    param(
        [string]$Name,
        [string]$ElementRu,
        [string]$Weapon,
        [string]$Rating
    )

    $ratingBlock = if ($Rating) {
        @"

Условная отметка **$Rating** в метаданных карточки — ориентир «насколько универсально брать героя в открытый мир и рутину», а не финальный вердикт для [Витой Бездны](/guides/vitaya-bezdna-vvedenie) или вашего любимого билда.
"@
    }
    else {
        @"

Роль выводите из описаний талантов и оружия «$Weapon»: персонаж может быть **основным DD**, **саппортом** или гибридом в одной [четвёрке](/guides/otryady-roli-elementy).
"@
    }

    return @"
## Кратко о персонаже

**$Name** — герой стихии **$ElementRu** с оружием типа «$Weapon». Это **редакционный профиль**: без копирования полного текста талантов из клиента — только порядок мыслей и ссылки на опорные материалы.

## Роль в отряде

Оцените, больше времени герой проводит в поле с обычными атаками или закрывает команду **навыками и взрывом стихии**. От этого зависит приоритет прокачки и выбор [артефактов](/guides/artefakty-farm-i-vybor).

$ratingBlock

## Таланты: порядок прокачки

1. **Элементальный навык** и **взрыв стихии** — чаще всего первые кандидаты в корону по урону или полезности для команды.
2. **Обычная атака / заряд** — поднимайте осознанно, если билд реально стоит на автоатаках или зарядах (смотрите коэффициенты в игре).
3. Пассивы открываются возвышениями — не забывайте про календарь [книг и боссов](/guides/talanty-knigi-i-korony).

Проверяйте [энергию и ротацию ультимейтов](/guides/energiya-vosstanovlenie-vzryva): дорогой взрыв без «батарейки» в отряде ощущается сильнее, чем лишний процент крита.

## Артефакты и характеристики

Ориентируйтесь на роль: **криты** ([база про крит](/guides/krit-shans-i-krit-uron-baza)), **бонус стихии** и **ЭМ** для реакционных сборок ([про ЭМ](/guides/elementarnyy-masterstvo-kogda-kachat)), поддержку через HP/ориентацию на восстановление — по описаниям талантов. Общая логика сетов и фарма — в [гайде по артефактам](/guides/artefakty-farm-i-vybor).

## Оружие

Подберите слот под роль и стихию: заготовки по связанным оружиям смотрите в YAML карточки (ключ relatedWeapons). Системные затраты — в [материалах оружия](/guides/oruzhie-vozvyshenie-materialy).

## Отряды и синергия

Соберите **реакцию и резонансы** под ваш контент: [стихии и базовые реакции](/guides/stihii-i-reaktsii-baza), скелет [четвёрки](/guides/otryady-roli-elementy). На высокий урон обычно важнее согласованный цикл, чем «пять разных стихий ради красоты».

## Созвёздия

Усиления по созвёздиям часто дают комфорт, но не являются обязательными для прохождения основного контента. Заранее планируйте [бюджет молитв](/guides/sozvezdiya-i-krutki-investicii).

## Кому подойдёт

Если вам откликается стихия **$ElementRu** и темп оружия «$Weapon», герой хороший кандидат для прокачки «в основу» или ротации между командами. Не ориентируйтесь только на букву рейтинга — мета и ваш кайф от анимаций важнее таблиц.

## См. также

Блок «Связанные гайды» на странице содержит короткие переходы в опорный корпус сайта.
"@
}

function Get-MiscBody {
    param(
        [string]$Slug,
        [string]$Name,
        [string]$Title
    )

    $extra = ''
    if ($Slug -match '^why-pull-(.+)$') {
        $h = $Matches[1]
        $extra = "`n`nКарточка героя: [/characters/$h](/characters/$h). "
    }
    elseif ($Slug -match '^(.+)-vs-(.+)$') {
        $a = $Matches[1]
        $b = $Matches[2]
        $extra = "`n`nСравниваемые профили: [/characters/$a](/characters/$a) и [/characters/$b](/characters/$b). "
    }

    return @"
## О чём эта страница

Статья **«$Name»** — вспомогательный материал вокруг молитв, меты или сравнения билдов. Прежний текст с разнесёнными по строкам фразами **заменён** на короткое редакционное вступление: актуальные анонсы баннеров смотрите в клиенте и на официальных каналах HoYoverse.$extra

## Куда перейти

- [Молитвы и баннеры](/guides/bannery-sobytiya-molitvy)
- [Примогемы и планирование](/guides/primogemy-kopim-tratim)
- [Созвёздия и бюджет круток](/guides/sozvezdiya-i-krutki-investicii)
- [Тир-листы: как читать](/guides/tir-listy-kak-chitat)
- [Каталог персонажей](/characters)

## Примечание

Если нужен снова развёрнутый разбор «за / против», его лучше оформлять отдельной статьёй в [гайдах](/guides) с датой reviewedAt в frontmatter; карточки в `/characters/` держим компактными.
"@
}

function Add-FrontmatterField {
    param(
        [string]$Fm,
        [string]$FieldPattern,
        [string]$NewLine
    )
    if ($Fm -match $FieldPattern) { return $Fm }
    return ($Fm.TrimEnd() + "`n" + $NewLine)
}

$files = Get-ChildItem -LiteralPath $CharDir -Filter '*.md' -File | Sort-Object Name
$today = Get-Date -Format 'yyyy-MM-dd'

foreach ($file in $files) {
    $raw = Get-Content -LiteralPath $file.FullName -Raw -Encoding UTF8
    if ($raw -notmatch '(?s)\A---\r?\n(.+?)\r?\n---\r?\n(.*)\z') {
        Write-Warning "Пропуск (нет frontmatter): $($file.Name)"
        continue
    }
    $fm = $Matches[1]
    $slug = $file.BaseName

    $name = Get-MetaValue $fm 'name'
    if (-not $name) { $name = $slug }
    $element = Get-MetaValue $fm 'element'
    if (-not $element) { $element = 'Anemo' }
    $weapon = Get-MetaValue $fm 'weapon'
    if (-not $weapon) { $weapon = 'Прочее' }
    $rating = Get-MetaValue $fm 'rating'
    $title = Get-MetaValue $fm 'title'
    if (-not $title) { $title = $name }

    $elementKey = $element.Trim()
    $elementRu = $ElRuMap[$elementKey]
    if (-not $elementRu) { $elementRu = $elementKey }

    $summaryText = "$name — краткий справочный материал GenshinTop: стихия $elementRu, оружие «$weapon». Без копипасты текстов талантов из клиента."
    $fm2 = Add-FrontmatterField $fm '(?m)^summary:' ("summary: `"$summaryText`"")
    $fm2 = Add-FrontmatterField $fm2 '(?m)^gameVersion:' 'gameVersion: "6.x"'
    $fm2 = Add-FrontmatterField $fm2 '(?m)^reviewedAt:' "reviewedAt: $today"

    if (Test-IsCoreCharacterPage $slug) {
        $body = Get-CoreBody -Name $name -ElementRu $elementRu -Weapon $weapon -Rating $rating
    }
    else {
        $body = Get-MiscBody -Slug $slug -Name $name -Title $title
    }

    # Пишем UTF-8 без BOM (как большинство файлов в репо)
    $out = "---`r`n$fm2`r`n---`r`n`r`n" + $body.TrimEnd() + "`r`n"
    $utf8NoBom = New-Object System.Text.UTF8Encoding $false
    [System.IO.File]::WriteAllText($file.FullName, $out, $utf8NoBom)
}

Write-Host "Готово: $($files.Count) файлов, архив: $ArchiveDir"
