<?php

declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

class MatchedResult implements MatchedResultInterface
{
    public function __construct(private string $info)
    {
    }

    public function matched(): bool
    {
        return true;
    }

    public function getInfo(): string
    {
        return $this->info;
    }
}
