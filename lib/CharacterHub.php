<?php

declare(strict_types=1);

final class CharacterHub
{
    /** @return callable(array):bool */
    public static function filterElement(string $key): callable
    {
        $el = self::elementFromUrlKey($key);
        return fn (array $c) => ($c['element'] ?? '') === $el;
    }

    /** @return callable(array):bool */
    public static function filterWeapon(string $weaponRu): callable
    {
        return fn (array $c) => ($c['weapon'] ?? '') === $weaponRu;
    }

    /** @return callable(array):bool */
    public static function filterRarity(int $rarity): callable
    {
        return fn (array $c) => (int) ($c['rarity'] ?? 0) === $rarity;
    }

    public static function elementFromUrlKey(string $key): ?string
    {
        $map = [
            'pyro' => 'Pyro',
            'hydro' => 'Hydro',
            'electro' => 'Electro',
            'cryo' => 'Cryo',
            'anemo' => 'Anemo',
            'geo' => 'Geo',
            'dendro' => 'Dendro',
        ];
        return $map[$key] ?? null;
    }

    /** @return array<string, array{element:string,title:string,description:string,intro:string}> */
    public static function elementPageMeta(): array
    {
        $mk = fn (string $ru, string $en) => [
            'element' => $en,
            'title' => "Персонажи $ru ($en) в Genshin Impact",
            'description' => "Каталог героев стихии $ru в Genshin Impact: карточки и ссылки на гайды GenshinTop.",
            'intro' => "Все персонажи стихии $ru из базы GenshinTop.",
        ];
        return [
            'pyro' => $mk('Пиро', 'Pyro'),
            'hydro' => $mk('Гидро', 'Hydro'),
            'electro' => $mk('Электро', 'Electro'),
            'cryo' => $mk('Крио', 'Cryo'),
            'anemo' => $mk('Анемо', 'Anemo'),
            'geo' => $mk('Гео', 'Geo'),
            'dendro' => $mk('Дендро', 'Dendro'),
        ];
    }

    /** @return array<string, array{weapon:string,title:string,description:string,intro:string}> */
    public static function weaponPageMeta(): array
    {
        return [
            'sword' => [
                'weapon' => 'Одноручное',
                'title' => 'Персонажи с одноручным мечом в Genshin Impact',
                'description' => 'Каталог героев с одноручным оружием (меч) в Genshin Impact — GenshinTop.',
                'intro' => 'Персонажи с типом оружия «Одноручное» из коллекции сайта.',
            ],
            'claymore' => [
                'weapon' => 'Двуручное',
                'title' => 'Персонажи с двуручным мечом в Genshin Impact',
                'description' => 'Каталог героев с двуручным оружием в Genshin Impact — GenshinTop.',
                'intro' => 'Персонажи с типом оружия «Двуручное».',
            ],
            'polearm' => [
                'weapon' => 'Древковое',
                'title' => 'Персонажи с копьём в Genshin Impact',
                'description' => 'Каталог героев с древковым оружием в Genshin Impact — GenshinTop.',
                'intro' => 'Персонажи с типом оружия «Древковое».',
            ],
            'catalyst' => [
                'weapon' => 'Катализатор',
                'title' => 'Персонажи с катализатором в Genshin Impact',
                'description' => 'Каталог героев с катализатором в Genshin Impact — GenshinTop.',
                'intro' => 'Персонажи с типом оружия «Катализатор».',
            ],
            'bow' => [
                'weapon' => 'Лук',
                'title' => 'Персонажи с луком в Genshin Impact',
                'description' => 'Каталог героев с луком в Genshin Impact — GenshinTop.',
                'intro' => 'Персонажи с типом оружия «Лук».',
            ],
        ];
    }

    /** @return array<string, array{rarity:int,title:string,description:string,intro:string}> */
    public static function rarityPageMeta(): array
    {
        return [
            '4-star' => [
                'rarity' => 4,
                'title' => 'Персонажи ★★★★ в Genshin Impact',
                'description' => 'Четырёхзвёздочные персонажи Genshin Impact — каталог GenshinTop.',
                'intro' => 'Герои редкости 4★.',
            ],
            '5-star' => [
                'rarity' => 5,
                'title' => 'Персонажи ★★★★★ в Genshin Impact',
                'description' => 'Пятизвёздочные персонажи Genshin Impact — каталог GenshinTop.',
                'intro' => 'Герои редкости 5★.',
            ],
        ];
    }
}
