<?php

declare(strict_types=1);

final class Seo
{
    public const DEFAULT_OG_IMAGE_PATH = '/apple-touch-icon.png';
    public const DEFAULT_OG_W = 180;
    public const DEFAULT_OG_H = 180;
    public const OG_W = 1200;
    public const OG_H = 630;

    /** @param array<string,mixed> $cfg config.php */
    public static function siteUrl(array $cfg): string
    {
        return rtrim((string) ($cfg['site_url'] ?? 'https://genshintop.ru'), '/');
    }

    /** @param array<string,mixed> $cfg */
    public static function absoluteUrl(array $cfg, string $pathOrUrl): string
    {
        if (preg_match('/^https?:\/\//i', $pathOrUrl)) {
            return $pathOrUrl;
        }
        $path = str_starts_with($pathOrUrl, '/') ? $pathOrUrl : '/' . $pathOrUrl;
        $path = preg_replace('#/+#', '/', $path) ?? $path;
        return self::siteUrl($cfg) . $path;
    }

    public static function stripDescriptionNoise(string $raw): string
    {
        $t = $raw;
        $t = preg_replace('/\x{200b}|\x{feff}|\x{3164}/u', '', $t) ?? $t;
        $t = preg_replace('/^#{1,6}\s+/m', '', $t) ?? $t;
        $t = preg_replace('/!\[[^\]]*]\([^)]*\)/', '', $t) ?? $t;
        $t = preg_replace('/\[([^\]]+)]\([^)]*\)/', '$1', $t) ?? $t;
        $t = preg_replace('/\*\*([^*]+)\*\*/', '$1', $t) ?? $t;
        $t = preg_replace('/__([^_]+)__/', '$1', $t) ?? $t;
        $t = preg_replace('/`([^`]+)`/', '$1', $t) ?? $t;
        $t = preg_replace('/<[^>]+>/', ' ', $t) ?? $t;
        $t = preg_replace('/\s+/', ' ', $t) ?? $t;
        return trim($t);
    }

    public static function cleanMetaDescription(?string $input, string $fallback, int $maxLen = 160): string
    {
        if ($input === null || $input === '') {
            return $fallback;
        }
        $t = self::stripDescriptionNoise($input);
        if ($t === '') {
            return $fallback;
        }
        if (mb_strlen($t) <= $maxLen) {
            return $t;
        }
        $cut = mb_substr($t, 0, $maxLen);
        $lastSpace = mb_strrpos($cut, ' ');
        $safe = ($lastSpace !== false && $lastSpace > (int) ($maxLen * 0.55))
            ? mb_substr($cut, 0, $lastSpace)
            : $cut;
        return trim($safe) . '…';
    }

    /** @param array<string,mixed> $cfg */
    public static function publisherOrganization(array $cfg): array
    {
        $site = self::siteUrl($cfg);
        $sameRaw = $cfg['organization_same_as'] ?? [];
        $sameAs = is_array($sameRaw) && $sameRaw !== [] ? array_values(array_filter($sameRaw, fn ($u) => is_string($u) && preg_match('/^https?:\/\//i', $u))) : null;
        return array_filter([
            '@type' => 'Organization',
            '@id' => $site . '/#organization',
            'name' => 'GenshinTop',
            'url' => $site,
            'description' => 'Гайды, билды и справка по Genshin Impact на русском.',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => self::absoluteUrl($cfg, self::DEFAULT_OG_IMAGE_PATH),
                'width' => self::DEFAULT_OG_W,
                'height' => self::DEFAULT_OG_H,
            ],
            'sameAs' => $sameAs,
        ], fn ($v) => $v !== null && $v !== []);
    }

    /** @param array<string,mixed> $cfg */
    public static function editorialTeamPerson(array $cfg): array
    {
        $site = self::siteUrl($cfg);
        return [
            '@type' => 'Organization',
            '@id' => $site . '/#editorial-team',
            'name' => 'Редакция GenshinTop',
            'url' => $site . '/editorial-policy',
            'parentOrganization' => ['@id' => $site . '/#organization'],
        ];
    }

    /** @param array<string,mixed> $cfg */
    public static function webSiteNode(array $cfg): array
    {
        $site = self::siteUrl($cfg);
        return [
            '@type' => 'WebSite',
            '@id' => $site . '/#website',
            'name' => 'GenshinTop',
            'url' => $site,
            'inLanguage' => 'ru-RU',
            'publisher' => ['@id' => $site . '/#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => $site . '/guides?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /** @param list<array{label:string,href:string}> $items */
    public static function breadcrumbListSchema(array $cfg, array $items, ?string $id = null): array
    {
        return array_filter([
            '@type' => 'BreadcrumbList',
            '@id' => $id,
            'itemListElement' => array_map(
                fn ($item, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'name' => $item['label'],
                    'item' => self::absoluteUrl($cfg, $item['href']),
                ],
                $items,
                array_keys($items),
            ),
        ], static fn ($v) => $v !== null);
    }

    /** @param list<array<string,mixed>> $nodes */
    public static function jsonLdGraph(array $nodes): array
    {
        return [
            '@context' => 'https://schema.org',
            '@graph' => array_values($nodes),
        ];
    }

    /** @param list<array{question:string,answer:string}> $faqs */
    public static function faqPageSchema(array $faqs): array
    {
        return [
            '@type' => 'FAQPage',
            'mainEntity' => array_map(
                fn ($f) => [
                    '@type' => 'Question',
                    'name' => $f['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $f['answer'],
                    ],
                ],
                $faqs,
            ),
        ];
    }

    /** @param array{name:string,description:string,steps:list<string>} $params */
    public static function howToSchema(array $params): array
    {
        $steps = [];
        foreach ($params['steps'] as $i => $text) {
            $steps[] = [
                '@type' => 'HowToStep',
                'position' => $i + 1,
                'name' => 'Шаг ' . ($i + 1),
                'text' => $text,
            ];
        }
        return [
            '@type' => 'HowTo',
            'name' => $params['name'],
            'description' => $params['description'],
            'step' => $steps,
        ];
    }

    /**
     * Универсальный узел VideoGame для микроразметки страниц о Genshin Impact
     * (используется как `about` у Article-узлов).
     */
    public static function genshinVideoGameNode(): array
    {
        return [
            '@type' => 'VideoGame',
            'name' => 'Genshin Impact',
            'inLanguage' => 'ru-RU',
            'applicationCategory' => 'Game',
            'gamePlatform' => ['PC', 'PlayStation 4', 'PlayStation 5', 'Android', 'iOS'],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'HoYoverse',
                'url' => 'https://www.hoyoverse.com/',
            ],
            'url' => 'https://genshin.hoyoverse.com/',
        ];
    }

    public static function encodeJsonLd(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    /**
     * @return array{w:int,h:int,mime:string}
     */
    public static function ogImageDimensions(string $imagePath): array
    {
        $mime = str_ends_with($imagePath, '.svg')
            ? 'image/svg+xml'
            : (str_ends_with($imagePath, '.jpg') || str_ends_with($imagePath, '.jpeg')
                ? 'image/jpeg'
                : 'image/png');
        if ($imagePath === self::DEFAULT_OG_IMAGE_PATH) {
            return ['w' => self::DEFAULT_OG_W, 'h' => self::DEFAULT_OG_H, 'mime' => $mime];
        }
        return ['w' => self::OG_W, 'h' => self::OG_H, 'mime' => $mime];
    }
}
