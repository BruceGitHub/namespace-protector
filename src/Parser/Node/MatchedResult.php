<?php declare(strict_types=1);

namespace NamespaceProtector\Parser\Node;

class MatchedResult implements MatchedResultInterface
{
    /** @var string */
    private $info;

    public function __construct(string $info)
    {
        $this->info = $info;
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
