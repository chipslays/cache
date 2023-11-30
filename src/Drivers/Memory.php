<?php

namespace Please\Cache\Drivers;

use Please\Cache\Serializers\Serializer;

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
    public function set(string $key, mixed $value, int|string $ttl = '1 year'): self
    {
        $ttl = $this->covertTtlToSeconds($ttl) + time();

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
    public function clear(): self
    {
        $this->items = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): self
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOverriddenSerializer(): ?Serializer
    {
        return new Serializer;
    }
}
