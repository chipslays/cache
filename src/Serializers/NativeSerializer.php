<?php

namespace Please\Cache\Serializers;

use Please\Cache\Serializers\Contracts\Serializer;
use Please\Cache\Exceptions\SerializerException;
use Closure;
use ArrayAccess;
use Serializable;
use stdClass;
use Traversable;

/**
 * Native serializer uses the functions `serialize()` and `unserialize()`.
 */
class NativeSerializer implements Serializer
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
    public function unserialize(mixed $value): mixed
    {
        return @unserialize($value);
    }

    /**
     * @param mixed $variable
     * @return bool
     */
    protected function isIterable(mixed $variable): bool
    {
        return is_array($variable) || (is_object($variable) && ($variable instanceof Traversable));
    }

    /**
     * @param mixed $variable
     * @param bool $iterate
     * @return bool
     */
    protected function isSerializable(mixed $variable, bool $iterate = true): bool
    {
        if (is_resource($variable)) {
            return false;
        } else if (is_object($variable)) {
            if ($variable instanceof Closure) {
                return false;
            } else if ($variable instanceof stdClass) {
                return true;
            } else if (!$variable instanceof Serializable && !$variable instanceof ArrayAccess) {
                return false;
            }
        }

        if ($iterate && $this->isIterable($variable)) {
            foreach ($variable as $value) {
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

            throw new SerializerException(
                'Cannot serialize value of type: ' . $type
            );
        }
    }
}