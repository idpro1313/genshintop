<?php

declare(strict_types=1);

final class Partners
{
    private const LOOTBAR_ORIGIN = 'https://lootbar.gg';
    private const LOOTBAR_PATH = '/ru/top-up/genshin-impact';

    /** @param string $campaign utm_campaign */
    public static function lootbarGenshinTopupUrl(string $campaign = 'genshin_topup'): string
    {
        $u = self::LOOTBAR_ORIGIN . self::LOOTBAR_PATH;
        $q = http_build_query([
            'aff_short' => 'dandnagers',
            'utm_source' => 'genshintop',
            'utm_medium' => 'referral',
            'utm_campaign' => $campaign,
        ]);
        return $u . '?' . $q;
    }
}
