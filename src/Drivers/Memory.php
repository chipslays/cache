<?php

namespace Please\Cache\Drivers;

use Please\Cache\Serializers\Contracts\Serializer;
use Please\Cache\Serializers\DummySerializer;

class Memory extends AbstractDriver
{
    protected array $items = [];

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!isset($this->items[$key])) {
            return $this->handleDefaultValue($default);
        }

        $item = $this->items[$key];

        if ($item['ttl'] < time()) {
            return $this->handleDefaultValue($default);
        }

        return $item['value'];
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, string|int|null $ttl = null): static
    {
        if ($ttl === null) {
            return $this;
        }

        $ttl = $this->ttlToSeconds($ttl) + time();

        $this->items[$key] = compact('value', 'ttl');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        if (!isset($this->items[$key])) {
            return false;
        }

        $item = $this->items[$key];

        return $item['ttl'] > time();
    }

    /**
     * @inheritDoc
     */
    public function clear(): static
    {
        $this->items = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): static
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSerializer(): ?Serializer
    {
        return new DummySerializer;
    }
}
