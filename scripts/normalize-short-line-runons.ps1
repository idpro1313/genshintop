# Склеивает «лесенку»: игнорирует пустые строки, сливает подряд идущие строки в абзацы.
# Разрыв абзаца — когда предыдущий закончен [.!?…] и следующая строка длинная (≥55) и с заглавной.
# Поддерживает строки-заголовки Markdown ## … .
# Запуск: pwsh scripts/normalize-short-line-runons.ps1 -RelativePath content/guides/paralogism-5-6.md

param(
    [Parameter(Mandatory = $true)]
    [string]$RelativePath
)

$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$path = Join-Path $root ($RelativePath -replace '/', [IO.Path]::DirectorySeparatorChar)
if (-not (Test-Path $path)) { throw "Not found: $path" }

$raw = [System.IO.File]::ReadAllText($path, [System.Text.UTF8Encoding]::new($false))
if ($raw -notmatch '(?s)\A(---\r?\n.+?\r?\n---\r?\n)(.*)\z') {
    throw 'Expected YAML frontmatter --- ... ---'
}
$fm = $Matches[1]
$body = $Matches[2]

$nonEmpty = foreach ($line in ($body -split '\r?\n')) {
    $t = $line.Trim()
    if ($t -ne '') { $t }
}

$blocks = New-Object System.Collections.Generic.List[string]
$cur = ''

foreach ($t in $nonEmpty) {
    if ($t -match '^\#{1,6}\s') {
        if ($cur -ne '') {
            [void]$blocks.Add($cur.Trim())
            $cur = ''
        }
        [void]$blocks.Add($t)
        continue
    }

    if ($cur -eq '') {
        $cur = $t
        continue
    }

    $endsSentence = $cur -match '[\.!?…]["»"\)]?\s*$'
    $nxLong = $t.Length -ge 55
    $nxStartsCapital = $t -cmatch '^[А-ЯЁA-Z«"(]'
    if ($endsSentence -and $nxLong -and $nxStartsCapital) {
        [void]$blocks.Add($cur.Trim())
        $cur = $t
    }
    else {
        $cur = ($cur + ' ' + $t).Trim()
    }
}

if ($cur -ne '') {
    [void]$blocks.Add($cur.Trim())
}

$newBody = (($blocks -join "`n`n").TrimEnd() + "`n")
[System.IO.File]::WriteAllText($path, $fm + $newBody, [System.Text.UTF8Encoding]::new($false))
Write-Host "Normalized: $RelativePath (paragraph blocks: $($blocks.Count))"
