<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

class EmptyMatchedResult implements MatchedResultInterface
{
    public function matched(): bool
    {
        return false;
    }
}
