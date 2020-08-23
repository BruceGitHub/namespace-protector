<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

interface MatchedResultInterface
{
    public function matched(): bool;
}
