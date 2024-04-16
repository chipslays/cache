<?php

namespace Please\Cache\Serializers\Contracts;

use Please\Cache\Exceptions\SerializerException;

interface Serializer
{
    /**
     * Serialize cache value.
     *
     * @param mixed $value
     * @throws SerializerException
     * @return mixed
     */
    public function serialize(mixed $value): mixed;

    /**
     * Unserialize cache value.
     *
     * @param mixed $value
     * @return mixed
     */
    public function unserialize(mixed $value): mixed;
}