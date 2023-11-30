<?php

namespace Please\Cache\Serializers;

interface SerializerInterface
{
    /**
     * Serialize cache value.
     *
     * @param mixed $value
     * @return string
     */
    public function serialize(mixed $value): string;

    /**
     * Unserialize cache value.
     *
     * @param string $value
     * @return mixed
     */
    public function unserialize(string $value): mixed;
}