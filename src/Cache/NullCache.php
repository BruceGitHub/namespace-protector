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

    /*** @inheritDoc */
    public function getMultiple($keys, $default = null)
    {
        return [];
    }

    /*** @inheritDoc */
    public function setMultiple($values, $ttl = null)
    {
        return false;
    }

    /*** @inheritDoc */
    public function deleteMultiple($keys)
    {
        return false;
    }

    /*** @inheritDoc */
    public function has($key)
    {
        return false;
    }
}
