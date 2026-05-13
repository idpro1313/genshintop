<?php

declare(strict_types=1);

return [
    'site_url' => getenv('SITE_URL') ?: 'https://genshintop.ru',
    'site_name' => 'GenshinTop',
    'yandex_metrika_id' => 109020836,
    /** Optional verification meta (PUBLIC_* in Astro). */
    'meta_verification' => [
        'yandex' => getenv('PUBLIC_YANDEX_VERIFICATION') ?: getenv('YANDEX_VERIFICATION') ?: '',
        'google' => getenv('PUBLIC_GOOGLE_SITE_VERIFICATION') ?: getenv('GOOGLE_SITE_VERIFICATION') ?: '',
        'mailru' => getenv('PUBLIC_MAILRU_DOMAIN') ?: getenv('MAILRU_DOMAIN') ?: '',
    ],
    'organization_same_as' => array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', (string) (getenv('PUBLIC_ORGANIZATION_SAME_AS') ?: ''), -1, PREG_SPLIT_NO_EMPTY)))),
];
