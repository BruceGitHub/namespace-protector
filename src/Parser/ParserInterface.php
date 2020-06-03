<?php

namespace App\Parser;

use App\Common\PathInterface;
use App\Result\ResultCollector;

interface ParserInteface {
    public function parseFile(PathInterface $pathInterface): void; 
    public function getListResult(): ResultCollector;
}