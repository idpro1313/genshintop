# START_MODULE_CONTRACT
#   PURPOSE: Normalize migrated guide Markdown without dropping article text.
#   SCOPE: Cleans duplicated headings, broken migration links/images, spacing, and SEO summaries in src/content/guides.
#   DEPENDS: src/content/guides Markdown collection
#   LINKS: M-CONTENT-PIPELINE
# END_MODULE_CONTRACT
#
# START_MODULE_MAP
#   Clean-GuideMarkdown - applies conservative formatting cleanup to one guide file
# END_MODULE_MAP

$ErrorActionPreference = 'Stop'

$Root = Split-Path -Parent $PSScriptRoot
$GuidesDir = Join-Path $Root 'src/content/guides'

$WordBanners = -join ([char[]](0x0411,0x0430,0x043d,0x043d,0x0435,0x0440,0x044b))
$WordNew = -join ([char[]](0x041d,0x043e,0x0432,0x044b,0x0435))
$WordUpdatesLower = -join ([char[]](0x043e,0x0431,0x043d,0x043e,0x0432,0x043b,0x0435,0x043d,0x0438,0x044f))
$WordCharactersLower = -join ([char[]](0x043f,0x0435,0x0440,0x0441,0x043e,0x043d,0x0430,0x0436,0x0438))
$WordPrayerEvent = -join ([char[]](0x041c,0x043e,0x043b,0x0438,0x0442,0x0432,0x0430,0x0020,0x0441,0x043e,0x0431,0x044b,0x0442,0x0438,0x044f))
$WordCharacterGen = -join ([char[]](0x043f,0x0435,0x0440,0x0441,0x043e,0x043d,0x0430,0x0436,0x0430))
$WordWeaponGen = -join ([char[]](0x043e,0x0440,0x0443,0x0436,0x0438,0x044f))
$Star = [char]0x2605
$RightGuillemet = [char]0x00BB

function Normalize-Text([string] $Value) {
  if ($null -eq $Value) {
    return ''
  }

  return ($Value -replace '\u00a0', ' ' -replace '\s+', ' ').Trim()
}

function Normalize-Comparable([string] $Value) {
  return (Normalize-Text $Value).ToLowerInvariant() -replace '[^\p{L}\p{N}]+', ''
}

function Escape-YamlString([string] $Value) {
  $escaped = $Value -replace '\\', '\\' -replace '"', '\"'
  return '"' + $escaped + '"'
}

function Strip-MarkdownForSummary([string] $Body) {
  $text = $Body
  $text = $text -replace '(?s)```.*?```', ' '
  $text = $text -replace '\[!\[[^\]]*\]\([^\)]*\)\]\([^\)]*\)', ' '
  $text = $text -replace '!\[([^\]]*)\]\([^\)]*\)', '$1'
  $text = $text -replace '\[([^\]]+)\]\([^\)]*\)', '$1'
  $text = $text -replace '[#>*_`~\[\]\(\)|]', ' '
  $text = [regex]::Replace($text, "(?<=$Star)(?=\p{L})", ' ')
  $text = [regex]::Replace($text, "(?<=$RightGuillemet)(?=\d)", ' ')
  $text = Normalize-Text $text

  if ($text.Length -le 240) {
    return $text
  }

  $cut = $text.Substring(0, 240)
  $lastSpace = $cut.LastIndexOf(' ')
  if ($lastSpace -gt 160) {
    $cut = $cut.Substring(0, $lastSpace)
  }
  return $cut.TrimEnd('.', ',', ';', ':') + '...'
}

