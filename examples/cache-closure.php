<?php

use Please\Cache\Cache;

require __DIR__ . '/../vendor/autoload.php';

$cache = new Cache;

$closure = fn (): int => mt_rand();

echo $cache->through($closure) . PHP_EOL;
echo $cache->through($closure) . PHP_EOL;