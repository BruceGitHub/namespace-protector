<?php declare(strict_types=1);

namespace NamespaceProtector\Showable;

use NamespaceProtector\Config;

interface ConfigShowableInterface
{
    public function show(Config\Config $config): void;
}