function Clean-GuideMarkdown([string] $Path) {
  $original = [System.IO.File]::ReadAllText($Path, [System.Text.Encoding]::UTF8)
  $normalizedNewlines = $original -replace "`r`n", "`n"

  if ($normalizedNewlines -notmatch '(?s)^---\n(.*?)\n---\n?(.*)$') {
    return $false
  }

  $frontmatter = $Matches[1]
  $body = $Matches[2]

  $title = ''
  if ($frontmatter -match '(?m)^title:\s*(.+)$') {
    $title = ($Matches[1].Trim() -replace '^"(.*)"$', '$1')
  }

  $lines = New-Object System.Collections.Generic.List[string]
  foreach ($line in ($body -split "`n", -1)) {
    $current = $line -replace '\u00a0', ' '
    $trimmed = $current.Trim()

    if ($trimmed -eq '[' -or $trimmed -eq ']' -or $trimmed -eq '**') {
      continue
    }

    if ($trimmed -eq '') {
      $lines.Add($current)
      continue
    }

    # Convert common broken migration fragments like "**Name](#)[" back into readable emphasis.
    if ($trimmed -match '^\*\*\s*(.+?)\]\(#\)\s*(.*)$') {
      $suffix = $Matches[2].Trim()
      $current = '**' + $Matches[1].Trim() + '**'
      if ($suffix.Length -gt 0) {
        $current = $current + ' ' + $suffix
      }
    } elseif ($trimmed -match '^\*\*(.+?)\]\(#\)\[?$') {
      $current = '**' + $Matches[1].Trim() + '**'
    } elseif ($trimmed -match '^\*\*(.+)$' -and $trimmed -notmatch '\*\*$' -and $trimmed -notmatch '\]\(') {
      $current = '**' + $Matches[1].Trim() + '**'
    }

    $lines.Add($current)
  }

  $body = ($lines -join "`n")

  # Remove empty ad images/links and replace other empty images with their alt text.
  $body = $body -replace '\[!\[[^\]]*\]\(\s*\)\]\(https?://[^\)]*\)', ''
  $body = $body -replace '!\[[^\]]*\]\(\s*\)(?=\]\(https?://)', ''
  $body = $body -replace '!\[([^\]]+)\]\(\s*\)', '*$1*'
  $body = $body -replace '(?m)^!\[([^\]]+)\]\(\s*$', '*$1*'

  # Drop migrated breadcrumbs/navigation crumbs, not article text.
  $body = $body -creplace '\[\u0413\u043b\u0430\u0432\u043d\u0430\u044f\]\((?:#|\.\./index\.html)\)', ''
  $body = $body -creplace '\[(?:\u0411\u0430\u043d\u043d\u0435\u0440\u044b|\u0413\u0430\u0439\u0434\u044b|\u041f\u0435\u0440\u0441\u043e\u043d\u0430\u0436\u0438|\u041f\u0440\u043e\u043c\u043e\u043a\u043e\u0434\u044b|\u041e\u0431\u043d\u043e\u0432\u043b\u0435\u043d\u0438\u044f)\]\(#\)', ''
  $body = $body -replace '\]\(\.\./index\.html\)', '](#)'

  # Make adjacent links/text readable.
  $body = $body -replace '\]\((#[^\)]*)\)\[', ']($1) ['
  $body = $body -replace '\]\((#[^\)]*)\)\(', ']($1) ('
  $body = $body -replace '\]\(([^)]+)\)(?=[\p{L}\p{N}])', ']($1) '
  $body = $body -replace '\*\*\[\*\*', "**`n`n**"
  $body = $body -replace '(?<=[\p{L}\p{N}])\[', ' ['
  $body = [regex]::Replace($body, "(?<=$Star)(?=\p{L})", ' ')
  $body = [regex]::Replace($body, "(?<=$RightGuillemet)(?=\d)", ' ')
  $body = $body -replace '(?<=[\.:,;!?])\[', ' ['
  $body = $body -replace '(?<=[\p{L}\p{N}])\*\*', ' **'
  $body = $body -replace '\*\*(?=[\p{L}\p{N}])', '** '
  $body = $body -replace '\*\*\s+([^*\n]+?)\s+\*\*', '**$1**'
  $body = $body -replace '\*\*\s+([^*\n]+?)\*\*', '**$1**'
  $body = $body -replace '\*\*([^*\n]+?)\s+\*\*', '**$1**'
  $body = $body -creplace '(?m)^(#{2,6}\s+.+?)(?:\u0411\u0430\u043d\u043d\u0435\u0440\u044b|\u0413\u0430\u0439\u0434\u044b|\u041f\u0435\u0440\u0441\u043e\u043d\u0430\u0436\u0438|\u041f\u0440\u043e\u043c\u043e\u043a\u043e\u0434\u044b|\u041e\u0431\u043d\u043e\u0432\u043b\u0435\u043d\u0438\u044f)\s*$', '$1'
  $body = [regex]::Replace($body, "(?m)^(#{2,6}\s+$WordBanners)\s*$", { param($m) $m.Groups[1].Value + ' ' + $WordUpdatesLower })
  $body = [regex]::Replace($body, "(?m)^(#{2,6}\s+$WordNew)\s*$", { param($m) $m.Groups[1].Value + ' ' + $WordCharactersLower })
  $eventPattern = "(?m)^(#{2,6}\s+$WordPrayerEvent\s+(?:$WordCharacterGen|$WordWeaponGen)\s+"".+?"")\S.+$"
  $body = [regex]::Replace($body, $eventPattern, { param($m) $m.Groups[1].Value })
  $body = $body -replace '(?m)^(#{2,6}\s+.*?)\s+\[$', '$1'
  $body = $body -replace '(?m)\s*\[$', ''
  $body = $body -replace '(?m)^[ \t]+(?=\S)', ''
  $body = $body -replace '[ \t]{2,}', ' '

  # Remove duplicate page H1 if it repeats frontmatter title; otherwise demote H1 to preserve content hierarchy.
  $bodyLines = New-Object System.Collections.Generic.List[string]
  foreach ($line in ($body -split "`n", -1)) {
    $bodyLines.Add($line)
  }

  for ($i = 0; $i -lt $bodyLines.Count; $i++) {
    if ($bodyLines[$i].Trim().Length -eq 0) {
      continue
    }

    if ($bodyLines[$i] -match '^\s*#\s+(.+?)\s*$') {
      $headingText = $Matches[1]
      if ((Normalize-Comparable $headingText) -eq (Normalize-Comparable $title)) {
        $bodyLines.RemoveAt($i)
        if ($i -lt $bodyLines.Count -and $bodyLines[$i].Trim().Length -eq 0) {
          $bodyLines.RemoveAt($i)
        }
      } else {
        $bodyLines[$i] = $bodyLines[$i] -replace '^\s*#\s+', '## '
      }
    }
    break
  }

  $body = ($bodyLines -join "`n")

  # Drop adjacent duplicate lines caused by old card/link markup.
  $dedupedLines = New-Object System.Collections.Generic.List[string]
  $lastNonEmpty = ''
  foreach ($line in ($body -split "`n", -1)) {
    $trimmed = $line.Trim()
    if ($trimmed.Length -gt 0 -and $trimmed -eq $lastNonEmpty) {
      continue
    }
    $dedupedLines.Add($line)
    if ($trimmed.Length -gt 0) {
      $lastNonEmpty = $trimmed
    }
  }
  $body = ($dedupedLines -join "`n")

  # Compact excessive blank lines introduced by migration artifacts.
  $body = $body -replace "(?m)^[ \t]+$", ''
  $body = $body -replace "\n{3,}", "`n`n"
  $body = $body.Trim() + "`n"

  # Склеенные **заголовок**#### подзаголовок — разнести переносами.
  $body = [regex]::Replace($body, '(\*\*[^*\r\n]+\*\*)(#{2,6})', {
      param($m) $m.Groups[1].Value + "`n`n" + $m.Groups[2].Value
    })

  $summary = Strip-MarkdownForSummary $body
  $tocWord = -join ([char[]](0x0421,0x043e,0x0434,0x0435,0x0440,0x0436,0x0430,0x043d,0x0438,0x0435))
  if ($summary.Length -gt 0 -and $summary.StartsWith($tocWord)) {
    $tail = $body -replace '(?s)^(?:#[^\n]+\n+)+', ''
    $summary2 = Strip-MarkdownForSummary $tail
    if ($summary2.Length -ge 40) { $summary = $summary2 }
  }
  if ($summary.Length -gt 0) {
    if ($frontmatter -match '(?m)^summary:\s*.*$') {
      $frontmatter = $frontmatter -replace '(?m)^summary:\s*.*$', ('summary: ' + (Escape-YamlString $summary))
    } else {
      $frontmatter = $frontmatter.TrimEnd() + "`nsummary: " + (Escape-YamlString $summary)
    }
  }

  $cleaned = "---`n$frontmatter`n---`n`n$body"
  if ($cleaned -ne $normalizedNewlines) {
    [System.IO.File]::WriteAllText($Path, ($cleaned -replace "`n", "`r`n"), [System.Text.UTF8Encoding]::new($false))
    return $true
  }

  return $false
}

$changed = 0
$files = Get-ChildItem -Path $GuidesDir -Filter '*.md' -File
foreach ($file in $files) {
  if (Clean-GuideMarkdown $file.FullName) {
    $changed++
  }
}

Write-Host "Processed $($files.Count) guide files; changed $changed files."
