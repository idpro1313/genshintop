<?php

declare(strict_types=1);

final class Router
{
    public static function dispatch(array $cfg): void
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        $path = $path === null || $path === '' ? '/' : $path;
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/') ?: '/';
        }

        if ($path === '/rss.xml') {
            http_response_code(404);
            self::send($cfg, PageRenderer::notFound($cfg));

            return;
        }

        if ($path === '/') {
            self::send($cfg, PageRenderer::home($cfg));

            return;
        }

        // --- Static exact ---
        $static = PageRenderer::staticExactPages($cfg);
        if (isset($static[$path])) {
            self::send($cfg, $static[$path]);

            return;
        }

        // --- Content Generic Routing ---
        $item = ContentRepository::findItemByUrl($path);
        if ($item) {
            if ($item['isIndex']) {
                self::send($cfg, PageRenderer::contentSectionIndex($cfg, $item));
            } else {
                self::send($cfg, PageRenderer::contentArticle($cfg, $item));
            }
            return;
        }

        // --- Guides ---
        if ($path === '/guides') {
            self::send($cfg, PageRenderer::guidesIndex($cfg));

            return;
        }
        if (preg_match('#^/guides/([^/]+)$#', $path, $m)) {
            $seg = $m[1];
            $hubs = guide_hub_definitions();
            if (isset($hubs[$seg])) {
                self::send($cfg, PageRenderer::guideHub($cfg, $seg, $hubs[$seg]));

                return;
            }
            $g = ContentRepository::guideBySlug($seg);
            if ($g) {
                self::send($cfg, PageRenderer::guideArticle($cfg, $g));

                return;
            }
            http_response_code(404);
            self::send($cfg, PageRenderer::notFound($cfg));

            return;
        }

        // --- Characters ---
        if ($path === '/characters') {
            self::send($cfg, PageRenderer::charactersIndex($cfg));

            return;
        }
        if (preg_match('#^/characters/([^/]+)$#', $path, $m)) {
            $seg = $m[1];
            $elMeta = CharacterHub::elementPageMeta();
            if (isset($elMeta[$seg])) {
                self::send($cfg, PageRenderer::characterElementHub($cfg, $seg, $elMeta[$seg]));

                return;
            }
            $wMeta = CharacterHub::weaponPageMeta();
            if (isset($wMeta[$seg])) {
                self::send($cfg, PageRenderer::characterWeaponHub($cfg, $seg, $wMeta[$seg]));

                return;
            }
            $rMeta = CharacterHub::rarityPageMeta();
            if (isset($rMeta[$seg])) {
                self::send($cfg, PageRenderer::characterRarityHub($cfg, $seg, $rMeta[$seg]));

                return;
            }
            $c = ContentRepository::characterBySlug($seg);
            if ($c) {
                self::send($cfg, PageRenderer::characterArticle($cfg, $c));

                return;
            }
            http_response_code(404);
            self::send($cfg, PageRenderer::notFound($cfg));

            return;
        }

        // --- Regions ---
        if ($path === '/regions') {
            self::send($cfg, PageRenderer::regionsIndex($cfg));

            return;
        }
        if (preg_match('#^/regions/([^/]+)$#', $path, $m)) {
            $defs = regions_definitions();
            if (isset($defs[$m[1]])) {
                self::send($cfg, PageRenderer::regionPage($cfg, $defs[$m[1]]));

                return;
            }
            http_response_code(404);
            self::send($cfg, PageRenderer::notFound($cfg));

            return;
        }

        // --- LootBar ---
        if ($path === '/lootbar') {
            self::send($cfg, PageRenderer::lootbarIndex($cfg));

            return;
        }
        if (preg_match('#^/lootbar/([^/]+)$#', $path, $m)) {
            $page = PageRenderer::lootbarSubpage($cfg, $m[1]);
            if ($page) {
                self::send($cfg, $page);

                return;
            }
            http_response_code(404);
            self::send($cfg, PageRenderer::notFound($cfg));

            return;
        }

        http_response_code(404);
        self::send($cfg, PageRenderer::notFound($cfg));
    }

    /**
     * @param array<string,mixed> $cfg
     * @param array<string,mixed> $page layout vars + optional jsonLd array
     */
    public static function send(array $cfg, array $page): void
    {
        $defaults = [
            'robots' => 'index, follow',
            'ogType' => 'website',
            'hideLootBarPromo' => false,
            'ogImage' => null,
            'ogAlt' => '',
            'articleTimes' => null,
            'jsonLdRaw' => null,
        ];
        $page = array_merge($defaults, $page);
        if (isset($page['jsonLd']) && is_array($page['jsonLd'])) {
            $page['jsonLdRaw'] = json_encode($page['jsonLd'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
            unset($page['jsonLd']);
        }
        $merged = array_merge(['cfg' => $cfg], $page);
        extract($merged, EXTR_SKIP);
        require SITE_ROOT . '/lib/layout.php';
    }
}
