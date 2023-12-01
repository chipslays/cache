<?php

use Please\Cache\Cache;

require __DIR__ . '/../vendor/autoload.php';

$cache = new Cache;

$data = $cache->through(function () {
    return time();
});

dump($data);

sleep(2);

$data = $cache->through(function () {
    return time();
});

dump($data);