<?php

declare(strict_types=1);

final class ContentRepository
{
    private static ?array $allCache = null;

    /** @return list<array<string,mixed>> */
    public static function allLive(): array
    {
        if (self::$allCache !== null) {
            return self::$allCache;
        }

        $dir = SITE_ROOT . '/content';
        $out = [];

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        $mdFiles = new RegexIterator($iterator, '/^.+\.md$/i', RecursiveRegexIterator::GET_MATCH);

        foreach ($mdFiles as $file) {
            $path = $file[0];
            $relPath = str_replace('\\', '/', substr($path, strlen($dir)));
            
            if (str_starts_with($relPath, '/_templates/') || str_contains($relPath, '/_by-')) {
                continue;
            }
            if (str_ends_with($relPath, 'README.md') || str_ends_with($relPath, 'STYLE.md')) {
                continue;
            }

            $raw = (string) file_get_contents($path);
            $split = Frontmatter::split($raw);
            $meta = $split['meta'];

            $status = $meta['status'] ?? 'live';
            if ($status !== 'live') {
                continue;
            }

            $isIndex = basename($path) === '_index.md';
            
            $slug = $meta['slug'] ?? basename($path, '.md');
            $section = $meta['section'] ?? trim(dirname($relPath), '/');

            // Fallbacks
            $title = isset($meta['title']) && is_string($meta['title']) ? $meta['title'] : $slug;
            $name = isset($meta['name']) && is_string($meta['name']) ? $meta['name'] : $title;
            $category = isset($meta['category']) && is_string($meta['category']) ? $meta['category'] : 'general';
            $summary = isset($meta['summary']) && is_string($meta['summary']) ? $meta['summary'] : null;

            $element = isset($meta['element']) && is_string($meta['element']) ? $meta['element'] : 'Anemo';
            $weapon = isset($meta['weapon']) && is_string($meta['weapon']) ? $meta['weapon'] : 'Прочее';
            $rarity = isset($meta['rarity']) && is_numeric($meta['rarity']) ? (int)$meta['rarity'] : null;

            $out[] = [
                'path' => $path,
                'slug' => $slug,
                'section' => $section,
                'isIndex' => $isIndex,
                'title' => $title,
                'name' => $name,
                'category' => $category,
                'summary' => $summary,
                'element' => $element,
                'weapon' => $weapon,
                'rarity' => $rarity,
                'meta' => $meta,
                'body_md' => $split['body'],
            ];
        }

        self::$allCache = $out;
        return $out;
    }

    /** @return list<array<string,mixed>> */
    public static function guides(): array
    {
        $all = self::allLive();
        $out = [];
        foreach ($all as $item) {
            if (!$item['isIndex'] && str_starts_with($item['section'], 'guides')) {
                $out[] = $item;
            }
        }
        usort($out, fn ($a, $b) => strcmp((string) $a['slug'], (string) $b['slug']));
        return $out;
    }

    /** @param array<string,mixed> $item */
    public static function itemUrl(array $item): string
    {
        $section = trim((string) ($item['section'] ?? ''), '/');
        if (!empty($item['isIndex'])) {
            return '/' . $section;
        }

        $slug = trim((string) ($item['slug'] ?? ''), '/');
        return '/' . ltrim($section . '/' . $slug, '/');
    }

    public static function guideTimestamp(array $g): int
    {
        $meta = $g['meta'] ?? [];
        if (is_array($meta)) {
            foreach (['updatedAt', 'date', 'reviewedAt'] as $k) {
                if (!empty($meta[$k]) && is_string($meta[$k])) {
                    $t = strtotime($meta[$k]);
                    if ($t !== false) {
                        return $t;
                    }
                }
            }
        }

        return (int) @filemtime((string) ($g['path'] ?? ''));
    }

    /** @return list<array<string,mixed>> */
    public static function guidesSortedByRecent(): array
    {
        $copy = self::guides();
        usort($copy, fn ($a, $b) => self::guideTimestamp($b) <=> self::guideTimestamp($a));

        return $copy;
    }

    /** @return array<string,mixed>|null */
    public static function guideBySlug(string $slug): ?array
    {
        foreach (self::guides() as $g) {
            if (($g['slug'] ?? '') === $slug) {
                return $g;
            }
        }
        return null;
    }

    /** @return list<array<string,mixed>> */
    public static function characters(): array
    {
        $all = self::allLive();
        $out = [];
        foreach ($all as $item) {
            if (!$item['isIndex'] && $item['section'] === 'characters') {
                $out[] = $item;
            }
        }
        usort($out, fn ($a, $b) => strcmp((string) $a['name'], (string) $b['name']));
        return $out;
    }

