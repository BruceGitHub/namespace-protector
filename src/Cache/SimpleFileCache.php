<?php

declare(strict_types=1);

namespace NamespaceProtector\Cache;

use Webmozart\Assert\Assert;
use NamespaceProtector\Common\PathInterface;

final class SimpleFileCache implements \Psr\SimpleCache\CacheInterface
{
    /** @var PathInterface */
    private $path;

    public function __construct(PathInterface $cachePath)
    {
        $this->path = $cachePath;
        if (!\is_dir($this->path->get())) {
            \safe\mkdir($this->path->get());
        }
    }

    public function get($key, $default = null)
    {
        $fileCached = $this->createFileNameFromKey($key);
        if (!\file_exists($fileCached)) {
            return $default;
        }

        $jsonDecoder = new \PhpParser\JsonDecoder();

        $content = \Safe\file_get_contents($fileCached);

        return $jsonDecoder->decode($content);
    }

    public function set($key, $value, $ttl = null)
    {
        $fileCached = $this->createFileNameFromKey($key);
        \file_put_contents($fileCached, json_encode($value, JSON_PRETTY_PRINT));

        return true;
    }

    public function delete($key)
    {
        $fileCached = $this->createFileNameFromKey($key);
        \unlink($fileCached);

        return true;
    }

    public function clear()
    {
        $this->delete_directory($this->path->get());
        return true;
    }

    /**
     * @param array<string> $keys
     * @return array<mixed>
     */
    public function getMultiple($keys, $default = null)
    {
        throw new \RuntimeException('Non implemented yet');
    }

    /**
     * @param array<mixed> $values
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        throw new \RuntimeException('Non implemented yet');
    }

    /**
     * @param array<string> $keys
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        throw new \RuntimeException('Non implemented yet');
    }

    public function has($key)
    {
        $fileCached = $this->createFileNameFromKey($key);
        if (\file_exists($fileCached)) {
            return true;
        }

        return false;
    }

    private function createFileNameFromKey(string $key): string
    {
        return $this->path->get() . '/' . $key;
    }

    private function delete_directory(string $dirname): bool
    {
        $dir_handle = \Safe\opendir($dirname);
        Assert::notNull($dir_handle);

        while ($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname . \DIRECTORY_SEPARATOR . $file)) {
                    \Safe\unlink($dirname . \DIRECTORY_SEPARATOR . $file);
                } else {
                    $this->delete_directory($dirname . \DIRECTORY_SEPARATOR . $file);
                }
            }
        }
        closedir($dir_handle);
        \rmdir($dirname);
        return true;
    }
}
