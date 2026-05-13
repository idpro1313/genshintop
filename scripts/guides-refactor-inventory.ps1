# Инвентаризация content/guides (альтернатива php scripts/guides-refactor-inventory.php на машинах без PHP в PATH).
# Запуск из корня: pwsh scripts/guides-refactor-inventory.ps1

$ErrorActionPreference = 'Stop'
$root = Split-Path -Parent $PSScriptRoot
if (-not (Test-Path (Join-Path $root 'content/guides'))) {
    $root = Get-Location
}
$guidesDir = Join-Path $root 'content/guides'
$reportsDir = Join-Path $root 'reports'
if (-not (Test-Path $reportsDir)) { New-Item -ItemType Directory -Path $reportsDir | Out-Null }

function Get-FrontmatterBody([string]$raw) {
    if ($raw -notmatch '(?s)\A---\r?\n(.+?)\r?\n---\r?\n(.*)\z') {
        return @{ meta = @{}; body = $raw }
    }
    $yamlBlock = $Matches[1]
    $body = $Matches[2]
    $meta = @{}
    foreach ($line in $yamlBlock -split "`n") {
        if ($line -match '^([A-Za-z0-9_]+):\s*(.*)$') {
            $meta[$Matches[1]] = $Matches[2].Trim().Trim('"').Trim("'")
        }
    }
    return @{ meta = $meta; body = $body }
}

$guideRows = @()
$titleIndex = @{}
$sourceSlugIndex = @{}

Get-ChildItem -Path (Join-Path $guidesDir '*.md') | ForEach-Object {
    $slug = [System.IO.Path]::GetFileNameWithoutExtension($_.Name)
    if ($slug.StartsWith('_')) { return }

    $raw = Get-Content $_.FullName -Raw -Encoding UTF8
    $parsed = Get-FrontmatterBody $raw
    $meta = $parsed.meta
    $body = $parsed.body

    $title = [string]$meta['title']
    $normTitle = ($title.ToLowerInvariant() -replace '\s+', ' ').Trim()
    if ($normTitle) {
        if (-not $titleIndex.ContainsKey($normTitle)) { $titleIndex[$normTitle] = [System.Collections.ArrayList]@() }
        [void]$titleIndex[$normTitle].Add($slug)
    }

    $srcSlug = if ($meta['sourceSlug']) { [string]$meta['sourceSlug'] } else { $slug }
    if (-not $sourceSlugIndex.ContainsKey($srcSlug)) { $sourceSlugIndex[$srcSlug] = [System.Collections.ArrayList]@() }
    [void]$sourceSlugIndex[$srcSlug].Add($slug)

    $shortLineCount = 0
    $contentLines = 0
    foreach ($line in $body -split "`n") {
        $t = $line.Trim()
        if ([string]::IsNullOrEmpty($t)) { continue }
        if ($t -match '^\#{1,6}\s') { continue }
        if ($t -match '^[-*+]\s') { continue }
        if ($t -match '^\d+\.\s') { continue }
        if ($t -match '^\|') { continue }
        if ($t -match '^---+$') { continue }
        $contentLines++
        if ($t.Length -lt 48) { $shortLineCount++ }
    }
    $ladderRatio = if ($contentLines -gt 0) { [math]::Round($shortLineCount / $contentLines, 4) } else { 0 }

    $headingCount = ([regex]::Matches($body, '(?m)^#{2,6}\s')).Count
    $plain = ($body -replace '<[^>]+>', ' ') -replace '\s+', ' '
    $charLen = $plain.Trim().Length
    $splitCandidate = ($charLen -gt 9000) -and ($headingCount -lt 4)
    $brokenAnchorCount = ([regex]::Matches($body, '\]\(#\)')).Count

    $wordCount = ([regex]::Matches($body, '\p{L}[\p{L}\p{N}_-]*', [System.Text.RegularExpressions.RegexOptions]::None)).Count

    $nonAsciiSlug = $slug -match '[^\x00-\x7F]'

    $guideRows += [ordered]@{
        slug              = $slug
        title             = $title
        category          = $meta['category']
        topic             = $meta['topic']
        gameVersion       = $meta['gameVersion']
        status            = $meta['status']
        wordCount         = $wordCount
        headingCount      = $headingCount
        ladderRatio       = $ladderRatio
        brokenAnchorCount = $brokenAnchorCount
        splitCandidate    = [bool]$splitCandidate
        slugHasNonAscii   = [bool]$nonAsciiSlug
    }
}

$mergeByTitle = @()
foreach ($kv in $titleIndex.GetEnumerator()) {
    $slugs = $kv.Value | Select-Object -Unique
    if (@($slugs).Count -gt 1) {
        $mergeByTitle += [ordered]@{ normalizedTitle = $kv.Key; slugs = @($slugs) }
    }
}

$mergeBySourceSlug = @()
foreach ($kv in $sourceSlugIndex.GetEnumerator()) {
    $slugs = $kv.Value | Select-Object -Unique
    if (@($slugs).Count -gt 1) {
        $mergeBySourceSlug += [ordered]@{ sourceSlug = $kv.Key; slugs = @($slugs) }
    }
}

$splitCandidates = @($guideRows | Where-Object { $_.splitCandidate } | ForEach-Object { $_.slug })

$guideRows = @($guideRows | Sort-Object @{ Expression = 'ladderRatio'; Descending = $true }, @{ Expression = 'slug'; Descending = $false })

$out = [ordered]@{
    generatedAt                   = [datetime]::UtcNow.ToString('s') + 'Z'
    guidesDir                     = 'content/guides'
    totalGuides                   = $guideRows.Count
    guides                        = @($guideRows)
    mergeCandidatesByTitle        = @($mergeByTitle | Sort-Object normalizedTitle)
    mergeCandidatesBySourceSlug   = @($mergeBySourceSlug | Sort-Object sourceSlug)
    splitCandidates               = $splitCandidates
}

$jsonPath = Join-Path $reportsDir 'guides-refactor-inventory.json'
$json = $out | ConvertTo-Json -Depth 12 -Compress:$false
[System.IO.File]::WriteAllText($jsonPath, $json + "`n", [System.Text.UTF8Encoding]::new($false))
Write-Host "Wrote $jsonPath"
