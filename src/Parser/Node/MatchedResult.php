<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

class MatchedResult implements MatchedResultInterface
{
    public function __construct()
    {
    }

    public function matched(): bool
    {
        return true;
    }
}