    /** @return array<string,mixed>|null */
    public static function characterBySlug(string $slug): ?array
    {
        foreach (self::characters() as $c) {
            if (($c['slug'] ?? '') === $slug) {
                return $c;
            }
        }
        return null;
    }

    public static function markdownToHtml(string $md): string
    {
        static $parser = null;
        if ($parser === null) {
            $parser = new Parsedown();
            /** @phpstan-ignore-next-line */
            $parser->setSafeMode(false);
        }
        $html = $parser->text($md);

        return self::sanitizeContentLinks($html);
    }

    /**
     * Подчищает ссылки в готовом HTML контента:
     *  - срезает суффикс `.md` у относительных href;
     *  - нейтрализует ссылки на служебные пути (_templates/, _by-*, STYLE.md, README.md, info/);
     *  - если текст ссылки — `<code>что-то.md</code>`, разворачивает <code> в обычный текст
     *    без хвоста `.md`, чтобы редакторские «файловые» лейблы не выглядели плашкой.
     */
    private static function sanitizeContentLinks(string $html): string
    {
        if ($html === '' || stripos($html, '<a ') === false) {
            return $html;
        }

        return (string) preg_replace_callback(
            '#<a\b([^>]*?)\bhref="([^"]*)"([^>]*)>(.*?)</a>#is',
            static function (array $m): string {
                $preAttrs = $m[1];
                $href = $m[2];
                $postAttrs = $m[3];
                $inner = $m[4];

                $isAbsolute = preg_match('#^(?:[a-z][a-z0-9+.\-]*:|//|mailto:|tel:|#)#i', $href) === 1;

                $cleanInner = static function (string $s): string {
                    if (preg_match('#^\s*<code>(.*)</code>\s*$#is', $s, $cm) === 1) {
                        $text = $cm[1];
                        $text = preg_replace('/\.md$/i', '', $text) ?? $text;

                        return $text;
                    }

                    return $s;
                };

                if ($isAbsolute) {
                    return '<a' . $preAttrs . 'href="' . $href . '"' . $postAttrs . '>' . $cleanInner($inner) . '</a>';
                }

                $stripped = ltrim($href, './');
                $isService =
                    str_contains($stripped, '_templates/')
                    || str_contains($stripped, '_by-')
                    || preg_match('#(^|/)STYLE\.md(?:[/?#]|$)#i', $stripped) === 1
                    || preg_match('#(^|/)README\.md(?:[/?#]|$)#i', $stripped) === 1
                    || preg_match('#(^|/)info/#i', $stripped) === 1;

                if ($isService) {
                    return $cleanInner($inner);
                }

                $newHref = preg_replace('/\.md(?=$|[?#])/i', '', $href) ?? $href;

                return '<a' . $preAttrs . 'href="' . $newHref . '"' . $postAttrs . '>' . $cleanInner($inner) . '</a>';
            },
            $html
        ) ?: $html;
    }

    /** @param list<string>|null $slugs */
    public static function guidesBySlugs(?array $slugs): array
    {
        if ($slugs === null || $slugs === []) {
            return [];
        }
        $map = [];
        foreach (self::guides() as $g) {
            $map[(string) $g['slug']] = $g;
        }
        $out = [];
        foreach ($slugs as $s) {
            if (isset($map[$s])) {
                $out[] = $map[$s];
            }
        }
        return $out;
    }

    /** @param list<string>|null $slugs */
    public static function charactersBySlugs(?array $slugs): array
    {
        if ($slugs === null || $slugs === []) {
            return [];
        }
        $map = [];
        foreach (self::characters() as $c) {
            $map[(string) $c['slug']] = $c;
        }
        $out = [];
        foreach ($slugs as $s) {
            if (isset($map[$s])) {
                $out[] = $map[$s];
            }
        }
        return $out;
    }

    /** @param callable(array):bool $fn */
    public static function filterGuides(callable $fn): array
    {
        return array_values(array_filter(self::guides(), $fn));
    }

    /** @param callable(array):bool $fn */
    public static function filterCharacters(callable $fn): array
    {
        return array_values(array_filter(self::characters(), $fn));
    }

    /** @return array<string,mixed>|null */
    public static function findItemByUrl(string $urlPath): ?array
    {
        $path = trim($urlPath, '/');
        foreach (self::allLive() as $item) {
            $itemUrl = trim(self::itemUrl($item), '/');
            if ($itemUrl === $path) {
                return $item;
            }
        }
        return null;
    }
}
