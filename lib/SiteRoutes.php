<?php

declare(strict_types=1);

final class SiteRoutes
{
    /** @return list<string> paths starting with / */
    public static function staticPaths(): array
    {
        return [
            '/',
            '/about',
            '/contacts',
            '/404',
            '/editorial-policy',
            '/partnership-disclosure',
            '/content-updates',
            '/lootbar',
            '/lootbar/kak-popolnit-genshin-impact',
            '/lootbar/skidki-i-kupony',
        ];
    }

    /** @return list<string> */
    public static function lootbarPaths(): array
    {
        return array_values(array_filter(self::staticPaths(), fn ($p) => str_starts_with($p, '/lootbar')));
    }
}
