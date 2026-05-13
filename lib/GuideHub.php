<?php

declare(strict_types=1);

final class GuideHub
{
    /** @param array<string,mixed> $g guide row from repository */
    public static function bodyHint(array $g): string
    {
        $s = $g['summary'] ?? '';
        return is_string($s) ? $s : '';
    }

    /** @param array<string,mixed> $g */
    public static function fullText(array $g): string
    {
        $slug = $g['slug'] ?? '';
        $title = $g['title'] ?? '';
        return strtolower($title . ' ' . $slug . ' ' . self::bodyHint($g));
    }

    /** @param array<string,mixed> $g */
    public static function effectiveTopic(array $g): string
    {
        $slugFile = ($g['slug'] ?? '') . '.md';
        return GuideTaxonomy::effectiveTopic($g['meta'] ?? [], $slugFile, self::bodyHint($g));
    }

    /** Canonical PLAN pillar from guide frontmatter; null if unset or invalid. */
    public static function planTrack(array $g): ?string
    {
        $meta = $g['meta'] ?? [];
        if (!is_array($meta)) {
            return null;
        }
        $p = $meta['planTrack'] ?? null;
        if (!is_string($p) || $p === '') {
            return null;
        }

        return match ($p) {
            'basics', 'advanced', 'walkthroughs' => $p,
            default => null,
        };
    }

    /** @param array<string,mixed> $g */
    public static function matchHubGameBasics(array $g): bool
    {
        $t = self::planTrack($g);
        if ($t !== null) {
            return $t === 'basics';
        }

        return self::matchHubNewbie($g) || self::matchHubCodes($g);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubAdvancedGuides(array $g): bool
    {
        $t = self::planTrack($g);
        if ($t !== null) {
            return $t === 'advanced';
        }

        return self::matchHubPatches($g) || self::matchHubTierList($g);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubQuestWalkthroughs(array $g): bool
    {
        $t = self::planTrack($g);
        if ($t !== null) {
            return $t === 'walkthroughs';
        }

        $full = self::fullText($g);

        return self::matchHubQuests($g)
            || (bool) preg_match('/hangout|истори(?:я|и)\s+зависим|миров(?:ые|ой)\s+квест|навигаци.*квест/ui', $full);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubBanners(array $g): bool
    {
        $cat = (string) ($g['category'] ?? '');
        $topic = self::effectiveTopic($g);
        return $cat === 'banner' || $topic === 'banner';
    }

    /** @param array<string,mixed> $g */
    public static function matchHubCodes(array $g): bool
    {
        $cat = (string) ($g['category'] ?? '');
        $topic = self::effectiveTopic($g);
        return $cat === 'codes' || $topic === 'codes';
    }

    /** @param array<string,mixed> $g */
    public static function matchHubPatches(array $g): bool
    {
        $cat = (string) ($g['category'] ?? '');
        $topic = self::effectiveTopic($g);
        return $cat === 'patch' || $topic === 'patch';
    }

    /** @param array<string,mixed> $g */
    public static function matchHubNewbie(array $g): bool
    {
        $cat = (string) ($g['category'] ?? '');
        $topic = self::effectiveTopic($g);
        return $cat === 'newbie' || $topic === 'newbie';
    }

    /** @param array<string,mixed> $g */
    public static function matchHubEconomy(array $g): bool
    {
        $topic = self::effectiveTopic($g);
        $t = strtolower(($g['title'] ?? '') . ' ' . ($g['slug'] ?? '') . ' ' . self::bodyHint($g));
        return $topic === 'economy' || (bool) preg_match('/примогем|молитв|донат|крутк|genesis|кристалл|пополн/ui', $t);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubTierList(array $g): bool
    {
        $cat = (string) ($g['category'] ?? '');
        $topic = self::effectiveTopic($g);
        $t = strtolower(($g['title'] ?? '') . ' ' . self::bodyHint($g));
        return $cat === 'tier' || $topic === 'party' || (bool) preg_match('/тир[\s-]?лист|tier/ui', $t);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubEvents(array $g): bool
    {
        $t = self::fullText($g);
        if (preg_match('/молитва события/ui', $t)) {
            return false;
        }
        return (bool) preg_match('/(^|[^а-я])ивент|event(?!s\.)|игровое событие|временн[оа]е событие|временный режим/ui', $t);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubTcg(array $g): bool
    {
        $t = self::fullText($g);
        return (bool) preg_match('/tcg|священный призыв|карточн|колод[ау]|инвокаци|geni[uo]s invokation/ui', $t);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubDomains(array $g): bool
    {
        $t = self::fullText($g);
        return (bool) preg_match('/подземель|подзем[е]|домен[аыу]?\b|фарм артефакт|фарм оружи/ui', $t);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubBosses(array $g): bool
    {
        $t = self::fullText($g);
        return (bool) preg_match('/босс[аыу]?\b|еженедельн[ыо]\s+(?:босс|противник)|world boss|босс-?файт/ui', $t);
    }

    /** @param array<string,mixed> $g */
    public static function matchHubQuests(array $g): bool
    {
        $t = self::fullText($g);
        return (bool) preg_match('/квест архонтов|архонт[\s-]?квест|сюжет.*глав|глава\s+(?:[i]+|\d)|квест легенд|сюжетн[ыа]?\s+квест/ui', $t);
    }
}
