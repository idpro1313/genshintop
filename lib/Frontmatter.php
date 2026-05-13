<?php

declare(strict_types=1);

final class Frontmatter
{
    /**
     * @return array{meta: array<string, mixed>, body: string}
     */
    public static function split(string $raw): array
    {
        $raw = str_replace("\r\n", "\n", $raw);
        if (!preg_match('/^---\n(.+?)\n---\n/s', $raw, $m)) {
            return ['meta' => [], 'body' => $raw];
        }
        $yaml = $m[1];
        $body = substr($raw, strlen($m[0]));
        return ['meta' => self::parseYamlLike($yaml), 'body' => $body];
    }

    /**
     * Minimal YAML subset: key: value, quoted strings, simple lists under key.
     *
     * @return array<string, mixed>
     */
    public static function parseYamlLike(string $yaml): array
    {
        $lines = explode("\n", $yaml);
        $out = [];
        $i = 0;
        $n = count($lines);
        while ($i < $n) {
            $line = $lines[$i];
            if ($line === '' || str_starts_with(trim($line), '#')) {
                $i++;
                continue;
            }
            if (!preg_match('/^([A-Za-z0-9_]+):\s*(.*)$/', $line, $km)) {
                $i++;
                continue;
            }
            $key = $km[1];
            $rest = $km[2];
            if ($rest !== '') {
                $out[$key] = self::scalarValue(trim($rest));
                $i++;
                continue;
            }
            // Multiline list or empty scalar
            $items = [];
            $j = $i + 1;
            while ($j < $n) {
                $nl = $lines[$j];
                if ($nl === '') {
                    $j++;
                    continue;
                }
                if (preg_match('/^\s-\s+(.*)$/', $nl, $mm)) {
                    $items[] = self::scalarValue(trim($mm[1]));
                    $j++;
                    continue;
                }
                if (!preg_match('/^\s/', $nl)) {
                    break;
                }
                $j++;
            }
            $out[$key] = $items;
            $i = $j;
        }
        return $out;
    }

    private static function scalarValue(string $v): mixed
    {
        if ($v === '' || strtolower($v) === 'null') {
            return null;
        }
        if (($v[0] === '"' && str_ends_with($v, '"')) || ($v[0] === "'" && str_ends_with($v, "'"))) {
            return stripcslashes(substr($v, 1, -1));
        }
        if (is_numeric($v)) {
            return str_contains($v, '.') ? (float) $v : (int) $v;
        }
        return $v;
    }
}
