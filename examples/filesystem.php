<?php

use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;

require __DIR__ . '/../vendor/autoload.php';

// provide arguments
$driver = new Filesystem(
    folder: __DIR__ . '/cache',
    prefix: 'cached_',
    extension: '.bin',
);

// or use simple as
$cache = new Cache(new Filesystem);

// default create Filesystem instance under hood
$cache = new Cache;

$cache->set('foo', 'bar', 1);

dump($cache->get('foo')); // bar

sleep(1);

dump($cache->get('foo', 'baz')); // baz

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



