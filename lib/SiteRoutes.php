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
            '/guides',
            '/characters',
            '/regions',
            '/lootbar',
            '/guides/game-basics',
            '/guides/advanced-guides',
            '/guides/quest-walkthroughs',
            '/guides/banners',
            '/guides/codes',
            '/guides/patches',
            '/guides/newbie',
            '/guides/economy',
            '/guides/tier-list',
            '/guides/events',
            '/guides/tcg',
            '/guides/domains',
            '/guides/bosses',
            '/guides/quests',
            '/characters/pyro',
            '/characters/hydro',
            '/characters/electro',
            '/characters/cryo',
            '/characters/anemo',
            '/characters/geo',
            '/characters/dendro',
            '/characters/sword',
            '/characters/claymore',
            '/characters/polearm',
            '/characters/catalyst',
            '/characters/bow',
            '/characters/4-star',
            '/characters/5-star',
            '/regions/sumeru',
            '/regions/fontaine',
            '/regions/natlan',
            '/lootbar/kristally-sotvoreniya',
            '/lootbar/blagoslovenie-luny',
            '/lootbar/promokod',
            '/lootbar/kak-popolnit-genshin-impact',
            '/lootbar/bezopasnost-i-oplata',
            '/lootbar/skidki-i-kupony',
        ];
    }

    /** @return list<string> */
    public static function lootbarPaths(): array
    {
        return array_values(array_filter(self::staticPaths(), fn ($p) => str_starts_with($p, '/lootbar')));
    }
}
