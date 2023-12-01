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
     * @param mixed $value
     * @return mixed
     */
    public function unserialize(mixed $value): mixed
    {
        return $value;
    }
}