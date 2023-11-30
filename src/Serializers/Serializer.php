<?php

namespace Please\Cache\Serializers;

class Serializer
{
    /**
     * Serialize cache value.
     *
     * @param mixed $value
     * @return mixed
     */
    public function serialize(mixed $value): mixed
    {
        return $value;
    }

    /**
     * Unserialize cache value.
     *
     * @param string $value
     * @return mixed
     */
    public function unserialize(string $value): mixed
    {
        return $value;
    }
}