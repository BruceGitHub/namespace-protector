<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use PhpParser\NodeVisitor;
use NamespaceProtector\Result\ErrorResult;
use NamespaceProtector\Result\ResultCollectedReadable;

interface NamespaceProtectorVisitorInterface extends NodeVisitor
{
    /**
    * @return ResultCollectedReadable<ErrorResult>
    */
    public function getStoreProcessedResult(): ResultCollectedReadable;

    public function clearStoredProcessedResult(): void;
}
