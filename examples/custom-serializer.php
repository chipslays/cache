<?php

use Please\Cache\Cache;
use Please\Cache\Drivers\Filesystem;
use Please\Cache\Serializers\Contracts\Serializer;

require __DIR__ . '/../vendor/autoload.php';

class JsonSerializer implements Serializer
{
    public function serialize(mixed $value): string
    {
        return json_encode($value);
    }

    public function unserialize(mixed $value): mixed
    {
        return json_decode($value, true);
    }
}

$cache = new Cache(new Filesystem, new JsonSerializer);

$cache->set('foo', ['bar', 'baz'], 3600);

dump($cache->get('foo')); // [bar, baz]


