<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use PhpParser\NodeVisitor;
use NamespaceProtector\Result\ResultCollectedReadable;

interface NamespaceProtectorVisitorInterface extends NodeVisitor
{
    public function getStoreProcessedResult(): ResultCollectedReadable;

    public function clearStoredProcessedResult(): void;
}
