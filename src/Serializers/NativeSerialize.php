<?php

namespace Please\Cache\Serializers;

use Please\Cache\Exceptions\SerializerException;
use Closure;
use ArrayAccess;
use Serializable;
use Traversable;

/**
 * Native serializer uses the functions `serialize()` and `unserialize()`.
 */
class NativeSerialize implements SerializerInterface
{
    /**
     * @inheritDoc
     */
    public function serialize(mixed $value): string
    {
        $this->throwExceptionIsNotSerializable($value);

        return serialize($value);
    }

    /**
     * @inheritDoc
     */
    public function unserialize(string $value): mixed
    {
        return @unserialize($value);
    }

    /**
     * @param mixed $var
     * @return boolean
     */
    protected function isIterable(mixed $var): bool
    {
        return is_array($var) || (is_object($var) && ($var instanceof Traversable));
    }

    /**
     * @param mixed $var
     * @param boolean $iterate
     * @return boolean
     */
    protected function isSerializable(mixed $var, bool $iterate = true): bool
    {
        if (is_resource($var)) {
            return false;
        } else if (is_object($var)) {
            if ($var instanceof Closure) {
                return false;
            } else if (!$var instanceof Serializable && !$var instanceof ArrayAccess) {
                return false;
            }
        }

        if ($iterate && $this->isIterable($var)) {
            foreach ($var as $value) {
                if (!$this->isSerializable($value, true)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param mixed $value
     * @return void
     *
     * @throws SerializerException
     */
    protected function throwExceptionIsNotSerializable(mixed $value): void
    {
        if (!$this->isSerializable($value)) {
            $type = is_object($value) ? get_class($value) : gettype($value);
            throw new SerializerException('Cannot serialize value of type: ' . $type);
        }
    }
}