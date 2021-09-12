<?php declare(strict_types=1);

namespace NamespaceProtector\Parser;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultParserInterface;

interface ParserInterface
{
    public function parseFile(PathInterface $pathFile): ResultParserInterface;
}
