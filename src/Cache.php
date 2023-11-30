<?php

namespace Please\Cache;

use Please\Cache\Drivers\Filesystem;
use Please\Cache\Drivers\DriverInterface;
use Please\Cache\Serializers\NativeSerialize;
use Please\Cache\Serializers\SerializerInterface;

class Cache implements DriverInterface
{
    /**
     * Constructor.
     *
     * @param DriverInterface|null $driver
     */
    public function __construct(
        protected ?DriverInterface $driver = new Filesystem,
        protected SerializerInterface $serializer = new NativeSerialize,
    ) {
        //
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $lazyCallback = fn () => $this->serializer->serialize($default);

        $serializedValue = $this->driver->get($key, $lazyCallback);

        return $this->serializer->unserialize($serializedValue);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, int|string $ttl = '1 year'): self
    {
        $serializedValue = $this->serializer->serialize($value);

        $this->driver->set($key, $serializedValue, $ttl);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return $this->driver->has($key);
    }

    /**
     * @inheritDoc
     */
    public function clear(): self
    {
        $this->driver->clear();

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): self
    {
        $this->driver->delete($key);

        return $this;
    }
}

