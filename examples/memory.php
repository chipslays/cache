<?php

use Please\Cache\Cache;
use Please\Cache\Drivers\Memory;

require __DIR__ . '/../vendor/autoload.php';

$cache = new Cache(new Memory);

$cache->set('foo', 'bar', 1);

dump($cache->get('foo')); // bar

sleep(2);

dump($cache->get('foo', 'baz')); // baz

die;

// pass string like for strtotime() function
$cache->set('foo', 'bar', '1 hour');

dump($cache->has('foo')); // true

$cache->delete('foo');

dump($cache->has('foo')); // false

// pass string like for strtotime() function
$cache->set('foo1', 'bar1', '24 hours');
$cache->set('foo2', 'bar2', '24 hours');
$cache->set('foo3', 'bar3', '24 hours');

$cache->clear();

dump(
    $cache->has('foo1'), // false
    $cache->has('foo2'), // false
    $cache->has('foo3'), // false
);



