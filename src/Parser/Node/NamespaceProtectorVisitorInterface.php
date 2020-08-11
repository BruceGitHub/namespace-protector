<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

use PhpParser\NodeVisitor;
use NamespaceProtector\Result\ResultCollectorReadable;

interface NamespaceProtectorVisitorInterface extends NodeVisitor
{
    public function getStoreProcessNodeResult(): ResultCollectorReadable;
}
