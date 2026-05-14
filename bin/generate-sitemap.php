#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * CLI: вывести актуальный sitemap (тот же XML, что отдаёт сайт на /sitemap.xml).
 *
 * Использование:
 *   php bin/generate-sitemap.php > /tmp/sitemap-preview.xml
 */

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "This script must be run from the CLI.\n");
    exit(1);
}

define('SITE_ROOT', dirname(__DIR__));
require SITE_ROOT . '/lib/bootstrap.php';

$cfg = require SITE_ROOT . '/lib/config.php';
echo SitemapBuild::xml($cfg);
