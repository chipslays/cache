<?php

namespace Please\Cache\Drivers;

use Closure;
use Please\Cache\Serializers\Serializer;

abstract class AbstractDriver implements DriverInterface
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
     * Convert a string to seconds.
     *
     * @param integer|string $ttl
     * @return integer
     */
    protected function covertTtlToSeconds(int|string $ttl): int
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
    public function getOverriddenSerializer(): ?Serializer
    {
        return null;
    }
}
