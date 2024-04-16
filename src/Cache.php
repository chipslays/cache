<?php

namespace Please\Cache;

use Please\Cache\Drivers\Memory;
use Please\Cache\Drivers\AbstractDriver;
use Please\Cache\Drivers\Contracts\Driver;
use Please\Cache\Serializers\NativeSerializer;
use Please\Cache\Serializers\Contracts\Serializer;
use Please\Cache\Support\ClosureHash;
use Closure;

class Cache implements Driver
{
    /**
     * Constructor.
     *
     * @param AbstractDriver|null $driver
     */
    public function __construct(
        protected ?AbstractDriver $driver = new Memory,
        protected ?Serializer $serializer = null,
    ) {
        $this->serializer = $this->driver->getSerializer()
            ?? $this->serializer
            ?? new NativeSerializer;
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
    public function set(string $key, mixed $value, string|int|null $ttl = null): self
    {
        if ($value instanceof Closure) {
            $value = $value();
        }

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
    public function forget(string $key): self
    {
        $this->driver->forget($key);

        return $this;
    }

    /**
     * Removes and returns an item from the cache by its key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function pluck(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);

        $this->driver->forget($key);

        return $value;
    }

    /**
     * If the closure is not cached, then executes it,
     * otherwise returns the cached result of executing
     * the closure.
     *
     * @param Closure $closure
     * @param int|string $ttl
     * @return mixed
     */
    public function through(Closure $closure, string|int|null $ttl = null): mixed
    {
        $hash = 'hashed_closure_' . ClosureHash::make($closure);

        if ($value = $this->get($hash)) {
            return $value;
        }

        $value = call_user_func($closure);

        $this->set($hash, $value, $ttl);

        return $value;
    }
}

