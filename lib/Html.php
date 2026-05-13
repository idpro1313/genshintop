<?php

declare(strict_types=1);

final class Html
{
    public static function e(?string $s): string
    {
        return htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
