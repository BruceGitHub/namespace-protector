<?php
declare(strict_types=1);

namespace NamespaceProtector\Cache;

use Psr\SimpleCache\CacheInterface;

final class NullCache implements CacheInterface
{
    /*** @inheritDoc */
    public function get($key, $default = null)
    {
        return $default;
    }

    /*** @inheritDoc */
    public function set($key, $value, $ttl = null)
    {
        return false;
    }

    /*** @inheritDoc */
    public function delete($key)
    {
        return false;
    }

    /*** @inheritDoc */
    public function clear()
    {
        return false;
    }

    /**
     * @param array<string> $keys
     * @return array<mixed>
     */
    public function getMultiple($keys, $default = null)
    {
        return [];
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
     * @param array<mixed> $keys
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return false;
    }

    public function has($key)
    {
        return false;
    }
}
