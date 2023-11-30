<?php

namespace Please\Cache\Drivers;

interface DriverInterface
{
    /**
     * Fetches a value from the cache.
     *
     * @param string $key The unique key of this item in the cache.
     * @param mixed $default Default value to return if the key does not exist.
     *
     * @return mixed The value of the item from the cache, or $default in case of cache miss.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
     *
     * @param string $key The key of the item to store.
     * @param mixed $value The value of the item to store.
     * @param int $ttl [Optional] The TTL value of this item.
     *
     * @return self
     */
    public function set(string $key, mixed $value, int|string $ttl = '1 year'): self;

    /**
     * Determines whether an item is present in the cache.
     *
     * @param string $key The cache item key.
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * Wipes all cache.
     *
     * @return self
     */
    public function clear(): self;

    /**
     * Delete cache by key
     *
     * @param string $key
     * @return self
     */
    public function delete(string $key): self;
}
