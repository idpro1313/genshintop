<?php

declare(strict_types=1);

final class GuideTaxonomy
{
    public const TOPICS = ['banner', 'patch', 'codes', 'newbie', 'party', 'economy', 'lore', 'tech', 'general'];
    public const STATUSES = ['active', 'dated', 'historical'];
    public const AUDIENCES = ['all', 'beginner', 'returning', 'meta'];

    /** @var array<string,string> */
    public static function topicLabelsRu(): array
    {
        return [
            'banner' => 'Баннеры и молитвы',
            'patch' => 'Обновления патчей',
            'codes' => 'Промокоды',
            'newbie' => 'Новичкам',
            'party' => 'Отряды и персонажи',
            'economy' => 'Экономика и фарм',
            'lore' => 'Лор и сюжет',
            'tech' => 'ПК, железо, инструменты',
            'general' => 'Разное',
        ];
    }

    /** @var array<string,string> */
    public static function statusLabelsRu(): array
    {
        return [
            'active' => 'Актуально',
            'dated' => 'Привязано к версии',
            'historical' => 'Архив',
        ];
    }

    /** @param string $category GuideCategory */
    public static function topicFromCategory(string $category): string
    {
        return match ($category) {
            'banner' => 'banner',
            'patch' => 'patch',
            'codes' => 'codes',
            'newbie' => 'newbie',
            'tier' => 'party',
            'hardware' => 'tech',
            default => 'general',
        };
    }

    /** @param array<string,mixed> $data guide meta */
    public static function effectiveTopic(array $data, string $slugFileName, string $bodyHint = ''): string
    {
        $t = isset($data['topic']) && is_string($data['topic']) ? $data['topic'] : null;
        if ($t) {
            return $t;
        }
        $cat = isset($data['category']) && is_string($data['category']) ? $data['category'] : 'general';
        return self::inferTopic($cat, $slugFileName, $bodyHint);
    }

    public static function inferTopic(string $category, string $slugFileName, string $bodySample): string
    {
        $lower = strtolower($slugFileName . ' ' . substr($bodySample, 0, 2000));
        return match ($category) {
            'banner' => 'banner',
            'patch' => 'patch',
            'codes' => 'codes',
            'newbie' => 'newbie',
            'tier' => 'party',
            'hardware' => 'tech',
            default => self::inferTopicHeuristic($lower),
        };
    }

    private static function inferTopicHeuristic(string $lower): string
    {
        if (str_contains($lower, 'тир') || str_contains($lower, 'tier') || str_contains($lower, 'отряд') || str_contains($lower, 'команд')) {
            return 'party';
        }
        if (str_contains($lower, 'примогем') || str_contains($lower, 'фарм') || str_contains($lower, 'крутк') || str_contains($lower, 'экономик')) {
            return 'economy';
        }
        if (str_contains($lower, 'лор') || str_contains($lower, 'сюжет') || str_contains($lower, 'теори') || str_contains($lower, 'фатуи')) {
            return 'lore';
        }
        if (
            str_contains($lower, 'ноутбук') || str_contains($lower, 'noutbuk')
            || str_contains($lower, 'пк ') || str_contains($lower, ' pk')
            || str_contains($lower, 'железо') || str_contains($lower, 'fps')
            || preg_match('/\bpc\b/i', $lower)
        ) {
            return 'tech';
        }
        return 'general';
    }

    /** @param array<string,mixed> $data */
    public static function effectiveGameVersion(array $data, string $slug, string $bodyHint = ''): ?string
    {
        $gv = isset($data['gameVersion']) && is_string($data['gameVersion']) ? $data['gameVersion'] : null;
        return $gv ?: self::extractGameVersion($slug, $bodyHint);
    }

    public static function extractGameVersion(string $slug, string $bodySample): ?string
    {
        if (preg_match('/(?:^|[-_])update[-_]?(\d+)[-_](\d+)/i', $slug, $m)) {
            return $m[1] . '.' . $m[2];
        }
        if (preg_match('/\b(\d+)\.(\d+)\s*«/u', $bodySample, $m)) {
            return $m[1] . '.' . $m[2];
        }
        return null;
    }

    /** @param array<string,mixed> $data */
    public static function effectiveStatus(array $data, string $slug, string $bodyHint = ''): string
    {
        $st = isset($data['status']) && is_string($data['status']) ? $data['status'] : null;
        if ($st) {
            return $st;
        }
        $cat = isset($data['category']) && is_string($data['category']) ? $data['category'] : 'general';
        $date = self::parseDate($data['date'] ?? null);
        $gv = self::extractGameVersion($slug, $bodyHint);
        return self::inferStatus($cat, $date, $gv);
    }

    private static function inferStatus(string $category, ?int $dateTs, ?string $gameVersion): string
    {
        if ($category === 'patch' || $gameVersion) {
            return 'dated';
        }
        if ($category === 'banner' && $dateTs) {
            $ageSec = time() - $dateTs;
            if ($ageSec > 180 * 24 * 60 * 60) {
                return 'historical';
            }
            return 'dated';
        }
        if ($category === 'codes') {
            return 'dated';
        }
        return 'active';
    }

    /** @param array<string,mixed> $data */
    public static function effectiveAudience(array $data, string $bodyHint = ''): string
    {
        $a = isset($data['audience']) && is_string($data['audience']) ? $data['audience'] : null;
        if ($a) {
            return $a;
        }
        $cat = isset($data['category']) && is_string($data['category']) ? $data['category'] : 'general';
        return self::inferAudience($cat, $bodyHint);
    }

    private static function inferAudience(string $category, string $bodySample): string
    {
        $lower = strtolower(substr($bodySample, 0, 1500));
        if ($category === 'newbie') {
            return 'beginner';
        }
        if (preg_match('/бездн|спирал|meta|мета|абисс/i', $lower)) {
            return 'meta';
        }
        if (preg_match('/нович|старт|первые шаги/i', $lower)) {
            return 'beginner';
        }
        return 'all';
    }

    private static function parseDate(mixed $d): ?int
    {
        if ($d === null || $d === '') {
            return null;
        }
        if (is_int($d)) {
            return $d;
        }
        if (is_string($d)) {
            $ts = strtotime($d);
            return $ts !== false ? $ts : null;
        }
        return null;
    }
}
