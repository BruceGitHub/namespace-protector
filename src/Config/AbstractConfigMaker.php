<?php declare(strict_types=1);

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\PathInterface;

abstract class AbstractConfigMaker
{
    abstract public function createFromFile(PathInterface $path): Config;

    /** @param array<string,string> $parameters */
    abstract public function createFromItSelf(Config $config, array $parameters): Config;
}
