<?php

namespace Please\Cache\Drivers;

use Please\Cache\Drivers\Contracts\Driver;
use Please\Cache\Serializers\Contracts\Serializer;
use Closure;

abstract class AbstractDriver implements Driver
{
    /**
     * @param mixed $defaultValue
     * @return mixed
     */
    protected function handleDefaultValue(mixed $defaultValue): mixed
    {
        return $defaultValue instanceof Closure
            ? call_user_func($defaultValue)
            : $defaultValue;
    }

    /**
     * Convert a ttl to seconds.
     *
     * @param int|string $ttl
     * @return int
     */
    protected function ttlToSeconds(int|string $ttl): int
    {
        return is_string($ttl)
            ? strtotime($ttl) - time()
            : $ttl;
    }

    /**
     * This serializer override a current
     * serializer in cache instance.
     *
     * @return Serializer|null
     */
    public function getSerializer(): ?Serializer
    {
        return null;
    }
}
