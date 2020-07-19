<?php declare(strict_types=1);

namespace NamespaceProtector\Parser;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultCollectorReadable;
use NamespaceProtector\Result\ResultParserInterface;

interface ParserInterface
{
    public function parseFile(PathInterface $pathInterface): ResultParserInterface;

    public function getListResult(): ResultCollectorReadable;
}
