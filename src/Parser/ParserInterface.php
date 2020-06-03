<?php

namespace NamespaceProtector\Parser;

use NamespaceProtector\Common\PathInterface;
use NamespaceProtector\Result\ResultCollector;

interface ParserInterface {
    public function parseFile(PathInterface $pathInterface): void; 
    public function getListResult(): ResultCollector;
}
