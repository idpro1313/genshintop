<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';

$cfg = require dirname(__DIR__) . '/config.php';
OgManifest::load(dirname(__DIR__) . '/lib/og-manifest.json');

Router::dispatch($cfg);
