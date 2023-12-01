<?php

namespace Please\Cache\Support;

use SplFileObject;
use SplObjectStorage;
use ReflectionFunction;
use Closure;

class ClosureHash
{
    /**
     * List of hashes.
     *
     * @var SplObjectStorage
     */
    protected static $hashes = null;

    /**
     * Returns a hash for closure.
     *
     * @param Closure $closure
     *
     * @return string
     */
    public static function make(Closure $closure): string
    {
        if (!self::$hashes) {
            self::$hashes = new SplObjectStorage;
        }

        if (!isset(self::$hashes[$closure])) {
            $ref = new ReflectionFunction($closure);

            $file = new SplFileObject($ref->getFileName());
            $file->seek($ref->getStartLine() - 1);

            $content = '';

            while ($file->key() < $ref->getEndLine()) {
                $content .= $file->current();
                $file->next();
            }

            // dd(array(
            //     $content,
            //     $ref->getStaticVariables()
            // ));

            $hash = md5(json_encode(array(
                $content,
                $ref->getStaticVariables()
            )));

            self::$hashes[$closure] = $hash;
        }

        return self::$hashes[$closure];
    }
}
