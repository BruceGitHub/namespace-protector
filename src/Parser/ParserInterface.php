<?php declare(strict_types=1);

namespace NamespaceProtector\Parser;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultCollectorReadable;

interface ParserInterface
{
    public function parseFile(PathInterface $pathInterface): void;

    public function getListResult(): ResultCollectorReadable;
}
