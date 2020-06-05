<?php
declare(strict_types=1);

namespace NamespaceProtector\Cache;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Exception\NamespaceProtectorExceptionInterface;

final class SimpleFileCache implements \Psr\SimpleCache\CacheInterface
{
    /** @var PathInterface */
    private $path;

    public function __construct(PathInterface $cachePath)
    {
        $this->path = $cachePath;
        if (!is_dir($this->path->get())) {
            \mkdir($this->path->get());
        }
    }

    public function get($key, $default = null)
    {
        $fileCached = $this->createFileNameFromKey($key);
        if (!\file_exists($fileCached)) {
            return $default;
        }

        $jsonDecoder = new \PhpParser\JsonDecoder();

        $content = \file_get_contents($fileCached);
        if ($content === false) {
            throw new \RuntimeException(NamespaceProtectorExceptionInterface::MSG_PLAIN_ERROR_PHP_PARSE_JSON_DECODE);
        }

        return $jsonDecoder->decode($content);
    }

    public function set($key, $value, $ttl = null)
    {
        $fileCached = $this->createFileNameFromKey($key);
        file_put_contents($fileCached, json_encode($value, JSON_PRETTY_PRINT));

        return true;
    }

    public function delete($key)
    {
        $fileCached = $this->createFileNameFromKey($key);
        unlink($fileCached);

        return true;
    }

    public function clear()
    {
        rmdir($this->path->get());
        return true;
    }

    /**
     * @param array<string> $keys
     * @return array<mixed>
     */
    public function getMultiple($keys, $default = null)
    {
        return $default;
    }

    /**
     * @param array<mixed> $values
     * @return bool
     */
    public function setMultiple($values, $ttl = null)
    {
        return false;
    }

    /**
     * @param array<string> $keys
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return false;
    }

    public function has($key)
    {
        $fileCached = $this->createFileNameFromKey($key);
        if (file_exists($fileCached)) {
            return true;
        }

        return false;
    }

    private function createFileNameFromKey(string $key): string
    {
        return $this->path->get() . '/' . $key;
    }
}
