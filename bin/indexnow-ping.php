#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * CLI: пинг IndexNow по каноническому списку URL (как в SitemapBuild / /sitemap.xml).
 *
 * Использование:
 *   php bin/indexnow-ping.php [--match=REGEX] [--limit=N] [--dry-run]
 *
 * Примеры:
 *   php bin/indexnow-ping.php
 *   php bin/indexnow-ping.php --match='^https://genshintop\.ru/(news|guides)/'
 *   php bin/indexnow-ping.php --limit=50 --dry-run
 *
 * Переменная окружения INDEXNOW_KEY переопределяет ключ по умолчанию.
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the CLI.\n");
    exit(1);
}

define('SITE_ROOT', dirname(__DIR__));
require SITE_ROOT . '/lib/bootstrap.php';
require SITE_ROOT . '/lib/IndexNow.php';

$cfg = require SITE_ROOT . '/lib/config.php';

$opts = getopt('', ['match::', 'limit::', 'dry-run']);
$match = isset($opts['match']) && is_string($opts['match']) && $opts['match'] !== '' ? $opts['match'] : null;
$limit = isset($opts['limit']) ? (int) $opts['limit'] : 0;
$dryRun = array_key_exists('dry-run', $opts);

$r = SitemapBuild::absoluteUrls($cfg);

$urls = [];
/**
 * Разделитель для regexp в --match: не '#', чтобы в шаблоне можно использовать фрагменты URL (#…).
 * Символ U+0007 (BEL) в паттерне не допускается.
 */
$matchDelim = "\x07";
foreach ($r as $loc) {
    if ($loc === '') {
        continue;
    }
    if ($match !== null) {
        if (str_contains($match, $matchDelim)) {
            fwrite(STDERR, "--match regexp must not contain character U+0007 (BEL)\n");
            exit(4);
        }
        $matched = preg_match($matchDelim . $match . $matchDelim . 'u', $loc);
        if ($matched === false) {
            fwrite(STDERR, "Invalid --match regexp (PCRE error).\n");
            exit(4);
        }
        if ($matched !== 1) {
            continue;
        }
    }
    $urls[] = $loc;
    if ($limit > 0 && count($urls) >= $limit) {
        break;
    }
}

printf("Sitemap URLs to submit: %d\n", count($urls));
printf("IndexNow key: %s (file: %s)\n", IndexNow::key(), IndexNow::keyLocation());

if ($urls === []) {
    echo "Nothing to submit.\n";
    exit(0);
}

if ($dryRun) {
    foreach (array_slice($urls, 0, 20) as $u) {
        echo "  $u\n";
    }
    if (count($urls) > 20) {
        printf("  ... and %d more\n", count($urls) - 20);
    }
    echo "[dry-run] not sending\n";
    exit(0);
}

$results = IndexNow::submitMany($urls);
$failed = 0;
foreach ($results as $i => $res) {
    printf("Batch %d: status=%d ok=%s body=%.200s\n", $i + 1, $res['status'], $res['ok'] ? 'true' : 'false', $res['body']);
    if (!$res['ok']) {
        $failed++;
    }
}

if ($failed > 0) {
    fwrite(STDERR, "$failed batch(es) failed\n");
    exit(3);
}

echo "Done.\n";
exit(0);
