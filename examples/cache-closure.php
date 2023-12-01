<?php

use Please\Cache\Cache;

require __DIR__ . '/../vendor/autoload.php';

$cache = new Cache;

// returns same value
echo $cache->through(fn () => mt_rand()) . PHP_EOL;
echo $cache->through(fn () => mt_rand()) . PHP_EOL;
