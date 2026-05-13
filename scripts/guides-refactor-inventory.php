<?php

declare(strict_types=1);

/**
 * CLI: инвентаризация content/guides-archive для редакционного рефакторинга.
 * Запуск из корня: php scripts/guides-refactor-inventory.php
 */

define('SITE_ROOT', dirname(__DIR__));
require_once SITE_ROOT . '/lib/Frontmatter.php';

$guidesDir = SITE_ROOT . '/content/guides-archive';
$files = glob($guidesDir . '/*.md') ?: [];

/** @var list<array<string,mixed>> */
$guideRows = [];
/** @var array<string, list<string>> */
$titleIndex = [];
/** @var array<string, list<string>> */
$sourceSlugIndex = [];

foreach ($files as $path) {
    $slug = basename($path, '.md');
    if (str_starts_with($slug, '_')) {
        continue;
    }
    $raw = (string) file_get_contents($path);
    $split = Frontmatter::split($raw);
    /** @var array<string, mixed> $meta */
    $meta = $split['meta'];
    $body = $split['body'];

    $title = isset($meta['title']) && is_string($meta['title']) ? $meta['title'] : '';
    $normTitle = mb_strtolower(preg_replace('/\s+/u', ' ', trim($title)));
    if ($normTitle !== '') {
        $titleIndex[$normTitle][] = $slug;
    }

    $srcSlug = isset($meta['sourceSlug']) && is_string($meta['sourceSlug']) ? $meta['sourceSlug'] : $slug;
    $sourceSlugIndex[$srcSlug][] = $slug;

    $lines = explode("\n", $body);
    $shortLineCount = 0;
    $contentLines = 0;
    foreach ($lines as $line) {
        $t = trim($line);
        if ($t === '') {
            continue;
        }
        if (preg_match('/^#{1,6}\s/', $t)) {
            continue;
        }
        if (preg_match('/^[-*+]\s/', $t)) {
            continue;
        }
        if (preg_match('/^\d+\.\s/', $t)) {
            continue;
        }
        if (preg_match('/^\|/', $t)) {
            continue;
        }
        if (preg_match('/^---+$/', $t)) {
            continue;
        }
        $contentLines++;
        if (mb_strlen($t) < 48) {
            $shortLineCount++;
        }
    }

    $ladderRatio = $contentLines > 0 ? round($shortLineCount / $contentLines, 4) : 0.0;

    $headingCount = preg_match_all('/^#{2,6}\s/m', $body) ?: 0;
    $charLen = mb_strlen(preg_replace('/\s+/u', ' ', trim(strip_tags($body))) ?? '');
    $splitCandidate = $charLen > 9000 && $headingCount < 4;

    $matchesBrokenAnchors = preg_match_all('/\]\(#\)/', $body) ?: 0;

    preg_match_all('/\p{L}[\p{L}\p{N}_-]*/u', strip_tags($body), $wm);
    $wordCount = isset($wm[0]) ? count($wm[0]) : 0;

    $guideRows[] = [
        'slug' => $slug,
        'title' => $title,
        'category' => isset($meta['category']) && is_string($meta['category']) ? $meta['category'] : null,
        'topic' => isset($meta['topic']) && is_string($meta['topic']) ? $meta['topic'] : null,
        'gameVersion' => isset($meta['gameVersion']) && is_string($meta['gameVersion']) ? $meta['gameVersion'] : null,
        'status' => isset($meta['status']) && is_string($meta['status']) ? $meta['status'] : null,
        'wordCount' => $wordCount,
        'headingCount' => $headingCount,
        'ladderRatio' => $ladderRatio,
        'brokenAnchorCount' => $matchesBrokenAnchors,
        'splitCandidate' => $splitCandidate,
        'slugHasNonAscii' => (bool) preg_match('/[^\x00-\x7F]/', $slug),
    ];
}

$mergeByTitle = [];
foreach ($titleIndex as $norm => $slugs) {
    $slugs = array_values(array_unique($slugs));
    if (count($slugs) > 1) {
        $mergeByTitle[] = ['normalizedTitle' => $norm, 'slugs' => $slugs];
    }
}

$mergeBySourceSlug = [];
foreach ($sourceSlugIndex as $src => $slugs) {
    $slugs = array_values(array_unique($slugs));
    if (count($slugs) > 1) {
        $mergeBySourceSlug[] = ['sourceSlug' => $src, 'slugs' => $slugs];
    }
}

$splitCandidates = [];
foreach ($guideRows as $row) {
    if (!empty($row['splitCandidate'])) {
        $splitCandidates[] = $row['slug'];
    }
}

usort($guideRows, static fn ($a, $b) => ($b['ladderRatio'] <=> $a['ladderRatio']) ?: strcmp((string) $a['slug'], (string) $b['slug']));

$out = [
    'generatedAt' => gmdate('c'),
    'guidesDir' => 'content/guides-archive',
    'totalGuides' => count($guideRows),
    'guides' => $guideRows,
    'mergeCandidatesByTitle' => $mergeByTitle,
    'mergeCandidatesBySourceSlug' => $mergeBySourceSlug,
    'splitCandidates' => $splitCandidates,
];

$reportsDir = SITE_ROOT . '/reports';
if (!is_dir($reportsDir)) {
    mkdir($reportsDir, 0775, true);
}
$jsonPath = $reportsDir . '/guides-refactor-inventory.json';
file_put_contents(
    $jsonPath,
    json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n"
);

fwrite(STDOUT, "Wrote {$jsonPath}\n");
