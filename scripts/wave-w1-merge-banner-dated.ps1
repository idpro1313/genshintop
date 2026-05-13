# W1: датированные дубли баннеров → один канонический slug (без суффикса даты).
# Читает reports/guides-refactor-inventory.json, дописывает docker/genshintop-redirects.conf,
# удаляет лишние info/guides/*.md, заменяет ссылки в content/**/*.md и info/**/*.md.
# Запуск из корня: pwsh scripts/wave-w1-merge-banner-dated.ps1

$ErrorActionPreference = 'Stop'
$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
Set-Location $root

function Test-DatedSlugSuffix([string]$slug) {
    return $slug -match '(-\d{1,2}-\d{1,2}-\d{4}(-\d{2}-\d{2}-\d{2})?|-\d{4}-\d{2}-\d{2}(-\d{2}-\d{2}-\d{2})?)$'
}

$invPath = Join-Path $root 'reports/guides-refactor-inventory.json'
if (-not (Test-Path $invPath)) {
    throw "Missing $invPath — run guides-refactor-inventory.ps1 first."
}

$inv = Get-Content $invPath -Raw -Encoding UTF8 | ConvertFrom-Json
$slugMap = [ordered]@{}

foreach ($grp in $inv.mergeCandidatesByTitle) {
    $slugs = @($grp.slugs)
    $undated = @(
        $slugs |
            Where-Object { -not (Test-DatedSlugSuffix $_) } |
            Sort-Object { $_.Length }, { $_ }
    )
    if ($undated.Count -eq 0) {
        Write-Warning "Skip group (no undated slug): $($grp.normalizedTitle)"
        continue
    }
    $canonical = $undated[0]
    $cf = Join-Path $root "info/guides/$canonical.md"
    if (-not (Test-Path $cf)) {
        Write-Warning "Skip group (missing canonical): $canonical"
        continue
    }

    foreach ($s in $slugs) {
        if ($s -eq $canonical) { continue }
        $pf = Join-Path $root "info/guides/$s.md"
        if (-not (Test-Path $pf)) { continue }
        $slugMap[$s] = $canonical
    }
}

Write-Host "Slug redirects (pairs): $($slugMap.Count)"

$confPath = Join-Path $root 'docker/genshintop-redirects.conf'
$existing = Get-Content $confPath -Raw -Encoding UTF8
$newRewrites = New-Object System.Collections.Generic.List[string]
$newRewrites.Add('')
$newRewrites.Add('# W1: датированные URL баннеров → канонический slug (генератор wave-w1-merge-banner-dated.ps1).')

foreach ($old in ($slugMap.Keys | Sort-Object)) {
    $new = $slugMap[$old]
    $line = "rewrite ^/guides/$old/?`$ /guides/$new permanent;"
    if ($existing -notmatch [regex]::Escape($line)) {
        $newRewrites.Add($line)
    }
}

Add-Content -Path $confPath -Value ($newRewrites -join "`n") -Encoding UTF8

# Replace links / relatedGuides before deleting files (longest slug first).
$sortedOld = $slugMap.Keys | Sort-Object { $_.Length } -Descending
$utf8 = New-Object System.Text.UTF8Encoding($false)
$scanRoots = @(
    (Join-Path $root 'content'),
    (Join-Path $root 'info')
)
foreach ($scanRoot in $scanRoots) {
    if (-not (Test-Path $scanRoot)) { continue }
    Get-ChildItem $scanRoot -Recurse -Filter '*.md' | ForEach-Object {
        $text = [System.IO.File]::ReadAllText($_.FullName, $utf8)
        $orig = $text
        foreach ($old in $sortedOld) {
            $new = $slugMap[$old]
            $text = $text.Replace("/guides/$old`"", "/guides/$new`"")
            $text = $text.Replace('/guides/' + $old + '/', '/guides/' + $new + '/')
            $text = $text.Replace("/guides/$old)", "/guides/$new)")
            $text = $text.Replace("/guides/$old>", "/guides/$new>")
            $text = $text.Replace("`"- $old`"", "`"- $new`"")
            $text = $text.Replace("- $old`r`n", "- $new`r`n")
            $text = $text.Replace("- $old`n", "- $new`n")
            $text = $text.Replace("($old.md)", "($new.md)")
        }
        if ($text -cne $orig) {
            [System.IO.File]::WriteAllText($_.FullName, $text, $utf8)
            Write-Host "Updated links: $($_.FullName.Substring($root.Length))"
        }
    }
}

$deleted = 0
foreach ($old in $slugMap.Keys) {
    $pf = Join-Path $root "info/guides/$old.md"
    if (Test-Path $pf) {
        Remove-Item -LiteralPath $pf -Force
        $deleted++
    }
}

Write-Host "Deleted duplicate guides: $deleted"
Write-Host 'Done. Regenerate: pwsh scripts/guides-refactor-inventory.ps1'
