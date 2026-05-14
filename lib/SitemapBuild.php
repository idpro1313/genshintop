<?php

declare(strict_types=1);

/**
 * Динамический sitemap: канонические публичные пути и lastmod (aligned с ContentRepository).
 */
final class SitemapBuild
{
    /**
     * @param array<string,mixed> $cfg
     *
     * @return list<string> абсолютные URL (https), отсортированы по пути
     */
    public static function absoluteUrls(array $cfg): array
    {
        $map = self::pathLastmodMap();
        $site = Seo::siteUrl($cfg);
        $urls = [];
        foreach (array_keys($map) as $path) {
            $urls[] = $site . $path;
        }
        sort($urls);

        return $urls;
    }

    /**
     * Максимальный timestamp среди всех URL карты — для Last-Modified всего документа.
     */
    public static function latestChangeTs(): int
    {
        $m = 0;
        foreach (self::pathLastmodMap() as $ts) {
            $m = max($m, $ts);
        }

        return $m;
    }

    /**
     * @param array<string,mixed> $cfg
     */
    public static function xml(array $cfg): string
    {
        $map = self::pathLastmodMap();
        $site = Seo::siteUrl($cfg);
        ksort($map);

        $lines = ['<?xml version="1.0" encoding="UTF-8"?>', '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'];
        foreach ($map as $path => $ts) {
            [$freq, $pri] = self::changefreqAndPriority($path);
            $loc = htmlspecialchars($site . $path, ENT_XML1 | ENT_COMPAT, 'UTF-8');
            $lines[] = '  <url>';
            $lines[] = '    <loc>' . $loc . '</loc>';
            if ($ts > 0) {
                $lines[] = '    <lastmod>' . gmdate('Y-m-d', $ts) . '</lastmod>';
            }
            $lines[] = '    <changefreq>' . $freq . '</changefreq>';
            $lines[] = '    <priority>' . $pri . '</priority>';
            $lines[] = '  </url>';
        }
        $lines[] = '</urlset>';

        return implode("\n", $lines) . "\n";
    }

    /**
     * @return array<string,int> путь (например /guides) => unix time
     */
    public static function pathLastmodMap(): array
    {
        $map = [];

        foreach (ContentRepository::allLive() as $item) {
            $p = ContentRepository::itemUrl($item);
            if ($p === '' || $p === '/') {
                continue;
            }
            self::merge($map, $p, ContentRepository::itemMtime($item));
        }

        $shellMtime = self::filesMaxMtime(['lib/PageRenderer.php', 'lib/guide_hub_definitions.php', 'lib/CharacterHub.php']);
        $lootMtime = self::filesMaxMtime(['lib/PageRenderer.php', 'lib/LootbarConfig.php', 'lib/Partners.php']);

        self::merge($map, '/', ContentRepository::latestMtime());
        self::merge($map, '/guides', ContentRepository::latestMtime(
            static fn (array $i): bool => !$i['isIndex'] && str_starts_with((string) $i['section'], 'guides')
        ));
        self::merge($map, '/characters', ContentRepository::latestMtime(
            static fn (array $i): bool => !$i['isIndex'] && ($i['section'] ?? '') === 'characters'
        ));

        foreach (array_keys(guide_hub_definitions()) as $hubId) {
            $match = self::hubMatcher($hubId);
            $guides = ContentRepository::filterGuides($match);
            $hubM = 0;
            foreach ($guides as $g) {
                $hubM = max($hubM, ContentRepository::itemMtime($g));
            }
            if ($hubM === 0) {
                $hubM = $shellMtime;
            }
            self::merge($map, '/guides/' . $hubId, $hubM);
        }

        foreach (array_keys(CharacterHub::elementPageMeta()) as $key) {
            $mtime = ContentRepository::latestMtime(CharacterHub::filterElement($key));
            self::merge($map, '/characters/' . $key, $mtime > 0 ? $mtime : $shellMtime);
        }
        foreach (CharacterHub::weaponPageMeta() as $key => $wMeta) {
            $mtime = ContentRepository::latestMtime(CharacterHub::filterWeapon($wMeta['weapon']));
            self::merge($map, '/characters/' . $key, $mtime > 0 ? $mtime : $shellMtime);
        }
        foreach (array_keys(CharacterHub::rarityPageMeta()) as $key) {
            $r = CharacterHub::rarityPageMeta()[$key]['rarity'];
            $mtime = ContentRepository::latestMtime(CharacterHub::filterRarity($r));
            self::merge($map, '/characters/' . $key, $mtime > 0 ? $mtime : $shellMtime);
        }

        foreach (['/about', '/contacts', '/editorial-policy', '/partnership-disclosure', '/content-updates'] as $st) {
            self::merge($map, $st, $shellMtime);
        }

        self::merge($map, '/lootbar', $lootMtime);
        self::merge($map, '/lootbar/kak-popolnit-genshin-impact', $lootMtime);
        self::merge($map, '/lootbar/skidki-i-kupony', $lootMtime);

        return $map;
    }

    /** @param array<string,int> $map */
    private static function merge(array &$map, string $path, int $ts): void
    {
        if ($ts <= 0) {
            return;
        }
        $norm = $path === '/' ? '/' : '/' . trim($path, '/');
        $map[$norm] = max($map[$norm] ?? 0, $ts);
    }

    /** @return array{0:string,1:string} [changefreq, priority] */
    private static function changefreqAndPriority(string $path): array
    {
        if ($path === '/') {
            return ['daily', '1.0'];
        }

        $low = ['/about', '/contacts', '/editorial-policy', '/partnership-disclosure', '/content-updates'];
        if (in_array($path, $low, true)) {
            return ['monthly', '0.4'];
        }

        if (str_starts_with($path, '/lootbar')) {
            return ['weekly', '0.85'];
        }

        if (preg_match('#^/[^/]+$#', $path)) {
            return ['weekly', '0.9'];
        }

        return ['monthly', '0.7'];
    }

    private static function hubMatcher(string $hub): callable
    {
        return match ($hub) {
            'banners' => fn (array $g) => GuideHub::matchHubBanners($g),
            'codes' => fn (array $g) => GuideHub::matchHubCodes($g),
            'patches' => fn (array $g) => GuideHub::matchHubPatches($g),
            'newbie' => fn (array $g) => GuideHub::matchHubNewbie($g),
            'economy' => fn (array $g) => GuideHub::matchHubEconomy($g),
            'tier-list' => fn (array $g) => GuideHub::matchHubTierList($g),
            'events' => fn (array $g) => GuideHub::matchHubEvents($g),
            'tcg' => fn (array $g) => GuideHub::matchHubTcg($g),
            'domains' => fn (array $g) => GuideHub::matchHubDomains($g),
            'bosses' => fn (array $g) => GuideHub::matchHubBosses($g),
            'quests' => fn (array $g) => GuideHub::matchHubQuests($g),
            default => static fn () => false,
        };
    }

    /** @param list<string> $rels относительно SITE_ROOT */
    private static function filesMaxMtime(array $rels): int
    {
        $max = 0;
        foreach ($rels as $rel) {
            $path = SITE_ROOT . '/' . ltrim($rel, '/');
            $t = @filemtime($path);
            if ($t !== false) {
                $max = max($max, $t);
            }
        }

        return $max;
    }
}
