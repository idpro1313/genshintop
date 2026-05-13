<?php

declare(strict_types=1);

/**
 * CLI: собирает единый public/sitemap.xml (Sitemap 0.9).
 * Запуск из корня репозитория: php lib/build-sitemap.php
 */

require __DIR__ . '/bootstrap.php';

$cfg = require __DIR__ . '/config.php';
$base = rtrim((string) ($cfg['site_url'] ?? 'https://genshintop.ru'), '/');

/** @return array{0: string, 1: string} priority, changefreq */
function sitemap_meta(string $path, bool $isIndex = false): array
{
    $priority = '0.5';
    $changefreq = 'monthly';
    $p = $path === '' ? '/' : $path;
    if ($p !== '/' && str_ends_with($p, '/')) {
        $p = rtrim($p, '/') ?: '/';
    }

    if ($p === '/') {
        $priority = '1.0';
        $changefreq = 'daily';
    } elseif (preg_match('#^/(guides|characters|weapons|artifacts|materials|enemies|world|news|community|tools)$#', $p)) {
        $priority = '0.9';
        $changefreq = 'weekly';
    } elseif ($isIndex) {
        $priority = '0.85';
        $changefreq = 'weekly';
    } elseif (preg_match('#^/lootbar/[^/]+$#', $p)) {
        $priority = '0.85';
        $changefreq = 'weekly';
    } elseif (preg_match('#^/(about|editorial-policy|partnership-disclosure|contacts|content-updates)$#', $p)) {
        $priority = '0.4';
        $changefreq = 'monthly';
    } else {
        $priority = '0.7';
        $changefreq = 'monthly';
    }

    return [$priority, $changefreq];
}

/** @param array<string, array{lastmod?: int, priority: string, changefreq: string}> $urls */
function write_sitemap(string $baseUrl, array $urls): void
{
    ksort($urls);
    $out = SITE_ROOT . '/public/sitemap.xml';
    $fh = fopen($out, 'wb');
    if ($fh === false) {
        throw new RuntimeException('Cannot write ' . $out);
    }
    fwrite($fh, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
    fwrite($fh, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");

    foreach ($urls as $path => $meta) {
        $loc = $baseUrl . ($path === '/' ? '/' : $path);
        fwrite($fh, "  <url>\n");
        fwrite($fh, '    <loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</loc>\n");
        if (!empty($meta['lastmod'])) {
            fwrite($fh, '    <lastmod>' . gmdate('Y-m-d', $meta['lastmod']) . "</lastmod>\n");
        }
        fwrite($fh, '    <changefreq>' . htmlspecialchars($meta['changefreq'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</changefreq>\n");
        fwrite($fh, '    <priority>' . htmlspecialchars($meta['priority'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . "</priority>\n");
        fwrite($fh, "  </url>\n");
    }

    fwrite($fh, "</urlset>\n");
    fclose($fh);
}

$urls = [];

foreach (SiteRoutes::staticPaths() as $p) {
    if ($p === '/404') {
        continue;
    }
    [$pr, $cf] = sitemap_meta($p, false);
    $urls[$p] = ['priority' => $pr, 'changefreq' => $cf];
}

foreach (ContentRepository::allLive() as $item) {
    $slug = (string) ($item['slug'] ?? '');
    if ($slug === '' || str_contains($slug, '_placeholder')) {
        continue;
    }
    $path = '/' . ($item['isIndex'] ? $item['section'] : ltrim($item['section'] . '/' . $item['slug'], '/'));
    [$pr, $cf] = sitemap_meta($path, $item['isIndex']);
    
    $meta = $item['meta'];
    $lm = null;
    
    foreach (['updatedAt', 'reviewedAt', 'date'] as $k) {
        if (!empty($meta[$k]) && is_string($meta[$k])) {
            $t = strtotime($meta[$k]);
            if ($t !== false) {
                $lm = $t;
                break;
            }
        }
    }
    if ($lm === null) {
        $lm = (int) @filemtime((string) ($item['path'] ?? ''));
    }

    $urls[$path] = [
        'lastmod' => $lm > 0 ? $lm : null,
        'priority' => $pr,
        'changefreq' => $cf,
    ];
}

// Главная — lastmod по точке входа
if (isset($urls['/'])) {
    $lm = @filemtime(SITE_ROOT . '/public/index.php');
    if ($lm !== false) {
        $urls['/']['lastmod'] = $lm;
    }
}

write_sitemap($base, $urls);

echo 'Written ' . SITE_ROOT . "/public/sitemap.xml (" . count($urls) . " urls)\n";
