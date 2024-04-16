<?php

namespace Please\Cache\Serializers;

use Please\Cache\Serializers\Contracts\Serializer;

class DummySerializer implements Serializer
{
    /**
     * @inheritDoc
     */
    public function serialize(mixed $value): mixed
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function unserialize(mixed $value): mixed
    {
        return $value;
    }
}