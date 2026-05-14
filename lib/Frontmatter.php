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
     * Minimal YAML subset used by content frontmatter:
     * key: value, quoted strings, booleans, inline lists, indented lists and
     * one-level maps with lists (for sources.web).
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
            $children = [];
            $j = $i + 1;
            while ($j < $n) {
                $nl = $lines[$j];
                if (trim($nl) === '' || str_starts_with(trim($nl), '#')) {
                    $j++;
                    continue;
                }
                if (!preg_match('/^\s/', $nl)) {
                    break;
                }
                $children[] = $nl;
                $j++;
            }
            $out[$key] = self::parseNested($children);
            $i = $j;
        }
        return $out;
    }

    /**
     * @param list<string> $lines
     */
    private static function parseNested(array $lines): mixed
    {
        $lines = array_values(array_filter($lines, static fn (string $line): bool => trim($line) !== ''));
        if ($lines === []) {
            return [];
        }

        $hasMap = false;
        foreach ($lines as $line) {
            if (preg_match('/^\s+([A-Za-z0-9_]+):\s*(.*)$/', $line) === 1) {
                $hasMap = true;
                break;
            }
        }

        if (!$hasMap) {
            $items = [];
            foreach ($lines as $line) {
                if (preg_match('/^\s*-\s+(.*)$/', $line, $mm) === 1) {
                    $items[] = self::scalarValue(trim($mm[1]));
                }
            }
            return $items;
        }

        $out = [];
        $i = 0;
        $n = count($lines);
        while ($i < $n) {
            $line = $lines[$i];
            if (preg_match('/^\s+([A-Za-z0-9_]+):\s*(.*)$/', $line, $km) !== 1) {
                $i++;
                continue;
            }
            $key = $km[1];
            $rest = trim($km[2]);
            if ($rest !== '') {
                $out[$key] = self::scalarValue($rest);
                $i++;
                continue;
            }

            $items = [];
            $j = $i + 1;
            while ($j < $n) {
                $nl = $lines[$j];
                if (preg_match('/^\s+([A-Za-z0-9_]+):\s*(.*)$/', $nl) === 1) {
                    break;
                }
                if (preg_match('/^\s*-\s+(.*)$/', $nl, $mm) === 1) {
                    $items[] = self::scalarValue(trim($mm[1]));
                    $j++;
                    continue;
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
        if (strtolower($v) === 'true') {
            return true;
        }
        if (strtolower($v) === 'false') {
            return false;
        }
        if (($v[0] === '"' && str_ends_with($v, '"')) || ($v[0] === "'" && str_ends_with($v, "'"))) {
            return stripcslashes(substr($v, 1, -1));
        }
        if ($v[0] === '[' && str_ends_with($v, ']')) {
            $inner = trim(substr($v, 1, -1));
            if ($inner === '') {
                return [];
            }
            return array_map(
                static fn (string $part): mixed => self::scalarValue(trim($part)),
                explode(',', $inner)
            );
        }
        if (is_numeric($v)) {
            return str_contains($v, '.') ? (float) $v : (int) $v;
        }
        return $v;
    }
}
