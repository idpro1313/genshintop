<?php

declare(strict_types=1);

/**
 * Клиент протокола IndexNow (Яндекс — официальный участник).
 *
 * Ключ хранится в public/<KEY>.txt (содержимое = сам ключ); Яндекс верифицирует
 * наличие файла перед приёмом пингов. Имя ключа задаётся переменной окружения
 * INDEXNOW_KEY либо берётся из дефолта в этом классе.
 *
 * Документация: https://yandex.ru/support/webmaster/indexnow/key.html
 */
final class IndexNow
{
    public const DEFAULT_KEY = 'cfee9df50829202f695cf93c8b24f554';
    public const ENDPOINT = 'https://yandex.com/indexnow';
    public const HOST = 'genshintop.ru';
    public const BATCH_SIZE = 10000;

    public static function key(): string
    {
        $env = getenv('INDEXNOW_KEY');
        if (is_string($env) && $env !== '') {
            return $env;
        }
        return self::DEFAULT_KEY;
    }

    public static function keyLocation(): string
    {
        return 'https://' . self::HOST . '/' . self::key() . '.txt';
    }

    /**
     * Пинг одного URL (GET).
     *
     * @return array{ok:bool,status:int,body:string}
     */
    public static function submit(string $url): array
    {
        $params = http_build_query([
            'url' => $url,
            'key' => self::key(),
            'keyLocation' => self::keyLocation(),
        ]);
        return self::httpGet(self::ENDPOINT . '?' . $params);
    }

    /**
     * Пинг батчем (POST application/json). Возвращает массив ответов на каждый батч
     * (по BATCH_SIZE URL).
     *
     * @param list<string> $urls
     * @return list<array{ok:bool,status:int,body:string}>
     */
    public static function submitMany(array $urls): array
    {
        $urls = array_values(array_unique(array_filter($urls, static fn ($u) => is_string($u) && $u !== '')));
        if ($urls === []) {
            return [];
        }
        $results = [];
        foreach (array_chunk($urls, self::BATCH_SIZE) as $batch) {
            $payload = [
                'host' => self::HOST,
                'key' => self::key(),
                'keyLocation' => self::keyLocation(),
                'urlList' => $batch,
            ];
            $results[] = self::httpPostJson(self::ENDPOINT, $payload);
        }
        return $results;
    }

    /** @return array{ok:bool,status:int,body:string} */
    private static function httpGet(string $url): array
    {
        $ctx = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 10,
                'ignore_errors' => true,
                'header' => 'User-Agent: GenshinTop-IndexNow/1.0',
            ],
        ]);
        $body = @file_get_contents($url, false, $ctx);
        $status = self::statusFromHeaders($http_response_header ?? []);
        return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'body' => is_string($body) ? $body : ''];
    }

    /**
     * @param array<string,mixed> $payload
     * @return array{ok:bool,status:int,body:string}
     */
    private static function httpPostJson(string $url, array $payload): array
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'timeout' => 30,
                'ignore_errors' => true,
                'header' => "Content-Type: application/json; charset=utf-8\r\nUser-Agent: GenshinTop-IndexNow/1.0",
                'content' => $json,
            ],
        ]);
        $body = @file_get_contents($url, false, $ctx);
        $status = self::statusFromHeaders($http_response_header ?? []);
        return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'body' => is_string($body) ? $body : ''];
    }

    /** @param list<string> $headers */
    private static function statusFromHeaders(array $headers): int
    {
        foreach ($headers as $h) {
            if (preg_match('#^HTTP/\d(?:\.\d)?\s+(\d{3})#', $h, $m)) {
                return (int) $m[1];
            }
        }
        return 0;
    }
}
