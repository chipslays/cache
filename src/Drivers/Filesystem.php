<?php

namespace Please\Cache\Drivers;

use Please\Cache\Exceptions\DriverException;
use FilesystemIterator;

class Filesystem extends AbstractDriver
{
    private FilesystemIterator $filesystemIterator;

    /**
     * @param string|null $folder
     * @param string $prefix
     */
    public function __construct(
        protected ?string $folder = null,
        protected string $prefix = 'cache',
    ) {
        if (!$this->folder) {
            $this->folder = $this->getDefaultFolder();
        }

        $this->folder = $this->ensureFolderIsExistsAndWritable($this->folder);

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
     * @inheritDoc
     */
    public function set(string $key, mixed $value, string|int|null $ttl = null): static
    {
        if ($ttl === null) {
            return $this;
        }

        $ttl = $this->ttlToSeconds($ttl);

        if ($ttl <= 0) {
            return $this;
        }

        $cacheFile = $this->getFilePath($key);

        if (is_file($cacheFile)) {
            @unlink($cacheFile);
        }

        if (@file_put_contents($cacheFile, $value, LOCK_EX) !== false) {
            @chmod($cacheFile, 0666);
            @touch($cacheFile, $ttl + time());
        }

        return $this;
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
    public function clear(): static
    {
        foreach ($this->filesystemIterator as $file) {
            if (strpos($file->getFilename(), $this->prefix) === false) {
                continue;
            }

            if ($file->isDir()) {
                // рекурсию надо сделать
            }

            @unlink($file->getPathname());
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function forget(string $key): static
    {
        @unlink($this->getFilePath($key));

        return $this;
    }

    /**
     * Garbage collector.
     *
     * Removing expired cache files under a directory.
     *
     * @return void
     */
    protected function collectGarbage(): void
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
     * Get a file path.
     *
     * @param string $key
     */
    protected function getFilePath(string $key)
    {
        return $this->folder . DIRECTORY_SEPARATOR . $this->getHashedKey($key);
    }

    /**
     * Get a key.
     *
     * @param string $key
     * @return string
     */
    protected function getHashedKey(string $key): string
    {
        return $this->prefix . '_' . md5($key);
    }

    protected function getDefaultFolder(): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'please-php-cache';
    }

    protected function ensureFolderIsExistsAndWritable(string $folder): string
    {
        $folder = rtrim($folder, '\/');

        @mkdir($folder, 0666, true);

        if (!is_writable($folder)) {
            throw new DriverException('Cache folder is not writable: ' . $folder);
        }

        return $folder;
    }
}
