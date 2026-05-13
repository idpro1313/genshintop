<?php

declare(strict_types=1);

final class SectionLabels
{
    /** @var array<string,string> */
    private const LABELS = [
        'characters' => 'Персонажи',
        'guides' => 'Гайды',
        'weapons' => 'Оружие',
        'artifacts' => 'Артефакты',
        'materials' => 'Материалы',
        'enemies' => 'Враги',
        'tools' => 'Инструменты',
        'community' => 'Сообщество',
        'world' => 'Мир',
        'news' => 'Новости',
        'basics' => 'Основы',
        'advanced' => 'Продвинутые гайды',
        'walkthroughs' => 'Прохождения',
        'regions' => 'Регионы',
        'lore' => 'Лор',
        'factions' => 'Фракции',
        'npc' => 'NPC',
        'fatui-harbingers' => 'Предвестники Фатуи',
        'events' => 'События',
        'announcements' => 'Анонсы',
        'banners' => 'Баннеры',
        'patches' => 'Патчи',
        'character-banners' => 'Баннеры персонажей',
        'weapon-banners' => 'Оружейные баннеры',
        'ingredients' => 'Ингредиенты',
        'ascension-materials' => 'Материалы возвышения',
        'local-specialties' => 'Диковинки',
        'common' => 'Обычные враги',
        'elite' => 'Элитные враги',
        'world-bosses' => 'Мировые боссы',
        'weekly-bosses' => 'Еженедельные боссы',
        'mondstadt' => 'Мондштадт',
        'liyue' => 'Ли Юэ',
        'inazuma' => 'Инадзума',
        'sumeru' => 'Сумеру',
        'fontaine' => 'Фонтейн',
        'natlan' => 'Натлан',
        'snezhnaya' => 'Снежная',
    ];

    public static function ru(string $segment): string
    {
        return self::LABELS[$segment] ?? self::humanize($segment);
    }

    /** @return list<array{label:string,href:string}> */
    public static function breadcrumbsForSection(string $section): array
    {
        $crumbs = [
            ['label' => 'Главная', 'href' => '/'],
        ];
        $current = '';
        foreach (explode('/', trim($section, '/')) as $part) {
            if ($part === '') {
                continue;
            }
            $current .= '/' . $part;
            $crumbs[] = ['label' => self::ru($part), 'href' => $current];
        }

        return $crumbs;
    }

    private static function humanize(string $segment): string
    {
        $label = str_replace('-', ' ', $segment);
        if ($label === '') {
            return $segment;
        }

        return mb_strtoupper(mb_substr($label, 0, 1)) . mb_substr($label, 1);
    }
}
