<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$cfg = require __DIR__ . '/config.php';
OgManifest::load(__DIR__ . '/og-manifest.json');

Router::dispatch($cfg);
