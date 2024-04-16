<?php

namespace Please\Cache\Drivers;

use Please\Cache\Serializers\Serializer;

class Session extends AbstractDriver
{
    public function __construct(
        protected string $name = '__cache_values__',
    )
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (!isset($_SESSION[$this->name][$key])) {
            return $this->handleDefaultValue($default);
        }

        $item = $_SESSION[$this->name][$key];

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

        $_SESSION[$this->name][$key] = compact('value', 'ttl');

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        if (!isset($_SESSION[$this->name][$key])) {
            return false;
        }

        $item = $_SESSION[$this->name][$key];

        return $item['ttl'] > time();
    }

    /**
     * @inheritDoc
     */
    public function clear(): static
    {
        $_SESSION[$this->name] = [];

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): static
    {
        unset($_SESSION[$this->name][$key]);

        return $this;
    }
}
