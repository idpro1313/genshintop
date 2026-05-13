<?php

declare(strict_types=1);

final class ContentRepository
{
    private static ?array $guidesCache = null;
    private static ?array $charactersCache = null;

    /** @return list<array<string,mixed>> */
    public static function guides(): array
    {
        if (self::$guidesCache !== null) {
            return self::$guidesCache;
        }
        $dir = SITE_ROOT . '/info/guides';
        $out = [];
        foreach (glob($dir . '/*.md') ?: [] as $path) {
            $slug = basename($path, '.md');
            $raw = (string) file_get_contents($path);
            $split = Frontmatter::split($raw);
            $meta = $split['meta'];
            $srcSlug = isset($meta['sourceSlug']) ? (string) $meta['sourceSlug'] : $slug;
            if (str_starts_with($srcSlug, '_placeholder')) {
                continue;
            }
            $title = isset($meta['title']) && is_string($meta['title']) ? $meta['title'] : $slug;
            $category = isset($meta['category']) && is_string($meta['category']) ? $meta['category'] : 'general';
            $summary = isset($meta['summary']) && is_string($meta['summary']) ? $meta['summary'] : null;
            $out[] = [
                'slug' => $slug,
                'path' => $path,
                'title' => $title,
                'category' => $category,
                'summary' => $summary,
                'meta' => $meta,
                'body_md' => $split['body'],
            ];
        }
        usort($out, fn ($a, $b) => strcmp((string) $a['slug'], (string) $b['slug']));
        self::$guidesCache = $out;
        return $out;
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
        if (self::$charactersCache !== null) {
            return self::$charactersCache;
        }
        $dir = SITE_ROOT . '/info/characters';
        $out = [];
        foreach (glob($dir . '/*.md') ?: [] as $path) {
            $slug = basename($path, '.md');
            $raw = (string) file_get_contents($path);
            $split = Frontmatter::split($raw);
            $meta = $split['meta'];
            $srcSlug = isset($meta['sourceSlug']) ? (string) $meta['sourceSlug'] : $slug;
            if (str_starts_with($srcSlug, '_placeholder')) {
                continue;
            }
            $name = isset($meta['name']) && is_string($meta['name']) ? $meta['name'] : $slug;
            $element = isset($meta['element']) && is_string($meta['element']) ? $meta['element'] : 'Anemo';
            $weapon = isset($meta['weapon']) && is_string($meta['weapon']) ? $meta['weapon'] : 'Прочее';
            $rarity = $meta['rarity'] ?? null;
            $out[] = [
                'slug' => $slug,
                'path' => $path,
                'name' => $name,
                'element' => $element,
                'weapon' => $weapon,
                'rarity' => is_numeric($rarity) ? (int) $rarity : null,
                'meta' => $meta,
                'body_md' => $split['body'],
            ];
        }
        usort($out, fn ($a, $b) => strcmp((string) $a['name'], (string) $b['name']));
        self::$charactersCache = $out;
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
        return $parser->text($md);
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
}
