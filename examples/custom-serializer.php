<?php

use Please\Cache\Cache;
use Please\Cache\Serializers\NativeSerializer;

require __DIR__ . '/../vendor/autoload.php';

class JsonSerializer extends NativeSerializer
{
    public function serialize(mixed $value): string
    {
        $this->throwExceptionIsNotSerializable($value);

        return json_encode($value);
    }

    public function unserialize(string $value): mixed
    {
        return json_decode($value, true);
    }
}

$cache = new Cache(serializer: new JsonSerializer);

$cache->set('foo', ['bar', 'baz']);

dump($cache->get('foo')); // [bar, baz]


