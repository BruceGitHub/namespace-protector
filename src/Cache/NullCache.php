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
        return true;
    }

    /*** @inheritDoc */
    public function delete($key)
    {
        return true;
    }

    /*** @inheritDoc */
    public function clear()
    {
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
        return true;
    }

    /**
     * @param array<mixed> $keys
     * @return bool
     */
    public function deleteMultiple($keys)
    {
        return true;
    }

    public function has($key)
    {
        return false;
    }
}
