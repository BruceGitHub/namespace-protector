<?php declare(strict_types=1);

namespace NamespaceProtector\Config;

use NamespaceProtector\Common\PathInterface;

interface ConfigTemplateCreatorInterface
{
    public function create(PathInterface $destinationPathFileJson): void;
}
