<?php

declare(strict_types=1);

final class OgManifest
{
    /** @var array<string,true> */
    private static array $set = [];

    public static function load(string $path): void
    {
        self::$set = [];
        if (!is_readable($path)) {
            return;
        }
        $json = json_decode((string) file_get_contents($path), true);
        if (!is_array($json)) {
            return;
        }
        $entries = $json['entries'] ?? [];
        if (!is_array($entries)) {
            return;
        }
        foreach ($entries as $e) {
            if (is_string($e)) {
                self::$set[$e] = true;
            }
        }
    }

    public static function imageForEntry(string $collection, string $slug): string
    {
        $key = $collection . '/' . $slug;
        if (isset(self::$set[$key])) {
            return '/og/' . $key . '.png';
        }
        return '/og-default.svg';
    }
}
