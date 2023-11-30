<?php

namespace Please\Cache\Drivers;

use Please\Cache\Exceptions\DriverException;
use FilesystemIterator;

class Filesystem extends AbstractDriver
{
    private FilesystemIterator $filesystemIterator;

    public function __construct(
        protected ?string $folder = null,
        protected ?string $prefix = null,
        protected ?string $extension = null,
    ) {
        if (!$this->folder) {
            $this->folder = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'please-php-cache';
            @mkdir($this->folder, 0666, true);
        } else if (!file_exists($this->folder)) {
            throw new DriverException('Provided folder is not exists: ' . $this->folder);
        }

        if (!is_writable($this->folder)) {
            throw new DriverException('Provided folder is not writable: ' . $this->folder);
        }

        $this->filesystemIterator = new FilesystemIterator($this->folder);
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $cacheFile = $this->getFilePath($key);

        if (@filemtime($cacheFile) > time()) {
            $fp = @fopen($cacheFile, 'rb');

            if ($fp !== false) {
                @flock($fp, LOCK_SH);

                $cacheValue = @stream_get_contents($fp);

                @flock($fp, LOCK_UN);
                @fclose($fp);

                return $cacheValue !== false ? $cacheValue : $this->handleDefaultValue($default);
            }
        }

        return $this->handleDefaultValue($default);
    }

    /**
     * Get a file path.
     *
     * @param string $key
     */
    protected function getFilePath(string $key)
    {
        $key = $this->buildKey($key);

        return $this->folder . DIRECTORY_SEPARATOR . $key . $this->extension;
    }

    /**
     * Get a key.
     *
     * @param string $key
     * @return string
     */
    protected function buildKey(string $key): string
    {
        return $this->prefix . md5($key);
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value, int|string $ttl = '1 year'): self
    {
        $this->gc();

        $ttl = $this->covertTtlToSeconds($ttl);

        $cacheFile = $this->getFilePath($key);

        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }

        if (@file_put_contents($cacheFile,  $value, LOCK_EX) !== false) {
            @chmod($cacheFile, 0666);

            if ($ttl <= 0) {
                $ttl = 31536000; // 365 days
            }

            @touch($cacheFile, $ttl + time());
        }

        return $this;
    }

    /**
     * Garbage collector Removing expired cache files under a directory.
     *
     * @return void
     */
    protected function gc(): void
    {
        $time = time();

        foreach ($this->filesystemIterator as $file) {
            if ($file->isDir() || strpos($file->getFilename(), $this->prefix ?? '') === false) {
                continue;
            }

            if ($file->getMTime() < $time) {
                @unlink($file->getRealPath());
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return @filemtime($this->getFilePath($key)) > time();
    }

    /**
     * @inheritDoc
     */
    public function clear(): self
    {
        foreach ($this->filesystemIterator as $file) {
            if ($file->isDir() || strpos($file->getFilename(), $this->prefix ?? '') === false) {
                continue;
            }
            @unlink($file->getPathname());
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $key): self
    {
        @unlink($this->getFilePath($key));

        return $this;
    }
}
